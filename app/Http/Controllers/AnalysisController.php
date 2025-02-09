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
    public function getKlineData(Request $request, $symbol, $from=null, $to=null, $resolution=null): JsonResponse
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
    public function getKlineDataAllCoins(Request $request, $from=null, $to=null, $resolution=null): JsonResponse
    {
        if ($request->has('symbols')) {
            $klineData = (new Coin())->getKlineDataAllCoins($request->input('symbols'), $from, $to, $resolution);
        }else{
            $klineData = (new Coin())->getKlineDataAllCoins($from, $to, $resolution);
        }

        return response()->json($klineData);
    }
}
