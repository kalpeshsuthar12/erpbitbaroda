<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_expenses', function (Blueprint $table) {
            $table->id();
            $table->integer('epayid')->nullable();
            $table->integer('expoldamounts')->nullable();
            $table->integer('expnsenewamounts')->nullable();
            $table->string('expensepaymode')->nullable();
            $table->date('exppaymendate')->nullable();
            $table->string('expensefor')->nullable();
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
        Schema::dropIfExists('cash_expenses');
    }
}
