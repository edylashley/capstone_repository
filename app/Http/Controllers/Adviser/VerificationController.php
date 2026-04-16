<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function store(\App\Http\Requests\AdviserVerificationRequest $request, $projectId)
    {
        $data = $request->validated();
        $project = \App\Models\Project::findOrFail($projectId);

        // Only allow verification for pending projects
        if ($project->status !== 'pending') {
            return response()->json(['message' => 'Project not in pending state'], 422);
        }

        $verification = \App\Models\AdviserVerification::create([
            'project_id' => $project->id,
            'adviser_id' => $request->user()->id,
            'notes' => $data['notes'] ?? null,
            'recommended' => (bool) $data['recommended'],
            'verified_at' => now(),
        ]);

        if ($verification->recommended) {
            $project->status = 'verified';
            $project->save();
        }

        \App\Models\ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'adviser_verification',
            'target_type' => 'project',
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => ['recommended' => (bool)$data['recommended']],
        ]);

        return response()->json(['message' => 'Verification recorded', 'recommended' => (bool)$data['recommended']]);
    }

    public function revalidatePdf(Request $request, int $id)
    {
        $project = \App\Models\Project::with('files')->findOrFail($id);

        if ($request->user()->id !== $project->adviser_id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Find manuscript
        $manuscript = $project->files->firstWhere('type', 'manuscript');
        if (!$manuscript) {
            return redirect()->back()->with('error', 'No manuscript available to analyze.');
        }

        $fullPath = \Storage::disk('public')->path($manuscript->path);
        
        $validator = app(\App\Services\PDFValidator::class);
        $validation = $validator->validate($fullPath);

        // Update project with new validation results
        $combinedNotes = [
            '✓ Validator: ' . ($validation['valid'] ? 'Passed' : 'Failed')
        ];
        $combinedNotes = array_merge($combinedNotes, $validation['notes']);

        $project->manuscript_validated = $validation['valid'];
        $project->manuscript_validation_notes = implode("\n", $combinedNotes);
        $project->save();

        \App\Models\ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'manuscript_revalidated_adviser',
            'target_type' => 'project',
            'target_id' => $project->id,
            'ip' => $request->ip(),
            'meta' => ['valid' => $validation['valid'], 'notes' => $combinedNotes],
        ]);

        return redirect()->back()->with('status', 'System Verification Report updated successfully.');
    }
}
