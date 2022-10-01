<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundsSettlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds_settlements', function (Blueprint $table) {
            $table->id();
            $table->date('rspaymentsdate')->nullable();
            $table->string('rsstudentsnames')->nullable();
            $table->string('rsenrollmentno')->nullable();
            $table->string('rscourse')->nullable();
            $table->string('rspayablefees')->nullable();
            $table->string('rsrefundamounts')->nullable();
            $table->string('rsbalance')->nullable();
            $table->date('rscmonths')->nullable();
            $table->string('rsbranchs')->nullable();
            $table->string('rsusers')->nullable();
            $table->string('rsstudentadmissionids')->nullable();
            $table->string('rspaymentids')->nullable();
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
        Schema::dropIfExists('refunds_settlements');
    }
}
