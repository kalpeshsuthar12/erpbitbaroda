<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invstudentscategorys')->nullable();
            $table->integer('quotationsid')->nullable();
            $table->date('invdates')->nullable();
            $table->date('invstduedates')->nullable();
            $table->string('invstudentsocompanyname')->nullable();
            $table->string('invcontactperson')->nullable();
            $table->string('invscemail')->nullable();
            $table->string('invscphones')->nullable();
            $table->string('invscwhatsappno')->nullable();
            $table->string('invscsubtotal')->nullable();
            $table->string('invscdiscounttypes')->nullable();
            $table->string('invscdiscountstotals')->nullable();
            $table->string('invscgstamounts')->nullable();
            $table->string('invscfinaltotal')->nullable();
            $table->string('invscbranch')->nullable();
            $table->string('invscquonos')->nullable();
            $table->integer('invsjqnos')->nullable();
            $table->integer('invmjqnos')->nullable();
            $table->integer('invwgqnos')->nullable();
            $table->integer('invbitolqnos')->nullable();
            $table->integer('invelqnos')->nullable();
            $table->integer('invcvrublqnos')->nullable();
            $table->integer('invcvrukhqnos')->nullable();
            $table->integer('invrntuqnos')->nullable();
            $table->integer('invmanipalnos')->nullable();
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
        Schema::dropIfExists('quotation_invoices');
    }
}
