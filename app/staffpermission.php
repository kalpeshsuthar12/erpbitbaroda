<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class staffpermission extends Model
{
     protected $fillable=[
        'user_id','bulkpdfexports','contracts','creditnotes','students','emailtemplates','estimates','expenses','invoices','course','payments','projects','proposals','staffroles','staff','subscriptions','tasks','leads','surveys','commissionreceipt','commissionapplicablestaff','commissionapplicablestudents','commissionprogram','goals',
    ];
}
