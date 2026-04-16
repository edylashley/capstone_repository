<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectAuthor extends Model
{
    protected $table = 'project_authors';

    protected $fillable = [
        'project_id',
        'user_id',
        'author_order',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
