<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadmissioninstallmentfeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('readmissioninstallmentfees', function (Blueprint $table) {
            $table->id();
            $table->integer('reinvoid')->nullable();
            $table->date('reinvoicedate')->nullable();
            $table->string('reinstallmentamount')->nullable();
            $table->string('rependinamount')->nullable();
            $table->tinyInteger('restatus')->default('1');
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
        Schema::dropIfExists('readmissioninstallmentfees');
    }
}
