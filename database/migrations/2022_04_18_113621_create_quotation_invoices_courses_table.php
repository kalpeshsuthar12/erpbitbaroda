<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationInvoicesCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_invoices_courses', function (Blueprint $table) {
            $table->id();
            $table->integer('invstucompyid')->nullable();
            $table->string('invstudecompcourse')->nullable();
            $table->string('invstudecompspecializations')->nullable();
            $table->string('invstudecoursemode')->nullable();
            $table->string('invstudecoursefeess')->nullable();
            $table->string('invcompnystudents')->nullable();
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
        Schema::dropIfExists('quotation_invoices_courses');
    }
}
