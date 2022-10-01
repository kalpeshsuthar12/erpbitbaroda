<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPunchingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_punchings', function (Blueprint $table) {
            $table->id();
            $table->integer('pusersid')->nullable();
            $table->date('puncdates')->nullable();
            $table->time('punch_in')->nullable();
            $table->time('punch_out')->nullable();
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
        Schema::dropIfExists('user_punchings');
    }
}
