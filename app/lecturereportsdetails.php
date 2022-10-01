<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class lecturereportsdetails extends Model
{
    protected $fillable=[
        'lectureid','lectures','mainpoints','lecturedetails',
    ];
}
