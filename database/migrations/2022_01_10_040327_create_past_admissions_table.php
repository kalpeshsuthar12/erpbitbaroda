<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePastAdmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('past_admissions', function (Blueprint $table) {
            $table->id();
            $table->string('pstudentname')->nullable();
            $table->string('pfnames')->nullable();
            $table->string('pmnames')->nullable();
            $table->date('psdobs')->nullable();
            $table->string('psemails')->nullable();
            $table->string('psphone')->nullable();
            $table->string('pswhatsappno')->nullable();
            $table->date('psadate')->nullable();
            $table->string('psbrnanch')->nullable();
            $table->string('pstobranches')->nullable();
            $table->string('psuniversities')->nullable();
            $table->string('pserno')->nullable();
            $table->string('psstreet')->nullable();
            $table->string('pscity')->nullable();
            $table->string('psstate')->nullable();
            $table->string('pszipcode')->nullable();
            $table->string('pspreferrabbletime')->nullable();
            $table->string('prefeassignto')->nullable();
            $table->string('preferfrom')->nullable();
            $table->string('prefername')->nullable();
            $table->string('psremarknotes')->nullable();
            $table->string('pIbranchs')->nullable();
            $table->string('pInvoiceno')->nullable();
            $table->integer('pIsjno')->nullable();
            $table->integer('pImjno')->nullable();
            $table->integer('pIwgno')->nullable();
            $table->integer('pIbitolno')->nullable();
            $table->integer('pIcvrublno')->nullable();
            $table->integer('pIcvrukhno')->nullable();
            $table->integer('pIrntuno')->nullable();
            $table->integer('pImanipalno')->nullable();
            $table->date('pinvdate')->nullable();
            $table->date('pduedate')->nullable();
            $table->string('pipaymentmodes')->nullable();
            $table->string('pidiscounttypes')->nullable();
            $table->string('pisubtotal')->nullable();
            $table->string('pdiscount')->nullable();
            $table->string('pdiscounttotal')->nullable();
            $table->string('pitax')->nullable();
            $table->string('pinvtotal')->nullable();
            $table->string('puserid')->nullable();
            $table->string('pgstprices')->nullable();
            $table->string('poldtotalpice')->nullable();
            $table->string('padmissionstatus')->nullable();
            $table->string('pstatus')->nullable();
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
        Schema::dropIfExists('past_admissions');
    }
}
