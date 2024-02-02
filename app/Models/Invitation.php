<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class Invitation extends Model
{
    use HasFactory;
    use BelongsToThroughTrait;

    protected $fillable = [
        'visitor_id',
        'code',
        'companion',
    ];

    protected $with = [
        'agenda',
    ];

    public static function generateCode(): string
    {
        return bin2hex(random_bytes(20));
    }

    public function scanUrl(): Attribute
    {
        return Attribute::get(fn () => route('scan-agenda-invitation', ['agendaId' => $this->agenda->id, 'code' => $this->code]));
    }

    public function qrUrl(): Attribute
    {
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=';

        return Attribute::get(fn () => $qrUrl . urlencode($this->scan_url));
    }

    public function agenda(): BelongsToThrough
    {
        return $this->belongsToThrough(Agenda::class, Visitor::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }
}
