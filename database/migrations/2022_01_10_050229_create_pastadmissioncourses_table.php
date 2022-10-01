<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePastadmissioncoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pastadmissioncourses', function (Blueprint $table) {
            $table->id();
            $table->integer('pinvid')->nullable();
            $table->integer('pcourseid')->nullable();
            $table->integer('punivecoursid')->nullable();
            $table->string('padmissionfor')->nullable();
            $table->string('psubcourses')->nullable();
            $table->string('pstudentsin')->nullable();
            $table->string('pcoursemode')->nullable();
            $table->string('pcourseprice')->nullable();
            $table->string('punoverfeess')->nullable();
            $table->string('ptax')->nullable();
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
        Schema::dropIfExists('pastadmissioncourses');
    }
}
