<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('references', function (Blueprint $table) {
            $table->id();
            $table->string('referencefrom')->nullable();
            $table->string('referencename')->nullable();
            $table->string('assignto')->nullable();
            $table->string('rphone')->nullable();
            $table->string('courses')->nullable();
            $table->string('incentive')->nullable();
            $table->string('iamounts')->nullable();
            $table->string('paymentmode')->nullable();
            $table->boolean('status')->default('0');
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
        Schema::dropIfExists('references');
    }
}
