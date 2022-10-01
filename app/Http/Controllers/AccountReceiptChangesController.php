<?php

namespace App\Http\Controllers;
use App\SetPinforurl;
use App\payment;
use App\PaymentSource;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountReceiptChangesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('superadmin.chanageablereceipt.manage');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $getlatestpin = SetPinforurl::latest()->first();

        $upins = $request->uspins;

        if($getlatestpin->origingetpins == $upins)
        {
              //  dd('Yeah You  have Got Access!!!');

            return redirect('/get-all-receipts');
        }
        else
        {
            return redirect()->back()->with('error','Incorrect PIN!!');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userbranchs = Auth::user()->branchs;
        $paymensdatas = payment::where('branchs',$userbranchs)->orderBy('id','DESC')->get();

        return view('superadmin.chanageablereceipt.allreceipts',compact('paymensdatas'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $paymentdeta = payment::find($id);
        $psource = PaymentSource::all();

        return view('superadmin.chanageablereceipt.editreceipts',compact('paymentdeta','psource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         

         if($request->paymentmode == 'Bank (Cheque)')
         {
              $updates = payment::find($id);
              $updates->paymentreceived = $request->paymentrecieved;  
              $updates->remainingamount = $request->ramount;  
              $updates->paymentdate = $request->paymentdate;  
              $updates->paymentmode = $request->paymentmode;  
              $updates->nexamountdate = $request->remindersdates;  
              $updates->bankname = $request->bankname;  
              $updates->chequeno = $request->chequeno;  
              $updates->chequedate = $request->chequedate;  
              $updates->chequetype = $request->chequetype;  
              $updates->transactionsids = $request->transactionsids;  
              $updates->remarknoe = $request->remarknote;  
              $updates->save();

              return redirect('/get-all-receipts')->with('success','Receipt Change Successfully!!');  
         }

         else
         {
              $updates = payment::find($id);
              $updates->paymentreceived = $request->paymentrecieved;  
              $updates->remainingamount = $request->ramount;  
              $updates->paymentdate = $request->paymentdate;  
              $updates->paymentmode = $request->paymentmode;  
              $updates->nexamountdate = $request->remindersdates;  
              $updates->transactionsids = $request->transactionsids;  
              $updates->remarknoe = $request->remarknote;  
              $updates->save();

              return redirect('/get-all-receipts')->with('success','Receipt Change Successfully!!');
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
