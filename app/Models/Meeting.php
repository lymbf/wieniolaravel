<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'date',
        'type',
        'location',
        'title',
        'description',
        'status',
    ];

    // Relacja: spotkanie ma wiele komentarzy
    public function comments()
    {
        return $this->hasMany(MeetingComment::class);
    }

    // Relacja: spotkanie ma wiele załączników
    public function attachments()
    {
        return $this->hasMany(MeetingAttachment::class, 'meeting_id');
    }

    public function histories() {
        return $this->hasMany(\App\Models\MeetingHistory::class);
    }
    

}
