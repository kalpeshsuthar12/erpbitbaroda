<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUniversititiesfeeslistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universititiesfeeslists', function (Blueprint $table) {
            $table->id();
            $table->integer('coursid')->nullable();
            $table->string('universitiesfor')->nullable();
            $table->string('univfees')->nullable();
            $table->string('bifees')->nullable();
            $table->string('overallfees')->nullable();
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
        Schema::dropIfExists('universititiesfeeslists');
    }
}
