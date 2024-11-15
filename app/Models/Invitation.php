<?php

namespace App\Models;

use App\Enums\VisitorCheckInStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    protected $appends = [
        'scan_url',
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
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=';

        return Attribute::get(fn () => $qrUrl . urlencode($this->scan_url));
    }

    public function scopeCheckInStatus(Builder $query, ?VisitorCheckInStatus $status = null): Builder
    {
        return $query
            ->when($status, fn (Builder $query, VisitorCheckInStatus $status) => match ($status) {
                VisitorCheckInStatus::NOT_CHECKED_IN => $query->whereDoesntHave('checkIns'),
                VisitorCheckInStatus::PARTIALLY_CHECKED_IN => $query->whereHas('checkIns'),
            });
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

    public function oldestCheckIn(): HasOne
    {
        return $this
            ->hasOne(CheckIn::class)
            ->oldestOfMany();
    }

    public function latestCheckIn(): HasOne
    {
        return $this
            ->hasOne(CheckIn::class)
            ->latestOfMany();
    }
}
