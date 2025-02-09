<?php

namespace App\Console\Commands;

use App\Models\Coin;
use Illuminate\Console\Command;

class SyncCoinTickers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:tickers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync coin tickers';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $this->info('Syncing coin tickers...');

        // Get all coins
        if ((new Coin())->getTickersAllCoins()) {
            $this->info('Successfully synced coin tickers.');
        }else{
            $this->error('Failed to sync coin tickers.');
        }
    }
}
