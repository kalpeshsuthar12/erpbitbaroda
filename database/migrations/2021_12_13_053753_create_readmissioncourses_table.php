<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadmissioncoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('readmissioncourses', function (Blueprint $table) {
            $table->id();
            $table->integer('reinvid')->nullable();
            $table->integer('recourseid')->nullable();
            $table->integer('reunivecoursid')->nullable();
            $table->string('readmissionfor')->nullable();
            $table->string('resubcourses')->nullable();
            $table->string('restudentsin')->nullable();
            $table->string('recoursemode')->nullable();
            $table->string('recourseprice')->nullable();
            $table->string('reunoverfeess')->nullable();
            $table->string('retax')->nullable();
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
        Schema::dropIfExists('readmissioncourses');
    }
}
