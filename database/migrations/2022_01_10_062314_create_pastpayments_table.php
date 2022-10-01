<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePastpaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pastpayments', function (Blueprint $table) {
            $table->id();
            $table->integer('paid')->nullable();
            $table->integer('preinviceid')->nullable();
            $table->integer('pinviceid')->nullable();
            $table->integer('pstudentsid')->nullable();
            $table->integer('puserid')->nullable();
            $table->string('pstudentadmissiionstatus')->nullable();
            $table->string('pbranchs')->nullable();
            $table->string('preceiptno')->nullable();
            $table->integer('psjrecpno')->nullable();
            $table->integer('pmjrecpno')->nullable();
            $table->integer('pwgrecpno')->nullable();
            $table->integer('pbitolrecpno')->nullable();
            $table->integer('pcvrublrecpno')->nullable();
            $table->integer('pcvrukhrecpno')->nullable();
            $table->integer('prnturecpno')->nullable();
            $table->integer('pmanipalrecpno')->nullable();
            $table->string('pstudenterno')->nullable();
            $table->integer('psjerno')->nullable();
            $table->integer('pmjerno')->nullable();
            $table->integer('pwgerno')->nullable();
            $table->integer('pcvrublerno')->nullable();
            $table->integer('pcvrukherno')->nullable();
            $table->integer('pbitolerno')->nullable();
            $table->integer('prntuerno')->nullable();
            $table->integer('pmanipalerno')->nullable();
            $table->string('ptotalamount')->nullable();
            $table->string('premainingamount')->nullable();
            $table->string('ppaymentreceived')->nullable();
            $table->date('ppaymentdate')->nullable();
            $table->string('ppaymentmode')->nullable();
            $table->string('pbankname')->nullable();
            $table->string('pchequeno')->nullable();
            $table->string('pchequedate')->nullable();
            $table->string('pchequetype')->nullable();
            $table->string('premarknoe')->nullable();
            $table->string('pinstallmentid')->nullable();
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
        Schema::dropIfExists('pastpayments');
    }
}
