<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignbatchesdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignbatchesdetails', function (Blueprint $table) {
            $table->id();
            $table->integer('assignbatchid')->nullable();
            $table->string('students')->nullable();
            $table->string('enrollmentno')->nullable();
            $table->string('branch')->nullable();
            $table->string('mobilenos')->nullable();
            $table->string('course')->nullable();
            $table->string('subcourse')->nullable();
            $table->string('batchtimes')->nullable();
            $table->string('days')->nullable();
            $table->string('faculty')->nullable();
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
        Schema::dropIfExists('assignbatchesdetails');
    }
}
