<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HrManag extends Model
{
     protected $fillable = [
        'lettertype', 'usecompanys','lusers_id','lissuingdates','lissuingenddates','ltexts','lucategory',
    ];
}
