<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    protected $appends = [
        'is_started',
    ];

    public function isStarted(): Attribute
    {
        return Attribute::get(fn () => now()->gte($this->started_at));
    }

    public function isUncertain(): bool
    {
        return is_null($this->finished_at);
    }

    public function isADay(): bool
    {
        if ($this->isUncertain()) {
            return false;
        }

        return $this->started_at->startOfDay()
            ->equalTo($this->finished_at->startOfDay());
    }

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class);
    }

    public function gates(): HasMany
    {
        return $this->hasMany(Gate::class);
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(Visitor::class);
    }

    public function invitations(): HasManyThrough
    {
        return $this->hasManyThrough(Invitation::class, Visitor::class);
    }

    public function invitation(): HasManyThrough
    {
        return $this->hasOneThrough(Invitation::class, Visitor::class);
    }
}
