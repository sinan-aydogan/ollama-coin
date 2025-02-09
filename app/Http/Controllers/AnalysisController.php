<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnalysisController extends Controller
{
    public function index()
    {
        return Inertia::render('Analysis/Index',[
            'coins' => Coin::with('lastTicker')->get()
        ]);
    }

    /**
     * @throws Exception
     */
    public function getKlineData($symbol, $from=null, $to=null, $resolution=null): JsonResponse
    {
        $coin = Coin::where('symbol', $symbol)->first();

        if (!$coin) {
            return response()->json(['error' => 'Coin not found'], 404);
        }

        $klineData = $coin->getKlineData($from, $to, $resolution);

        return response()->json($klineData);
    }

    /**
     * @throws Exception
     */
    public function getKlineDataAllCoins($from=null, $to=null, $resolution=null): JsonResponse
    {
        $klineData = (new Coin())->getKlineDataAllCoins($from, $to, $resolution);

        return response()->json($klineData);
    }
}
