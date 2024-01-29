<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];

    public function gates(): BelongsToMany
    {
        return $this
            ->belongsToMany(Gate::class)
            ->using(CategoryGate::class)
            ->withTimestamps();
    }

    public function visitors(): BelongsToMany
    {
        return $this
            ->belongsToMany(Gate::class)
            ->using(CategoryVisitor::class)
            ->withTimestamps();
    }
}
