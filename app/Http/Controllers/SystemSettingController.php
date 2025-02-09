<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Models\Wallet;
use App\Settings\GeneralSettings;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use RuntimeException;
use Spatie\LaravelSettings\Settings;

class SystemSettingController extends Controller
{
    public function index(GeneralSettings $settings)
    {

        return Inertia::render('SystemSettings', [
            'settings' => $settings,
            'exchangeOptions' => Wallet::EXCHANGE_OPTIONS
        ]);
    }

    public function update(Request $request, GeneralSettings $settings)
    {
        $settings->ai_base_url = $request->input('ai_base_url');
        $settings->ai_model = $request->input('ai_model');
        $settings->default_exchange_for_market_data = $request->input('default_exchange_for_market_data');
        $settings->save();

        return redirect()->route('system-settings.index');
    }

    /**
     * @throws GuzzleException
     */
    public function getAiModels(GeneralSettings $settings): JsonResponse
    {
        $client = new Client([
            'base_uri' => $settings->ai_base_url,
            'timeout'  => 10.0,
        ]);

        try {
            $response = $client->get('/api/tags');
            $models = json_decode($response->getBody(), false);

            return response()->json($models);
        } catch (\GuzzleException $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
