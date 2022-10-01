<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketingblogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketingblogs', function (Blueprint $table) {
            $table->id();
            $table->integer('blogcat')->nullable();
            $table->integer('blogsubcat')->nullable();
            $table->string('blogname')->nullable();
            $table->string('blogurl')->nullable();
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
        Schema::dropIfExists('marketingblogs');
    }
}
