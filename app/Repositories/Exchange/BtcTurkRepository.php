<?php

namespace App\Repositories\Exchange;

use App\Interfaces\ExchangeRepositoryInterface;
use App\Models\Coin;
use App\Models\CoinTicker;
use App\Models\Wallet;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

class BtcTurkRepository implements ExchangeRepositoryInterface
{
    protected ?Wallet $wallet;
    protected Client $client;

    public function __construct(Wallet $wallet=null)
    {
        $this->wallet = $wallet;

        /*Create client*/
        $clientOptions = [
            'base_uri' => 'https://api.btcturk.com/api/',
        ];

        /*Add headers if wallet is not null for authorized end points*/
        if ($this->wallet) {
            $clientOptions['headers'] = $this->authHeader();
        }

        $this->client = new Client($clientOptions);
    }

    public function authHeader(): array
    {
        $apiKey = $this->wallet->api_credentials['key'];
        $apiSecret = $this->wallet->api_credentials['secret'];
        $message = $apiKey.(time()*1000);
        $signatureBytes = hash_hmac('sha256', $message, base64_decode($apiSecret), true);
        $signature = base64_encode($signatureBytes);
        $nonce = time()*1000;

        return [
            'X-PCK' => $apiKey,
            'X-Stamp' => $nonce,
            'X-Signature' => $signature,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getBalance(): array
    {
        $response = $this->client->get('v1/users/balances');
        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return array_filter($data['data'], function ($balance) {
            return $balance['balance'] > 0;
        });
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getCoinList(): mixed
    {
        $response = $this->client->get('v2/ticker');
        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $data = $data['data'];

        /*Filter coins undefined in db*/
        $allCoinsInDb = Coin::all()->pluck('symbol')->toArray();
        $creatingCoinList = array_filter($data, static function ($coin) use ($allCoinsInDb) {
            return !in_array($coin['pair'], $allCoinsInDb);
        });
        $createdCoinList = [];

        /*Create coins that are not in the database*/
        foreach ($creatingCoinList as $coin) {
            $coin = [
                'symbol' => $coin['pair'],
                'base_symbol' => $coin['numeratorSymbol'],
                'quote_symbol' => $coin['denominatorSymbol'],
            ];
            $createdCoinList[] = Coin::create($coin);
        }

        return $createdCoinList;
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getCoinTicker($symbol): mixed
    {
        $coin = Coin::where('symbol', $symbol)->first();

        if (!$coin) {
            return null;
        }

        $response = $this->client->get('v2/ticker', [
            'query' => [
                'pairSymbol' => $symbol,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $data = $data['data'][0];

        /*Create coin ticker data in db*/
        return $coin->tickers()->create([
            'open' => $data['open'],
            'high' => $data['high'],
            'low' => $data['low'],
            'last' => $data['last'],
            'average' => $data['average'],
            'bid' => $data['bid'],
            'ask' => $data['ask'],
            'volume' => $data['volume'],
            'price_change' => $data['daily'],
            'price_change_percent' => $data['dailyPercent'],
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getTickersAllCoins(): array
    {
        $coins = Coin::all();
        $tickers = [];

        foreach ($coins as $coin) {
            $tickers[] = $this->getCoinTicker($coin->symbol);
        }

        return $tickers;
    }
}
