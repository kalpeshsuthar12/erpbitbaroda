<?php

namespace App\Http\Controllers;

use App\Agreementstructure;
use Illuminate\Http\Request;

class AgreementstructureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agreementsdetails = Agreementstructure::all();

        return view('superadmin.affiliatesagreement.manage',compact('agreementsdetails')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.affiliatesagreement.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $Agreementstructuremodel = new Agreementstructure();
        $Agreementstructure = $Agreementstructuremodel->create([
            'agreementname'=> $request->anames,
            'agreementdetails'=> $request->agreementdetails,
        ]);

      

        return redirect('/affiliates-agreements')->with('success','Agreements created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agreementstructure  $agreementstructure
     * @return \Illuminate\Http\Response
     */
    public function show($k,Agreementstructure $agreementstructure)
    {
        $view = Agreementstructure::find($k);

        return response()->json($view);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Agreementstructure  $agreementstructure
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Agreementstructure $agreementstructure)
    {
        $edt =  Agreementstructure::find($id);

        //dd($edt);
        return view('superadmin.affiliatesagreement.edit',compact('edt'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Agreementstructure  $agreementstructure
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, Agreementstructure $agreementstructure)
    {
           $updates = Agreementstructure::find($id);
           $updates->agreementname = $request->anames;     
           $updates->agreementdetails = $request->agreementdetails;     
           $updates->save();   

            return redirect('/affiliates-agreements')->with('success','Agreements updated successfully!');  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Agreementstructure  $agreementstructure
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Agreementstructure $agreementstructure)
    {
       $deles = Agreementstructure::find($id);
       $deles->delete();

        return redirect('/affiliates-agreements')->with('success','Agreements Deleted successfully!'); 
    }
}
