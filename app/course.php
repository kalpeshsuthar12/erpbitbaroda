<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class course extends Model
{
    //

    protected $fillable=[
        'cat_id','subcat_id','coursename','bygroup','courseprice','courseonlineprice','coursetax','website','coursedurations','leadslimitations','brocheurefiles','byspecialization','branches','universitnames','universitiesfees','bitfees','totalfees','coursevisible','byuniversitites',
    ];
}
