<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_calculations', function (Blueprint $table) {
            $table->id();
            $table->string('user_details_id')->nullable();
            $table->string('usersworkinghrs')->nullable();
            $table->string('users_salarys')->nullable();
            $table->string('user_months')->nullable();
            $table->string('umsdays')->nullable();
            $table->string('workingdays')->nullable();
            $table->string('totalwrknghrs')->nullable();
            $table->string('ttlsphpl')->nullable();
            $table->string('ul')->nullable();
            $table->string('upl')->nullable();
            $table->string('flh')->nullable();
            $table->string('fld')->nullable();
            $table->string('uwrkinghrs')->nullable();
            $table->string('uwrkingsalary')->nullable();
            $table->string('uwrkingincentif')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('salary_calculations');
    }
}
