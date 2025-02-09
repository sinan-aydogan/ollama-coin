<?php

namespace App\Repositories\Exchange;

use App\Interfaces\ExchangeRepositoryInterface;
use App\Models\Wallet;

class BinanceRepository implements ExchangeRepositoryInterface
{
    protected Wallet $wallet;

    public function __construct(Wallet $wallet=null)
    {
        $this->wallet = $wallet;
    }

    public function getBalance(): array
    {
        // Binance API does not have a balance endpoint

        return [];
    }

    public function getCoinList(): void
    {
        // Binance API does not have a coin list endpoint
    }

    public function getCoinTicker($symbol): array
    {
        // Binance API does not have a coin ticker endpoint

        return [];
    }

    public function getTickersAllCoins(): array
    {
        // Binance API does not have a tickers all coins endpoint

        return [];
    }
}
