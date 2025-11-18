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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->double('price', 2);
            $table->string('payer_name');
            $table->string('payer_email');
            $table->string('seller_email');
            $table->string('currency_code');
            $table->string('payer_status');
            $table->string('transaction_id');
            $table->double('sales_tax', 2);
            $table->string('invoice_id');
            $table->double('shipping_amount', 2);
            $table->double('handling_amount', 2);
            $table->double('insurance_amount', 2);
            $table->double('paypal_fee', 2);
            $table->string('payment_mode');
            $table->boolean('paid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

