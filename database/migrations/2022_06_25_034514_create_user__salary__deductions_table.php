<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSalaryDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user__salary__deductions', function (Blueprint $table) {
            $table->id();
            $table->integer('salsusersid')->nullable();
            $table->integer('salssalarysid')->nullable();
            $table->string('salsworkingsalarys')->nullable();
            $table->string('salsfinalsalarys')->nullable();
            $table->string('totalrealeasesalary')->nullable();
            $table->string('salspaidsalarys')->nullable();
            $table->string('salspendingsalarys')->nullable();
            $table->date('salspaymentdate')->nullable();
            $table->string('salspaymoddes')->nullable();
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
        Schema::dropIfExists('user__salary__deductions');
    }
}
