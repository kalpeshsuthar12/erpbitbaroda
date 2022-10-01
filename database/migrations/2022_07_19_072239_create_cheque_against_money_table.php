<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequeAgainstMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cheque_against_money', function (Blueprint $table) {
            $table->id();
            $table->integer('cacpid')->nullable();
            $table->string('cacpaymodes')->nullable();
            $table->string('cactotalamounts')->nullable();
            $table->string('cacpayableamounts')->nullable();
            $table->string('cacremainingamounts')->nullable();
            $table->string('cacbanknames')->nullable();
            $table->string('cacchequenos')->nullable();
            $table->string('cacchequtyoe')->nullable();
            $table->date('cacchequedates')->nullable();
            $table->date('cacpaymentdates')->nullable();
            $table->date('cacnextamountdates')->nullable();
            $table->string('cacremarks')->nullable();
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
        Schema::dropIfExists('cheque_against_money');
    }
}
