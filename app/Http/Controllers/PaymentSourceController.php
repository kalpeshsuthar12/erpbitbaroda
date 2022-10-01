<?php

namespace App\Http\Controllers;

use App\PaymentSource;
use Illuminate\Http\Request;

class PaymentSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $psources = PaymentSource::all();
        return view('superadmin.paymentsource.manage',compact('psources'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('superadmin.paymentsource.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $PaymentSourcemodel = new PaymentSource();
        $PaymentSource = $PaymentSourcemodel->create([
            'paymentname'=> $request->psourcesname,
        ]);

      

        return redirect('/payment-sources')->with('success','Payment Sources created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentSource  $paymentSource
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentSource $paymentSource)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaymentSource  $paymentSource
     * @return \Illuminate\Http\Response
     */
    public function edit($id,PaymentSource $paymentSource)
    {
        $edites = PaymentSource::find($id);
        return view('superadmin.paymentsource.edit',compact('edites'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentSource  $paymentSource
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, PaymentSource $paymentSource)
    {
        $upda = PaymentSource::find($id);
        $upda->paymentname = $request->psourcesname;
        $upda->save();

         return redirect('/payment-sources')->with('success','Payment Sources Updated Successfully!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentSource  $paymentSource
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,PaymentSource $paymentSource)
    {
          $deles = PaymentSource::find($id);
        $deles->delete();
        return redirect('/payment-sources')->with('success','Payment Sources Deleted successfully!');
    }
}
