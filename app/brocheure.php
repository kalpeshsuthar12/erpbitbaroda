<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class brocheure extends Model
{
    protected $fillable=[
        'categ_id','subcateg_id','brocheuresfiles','mcoursename','coursesurls',
    ];
}
