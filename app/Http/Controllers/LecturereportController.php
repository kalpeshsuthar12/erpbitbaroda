<?php

namespace App\Http\Controllers;

use App\lecturereport;
use App\lecturereportsdetails;
use App\course;
use App\coursebunchlist;
use App\coursespecializationlist;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeactureReportImport;
use Illuminate\Http\Request;
use DB;

class LecturereportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $importdata = lecturereport::all();

        return view('superadmin.lecturereport.manage',compact('importdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.lecturereport.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $execeldat = Excel::import(new LeactureReportImport,request()->file('file'));
        return redirect('/lecture-report')->with('success','Lecture Reports Imported successfully!!');
    }

    public function storelecturedetails(Request $request)
    {
          $lecturereportmodel = new lecturereport();
        $lecturereport = $lecturereportmodel->create([
            'courses'=> $request->courses,
            'totallectures'=> $request->totalslecture,
        ]);


        $lecturesssid = $lecturereport->id;
        $lectureslist = $request->lectures;
        $mapoints = $request->mainpoints;
        $detaillist = $request->details;

         for($i=0; $i < (count($lectureslist)); $i++)
                    {
                                $lecturereportsdetails = new lecturereportsdetails([
                                
                                'lectureid' => $lecturesssid,
                                'lectures'   => $lectureslist[$i],
                                'mainpoints'   => $mapoints[$i],
                                'lecturedetails'   => $detaillist[$i],
                            ]);
                            $lecturereportsdetails->save();
                    }

       return redirect('/lecture-report')->with('success','Lecture Reports created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\lecturereport  $lecturereport
     * @return \Illuminate\Http\Response
     */
    public function show(lecturereport $lecturereport)
    {
        $cours  = course::all();
        return view('superadmin.lecturereport.editor',compact('cours'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\lecturereport  $lecturereport
     * @return \Illuminate\Http\Response
     */
    public function edit($id,lecturereport $lecturereport)
    {
        $cours  = course::all();
        $lr = lecturereport::find($id);
        $ldetails = lecturereportsdetails::where('lectureid','=',$id)->get();
        //dd($ldetails);
        return view('superadmin.lecturereport.edit',compact('lr','cours','ldetails'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\lecturereport  $lecturereport
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request)
    {
        $lr = lecturereport::find($id);
         $lr->courses = $request->courses;
         $lr->totallectures = $request->totalslecture;
         $lr->save();
         

        
         
           $lect = $request->lectures;
            $mp = $request->mainpoints;
            $ldetails  = $request->lecturedetails;
            $fds  = $request->fd;

            $dele = lecturereportsdetails::where('lectureid',$fds)->get();
            $dele->each->delete();
          // dd($dele);


                   for($i=0; $i < (count($lect)); $i++)
                    {
                        
                         $productss = lecturereportsdetails::updateOrCreate(['lectureid' => $id,'lectures' => $lect[$i],'mainpoints' => $mp[$i],'lecturedetails' => $ldetails[$i] ]);
                    } 


            return redirect('/lecture-report')->with('success','Lecture Reports Updated successfully!');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\lecturereport  $lecturereport
     * @return \Illuminate\Http\Response
     */
    public function destroy(lecturereport $lecturereport)
    {
        //
    }
    public function getdetails(Request $request)
    {
        $ldetails = $request->lecturesdetails;
        $data= array();

        $result = DB::select('SELECT * FROM `lecturereportsdetails` WHERE lectureid = "'.$ldetails.'" ORDER BY id ASC');

        foreach($result as $res)
        {
            $row = array();
            $row[] = $res->lectures;
            $row[] = "<b>".$res->mainpoints."</b>";
            $row[] = $res->lecturedetails;
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);
    }

    public function getlecturesubcourse($mainsscourser)
    {
        $cid = course::where('coursename',$mainsscourser)->pluck('id');
        //$cid = course::where('coursename',$coursename)->pluck('id');

        $subncs = coursebunchlist::where('courseid',$cid)->get();
         return response()->json($subncs);
    }

     public function getspecializato($mainsscourser)
    {
        $cid = course::where('coursename',$mainsscourser)->pluck('id');
        //$cid = course::where('coursename',$coursename)->pluck('id');

        $special = coursespecializationlist::where('coursessid',$cid)->get();
         return response()->json($special);
    }
    public function getlectdetails($subcde)
    {
       //$ldedtails = lecturereport::where('courses',$subcde)->get();

        $data = lecturereport::select("courses")
                ->where("courses","LIKE","%{$subcde}%")
                ->get();

       //dd($ldedtails);
        return response()->json($data);
    }

    public function getlecturename($lecturenamesfind)
    {   
        $viewdata = lecturereport::find($lecturenamesfind);

        return response()->json($viewdata);
        
    }
}
