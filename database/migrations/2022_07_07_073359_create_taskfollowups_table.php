<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskfollowupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taskfollowups', function (Blueprint $table) {
            $table->id();
            $table->integer('tasksid')->nullable();
            $table->string('taskstatus')->nullable();
            $table->date('taskfoldate')->nullable();
            $table->string('tfremarks')->nullable();
            $table->date('tasknxtfoldate')->nullable();
            $table->string('tfollbys')->nullable();
            $table->tinyInteger('fstatus')->default('0');
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
        Schema::dropIfExists('taskfollowups');
    }
}
