<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallmentFollowupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installment_followups', function (Blueprint $table) {
            $table->id();
            $table->integer('admissionsfrom')->nullable();
            $table->string('afollowupsstatus')->nullable();
            $table->date('afollowupsdate')->nullable();
            $table->string('afollowupsremarks')->nullable();
            $table->date('anextfollowupsdate')->nullable();
            $table->boolean('afstatus')->default(0);
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
        Schema::dropIfExists('installment_followups');
    }
}
