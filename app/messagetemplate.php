<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class messagetemplate extends Model
{
    protected $fillable=[
        'messagename','messagedetails',
    ];
}
