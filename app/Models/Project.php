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
    /**
     * Recommendation Engine: Get projects related to this one.
     */
    public function getRelatedProjects($limit = 5)
    {
        $keywordMatches = collect();
        
        if (!empty($this->keywords)) {
            $keywordMatches = self::where('id', '!=', $this->id)
                ->where('status', 'published')
                ->where(function ($query) {
                    foreach ($this->keywords as $keyword) {
                        $query->orWhereJsonContains('keywords', $keyword);
                    }
                })
                ->get();
        }

        $categoryMatches = self::where('id', '!=', $this->id)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereHas('categories', function ($q) {
                    $q->whereIn('categories.id', $this->categories->pluck('id'));
                });
                
                if (!empty($this->custom_category)) {
                    $query->orWhere('custom_category', 'like', '%' . $this->custom_category . '%');
                }
            })
            ->get();

        // Combine and score
        $related = $keywordMatches->merge($categoryMatches)->map(function ($project) {
            $score = 0;
            
            // Score based on keyword overlap
            if (!empty($this->keywords) && !empty($project->keywords)) {
                $commonKeywords = array_intersect($this->keywords, $project->keywords);
                $score += count($commonKeywords) * 10;
            }

            // Score based on category overlap
            $commonCategories = $this->categories->pluck('id')->intersect($project->categories->pluck('id'));
            $score += $commonCategories->count() * 5;

            // Score based on custom category (Other) match
            if (!empty($this->custom_category) && !empty($project->custom_category)) {
                if (strtolower($this->custom_category) === strtolower($project->custom_category)) {
                    $score += 15; // High weight for exact custom category match
                } elseif (str_contains(strtolower($project->custom_category), strtolower($this->custom_category)) || 
                          str_contains(strtolower($this->custom_category), strtolower($project->custom_category))) {
                    $score += 8; // Partial match
                }
            }

            // Score based on program match
            if ($this->program === $project->program) {
                $score += 2;
            }

            $project->similarity_score = $score;
            return $project;
        })
        ->sortByDesc('similarity_score')
        ->unique('id')
        ->take($limit);

        return $related;
    }
}
