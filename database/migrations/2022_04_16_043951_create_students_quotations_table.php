<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('studentscategorys')->nullable();
            $table->date('quotationsdates')->nullable();
            $table->date('quotationsduedates')->nullable();
            $table->string('studentsocompanyname')->nullable();
            $table->string('contactperson')->nullable();
            $table->string('scemail')->nullable();
            $table->string('scphones')->nullable();
            $table->string('scwhatsappno')->nullable();
            $table->string('scsubtotal')->nullable();
            $table->string('scdiscounttypes')->nullable();
            $table->string('scdiscountstotals')->nullable();
            $table->string('scgstamounts')->nullable();
            $table->string('scfinaltotal')->nullable();
            $table->string('scbranch')->nullable();
            $table->string('scquonos')->nullable();
            $table->integer('sjqnos')->nullable();
            $table->integer('mjqnos')->nullable();
            $table->integer('bitolqnos')->nullable();
            $table->integer('elqnos')->nullable();
            $table->integer('cvrublqnos')->nullable();
            $table->integer('cvrukhqnos')->nullable();
            $table->integer('rntuqnos')->nullable();
            $table->integer('manipalnos')->nullable();
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
        Schema::dropIfExists('students_quotations');
    }
}
