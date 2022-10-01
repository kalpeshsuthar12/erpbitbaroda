<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePracticalQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('practical_questions', function (Blueprint $table) {
            $table->id();
            $table->longText('pqcourses')->nullable();
            $table->string('plectures')->nullable();
            $table->longText('pQuestions')->nullable();
            $table->string('pusersids')->nullable();
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
        Schema::dropIfExists('practical_questions');
    }
}
