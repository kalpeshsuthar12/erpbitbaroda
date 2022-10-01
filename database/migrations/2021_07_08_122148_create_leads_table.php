<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('source')->nullable();
            $table->string('assignedto')->nullable();
            $table->string('branch')->nullable();
            $table->string('studentname')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->integer('phone')->nullable();
            $table->string('course')->nullable();
            $table->string('coursemode')->nullable();
            $table->integer('lvalue')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->integer('zipcode')->nullable();
            $table->string('description')->nullable();
            $table->string('followupstatus')->nullable();
            $table->boolean('status')->default('0');
            $table->integer('leadduration')->nullable();
            $table->date('leaddate')->nullable();
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
        Schema::dropIfExists('leads');
    }
}
