<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 
        'user_id', 
        'title', 
        'description', 
        'status',
        'asked_at',
        'status_changed_by',      // dodane
        'status_changed_at'       // dodane
    ];

    protected $casts = [
        'asked_at' => 'datetime',
        'status_changed_at' => 'datetime',  // dodane
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
