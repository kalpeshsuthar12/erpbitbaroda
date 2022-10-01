<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class affiliatesLeads extends Model
{
    protected $fillable=[
        'asourcenames','afleadsdates','afassignto','afrombranch','atobranch','affiliatesnames','acompanyname','aemails','atrainingcategory','aaddress','aphone','acity','astate','affiliatescategorys','adescriptions','aconverteds','awhatsappno','auserids',
    ];
}
