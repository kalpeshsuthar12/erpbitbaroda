<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersSalarDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users__salar__data', function (Blueprint $table) {
            $table->id();
            $table->integer('husalarys_id')->nullable();
            $table->string('hutotalsalarys')->nullable();
            $table->string('humonthsdays')->nullable();
            $table->string('huworkingdays')->nullable();
            $table->string('hutwh')->nullable();
            $table->string('htsplphpl')->nullable();
            $table->string('howh')->nullable();
            $table->string('hwh')->nullable();
            $table->string('hpl')->nullable();
            $table->string('hl')->nullable();
            $table->string('hws')->nullable();
            $table->string('hi')->nullable();
            $table->string('hsi')->nullable();
            $table->string('hadt')->nullable();
            $table->string('htar')->nullable();
            $table->Date('hstrtdates')->nullable();
            $table->Date('henddates')->nullable();
            $table->string('hfinals')->nullable();
            $table->Date('hpaymentdate')->nullable();
            $table->string('hpaymentmode')->nullable();
            $table->string('hbanknames')->nullable();
            $table->string('hchqno')->nullable();
            $table->string('hchqdate')->nullable();
            $table->string('hchqtype')->nullable();
            $table->string('hremarks')->nullable();
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
        Schema::dropIfExists('users__salar__data');
    }
}
