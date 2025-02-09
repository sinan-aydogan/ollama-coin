<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('analysis.default_kline_data_resolution', '1');
        $this->migrator->add('analysis.default_kline_data_time_range', '15');
    }
};
