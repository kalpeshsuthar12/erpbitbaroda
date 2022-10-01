<?php

namespace App\Http\Controllers;

use App\coursesubcategory;
use App\coursecategory;
use Illuminate\Http\Request;
use DB;

class CoursesubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(coursesubcategory $coursesubcategory)
    {
        //

       /* $course = coursesubcategory::find(1);



        dd($course);*/
        $coursesub =DB::table('coursesubcategories')
                ->join('coursecategories', 'coursesubcategories.coursecat_id', '=', 'coursecategories.id')
                ->select('coursesubcategories.id','coursesubcategories.subcategory_name', 'coursecategories.coursecategoryname','coursesubcategories.created_at')
                ->get();

        //dd($coursesub);
                return view('superadmin.course.managesubcategory',compact('coursesub'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(coursecategory $coursecategory)
    {
        //

        $category = $coursecategory::get();

        //dd($category);
        return view('superadmin.course.createsubcategory',compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,coursesubcategory $coursesubcategory)
    {
        $coursesubcategorymodel = new coursesubcategory();
        $coursesubcategory = $coursesubcategorymodel->create([
            'coursecat_id'=> $request->coursecategory,
            'subcategory_name'=> $request->subcategoryname,
        ]);

        return redirect('/subcategory')->with('success','Course Subcategory created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\coursesubcategory  $coursesubcategory
     * @return \Illuminate\Http\Response
     */
    public function show(coursesubcategory $coursesubcategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\coursesubcategory  $coursesubcategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id,coursesubcategory $coursesubcategory,coursecategory $coursecategory)
    {
        //

        $cat = $coursecategory::all();
        $subcate = $coursesubcategory::find($id);

        return view('superadmin.course.editsubcategory',compact('cat','subcate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\coursesubcategory  $coursesubcategory
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, coursesubcategory $coursesubcategory)
    {
        //

         $updates = $coursesubcategory::find($id);
        $updates->coursecat_id = $request->ecoursecategory;
        $updates->subcategory_name = $request->esubcategoryname;
        $updates->save();

        return redirect('/subcategory')->with('success','Course Subcategory Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\coursesubcategory  $coursesubcategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,coursesubcategory $coursesubcategory)
    {
        $deletes = $coursesubcategory::find($id);
        $deletes->delete();
        return redirect('/subcategory')->with('success','Course Subcategory Deleted successfully!');
    }
}
