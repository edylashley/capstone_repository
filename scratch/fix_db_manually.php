<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tables = ['users', 'projects', 'categories'];

foreach ($tables as $table) {
    $hasColumn = Schema::hasColumn($table, 'deleted_at');
    echo "Table '$table' has 'deleted_at': " . ($hasColumn ? "YES" : "NO") . "\n";
    
    if (!$hasColumn) {
        try {
            DB::statement("ALTER TABLE `$table` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL");
            echo "Successfully ADDED 'deleted_at' to '$table'\n";
        } catch (\Exception $e) {
            echo "FAILED to add to '$table': " . $e->getMessage() . "\n";
        }
    }
}
