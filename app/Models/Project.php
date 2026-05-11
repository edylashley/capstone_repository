<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $casts = [
        'keywords' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'manuscript_validated' => 'boolean',
        'embedding' => 'array',
    ];

    protected $fillable = [
        'title',
        'slug',
        'abstract',
        'year',
        'adviser_id',
        'status',
        'program',
        'specialization',
        'keywords',
        'is_published',
        'published_at',
        'manuscript_validated',
        'manuscript_validation_notes',
        'rejection_reason',
        'authors_list',
        'adviser_name',
        'custom_category',
        'embedding',
    ];

    public function adviser()
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function authors()
    {
        return $this->belongsToMany(User::class, 'project_authors', 'project_id', 'user_id')
            ->withPivot('author_order')
            ->orderBy('project_authors.author_order');
    }

    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_project');
    }

    public function verification()
    {
        return $this->hasOne(AdviserVerification::class);
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'published' => 'Published',
            'pending' => 'Pending Review',
            'returned' => 'Returned for Revision',
            'archived' => 'Archived',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get CSS classes for status badge.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'published' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
            'pending' => 'bg-amber-500/10 text-amber-600 dark:text-amber-500 border-amber-500/20',
            'returned' => 'bg-rose-500/10 text-rose-600 dark:text-rose-500 border-rose-500/20',
            'archived' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
            default => 'bg-gray-500/10 text-gray-600 dark:text-gray-400 border-gray-500/20',
        };
    }

    protected static function boot()
    {
        parent::boot();

        // Invalidate the related projects cache whenever a project is updated or deleted
        static::saved(fn($project) => $project->clearRelatedCache());
        static::deleted(fn($project) => $project->clearRelatedCache());
    }

    /**
     * Clear the recommendation cache for this project.
     */
    public function clearRelatedCache()
    {
        \Illuminate\Support\Facades\Cache::forget("project_{$this->id}_related_ids_4");
        \Illuminate\Support\Facades\Cache::forget("project_{$this->id}_related_ids_5");
        \Illuminate\Support\Facades\Cache::forget("project_{$this->id}_related_ids_10");
    }

    /**
     * Recommendation Engine: Get projects related to this one.
     */
    public function getRelatedProjects($limit = 5)
    {
        $cacheKey = "project_{$this->id}_related_ids_{$limit}";

        // Cache only the IDs (much smaller/faster) for 24 hours
        $relatedIds = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addDay(), function () use ($limit) {
            // 1. Get all other published candidate projects
            $candidates = self::where('id', '!=', $this->id)
                ->where('status', 'published')
                ->whereNotNull('embedding')
                ->get();

            // 2. Calculate hybrid similarity score for each candidate
            $scored = $candidates->map(function ($project) {
                $score = 0;

                // A. Semantic Similarity (Gemini AI) - Weight: 60%
                if (!empty($this->embedding) && !empty($project->embedding)) {
                    $similarity = \App\Services\EmbeddingService::cosineSimilarity($this->embedding, $project->embedding);
                    $score += $similarity * 60;
                }

                // B. Keyword Overlap - Weight: 20%
                if (!empty($this->keywords) && !empty($project->keywords)) {
                    $commonKeywords = array_intersect($this->keywords, $project->keywords);
                    $score += min(20, count($commonKeywords) * 5);
                }

                // C. Academic Field (Category) Synergy - Weight: 15%
                $commonCategories = $this->categories->pluck('id')->intersect($project->categories->pluck('id'));
                if ($commonCategories->isNotEmpty()) {
                    $score += 15;
                }

                // D. Institutional Context (Program) - Weight: 5%
                if ($this->program === $project->program) {
                    $score += 5;
                }

                $project->similarity_score = $score;
                return $project;
            });

            // 3. Sort by total score and return only the IDs
            return $scored->sortByDesc('similarity_score')
                ->filter(fn($p) => $p->similarity_score > 10)
                ->take($limit)
                ->pluck('id')
                ->toArray();
        });

        if (empty($relatedIds)) {
            return collect();
        }

        // Fetch the full records for the cached IDs, maintaining the similarity order
        $idString = implode(',', $relatedIds);
        return self::whereIn('id', $relatedIds)
            ->orderByRaw("FIELD(id, {$idString})")
            ->get();
    }
}
