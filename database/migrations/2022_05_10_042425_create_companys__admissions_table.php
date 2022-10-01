<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanysAdmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companys__admissions', function (Blueprint $table) {
            $table->id();
            $table->integer('cadmissionsid')->nullable();
            $table->string('cstudentsnames')->nullable();
            $table->string('cemails')->nullable();
            $table->string('cphones')->nullable();
            $table->string('cwhatsappnos')->nullable();
            $table->string('cbranchs')->nullable();
            $table->string('cernos')->nullable();
            $table->string('csjerno')->nullable();
            $table->string('cwgerno')->nullable();
            $table->string('cmjerno')->nullable();
            $table->string('celerno')->nullable();
            $table->string('cbitolerno')->nullable();
            $table->string('ccvrublerno')->nullable();
            $table->string('ccvrukherno')->nullable();
            $table->string('crntuerno')->nullable();
            $table->string('cmanipalerno')->nullable();
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
        Schema::dropIfExists('companys__admissions');
    }
}
