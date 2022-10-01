<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePastadmissioninstallmentfeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pastadmissioninstallmentfees', function (Blueprint $table) {
            $table->id();
            $table->integer('pinvoid')->nullable();
            $table->date('pinvoicedate')->nullable();
            $table->string('pinstallmentamount')->nullable();
            $table->string('ppendinamount')->nullable();
            $table->tinyInteger('pstatus')->default('0');
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
        Schema::dropIfExists('pastadmissioninstallmentfees');
    }
}
