<?php

namespace App\Http\Controllers;

use App\course;
use App\BatchCourseDurations;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BatchCourseDurationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bcoursdurations = BatchCourseDurations::join('courses','courses.id','=','batch_course_durations.batcourids')->select('batch_course_durations.*','courses.coursename','batch_course_durations.id as bids')->get();

        return view('superadmin.batchcoursedurations.manage',compact('bcoursdurations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $call = course::all();
        
        return view('superadmin.batchcoursedurations.create',compact('call'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $BatchCourseDurationsmodel = new BatchCourseDurations();
        $BatchCourseDurations = $BatchCourseDurationsmodel->create([
            'batcourids'=> $request->courses,
            'batcscoursefor'=> $request->crsefor,
            'bindicoursedurations'=> $request->duratins,
        ]);

      

        return redirect('/batchs-course-durations')->with('success','Batch Course Durations created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BatchCourseDurations  $batchCourseDurations
     * @return \Illuminate\Http\Response
     */
    public function show(BatchCourseDurations $batchCourseDurations)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BatchCourseDurations  $batchCourseDurations
     * @return \Illuminate\Http\Response
     */
    public function edit($id,BatchCourseDurations $batchCourseDurations)
    {
        $edits = BatchCourseDurations::find($id);
        $call = course::all();

        return view('superadmin.batchcoursedurations.edit',compact('edits','call'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BatchCourseDurations  $batchCourseDurations
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, BatchCourseDurations $batchCourseDurations)
    {
        $uodate = BatchCourseDurations::find($id);
        $uodate->batcourids = $request->courses;
        $uodate->batcscoursefor = $request->crsefor;
        $uodate->bindicoursedurations = $request->duratins;
        $uodate->save();
         return redirect('/batchs-course-durations')->with('success','Batch Course Durations Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BatchCourseDurations  $batchCourseDurations
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,BatchCourseDurations $batchCourseDurations)
    {
        $des = BatchCourseDurations::find($id);
        $des->delete();

          return redirect('/batchs-course-durations')->with('success','Batch Course Durations Deleted successfully!');
    }

    public function getajaxvalue($courseval)
    {
        $getvlaue = BatchCourseDurations::where('batcourids',$courseval)->get();

        //$getdurations = $getvlaue->bindicoursedurations;

        return response()->json($getvlaue);

         //return response()->json($studentdata);
    }

    public function getenddates($strtdates,$bdurations)
    {
            $date = Carbon::createFromFormat('Y-m-d', $strtdates);
            $daysToAdd = $bdurations;
            $date = $date->addDays($daysToAdd);
            $dform = $date->format('Y-m-d');
        return response()->json($dform);
    }
}
