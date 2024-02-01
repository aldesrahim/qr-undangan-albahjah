<?php

namespace App\Models;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function genderCategories(): BelongsToMany
    {
        return $this
            ->belongsToMany(Category::class)
            ->using(CategoryGate::class)
            ->where('type', CategoryType::GENDER)
            ->withTimestamps();
    }

    public function colorCategories(): BelongsToMany
    {
        return $this
            ->belongsToMany(Category::class)
            ->using(CategoryGate::class)
            ->where('type', CategoryType::COLOR)
            ->withTimestamps();
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }
}
