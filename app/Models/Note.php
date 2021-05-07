<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    public function tasks()
    {
        return $this->hasMany('App\Models\Task')
            ->orderByRaw('is_done, due_time is null, due_time, created_at desc')
            ->select(
                'id',
                'name',
                'due_time',
                'is_done'
            )
            ->limit(20);
    }
}
