<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingAttachment extends Model
{
    protected $fillable = [
        'meeting_id',
        'file_path',
        'original_name',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
