<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compositions extends Model
{
    public $timestamps = true;
    protected $connection = 'mysql';
    protected $table = 'compositions';
}