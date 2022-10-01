<?php

namespace App\Http\Controllers;

use App\Designation_Category;
use App\usercategory;
use Illuminate\Http\Request;

class DesignationCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $uscates = Designation_Category::join('usercategories','usercategories.id','=','designation__categories.usecategoid')->select('usercategories.usercategoriesname','designation__categories.*')->get();

            return view('superadmin.designationscategory.manage',compact('uscates'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ucats = usercategory::all();

        return view('superadmin.designationscategory.create',compact('ucats'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $Designation_Categorymodel = new Designation_Category();

        $Designation_Category = $Designation_Categorymodel->create([
            'usecategoid'=> $request->categos,
            'designationsnames'=> $request->designations,
        ]);

        return redirect('/designation-category')->with('success','Designation Category created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Designation_Category  $designation_Category
     * @return \Illuminate\Http\Response
     */
    public function show(Designation_Category $designation_Category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Designation_Category  $designation_Category
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Designation_Category $designation_Category)
    {
        
        $ucats = usercategory::all();
        $edited = Designation_Category::find($id);

        return view('superadmin.designationscategory.edit',compact('ucats','edited'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Designation_Category  $designation_Category
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, Designation_Category $designation_Category)
    {
        $upda  = Designation_Category::find($id);
        $upda->usecategoid = $request->categos;
        $upda->designationsnames =  $request->designations;
        $upda->save();

        return redirect('/designation-category')->with('success','Designation Category Updated successfully!!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Designation_Category  $designation_Category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Designation_Category $designation_Category)
    {
        $del = Designation_Category::find($id);
        $del->delete();

        return redirect('/designation-category')->with('success','Designation Category Deleted successfully!!');

    }
}
