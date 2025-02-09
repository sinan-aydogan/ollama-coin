<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Models\Wallet;
use App\Settings\AnalysisSettings;
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
    public function index()
    {
        $generalSettings = new GeneralSettings()->toArray();
        $analysisSettings = new AnalysisSettings()->toArray();

        $settings = [
            ...$generalSettings,
            ...$analysisSettings
        ];

        return Inertia::render('SystemSettings', [
            'settings' => $settings,
            'exchangeOptions' => Wallet::EXCHANGE_OPTIONS
        ]);
    }

    public function update(Request $request)
    {
        $generalSettings = new GeneralSettings();

        $generalSettings->ai_base_url = $request->input('ai_base_url');
        $generalSettings->ai_model = $request->input('ai_model');
        $generalSettings->default_exchange_for_market_data = $request->input('default_exchange_for_market_data');
        $generalSettings->save();

        $analysisSettings = new AnalysisSettings();
        $analysisSettings->default_kline_data_resolution = $request->input('default_kline_data_resolution');
        $analysisSettings->default_kline_data_time_range = $request->input('default_kline_data_time_range');
        $analysisSettings->save();

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
