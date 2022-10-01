<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class clearaccounting extends Model
{
    protected $fillable=[
        'accountingdates','clbranchs','cashclearence','onlinepaymentsclearence','cvruclearence','bankclearence',
    ];
}
