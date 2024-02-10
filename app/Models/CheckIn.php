<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as BelongsToThroughTrait;

class CheckIn extends Model
{
    use BelongsToThroughTrait;

    protected $fillable = [
        'invitation_id',
        'gate_id',
        'user_id',
        'checked_in_at',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    protected $appends = [
        'checked_in_at_label',
    ];

    public function checkedInAtLabel(): Attribute
    {
        return Attribute::get(function () {
            $checkIn = $this->checked_in_at;

            if (blank($checkIn)) {
                return null;
            }

            if ($checkIn->isToday()) {
                return $checkIn->format('H:i');
            }

            return $checkIn->format('M j, Y H:i:s');
        });
    }

    public function agenda(): BelongsToThrough
    {
        return $this->belongsToThrough(Agenda::class, [Visitor::class, Invitation::class]);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function gate(): BelongsTo
    {
        return $this->belongsTo(Gate::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
