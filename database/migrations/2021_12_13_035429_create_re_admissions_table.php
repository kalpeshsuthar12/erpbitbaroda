<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReAdmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('re_admissions', function (Blueprint $table) {
            $table->id();
            $table->string('rstudents')->nullable();
            $table->string('rfnames')->nullable();
            $table->string('rmnames')->nullable();
            $table->date('rsdobs')->nullable();
            $table->string('rsemails')->nullable();
            $table->string('rsphone')->nullable();
            $table->string('rswhatsappno')->nullable();
            $table->date('rsadate')->nullable();
            $table->string('rsbrnanch')->nullable();
            $table->string('rstobranches')->nullable();
            $table->string('rsuniversities')->nullable();
            $table->string('rserno')->nullable();
            $table->integer('rsjerno')->nullable();
            $table->integer('rmjerno')->nullable();
            $table->integer('rwgerno')->nullable();
            $table->integer('rbitolerno')->nullable();
            $table->integer('rcvrublerno')->nullable();
            $table->integer('rcvrukherno')->nullable();
            $table->integer('rrntuerno')->nullable();
            $table->integer('rmanipalerno')->nullable();
            $table->string('rsstreet')->nullable();
            $table->string('rscity')->nullable();
            $table->string('rsstate')->nullable();
            $table->string('rszipcode')->nullable();
            $table->string('rspreferrabbletime')->nullable();
            $table->string('rrefeassignto')->nullable();
            $table->string('rreferfrom')->nullable();
            $table->string('rrefername')->nullable();
            $table->string('rsremarknotes')->nullable();
            $table->string('rIbranchs')->nullable();
            $table->string('rInvoiceno')->nullable();
            $table->integer('rIsjno')->nullable();
            $table->integer('rImjno')->nullable();
            $table->integer('rIwgno')->nullable();
            $table->integer('rIbitolno')->nullable();
            $table->integer('rIcvrublno')->nullable();
            $table->integer('rIcvrukhno')->nullable();
            $table->integer('rIrntuno')->nullable();
            $table->integer('rImanipalno')->nullable();
            $table->date('rinvdate')->nullable();
            $table->date('rduedate')->nullable();
            $table->string('ripaymentmodes')->nullable();
            $table->string('ridiscounttypes')->nullable();
            $table->string('risubtotal')->nullable();
            $table->string('rdiscounttotal')->nullable();
            $table->string('radmsisource')->nullable();
            $table->string('ridiscount')->nullable();
            $table->string('ritax')->nullable();
            $table->string('rinvtotal')->nullable();
            $table->string('ruserid')->nullable();
            $table->string('radmissionsusersid')->nullable();
            $table->string('rgstprices')->nullable();
            $table->string('roldtotalpice')->nullable();
            $table->string('rstatus')->nullable();
            $table->string('radmissionstatus')->nullable();
            $table->string('rbatchassigntime')->nullable();
            $table->string('rdays')->nullable();
            $table->string('rfacultys')->nullable();
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
        Schema::dropIfExists('re_admissions');
    }
}
