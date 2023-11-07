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
        Schema::create('stripe_prices', function (Blueprint $table)
        {
            $table->id();

            $table->timestamp('created')->nullable()->default(null);
            $table->string('price_id')->nullable()->default(null);

            $table->string('product')->nullable()->default(null);
            $table->string('nickname')->nullable()->default(null);

            $table->string('currency')->nullable()->default(null);
            $table->integer('unit_amount')->nullable()->default(null);
            $table->string('tax_behavior')->nullable()->default(null);

            $table->string('billing_scheme')->nullable()->default(null);
            $table->string('interval')->nullable()->default(null);
            $table->integer('interval_count')->nullable()->default(null);
            $table->string('usage_type')->nullable()->default(null);

            $table->unique('price_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_prices');
    }
};
