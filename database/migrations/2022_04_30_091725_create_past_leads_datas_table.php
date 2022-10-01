<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePastLeadsDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('past_leads_datas', function (Blueprint $table) {
            $table->id();
            $table->integer('oldid')->nullable();
            $table->string('ptsource')->nullable();
            $table->string('ptleadsdates')->nullable();
            $table->string('ptoldleadsdates')->nullable();
            $table->string('ptassignedto')->nullable();
            $table->string('ptbranch')->nullable();
            $table->string('pttobranchs')->nullable();
            $table->string('ptinstitutions')->nullable();
            $table->string('ptaffiliatescategorynames')->nullable();
            $table->string('ptstudentname')->nullable();
            $table->string('ptaddress')->nullable();
            $table->string('ptemail')->nullable();
            $table->string('ptphone')->nullable();
            $table->string('ptwhatsappno')->nullable();
            $table->string('ptcourse')->nullable();
            $table->string('ptcoursesmode')->nullable();
            $table->integer('ptlvalue')->nullable();
            $table->string('ptreffrom')->nullable();
            $table->string('ptrefname')->nullable();
            $table->string('ptrefassignto')->nullable();
            $table->string('ptcity')->nullable();
            $table->string('ptstate')->nullable();
            $table->integer('ptzipcode')->nullable();
            $table->string('ptdescription')->nullable();
            $table->string('ptleadstatus')->nullable();
            $table->string('ptleadduration')->nullable();
            $table->date('ptleaddate')->nullable();
            $table->tinyInteger('ptconversationstatus')->nullable();
            $table->tinyInteger('ptwalkedinstatus')->nullable();
            $table->integer('ptuser_id')->nullable();
            $table->string('pttransferfrom')->nullable();
            $table->string('pttransferbranch')->nullable();
            $table->string('pttransferto')->nullable();
            $table->date('pttransferdate')->nullable();
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
        Schema::dropIfExists('past_leads_datas');
    }
}
