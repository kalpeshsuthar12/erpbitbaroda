<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliatesLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates_leads', function (Blueprint $table) {
            $table->id();
            $table->string('sourcenames')->nullable();
            $table->date('afleadsdates')->nullable();
            $table->string('afassignto')->nullable();
            $table->string('afrombranch')->nullable();
            $table->string('atobranch')->nullable();
            $table->string('affiliatesnames')->nullable();
            $table->string('acompanyname')->nullable();
            $table->string('aemails')->nullable();
            $table->string('atrainingcategory')->nullable();
            $table->string('aaddress')->nullable();
            $table->string('aphone')->nullable();
            $table->string('acity')->nullable();
            $table->string('astate')->nullable();
            $table->string('affiliatescategorys')->nullable();
            $table->string('adescriptions')->nullable();
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
        Schema::dropIfExists('affiliates_leads');
    }
}
