<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvanceTakensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_takens', function (Blueprint $table) {
            $table->id();
            $table->string('ausersid')->nullable();
            $table->date('atkndate')->nullable();
            $table->string('atkamounts')->nullable();
            $table->string('atkmode')->nullable();
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
        Schema::dropIfExists('advance_takens');
    }
}
