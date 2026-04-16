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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('abstract')->nullable();
            $table->smallInteger('year')->unsigned()->nullable();
            $table->unsignedBigInteger('adviser_id')->nullable();
            $table->string('status')->default('pending'); // pending, verified, published, archived
            $table->string('program')->default('CSIT');
            $table->string('specialization')->nullable();
            $table->json('keywords')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('adviser_id')->references('id')->on('users')->nullOnDelete();
            $table->unique(['title','year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
