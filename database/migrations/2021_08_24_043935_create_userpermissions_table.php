<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserpermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userpermissions', function (Blueprint $table) {
            $table->id();
            $table->integer('usersid')->nullable();
            $table->string('coursecategory')->nullable();
            $table->string('coursesubcategory')->nullable();
            $table->string('course')->nullable();
            $table->string('leads')->nullable();
            $table->string('invoice')->nullable();
            $table->string('admission')->nullable();
            $table->string('directadmission')->nullable();
            $table->string('source')->nullable();
            $table->string('assigntarget')->nullable();
            $table->string('followup')->nullable();
            $table->string('generatepaymentreciept')->nullable();
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
        Schema::dropIfExists('userpermissions');
    }
}
