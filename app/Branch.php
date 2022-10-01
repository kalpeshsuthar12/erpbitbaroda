<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    //
    protected $fillable=[
        'branchname','branchlogo','ipaddresses','client_id','client_code',
    ];
}
