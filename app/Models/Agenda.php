<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $fillable = [
        'name',
        'started_at',
        'finished_at',
        'short_description',
        'description',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function isUncertain(): bool
    {
        return is_null($this->finished_at);
    }
}
