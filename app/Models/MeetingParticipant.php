<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingParticipant extends Model
{
    protected $fillable = [
        'meeting_id',
        'user_id',
    ];

    // Relacja: uczestnik należy do spotkania
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    // Relacja: uczestnik to użytkownik
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
