<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    /**
     * Store a new support ticket (any authenticated user).
     */
    public function store(Request $request)
    {
        $rules = [
            'category' => 'required|in:bug,correction,account,general,others',
            'subject'  => 'nullable|string|max:255',
            'message'  => 'required|string|max:5000',
            'custom_category' => 'nullable|string|max:100',
        ];

        // If guest, email is required. If auth, we use their account email.
        if (!auth()->check()) {
            $rules['email'] = 'required|email|max:255';
        }

        $validated = $request->validate($rules);

        // Silently prune any expired tickets
        SupportTicket::whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->delete();

        $category = $validated['category'];
        if ($category === 'others' && !empty($validated['custom_category'])) {
            $category = $validated['custom_category'];
        }

        $ticket = SupportTicket::create([
            'user_id'  => auth()->id(), // nullable
            'email'    => auth()->check() ? auth()->user()->email : $validated['email'],
            'category' => $category,
            'subject'  => $validated['subject'] ?? Str::limit($validated['message'], 50),
            'message'  => $validated['message'],
            'status'   => 'pending',
        ]);

        // Log the activity (user_id is nullable in ActivityLog too)
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'support_ticket_created',
            'target_type' => 'support_ticket',
            'target_id'   => $ticket->id,
            'ip'          => $request->ip(),
            'meta'        => [
                'subject'  => $ticket->subject,
                'category' => $ticket->category,
                'is_guest' => !auth()->check(),
            ],
        ]);

        return back()->with('success', 'Your support ticket has been submitted! An admin will review it shortly.');
    }

    /**
     * Display the user's own tickets.
     */
    public function myTickets(): View
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->where('category', '!=', 'security')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('support.my-tickets', compact('tickets'));
    }

    /* ───────────────────────────── Admin Methods ─────────────────────────── */

    /**
     * Admin: list all support tickets.
     */
    public function index(Request $request): View
    {
        $query = SupportTicket::with('user')->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['pending', 'resolved'])) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            if ($request->category === 'others') {
                $query->whereNotIn('category', ['bug', 'correction', 'account', 'general', 'security']);
            } elseif (in_array($request->category, ['bug', 'correction', 'account', 'general'])) {
                $query->where('category', $request->category);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => SupportTicket::count(),
            'pending'  => SupportTicket::where('status', 'pending')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
        ];

        return view('admin.support.index', compact('tickets', 'stats'));
    }



    /**
     * Admin: view a single ticket.
     */
    public function show(SupportTicket $ticket): View
    {
        $ticket->load('user');

        return view('admin.support.show', compact('ticket'));
    }

    /**
     * Admin: update ticket status (with optional reply message on resolve).
     */
    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status'      => 'required|in:pending,resolved',
            'admin_reply' => 'nullable|string|max:2000',
        ]);

        $oldStatus = $ticket->status;

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'resolved') {
            // Store the admin reply and schedule auto-deletion in 3 days
            $updateData['admin_reply'] = $validated['admin_reply'] ?? null;
            $updateData['expires_at']  = now()->addDays(3);
        } else {
            // Reopened — clear reply and expiry
            $updateData['admin_reply'] = null;
            $updateData['expires_at']  = null;
        }

        $ticket->update($updateData);

        // If resolved, notify the user via email (works for both guests and registered students)
        if ($validated['status'] === 'resolved') {
            \Illuminate\Support\Facades\Mail::to($ticket->email)->queue(new \App\Mail\SupportTicketResolved($ticket));
        }

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'support_ticket_status_changed',
            'target_type' => 'support_ticket',
            'target_id'   => $ticket->id,
            'ip'          => $request->ip(),
            'meta'        => [
                'subject' => $ticket->subject,
                'changes' => [
                    'status' => ['from' => $oldStatus, 'to' => $validated['status']],
                ],
            ],
        ]);

        $msg = 'Ticket updated.';
        if ($validated['status'] === 'resolved') {
            if ($ticket->category === 'security') {
                $msg = 'Security alert acknowledged and logged.';
            } elseif ($ticket->user_id) {
                $msg = 'Ticket resolved. The student will see your reply on their dashboard and via email.';
            } else {
                $msg = 'Ticket resolved. An email notification has been sent to the guest at ' . $ticket->email . '.';
            }
        } else {
            $msg = 'Ticket reopened.';
        }

        return back()->with('success', $msg);
    }

    /**
     * Admin: delete a single ticket.
     */
    public function destroy(SupportTicket $ticket)
    {
        $subject = $ticket->subject;
        $ticket->delete();

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'support_ticket_deleted',
            'target_type' => 'support_ticket',
            'target_id'   => 0,
            'ip'          => request()->ip(),
            'meta'        => [
                'subject' => $subject,
            ],
        ]);

        return redirect()->route('admin.support.index')->with('success', 'Ticket deleted successfully.');
    }

    /**
     * Admin: bulk delete tickets.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No tickets selected.');
        }

        $count = SupportTicket::whereIn('id', $ids)->count();
        SupportTicket::whereIn('id', $ids)->delete();

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'support_ticket_bulk_deleted',
            'target_type' => 'support_ticket',
            'target_id'   => 0,
            'ip'          => $request->ip(),
            'meta'        => [
                'count' => $count,
                'ids' => $ids,
            ],
        ]);

        return back()->with('success', $count . ' tickets deleted successfully.');
    }
    /**
     * Admin: get count of pending tickets and recent security alerts.
     */
    public function getPendingCount()
    {
        // Count all actually pending tickets
        $pendingCount = SupportTicket::where('status', 'pending')->count();
        
        // Also count security alerts from the last 24 hours so Admin sees them as "new" notifications
        $recentSecurityCount = SupportTicket::where('category', 'security')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return response()->json([
            'count' => $pendingCount + $recentSecurityCount
        ]);
    }
}

