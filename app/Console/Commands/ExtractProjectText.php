<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Services\PDFValidator;
use Illuminate\Support\Facades\Storage;

class ExtractProjectText extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:extract-text {--force : Extract even if full_text is already set}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retroactively extract text from project manuscript PDFs and save to full_text column';

    /**
     * Execute the console command.
     */
    public function handle(PDFValidator $validator)
    {
        $query = Project::query();

        if (!$this->option('force')) {
            $query->whereNull('full_text');
        }

        $projects = $query->with('files')->get();

        if ($projects->isEmpty()) {
            $this->info('No projects found needing text extraction.');
            return 0;
        }

        $this->info("Found {$projects->count()} projects to process.");
        $bar = $this->output->createProgressBar($projects->count());
        $bar->start();

        foreach ($projects as $project) {
            $manuscript = $project->files->where('type', 'manuscript')->first();

            if (!$manuscript) {
                $bar->advance();
                continue;
            }

            $path = Storage::disk('public')->path($manuscript->path);

            if (!file_exists($path)) {
                $this->error("\nFile missing for project ID {$project->id}: {$path}");
                $bar->advance();
                continue;
            }

            try {
                $result = $validator->validate($path);
                if (isset($result['text']) && !empty($result['text'])) {
                    $project->full_text = $result['text'];
                    $project->save();
                }
            } catch (\Exception $e) {
                $this->error("\nError processing project ID {$project->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nDone!");

        return 0;
    }
}
