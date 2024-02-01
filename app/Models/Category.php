<?php

namespace App\Models;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];

    protected $casts = [
        'type' => CategoryType::class,
    ];

    protected $appends = [
        'label',
    ];

    public function label(): Attribute
    {
        return Attribute::get(fn () => sprintf('[%s] %s', $this->type->getLabel(), $this->name));
    }

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
            ->belongsToMany(Visitor::class)
            ->using(CategoryVisitor::class)
            ->withTimestamps();
    }
}
