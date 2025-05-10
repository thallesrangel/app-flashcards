<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyMissions extends Model
{
    public $timestamps = true;
    protected $connection = 'mysql';
    protected $table = 'weekly_missions';
}