<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('studentid')->nullable();
            $table->integer('branchId')->nullable();
            $table->string('branchInvno')->nullable();
            $table->integer('sjIno')->nullable();
            $table->integer('mjIno')->nullable();
            $table->integer('wgIno')->nullable();
            $table->string('paymentmode')->nullable();
            $table->string('discounttype')->nullable();
            $table->date('invdate')->nullable();
            $table->date('duedate')->nullable();
            $table->string('invtotal')->nullable();
            $table->string('subtotal')->nullable();
            $table->string('discount')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
