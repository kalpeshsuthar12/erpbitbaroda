<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    protected $fillable = ['qcourseid','qlectures','qquestions','qusersids','aoptions','boptions','coptions','doptions','correctanswers','qimgs'];
}
