<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvanceDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_deductions', function (Blueprint $table) {
            $table->id();
            $table->date('addatses')->nullable();
            $table->integer('addeusersid')->nullable();
            $table->string('paidadvance')->nullable();
            $table->date('advededdate')->nullable();
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
        Schema::dropIfExists('advance_deductions');
    }
}
