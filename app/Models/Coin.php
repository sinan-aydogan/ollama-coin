<?php

namespace App\Models;

use App\Interfaces\ExchangeRepositoryInterface;
use App\Repositories\Exchange\BinanceRepository;
use App\Repositories\Exchange\BtcTurkRepository;
use App\Settings\GeneralSettings;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Coin extends Model
{
    protected $fillable = ['symbol', 'base_symbol', 'quote_symbol'];

    protected $appends = ['price', 'price_change_direction'];

    /**
     * @throws Exception
     */
    public function getExchangeRepository(): ExchangeRepositoryInterface
    {
        $exchange = (new GeneralSettings())->default_exchange_for_market_data;

        return match ($exchange) {
            'btc_turk' => new BtcTurkRepository(),
            'binance' => new BinanceRepository(),
            default => throw new Exception('Exchange not found'),
        };
    }

    public function tickers(): HasMany
    {
        return $this->hasMany(CoinTicker::class);
    }

    public function lastTicker(): HasOne
    {
        return $this->hasOne(CoinTicker::class)->latest('created_at');
    }

    public function klines(): HasMany
    {
        return $this->hasMany(CoinKline::class);
    }

    public function getPriceAttribute()
    {
        $latestTicker = $this->tickers()->latest('created_at')->first();
        return $latestTicker ? $latestTicker->last : null;
    }

    public function getPriceChangeDirectionAttribute(): ?string
    {
        $latestTicker = $this->tickers()->latest('created_at')->first();
        return $latestTicker ? $latestTicker->price_change > 0 ? 'up' : 'down' : null;
    }

    /**
     * @throws Exception
     */
    public function getCoinList(): array
    {
        return $this->getExchangeRepository()->getCoinList();
    }

    /**
     * @throws Exception
     */
    public function getCoinTicker($symbol): array
    {
        return $this->getExchangeRepository()->getCoinTicker($symbol);
    }

    /**
     * @throws Exception
     */
    public function getTickersAllCoins(): array
    {
        return $this->getExchangeRepository()->getTickersAllCoins();
    }

    /**
     * @throws Exception
     */
    public function getKlineData($from = null, $to = null, $resolution = null): ?CoinKline
    {
        return $this->getExchangeRepository()->getKlineData($this->symbol, $from, $to, $resolution);
    }

    /**
     * @throws Exception
     */
    public function getKlineDataAllCoins($from = null, $to = null, $resolution = null): ?array
    {
        return $this->getExchangeRepository()->getKlineDataAllCoins($from, $to, $resolution);
    }
}
