<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class invoices extends Model
{
      protected $fillable=[
        'studentid','branchId','branchInvno','sjIno','mjIno','wgIno','paymentmode','discounttype','invdate','duedate','invtotal','subtotal','discount','taxes','userid',
    ];
}
