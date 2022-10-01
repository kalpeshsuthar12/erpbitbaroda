<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSapaccountingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sapaccountings', function (Blueprint $table) {
            $table->id();
            $table->integer('sapid')->nullable();
            $table->date('sappaydates')->nullable();
            $table->string('sapenrollno')->nullable();
            $table->string('sapfees')->nullable();
            $table->string('sapbitfees')->nullable();
            $table->string('sapstudentsname')->nullable();
            $table->string('sapcourses')->nullable();
            $table->string('sapadmissionsfors')->nullable();
            $table->string('saptotfees')->nullable();
            $table->string('sapbalfees')->nullable();
            $table->string('sappayablefees')->nullable();
            $table->string('sapbufees')->nullable();
            $table->string('sapreceiptnos')->nullable();
            $table->date('sapreleaseddates')->nullable();
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
        Schema::dropIfExists('sapaccountings');
    }
}
