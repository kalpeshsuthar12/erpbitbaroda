<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncentiveReleasePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incentive_release_payments', function (Blueprint $table) {
            $table->id();
            $table->string('incentcollections')->nullable();
            $table->string('mincentivs')->nullable();
            $table->string('payableincentivespayments')->nullable();
            $table->string('remainingincentives')->nullable();
            $table->string('incpaymentsmodes')->nullable();
            $table->date('incentivespaymentsdates')->nullable();
            $table->integer('iusersids')->nullable();
            $table->string('ibranchs')->nullable();
            $table->date('mothsof')->nullable();
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
        Schema::dropIfExists('incentive_release_payments');
    }
}
