<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $table = 'classrooms';

    protected $fillable = [
        'name',
        'level',
        'capacity',
        'room_number',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }
}

