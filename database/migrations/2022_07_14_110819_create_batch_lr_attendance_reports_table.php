<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchLrAttendanceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_lr_attendance_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('abs_batch_id')->nullable();
            $table->integer('absstudentsid')->nullable();
            $table->integer('abslecids')->nullable();
            $table->date('absdates')->nullable();
            $table->string('absextrapoints')->nullable();
            $table->string('absstudentsattendance')->nullable();
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
        Schema::dropIfExists('batch_lr_attendance_reports');
    }
}
