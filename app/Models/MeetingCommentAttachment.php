<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingCommentAttachment extends Model
{
    protected $fillable = [
        'meeting_comment_id',
        'file_path',
        'original_name',
    ];

    public function comment()
    {
        return $this->belongsTo(MeetingComment::class, 'meeting_comment_id');
    }

    public function meetingComment()
{
    return $this->belongsTo(MeetingComment::class);
}

}
