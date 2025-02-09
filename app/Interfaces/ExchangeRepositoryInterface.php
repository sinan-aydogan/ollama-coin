<?php

namespace App\Interfaces;

interface ExchangeRepositoryInterface
{
    public function getBalance();

    public function getCoinList();

    public function getCoinTicker($symbol);
    public function getTickersAllCoins();

    public function getKlineData($symbol, $from=null, $to=null, $resolution=null);

    public function getKlineDataAllCoins($symbols=null, $from=null, $to=null, $resolution=null);
}
