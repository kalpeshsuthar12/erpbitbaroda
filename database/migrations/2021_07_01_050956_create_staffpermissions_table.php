<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffpermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staffpermissions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('bulkpdfexports')->nullable();
            $table->string('contracts')->nullable();
            $table->string('creditnotes')->nullable();
            $table->string('students')->nullable();
            $table->string('emailtemplates')->nullable();
            $table->string('estimates')->nullable();
            $table->string('expenses')->nullable();
            $table->string('invoices')->nullable();
            $table->string('course')->nullable();
            $table->string('knowledgebase')->nullable();
            $table->string('payments')->nullable();
            $table->string('projects')->nullable();
            $table->string('proposals')->nullable();
            $table->string('staffroles')->nullable();
            $table->string('staff')->nullable();
            $table->string('subscriptions')->nullable();
            $table->string('tasks')->nullable();
            $table->string('leads')->nullable();
            $table->string('surveys')->nullable();
            $table->string('commissionreceipt')->nullable();
            $table->string('commissionapplicablestaff')->nullable();
            $table->string('commissionapplicablestudents')->nullable();
            $table->string('commissionprogram')->nullable();
            $table->string('goals')->nullable();
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
        Schema::dropIfExists('staffpermissions');
    }
}
