<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.ai_model', '');
        $this->migrator->add('general.ai_base_url', 'http://localhost:11434');
        $this->migrator->add('general.default_exchange_for_market_data', 'btc_turk');
    }
};
