<?php

namespace App\Http\Controllers;

use App\usercategory;
use Illuminate\Http\Request;

class UsercategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(usercategory $usercategory)
    {
        $ucategory = usercategory::get();
        return view('superadmin.usercategories.manage',compact('ucategory'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.usercategories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,usercategory $usercategory)
    {
        $usercategorymodel = new usercategory();
        $usercategory = $usercategorymodel->create([
            'usercategoriesname'=> $request->ucname,
        ]);

        return redirect('/user-category')->with('success','User Category created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\usercategory  $usercategory
     * @return \Illuminate\Http\Response
     */
    public function show(usercategory $usercategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\usercategory  $usercategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id,usercategory $usercategory)
    {
        //
        $edited = usercategory::find($id);
        return view('superadmin.usercategories.edit',compact('edited'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\usercategory  $usercategory
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, usercategory $usercategory)
    {
        $updated = usercategory::find($id);
        $updated->usercategoriesname = $request->ucname;
        $updated->save();

        return redirect('/user-category')->with('success','User Category Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\usercategory  $usercategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,usercategory $usercategory)
    {
        $dele = usercategory::find($id);
        $dele->delete();

        return redirect('/user-category')->with('success','User Category Deleted Successfully!');
    }
}
