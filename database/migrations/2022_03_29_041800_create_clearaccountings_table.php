<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClearaccountingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clearaccountings', function (Blueprint $table) {
            $table->id();
            $table->date('accountingdates')->nullable();
            $table->tinyInteger('cashclearence')->nullable();
            $table->tinyInteger('onlinepaymentsclearence')->nullable();
            $table->tinyInteger('cvruclearence')->nullable();
            $table->tinyInteger('bankclearence')->nullable();
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
        Schema::dropIfExists('clearaccountings');
    }
}
