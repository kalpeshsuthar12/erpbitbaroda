<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class students extends Model
{
    //
     protected $fillable=[
        'studentname','dateofbirth','studemail','branch','brancherno','sjerno','wgerno','mjerno','phoneno','street','city','state','zipcode','preferrabletime','coursemode','remarknote','userId',
    ];
}
