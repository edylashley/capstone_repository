<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Migrate existing specialization data to the new pivot table
        try {
            $projects = \Illuminate\Support\Facades\DB::table('projects')->whereNotNull('specialization')->get();
            foreach ($projects as $project) {
                $category = \Illuminate\Support\Facades\DB::table('categories')->where('name', $project->specialization)->first();
                if ($category) {
                    \Illuminate\Support\Facades\DB::table('category_project')->insert([
                        'category_id' => $category->id,
                        'project_id' => $project->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log or ignore if table/columns don't exist yet in some environments
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_project');
    }
};
