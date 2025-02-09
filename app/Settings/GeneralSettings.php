<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $ai_model;
    public string $ai_base_url;
    public string $default_exchange_for_market_data;

    public static function group(): string
    {
        return 'general';
    }
}
