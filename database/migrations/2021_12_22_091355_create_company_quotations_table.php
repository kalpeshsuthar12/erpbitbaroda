<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_quotations', function (Blueprint $table) {
            $table->id();
            $table->date('quotatdate')->nullable();
            $table->date('quotatduedate')->nullable();
            $table->string('cname')->nullable();
            $table->string('ccontactperson')->nullable();
            $table->string('cphoneno')->nullable();
            $table->string('cwhatsappno')->nullable();
            $table->string('cemails')->nullable();
            $table->string('quotationno')->nullable();
            $table->integer('quotenos')->nullable();
            $table->string('csubtotal')->nullable();
            $table->string('cdiscountypes')->nullable();
            $table->string('cdiscounts')->nullable();
            $table->string('ctotal')->nullable();
            $table->string('cgsttax')->nullable();
            $table->string('ctaxamounts')->nullable();
            $table->string('cfinaltotals')->nullable();
            $table->string('userid')->nullable();
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
        Schema::dropIfExists('company_quotations');
    }
}
