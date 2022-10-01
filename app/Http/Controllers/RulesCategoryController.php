<?php

namespace App\Http\Controllers;

use App\RulesCategory;
use Illuminate\Http\Request;

class RulesCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rcategs = RulesCategory::all();

         return view('superadmin.rulescategory.manage',compact('rcategs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.rulescategory.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $RulesCategorymodel = new RulesCategory();
        $RulesCategory = $RulesCategorymodel->create([
            'rulecategnames'=> $request->rulecatego,
        ]);

      

        return redirect('/rules-category')->with('success','Rules Category created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RulesCategory  $rulesCategory
     * @return \Illuminate\Http\Response
     */
    public function show(RulesCategory $rulesCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RulesCategory  $rulesCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id,RulesCategory $rulesCategory)
    {
        $edis = RulesCategory::find($id);

        return view('superadmin.rulescategory.edit',compact('edis'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RulesCategory  $rulesCategory
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, RulesCategory $rulesCategory)
    {
        $updatw = RulesCategory::find($id);
        $updatw->rulecategnames = $request->rulecatego;
        $updatw->save(); 

        return redirect('/rules-category')->with('success','Rules Category Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RulesCategory  $rulesCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,RulesCategory $rulesCategory)
    {
         $updatw = RulesCategory::find($id);
        $updatw->delete(); 

        return redirect('/rules-category')->with('success','Rules Category Deleted successfully!');
    }
}
