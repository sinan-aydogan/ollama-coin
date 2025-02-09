<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Wallets/Index', [
            'wallets' => Wallet::with('user:id,name')->get(),
            'exchangeOptions' => Wallet::EXCHANGE_OPTIONS,
            'userOptions' => User::all(['id', 'name']),
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
        $wallet = Wallet::create($request->all());

        return redirect()->route('wallets.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Wallet $wallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wallet $wallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet)
    {
        $wallet->delete();

        return response()->json(['message' => 'Wallet deleted']);
    }

    /**
     * Get the balance of the specified wallet.
     * @throws \Exception
     */
    public function getBalance(Wallet $wallet)
    {
        $walletBalance = $wallet->getBalance();
        $generatedResponse = [];

        foreach ($walletBalance as $balance) {
            $symbol = $balance['asset'];
            $coinPairs = Coin::where('base_symbol', $symbol)->get();
            if (!$coinPairs) {
                $generatedResponse[] = $balance;
            }else{
                $price = [];

                foreach ($coinPairs as $coinPair) {
                    $price[$coinPair['quote_symbol']] = $coinPair->price * $balance['balance'];
                }

                $generatedResponse[] = [
                    ...$balance,
                    'price' => $price,
                ];
            }
        }

        return response()->json($generatedResponse);
    }
}
