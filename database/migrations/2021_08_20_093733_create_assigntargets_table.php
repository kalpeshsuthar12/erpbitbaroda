<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssigntargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assigntargets', function (Blueprint $table) {
            $table->id();
            $table->string('targtname')->nullable();
            $table->string('tmonth')->nullable();
            $table->string('usercategory')->nullable();
            $table->integer('tassignuser')->nullable();
            $table->integer('targetamount')->nullable();
            $table->integer('incentivepercent')->nullable();
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
        Schema::dropIfExists('assigntargets');
    }
}
