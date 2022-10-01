<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequeFollowupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cheque_followups', function (Blueprint $table) {
            $table->id();
            $table->integer('cadmissionsfrom')->nullable();
            $table->string('cafollowupsstatus')->nullable();
            $table->date('cafollowupsdate')->nullable();
            $table->string('cafollowupsremarks')->nullable();
            $table->date('canextfollowupsdate')->nullable();
            $table->string('cafollowupsby')->nullable();
            $table->boolean('cafstatus')->default(0);
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
        Schema::dropIfExists('cheque_followups');
    }
}
