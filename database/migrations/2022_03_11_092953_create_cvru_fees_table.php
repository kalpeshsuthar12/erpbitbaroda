<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCvruFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cvru_fees', function (Blueprint $table) {
            $table->id();
            $table->integer('studentid')->nullable();
            $table->date('paymentdate')->nullable();
            $table->string('sverno')->nullable();
            $table->string('cvrufees')->nullable();
            $table->string('bitfees')->nullable();
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
        Schema::dropIfExists('cvru_fees');
    }
}
