<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'user_id',
        'content',
    ];

    // Komentarz należy do spotkania
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    // Komentarz ma wiele załączników
    public function attachments()
    {
        return $this->hasMany(MeetingCommentAttachment::class, 'meeting_comment_id');
    }

    // Komentarz należy do użytkownika
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
