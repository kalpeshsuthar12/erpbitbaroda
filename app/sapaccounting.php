<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sapaccounting extends Model
{
    protected $fillable = ['sapid','sappaydates','sapenrollno','sapfees','sapbitfees','sapstudentsname','sapcourses','sapadmissionsfors','saptotfees','sapbalfees','sappayablefees','sapbufees','sapreceiptnos','sapreleaseddates'];
}
