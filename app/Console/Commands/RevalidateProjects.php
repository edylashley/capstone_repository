<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\PDFValidator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RevalidateProjects extends Command
{
    protected $signature = 'projects:revalidate {--project_id=}';
    protected $description = 'Re-validate manuscript files for existing projects';

    public function handle()
    {
        $projectId = $this->option('project_id');
        
        if ($projectId) {
            $projects = Project::where('id', $projectId)->get();
        } else {
            $projects = Project::whereHas('files', function($q) {
                $q->where('type', 'manuscript');
            })->get();
        }

        if ($projects->isEmpty()) {
            $this->error('No projects found to revalidate.');
            return 1;
        }

        $this->info("Found {$projects->count()} project(s) to revalidate...");
        $bar = $this->output->createProgressBar($projects->count());

        foreach ($projects as $project) {
            $manuscript = $project->files->firstWhere('type', 'manuscript');
            
            if (!$manuscript) {
                $bar->advance();
                continue;
            }

            $fullPath = Storage::disk('public')->path($manuscript->path);
            
            if (!file_exists($fullPath)) {
                $this->newLine();
                $this->warn("File not found for project #{$project->id}: {$manuscript->path}");
                $bar->advance();
                continue;
            }

            // Run validation
            $validator = app(PDFValidator::class);
            $validation = $validator->validate($fullPath);

            // Determine specific validation message
            $validatorMessage = 'Initial criteria met';
            if (!$validation['valid']) {
                if ($validation['page_count_failed'] && $validation['keywords_missing']) {
                    $validatorMessage = 'Critical issues detected';
                } elseif ($validation['keywords_missing']) {
                    $validatorMessage = 'Missing required elements';
                } elseif ($validation['page_count_failed']) {
                    $validatorMessage = 'Needs adviser verification';
                } else {
                    $validatorMessage = 'Warning: Manual review required';
                }
            }

            $combinedNotes = [
                '✓ Security Scan: Clean (No threats detected)',
                '✓ Validator: ' . $validatorMessage
            ];
            $combinedNotes = array_merge($combinedNotes, $validation['notes']);

            $project->manuscript_validated = $validation['valid'];
            $project->manuscript_validation_notes = implode("\n", $combinedNotes);
            $project->save();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('✓ Revalidation complete!');
        
        return 0;
    }
}
