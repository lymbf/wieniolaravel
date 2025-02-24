<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id', 'old_date', 'new_date', 'changed_by', 'changed_at'
    ];

    public function changedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'changed_by');
    }
}
