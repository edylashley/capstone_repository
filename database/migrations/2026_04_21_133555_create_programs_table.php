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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('abbreviation')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed initial data
        \DB::table('programs')->insert([
            ['name' => 'Bachelor of Science in Information Technology', 'abbreviation' => 'BSInT', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bachelor of Science in Computer Science', 'abbreviation' => 'Com-Sci', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
