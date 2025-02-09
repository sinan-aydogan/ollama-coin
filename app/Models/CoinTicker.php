<?php

namespace App\Models;

use App\Events\CoinTickerUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoinTicker extends Model
{
    protected $fillable = [
        'coin_id',
        'open',
        'high',
        'low',
        'last',
        'average',
        'bid',
        'ask',
        'volume',
        'price_change',
        'price_change_percent',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::created(function ($model) {
            event(new CoinTickerUpdated($model));
        });

        static::updated(function ($model) {
            event(new CoinTickerUpdated($model));
        });
    }

    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }
}
