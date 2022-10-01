<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class batch_lr_attendance_reports extends Model
{
        protected $fillable = ['abs_batch_id','absstudentsid','abslecids','absdates','absextrapoints','absstudentsattendance'];  
}
