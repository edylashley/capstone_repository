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
}
