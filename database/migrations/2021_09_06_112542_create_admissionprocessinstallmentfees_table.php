<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionprocessinstallmentfeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admissionprocessinstallmentfees', function (Blueprint $table) {
            $table->id();
            $table->integer('invoid')->nullable();
            $table->date('invoicedate')->nullable();
            $table->string('installmentamount')->nullable();
            $table->string('pendinamount')->nullable();
            
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
        Schema::dropIfExists('admissionprocessinstallmentfees');
    }
}
