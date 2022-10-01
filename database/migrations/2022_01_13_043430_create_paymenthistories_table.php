<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymenthistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paymenthistories', function (Blueprint $table) {
            $table->id();
            $table->integer('paymentinvoiceid')->nullable();
            $table->integer('paymentid')->nullable();
            $table->string('ppaymentmode')->nullable();
            $table->string('pbankname')->nullable();
            $table->string('pchequeno')->nullable();
            $table->date('pchequedate')->nullable();
            $table->string('pchequedepositto')->nullable();
            $table->tinyInteger('pchequestatus')->nullable();
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
        Schema::dropIfExists('paymenthistories');
    }
}
