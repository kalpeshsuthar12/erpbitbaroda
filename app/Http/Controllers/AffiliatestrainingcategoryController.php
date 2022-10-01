<?php

namespace App\Http\Controllers;

use App\affiliatestrainingcategory;
use Illuminate\Http\Request;

class AffiliatestrainingcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $afcnames = affiliatestrainingcategory::all();
        return view('superadmin.affiliatestrainingcategory.manage',compact('afcnames'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.affiliatestrainingcategory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //dd($request->atcname);
        $affiliatestrainingcategorymodel = new affiliatestrainingcategory();
        $affiliatestrainingcategory = $affiliatestrainingcategorymodel->create([
            'atcategoriesnames'=> $request->atcname,
            
        ]);

      

        return redirect('/affiliates-training-category')->with('success','Affiliates Training Category created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\affiliatestrainingcategory  $affiliatestrainingcategory
     * @return \Illuminate\Http\Response
     */
    public function show(affiliatestrainingcategory $affiliatestrainingcategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\affiliatestrainingcategory  $affiliatestrainingcategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id,affiliatestrainingcategory $affiliatestrainingcategory)
    {
        $edits = affiliatestrainingcategory::find($id);
        return view('superadmin.affiliatestrainingcategory.edit',compact('edits'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\affiliatestrainingcategory  $affiliatestrainingcategory
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, affiliatestrainingcategory $affiliatestrainingcategory)
    {
        $updates = affiliatestrainingcategory::find($id);
        $updates->atcategoriesnames = $request->atcname;
        $updates->save();

        return redirect('/affiliates-training-category')->with('success','Affiliates Training Category Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\affiliatestrainingcategory  $affiliatestrainingcategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,affiliatestrainingcategory $affiliatestrainingcategory)
    {
        $deles = affiliatestrainingcategory::find($id);
        $deles->delete();
         return redirect('/affiliates-training-category')->with('success','Affiliates Training Category Deleted successfully!');
    }
}
