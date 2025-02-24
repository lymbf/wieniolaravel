<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'file_path',
        'original_name',
        'notatka'
    ];

    /**
     * Polimorficzna relacja do modelu, z którym powiązany jest załącznik.
     */
    public function attachable()
    {
        return $this->morphTo();
    }
}
