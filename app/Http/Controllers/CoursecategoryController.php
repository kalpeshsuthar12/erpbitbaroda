<?php

namespace App\Http\Controllers;

use App\coursecategory;
use Illuminate\Http\Request;

class CoursecategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(coursecategory $coursecategory)
    {
        //

        $allCateg = $coursecategory::get();

        return view('superadmin.course.managecategory',compact('allCateg'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view('superadmin.course.createcategory');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,coursecategory $coursecategory)
    {
        //

         $coursecategorymodel = new coursecategory();
        $coursecategory = $coursecategorymodel->create([
            'coursecategoryname'=> $request->coursecategory,
        ]);

      

        return redirect('/course-category')->with('success','Course Category created successfully!');


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\coursecategory  $coursecategory
     * @return \Illuminate\Http\Response
     */
    public function show(coursecategory $coursecategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\coursecategory  $coursecategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id,coursecategory $coursecategory)
    {
        //

        $edise = $coursecategory::find($id);
       
        return view('superadmin.course.editcategory',compact('edise'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\coursecategory  $coursecategory
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, coursecategory $coursecategory)
    {
      
        $updates = $coursecategory::find($id);
        $updates->coursecategoryname = $request->editcoursecategory;
        $updates->save();

        return redirect('/course-category')->with('success','Category Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\coursecategory  $coursecategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,coursecategory $coursecategory)
    {
        //

        $delete = $coursecategory::find($id);
        $delete->delete();

        return redirect('/course-category')->with('success','Category Deleted Successfully');
    }
}
