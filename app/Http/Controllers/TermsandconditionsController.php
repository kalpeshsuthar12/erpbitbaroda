<?php

namespace App\Http\Controllers;

use App\termsandconditions;
use App\RulesCategory;
use App\User;
use App\usercategory;
use Illuminate\Http\Request;

class TermsandconditionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tall = termsandconditions::all();
       

        return view('superadmin.termsconditions.manage',compact('tall'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $rcategs = RulesCategory::all();
         $uca = usercategory::all();
        return view('superadmin.termsconditions.create',compact('rcategs','uca'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $ucateg = $request->ucategory;
        if(is_array($ucateg))
        {
            $ucats = implode(',', $ucateg);
        }
        else
        {
            $ucats = $request->ucategory;
        }

        $termsandconditionsmodel = new termsandconditions();
        $termsandconditions = $termsandconditionsmodel->create([
            'rulecategories'=> $request->rcategorys,
            'termsconditions'=> $request->tconditions,
            'userscategories'=> $ucats,
        ]);

      

        return redirect('/terms-conditions')->with('success','Terms and Conditions created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\termsandconditions  $termsandconditions
     * @return \Illuminate\Http\Response
     */
    public function show($f,termsandconditions $termsandconditions)
    {
            $tdetails = termsandconditions::find($f);

         return response()->json($tdetails);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\termsandconditions  $termsandconditions
     * @return \Illuminate\Http\Response
     */
    public function edit($id,termsandconditions $termsandconditions)
    {
        $eids = termsandconditions::find($id);
        $rcategs = RulesCategory::all();
        $selecteducategors = explode(',', $eids->userscategories);
        $uca = usercategory::all();
        return view('superadmin.termsconditions.edit',compact('eids','rcategs','uca','selecteducategors'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\termsandconditions  $termsandconditions
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, termsandconditions $termsandconditions)
    {
        $updat = termsandconditions::find($id);

           $ucateg = $request->ucategory;
        if(is_array($ucateg))
        {
            $ucats = implode(',', $ucateg);
        }
        else
        {
            $ucats = $request->ucategory;
        }

        $updat->rulecategories = $request->rcategorys;
        $updat->userscategories = $ucats;
        $updat->termsconditions = $request->tconditions;
        $updat->save();

        return redirect('/terms-conditions')->with('success','Terms and Conditions Updated successfully!');
    }

    public function mdify($id)
    {
        $midus = termsandconditions::find($id);
        $rcategs = RulesCategory::all();
        $selecteducategors = explode(',', $midus->userscategories);
        $uca = usercategory::all();
        return view('superadmin.termsconditions.modify',compact('midus','rcategs','selecteducategors','uca'));
    }

    public function modifytersm($id,Request $request)
    {
        $moduf = termsandconditions::find($id);
        $ucateg = $request->ucategory;
        if(is_array($ucateg))
        {
            $ucats = implode(',', $ucateg);
        }
        else
        {
            $ucats = $request->ucategory;
        }

        $moduf->rulecategories = $request->rcategorys;
        $moduf->userscategories = $ucats;
        $moduf->termsconditions = $request->tconditions;
        $moduf->save();

        $cahne = User::where('uafficategory',$request->rcategorys)->get();
        //dd($getusersid);
        foreach($cahne as $result)
        {
                $result->rstatus = 0;
                $result->tstatus = 0;
                $result->save();
        }
                   
        


            return redirect('/terms-conditions')->with('success','Terms and Conditions Modify successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\termsandconditions  $termsandconditions
     * @return \Illuminate\Http\Response
     */
    public function destroy(termsandconditions $termsandconditions)
    {
        //
    }
}
