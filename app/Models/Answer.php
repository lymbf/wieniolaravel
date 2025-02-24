<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'user_id',
        'answer_text',
        'answered_at', // Data i godzina udzielenia odpowiedzi
    ];

    protected $casts = [
        'answered_at' => 'datetime',
    ];

    public function question()
    {
        return $this->belongsTo(\App\Models\Question::class);
    }
    

    // Relacja do użytkownika, który udzielił odpowiedzi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacja do załączników - polimorficzna relacja
    public function attachments()
    {
        return $this->morphMany(\App\Models\Attachment::class, 'attachable');
    }
    
    
}
