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
        'time',
    ];

    protected $casts = [
        'kline_at' => 'datetime',
        'open' => 'array',
        'high' => 'array',
        'low' => 'array',
        'close' => 'array',
        'volume' => 'array',
        'time' => 'array',
    ];

    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class);
    }
}
