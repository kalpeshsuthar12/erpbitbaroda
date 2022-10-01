<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrManagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_manags', function (Blueprint $table) {
            $table->id();
            $table->string('lucategory')->nullable();
            $table->string('lettertype')->nullable();
            $table->string('usecompanys')->nullable();
            $table->integer('lusers_id')->nullable();
            $table->date('lissuingdates')->nullable();
            $table->longtext('ltexts')->nullable();
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
        Schema::dropIfExists('hr_manags');
    }
}
