<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function invitation(): HasOne
    {
        return $this->hasOne(Invitation::class);
    }
}
