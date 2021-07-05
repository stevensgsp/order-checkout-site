<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // default fields that cannot be changed :'(
            $table->string('customer_name', 80);
            $table->string('customer_email', 120);
            $table->string('customer_mobile', 40);
            $table->string('status', 20);

            // new fields
            $table->foreignId('product_id')->constrained();
            $table->integer('price')
                ->comment('Amounts are represented in the smallest unit (eg. cents), so USD 5.00 is stored as 500.');
            $table->string('currency', 3)->comment('ISO code of the currency.');
            $table->jsonb('payment_data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
