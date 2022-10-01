<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdcReleasePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdc_release_payments', function (Blueprint $table) {
            $table->id();
             $table->string('pdcollectionss')->nullable();
            $table->string('clerchcollections')->nullable();
            $table->string('cheincentives')->nullable();
            $table->string('pdctotalincentives')->nullable();
            $table->string('pdcpaidincentives')->nullable();
            $table->string('pdcpayableincentives')->nullable();
            $table->string('pdcremaininvcentives')->nullable();
            $table->string('pdcspmodes')->nullable();
            $table->date('pdcpaymtnsdates')->nullable();
            $table->integer('piusersids')->nullable();
            $table->string('pibranchs')->nullable();
            $table->date('pmothsof')->nullable();
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
        Schema::dropIfExists('pdc_release_payments');
    }
}
