<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationCoursesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_courses_details', function (Blueprint $table) {
            $table->id();
            $table->integer('companyquotationid')->nullable();
            $table->string('compcourse')->nullable();
            $table->string('compspecializations')->nullable();
            $table->string('compcoursemode')->nullable();
            $table->string('compcoursefees')->nullable();
            $table->string('compnofstudents')->nullable();
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
        Schema::dropIfExists('quotation_courses_details');
    }
}
