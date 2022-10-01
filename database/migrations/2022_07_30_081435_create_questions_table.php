<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->integer('qcourseid')->nullable();
            $table->string('qlectures')->nullable();
            $table->longText('qquestions')->nullable();
            $table->string('qusersids')->nullable();
            $table->longText('aoptions')->nullable();
            $table->longText('boptions')->nullable();
            $table->longText('coptions')->nullable();
            $table->longText('doptions')->nullable();
            $table->longText('correctanswers')->nullable();
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
        Schema::dropIfExists('questions');
    }
}
