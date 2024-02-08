<?php

namespace App\Models;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'agenda_id',
        'name',
        'address',
        'phone_number',
    ];

    public function scopeSearch(Builder $query, $term): Builder
    {
        return $query->where(
            fn (Builder $query) => $query
                ->orWhere('name', 'like', "%$term%")
                ->orWhere('address', 'like', "%$term%")
                ->orWhere('phone_number', 'like', "%$term%")
                ->orWhereHas('invitation', fn ($query) => $query->where('code', 'like', "%$term%"))
        );
    }

    public function scopeInvited(Builder $query): Builder
    {
        return $query->whereHas('invitation');
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class);
    }

    public function categories(): BelongsToMany
    {
        return $this
            ->belongsToMany(Category::class)
            ->using(CategoryVisitor::class)
            ->withTimestamps();
    }

    public function genderCategories(): BelongsToMany
    {
        return $this
            ->belongsToMany(Category::class)
            ->using(CategoryVisitor::class)
            ->where('type', CategoryType::GENDER)
            ->withTimestamps();
    }

    public function colorCategories(): BelongsToMany
    {
        return $this
            ->belongsToMany(Category::class)
            ->using(CategoryVisitor::class)
            ->where('type', CategoryType::COLOR)
            ->withTimestamps();
    }

    public function invitation(): HasOne
    {
        return $this->hasOne(Invitation::class);
    }

    public function checkIns(): HasManyThrough
    {
        return $this->hasManyThrough(CheckIn::class, Invitation::class);
    }
}
