<?php

namespace App\Console\Commands;

use App\Models\ProjectFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RehashFiles extends Command
{
    protected $signature = 'files:rehash {--type=manuscript}';
    protected $description = 'Compute and store SHA-256 hashes for existing files to enable duplicate detection';

    public function handle()
    {
        $type = $this->option('type');
        $files = ProjectFile::whereNull('file_hash')
            ->when($type, function($q) use ($type) {
                return $q->where('type', $type);
            })
            ->with('project')
            ->get();

        if ($files->isEmpty()) {
            $this->info('No files found that need hashing.');
            return 0;
        }

        $this->info("Found {$files->count()} files to process...");
        $bar = $this->output->createProgressBar($files->count());

        foreach ($files as $file) {
            // Manuscript paths are relative to storage/app/public usually
            // but the database 'path' stores relative to disk root
            
            if (Storage::disk('public')->exists($file->path)) {
                $fullPath = Storage::disk('public')->path($file->path);
                $hash = hash_file('sha256', $fullPath);
                
                $file->file_hash = $hash;
                $file->save();
            } else {
                $this->newLine();
                $this->warn("File missing: {$file->path} (Project: {$file->project_id})");
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('✓ File hashing complete!');
        
        return 0;
    }
}
