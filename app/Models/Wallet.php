<?php

namespace App\Models;

use App\Interfaces\ExchangeRepositoryInterface;
use App\Repositories\Exchange\BinanceRepository;
use App\Repositories\Exchange\BtcTurkRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['name', 'exchange', 'user_id','api_credentials'];

    protected $casts = [
        'api_credentials' => 'array',
    ];

    public const EXCHANGE_OPTIONS = [
        [
            'id' => 'btc_turk',
            'name'=> 'BtcTurk'
        ],
        [
            'id' => 'binance',
            'name'=> 'Binance'
        ],
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @throws Exception
     */
    public function getExchangeRepository(): ExchangeRepositoryInterface
    {
        return match ($this->exchange) {
            'btc_turk' => new BtcTurkRepository($this),
            'binance' => new BinanceRepository($this),
            default => throw new Exception('Exchange not found'),
        };
    }

    /**
     * @throws Exception
     */
    public function getBalance(): array
    {
        return $this->getExchangeRepository()->getBalance();
    }
}
