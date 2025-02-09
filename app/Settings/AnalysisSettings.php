<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AnalysisSettings extends Settings
{
    public string $default_kline_data_resolution;
    public string $default_kline_data_time_range;

    public static function group(): string
    {
        return 'analysis';
    }
}
