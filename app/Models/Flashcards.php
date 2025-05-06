<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flashcards extends Model
{
    public $timestamps = true;
    protected $connection = 'mysql';
    protected $table = 'flashcards';
    
    public function flashcardItems()
    {
        return $this->hasMany(FlashcardItems::class, 'flashcard_id', 'id');
    }


    protected $with = ['category'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

}