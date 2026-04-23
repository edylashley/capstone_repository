<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description'];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'category_project');
    }
}
