<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class studentscourse extends Model
{
    //

      protected $fillable=[
        'student_id','studentselectedcourse','studentcoursemode','selectedcourseprice'
    ];
}
