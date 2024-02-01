<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'agenda_id',
        'image_path',
        'image_disk',
        'description',
    ];

    public function imageUrl(): Attribute
    {
        return Attribute::get(
            fn () => Storage::disk($this->image_disk)->url($this->image_path)
        );
    }

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class);
    }
}
