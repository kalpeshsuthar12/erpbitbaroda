<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersOfficialsTimingsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_officials_timings_details', function (Blueprint $table) {
            $table->id();
            $table->integer('usersdetailsid')->nullable();
            $table->string('usersdetailsbranchs')->nullable();
            $table->string('usersdetailsmodes')->nullable();
            $table->string('usersdetailsdays')->nullable();
            $table->string('usersdetailsintimings')->nullable();
            $table->string('usersdetailsouttimings')->nullable();
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
        Schema::dropIfExists('users_officials_timings_details');
    }
}
