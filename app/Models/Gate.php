<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Gate extends Model
{
    protected $fillable = [
        'agenda_id',
        'name',
    ];

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class);
    }

    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class)
            ->using(GateUser::class)
            ->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this
            ->belongsToMany(Category::class)
            ->using(CategoryGate::class)
            ->withTimestamps();
    }
}
