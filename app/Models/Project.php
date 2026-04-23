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
}
