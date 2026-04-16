<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Show the system settings page.
     */
    public function index()
    {
        $deadline = Setting::get('submission_deadline', '');
        $deadlineDate = '';
        $deadlineTime = '';

        if ($deadline) {
            $dt = \Carbon\Carbon::parse($deadline);
            $deadlineDate = $dt->format('Y-m-d');
            $deadlineTime = $dt->format('H:i');
        }

        $settings = [
            'max_upload_size' => Setting::get('max_upload_size', '10'),
            'max_attachment_size' => Setting::get('max_attachment_size', '50'),
            'allowed_file_types' => Setting::get('allowed_file_types', 'pdf,zip'),
            'academic_year' => Setting::get('academic_year', date('Y')),
            'repository_name' => Setting::get('repository_name', 'CSIT Capstone Repository'),
            'maintenance_mode' => Setting::get('maintenance_mode', '0'),
            'submissions_open' => Setting::get('submissions_open', '1'),
            'submission_deadline_date' => $deadlineDate,
            'submission_deadline_time' => $deadlineTime,
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'max_upload_size' => 'required|numeric|min:1',
            'max_attachment_size' => 'required|numeric|min:1',
            'allowed_file_types' => 'required|string',
            'academic_year' => 'required|integer|min:2000',
            'repository_name' => 'required|string|max:255',
            'maintenance_mode' => 'nullable|boolean',
            'submissions_open' => 'nullable|boolean',
            'submission_deadline_date' => 'nullable|date',
            'submission_deadline_time' => 'nullable|string',
        ]);

        // Combine Date and Time
        $combinedDeadline = null;
        if (!empty($validated['submission_deadline_date'])) {
            $time = $validated['submission_deadline_time'] ?: '00:00';
            $combinedDeadline = $validated['submission_deadline_date'] . ' ' . $time . ':00';
        }

        // Capture old values for logging
        $oldSettings = [];
        $keysToLog = ['max_upload_size', 'max_attachment_size', 'allowed_file_types', 'academic_year', 'repository_name', 'maintenance_mode', 'submissions_open', 'submission_deadline'];
        foreach ($keysToLog as $k) {
            $oldSettings[$k] = Setting::get($k);
        }

        foreach ($validated as $key => $value) {
            if ($key !== 'submission_deadline_date' && $key !== 'submission_deadline_time') {
                Setting::set($key, $value);
            }
        }

        Setting::set('submission_deadline', $combinedDeadline ?: '');
        
        // Handle checkboxes/missing values from request
        Setting::set('maintenance_mode', isset($validated['maintenance_mode']) ? '1' : '0');
        Setting::set('submissions_open', isset($validated['submissions_open']) ? '1' : '0');

        // Calculate specific changes
        $actualChanges = [];
        foreach ($keysToLog as $k) {
            $newValue = Setting::get($k);
            if ($oldSettings[$k] !== $newValue) {
                $actualChanges[$k] = [
                    'from' => $oldSettings[$k],
                    'to' => $newValue
                ];
            }
        }

        if (!empty($actualChanges)) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'settings_updated',
                'target_type' => 'system',
                'target_id' => 0,
                'ip' => $request->ip(),
                'meta' => [
                    'changes' => $actualChanges,
                    'summary' => 'Modified: ' . implode(', ', array_keys($actualChanges))
                ]
            ]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'System settings updated successfully.');
    }
}
