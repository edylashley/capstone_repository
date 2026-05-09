<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Services\EmbeddingService;
use Illuminate\Support\Facades\Log;

class GenerateEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:generate-embeddings {--force : Regenerate embeddings for all projects}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate semantic search embeddings for projects via Gemini API';

    /**
     * Execute the console command.
     */
    public function handle(EmbeddingService $embeddingService)
    {
        $force = $this->option('force');

        $query = Project::query();
        
        if (!$force) {
            $query->whereNull('embedding');
        }

        $projects = $query->get();
        
        $total = $projects->count();
        if ($total === 0) {
            $this->info('No projects need embeddings generated.');
            return;
        }

        $this->info("Found {$total} projects to process. Generating embeddings...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $successCount = 0;
        $failCount = 0;

        foreach ($projects as $project) {
            $text = $embeddingService->buildProjectText($project);
            $embedding = $embeddingService->generate($text);

            if ($embedding) {
                $project->update(['embedding' => $embedding]);
                $successCount++;
            } else {
                $failCount++;
                $this->error("\nFailed to generate embedding for Project ID: {$project->id}");
            }

            $bar->advance();
            // Sleep for 500ms to avoid rate limiting on the free tier (15 requests per minute limit on some tiers, but 1500 per day)
            usleep(500000); 
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("Completed! Success: {$successCount}, Failed: {$failCount}");
    }
}

