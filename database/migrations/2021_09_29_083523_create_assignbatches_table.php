<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignbatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignbatches', function (Blueprint $table) {
            $table->id();
            $table->date('adates')->nullable();
            $table->string('studentname')->nullable();
            $table->string('branches')->nullable();
            $table->string('course')->nullable();
            $table->string('subcourse')->nullable();
            $table->string('enrollmentno')->nullable();
            $table->string('mobileno')->nullable();
            $table->string('email')->nullable();
            $table->string('batchtimes')->nullable();
            $table->string('days')->nullable();
            $table->string('faculty')->nullable();
            $table->date('startdate')->nullable();
            $table->date('enddate')->nullable();
            $table->string('jointo')->nullable();
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
        Schema::dropIfExists('assignbatches');
    }
}
