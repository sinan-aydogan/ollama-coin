<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoinKline extends Model
{
    protected $fillable = [
        'coin_id',
        'open',
        'high',
        'low',
        'close',
        'volume',
        'diff',
        'diff_percent',
        'kline_at',
    ];

    protected $casts = [
        'kline_at' => 'datetime',
    ];

    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }
}
