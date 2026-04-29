<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Project;

$project = Project::find(11);
if (!$project) {
    echo "Project 11 not found.\n";
    exit;
}

$manuscript = $project->files->firstWhere('type', 'manuscript');
if ($manuscript) {
    echo "Project ID: " . $project->id . "\n";
    echo "Manuscript File ID: " . $manuscript->id . "\n";
    echo "Route would be: " . route('files.view', $manuscript) . "\n";
} else {
    echo "No manuscript found for project 11.\n";
}
