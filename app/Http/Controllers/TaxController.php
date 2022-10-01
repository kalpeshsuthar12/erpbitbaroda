<?php

namespace App\Http\Controllers;

use App\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Tax $tax)
    {
        //

        $taxs = $tax::get();
        return view('superadmin.sales.managesales',compact('taxs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view('superadmin.sales.createtax');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Tax $tax)
    {
        //

        $taxmodel = new tax();
        $taxes = $taxmodel->create([
            'taxname'=> $request->taxname,
            'taxrate'=> $request->taxrate,
        ]);


         return redirect('/tax')->with('success','Tax created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function show(Tax $tax)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Tax $tax)
    {
        $taxedit = $tax::find($id);

        return view('superadmin.sales.edittax',compact('taxedit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, Tax $tax)
    {
         $updated = $tax::find($id);
        $updated->taxname = $request->taxname;
        $updated->taxrate = $request->taxrate;
        $updated->save();

        return redirect('/tax')->with('success','Tax Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Tax $tax)
    {
        //

        $del = $tax::find($id);
         $del->delete();

        return redirect('/tax')->with('success','Tax Deleted Successfully');

    }
}
