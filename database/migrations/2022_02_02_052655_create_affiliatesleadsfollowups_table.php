<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliatesleadsfollowupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliatesleadsfollowups', function (Blueprint $table) {
            $table->id();
            $table->integer('afleadsfrom')->nullable();
            $table->string('affollowupstatus')->nullable();
            $table->string('aftakenby')->nullable();
            $table->date('affollowupdates')->nullable();
            $table->string('affollowupremarks')->nullable();
            $table->date('afnextsfollowupdates')->nullable();
            $table->string('affollupsby')->nullable();
            $table->tinyInteger('afstatus')->default('0');
            $table->string('afuserid')->nullable();
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
        Schema::dropIfExists('affiliatesleadsfollowups');
    }
}
