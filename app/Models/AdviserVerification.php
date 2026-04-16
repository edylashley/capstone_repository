<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdviserVerification extends Model
{
    protected $fillable = [
        'project_id',
        'adviser_id',
        'notes',
        'recommended',
        'verified_at',
    ];

    protected $casts = [
        'recommended' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function adviser()
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }
}
