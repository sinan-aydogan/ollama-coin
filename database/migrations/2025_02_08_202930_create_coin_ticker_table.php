<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coin_tickers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coin_id')->constrained()->cascadeOnDelete();
            $table->decimal('open', 16, 8);
            $table->decimal('high', 16, 8);
            $table->decimal('low', 16, 8);
            $table->decimal('last', 16, 8);
            $table->decimal('average', 16, 8);
            $table->decimal('bid', 16, 8);
            $table->decimal('ask', 16, 8);
            $table->bigInteger('volume');
            $table->decimal('price_change', 16, 8);
            $table->decimal('price_change_percent', 16, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_tickers');
    }
};
