<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('studentname')->nullable();
            $table->date('dateofbirth')->nullable();
            $table->string('studemail')->nullable();
            $table->string('branch')->nullable();
            $table->string('brancherno')->nullable();
            $table->integer('sjerno')->nullable();
            $table->integer('wgerno')->nullable();
            $table->integer('mjerno')->nullable();
            $table->integer('phoneno')->nullable();
            $table->string('course')->nullable();
            $table->integer('price')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->integer('zipcode')->nullable();
            $table->string('preferrabletime')->nullable();
            $table->string('remarknote')->nullable();
            $table->string('coursemode')->nullable();
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
        Schema::dropIfExists('students');
    }
}
