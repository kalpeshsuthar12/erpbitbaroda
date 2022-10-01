<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicescoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoicescourses', function (Blueprint $table) {
            $table->id();
            $table->integer('invid')->nullable();
            $table->integer('courseid')->nullable();
            $table->string('courseprice')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('tax')->nullable();
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
        Schema::dropIfExists('invoicescourses');
    }
}
