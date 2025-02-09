<?php

namespace App\Interfaces;

interface ExchangeRepositoryInterface
{
    public function getBalance();

    public function getCoinList();

    public function getCoinTicker($symbol);
    public function getTickersAllCoins();
}
