<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrocheuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brocheures', function (Blueprint $table) {
            $table->id();
            $table->integer('categ_id')->nullable();
            $table->integer('subcateg_id')->nullable();
            $table->string('mcoursename')->nullable();
            $table->string('brocheuresfiles')->nullable();
            $table->longText('coursesurls')->nullable();
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
        Schema::dropIfExists('brocheures');
    }
}
