<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannedleadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bannedleads', function (Blueprint $table) {
            $table->id();
            $table->string('bsource')->nullable();
            $table->string('bbranch')->nullable();
            $table->string('bstudentname')->nullable();
            $table->string('baddress')->nullable();
            $table->string('bemail')->nullable();
            $table->string('bphone')->nullable();
            $table->string('bcourse')->nullable();
            $table->string('bcoursesmode')->nullable();
            $table->string('blvalue')->nullable();
            $table->string('bcity')->nullable();
            $table->string('bstate')->nullable();
            $table->string('bzipcode')->nullable();
            $table->string('bdescription')->nullable();
            $table->string('bfollowupstatus')->nullable();
            $table->date('bfollowupdate')->nullable();
            $table->boolean('bleadstatus')->nullable();
            $table->string('bleaddate')->nullable();
            $table->integer('busersid')->nullable();
            $table->string('bbyname')->nullable();
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
        Schema::dropIfExists('bannedleads');
    }
}
