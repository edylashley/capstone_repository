<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    /**
     * Store a new support ticket (any authenticated user).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:bug,correction,account,general',
            'subject'  => 'required|string|max:255',
            'message'  => 'required|string|max:5000',
        ]);

        // Silently prune any expired tickets
        SupportTicket::whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->delete();

        $ticket = SupportTicket::create([
            'user_id'  => auth()->id(),
            'email'    => auth()->user()->email,
            'category' => $validated['category'],
            'subject'  => $validated['subject'],
            'message'  => $validated['message'],
            'status'   => 'pending',
        ]);

        // Log the activity
        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'support_ticket_created',
            'target_type' => 'support_ticket',
            'target_id'   => $ticket->id,
            'ip'          => $request->ip(),
            'meta'        => [
                'subject'  => $ticket->subject,
                'category' => $ticket->category,
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
        if ($request->filled('category') && in_array($request->category, ['bug', 'correction', 'account', 'general'])) {
            $query->where('category', $request->category);
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

        $msg = $validated['status'] === 'resolved'
            ? 'Ticket resolved. The user will see your reply and the record will auto-delete in 3 days.'
            : 'Ticket reopened.';

        return back()->with('success', $msg);
    }

    /**
     * Admin: delete a ticket.
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
}

