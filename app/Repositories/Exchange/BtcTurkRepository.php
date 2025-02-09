<?php

namespace App\Repositories\Exchange;

use App\Interfaces\ExchangeRepositoryInterface;
use App\Models\Coin;
use App\Models\CoinKline;
use App\Models\Wallet;
use App\Settings\AnalysisSettings;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use RuntimeException;

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
            'timeout' => 120.0,
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
            'price_at' => \Carbon\Carbon::createFromTimestamp($data['timestamp']),
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

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getKlineData($symbol, $from=null, $to=null, $resolution = "15"): ?CoinKline
    {
        $coin = Coin::where('symbol', $symbol)->first();
        $analysisSettings = new AnalysisSettings();

        if (!$coin) {
            return null;
        }

        if (!$from) {
            $from = (int) Carbon::now()->subMinutes((int) $analysisSettings->default_kline_data_time_range)->timestamp;
        }
        if (!$to) {
            $to = (int) Carbon::now()->timestamp;
        }
        if (!$resolution) {
            $resolution = $analysisSettings->default_kline_data_resolution;
        }

        $endpoint = "https://graph-api.btcturk.com/v1/klines/history?symbol={$symbol}&from={$from}&to={$to}&resolution={$resolution}";
        $response = $this->client->get($endpoint);

        if ($response->getStatusCode() === 429) {
            throw new RuntimeException('Too many requests to the exchange');
        }

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $coin->klines()->create([
            'open' => $data['o'],
            'high' => $data['h'],
            'low' => $data['l'],
            'close' => $data['c'],
            'volume' => $data['v'],
            'time' => $data['t'],
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function getKlineDataAllCoins($symbols=null, $from=null, $to=null, $resolution = "15"): array
    {
        if (!$symbols) {
            $symbols = Coin::all()->pluck('symbol')->toArray();
        }

        $klines = [];

        foreach ($symbols as $symbol) {
            $kline = $this->getKlineData($symbol, $from, $to, $resolution);

            if (!$kline) {
                continue;
            }

            $klines[] = $kline;
            sleep(1);
        }

        return $klines;
    }
}
