<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsQuotationCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students_quotation_courses', function (Blueprint $table) {
            $table->id();
            $table->integer('stucompyid')->nullable();
            $table->string('studecompcourse')->nullable();
            $table->string('studecompspecializations')->nullable();
            $table->string('studecoursemode')->nullable();
            $table->string('studecoursefeess')->nullable();
            $table->string('compnystudents')->nullable();
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
        Schema::dropIfExists('students_quotation_courses');
    }
}
