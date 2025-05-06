<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashcardItems extends Model
{
    public $timestamps = true;
    protected $connection = 'mysql';
    protected $table = 'flashcard_items';
    protected $with = ['flashcard'];

    public function flashcard()
    {
        return $this->belongsTo(Flashcards::class, 'flashcard_id', 'id');
    }
}