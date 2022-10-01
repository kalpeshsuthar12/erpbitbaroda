<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionprocessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admissionprocesses', function (Blueprint $table) {
            $table->id();
            $table->string('studentname')->nullable();
            $table->date('sdobs')->nullable();
            $table->string('semails')->nullable();
            $table->string('sphone')->nullable();
            $table->string('sbrnanch')->nullable();
            $table->string('serno')->nullable();
            $table->integer('sjerno')->nullable();
            $table->integer('mjerno')->nullable();
            $table->integer('wgerno')->nullable();
            $table->string('sstreet')->nullable();
            $table->string('scity')->nullable();
            $table->string('sstate')->nullable();
            $table->string('szipcode')->nullable();
            $table->string('spreferrabbletime')->nullable();
            $table->string('sremarknotes')->nullable();
            $table->string('Ibranchs')->nullable();
            $table->integer('Isjno')->nullable();
            $table->integer('Imjno')->nullable();
            $table->integer('Iwgno')->nullable();
            $table->date('invdate')->nullable();
            $table->date('duedate')->nullable();
            $table->string('ipaymentmodes')->nullable();
            $table->string('idiscounttypes')->nullable();
            $table->string('isubtotal')->nullable();
            $table->string('idiscount')->nullable();
            $table->string('itax')->nullable();
            $table->string('invtotal')->nullable();
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
        Schema::dropIfExists('admissionprocesses');
    }
}
