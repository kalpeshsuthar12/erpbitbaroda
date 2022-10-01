<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class marketingblogs extends Model
{
     protected $fillable=[
        'blogcat','blogsubcat','blogname','blogurl',
    ];
}
