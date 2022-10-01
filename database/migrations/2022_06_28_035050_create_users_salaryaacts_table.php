<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersSalaryaactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_salaryaacts', function (Blueprint $table) {
            $table->id();
            $table->integer('acivusersid')->nullable();
            $table->string('achivsalarys')->nullable();
            $table->string('achivremarks')->nullable();
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
        Schema::dropIfExists('users_salaryaacts');
    }
}
