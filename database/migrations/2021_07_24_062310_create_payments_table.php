<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('inviceid')->nullable();
            $table->string('totalamount')->nullable();
            $table->string('remainingamount')->nullable();
            $table->string('paymentreceived')->nullable();
            $table->string('paymentdate')->nullable();
            $table->string('paymentmode')->nullable();
            $table->string('bankname')->nullable();
            $table->string('chequeno')->nullable();
            $table->string('chequedate')->nullable();
            $table->string('chequetype')->nullable();
            $table->string('remarknoe')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
