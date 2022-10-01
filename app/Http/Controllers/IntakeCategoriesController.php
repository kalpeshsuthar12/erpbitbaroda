<?php

namespace App\Http\Controllers;

use App\IntakeCategories;
use Illuminate\Http\Request;

class IntakeCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $itkaecatego = IntakeCategories::get();

        return view('superadmin.intakecategories.manage',compact('itkaecatego'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('superadmin.intakecategories.create');    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $IntakeCategoriesmodel = new IntakeCategories();
        $IntakeCategories = $IntakeCategoriesmodel->create([
            'intakecategoriesname'=> $request->icatname,
        ]);

       return redirect('/intake-categories')->with('success','Intake Category created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\IntakeCategories  $intakeCategories
     * @return \Illuminate\Http\Response
     */
    public function show(IntakeCategories $intakeCategories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\IntakeCategories  $intakeCategories
     * @return \Illuminate\Http\Response
     */
    public function edit($id,IntakeCategories $intakeCategories)
    {
        $edits = IntakeCategories::find($id);

        return view('superadmin.intakecategories.edit',compact('edits'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\IntakeCategories  $intakeCategories
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, IntakeCategories $intakeCategories)
    {
        $updat = IntakeCategories::find($id);
        $updat->intakecategoriesname = $request->icatname;
        $updat->save();
         return redirect('/intake-categories')->with('success','Intake Category Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\IntakeCategories  $intakeCategories
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,IntakeCategories $intakeCategories)
    {
        $deles = IntakeCategories::find($id);
        $deles->delete();
        return redirect('/intake-categories')->with('success','Intake Category Deleted successfully!');

    }
}

