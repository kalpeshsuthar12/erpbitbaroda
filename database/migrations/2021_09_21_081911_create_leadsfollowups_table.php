<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsfollowupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leadsfollowups', function (Blueprint $table) {
            $table->id();
            $table->date('flfollwpdate')->nullable();
            $table->string('flremarsk')->nullable();
            $table->date('nxtfollowupdate')->nullable();
            $table->string('userid')->nullable();
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
        Schema::dropIfExists('leadsfollowups');
    }
}
