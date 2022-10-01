<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionprocesscoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admissionprocesscourses', function (Blueprint $table) {
            $table->id();
            $table->integer('invid')->nullable();
            $table->integer('courseid')->nullable();
            $table->string('coursemode')->nullable();
            $table->string('courseprice')->nullable();
            $table->string('tax')->nullable();
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
        Schema::dropIfExists('admissionprocesscourses');
    }
}
