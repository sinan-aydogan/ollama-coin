<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Settings\GeneralSettings;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CoinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Coins/Index', [
            'coins' => Coin::with('lastTicker')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Coin $coin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coin $coin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coin $coin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coin $coin)
    {
        //
    }

    /**
     * @throws Exception
     */
    public function syncCoinList():array
    {
        return (new Coin())->getCoinList();
    }

    public function syncCoinPrices()
    {
        return (new Coin())->getTickersAllCoins();
    }

    /**
     * @throws Exception
     */
    public function syncCoinPrice($symbol)
    {
        return (new Coin())->getCoinTicker($symbol);
    }
}
