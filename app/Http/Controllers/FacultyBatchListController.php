<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\lecturereport;
use App\lecturereportsdetails;
use App\assignbatch;
use App\assignbatchesdetails;
use Auth;
use DB;

class FacultyBatchListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

         $uids = Auth::user()->id;
         $bdetails =  assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->where('assignbatches.faculty',$uids)->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->get();

         //dd($chat);
        

        
            return view('faculty.batchlist.manage',compact('bdetails'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
         $students = $request->studentsid;
        $data= array();
        $result = assignbatchesdetails::where('assignbatchid','=',$students)->get();
        foreach($result as $res)
        {
            $row = array();
            $row[] = $res->students;
            $row[] = $res->mobilenos;
            $row[] = $res->branch;
           $row[] = $res->enrollmentno;
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);

    
    }


    public function view(Request $request)
    {   
        $ciurslist = $request->coursename;

        $getcourse = assignbatchesdetails::where('assignbatchid',$ciurslist)->first();
        $getcoursename = $getcourse->course;


        $getlectid = lecturereport::where('courses',$getcoursename)->first();
        $getid = $getlectid->id;

        $data= array();
        $result = lecturereportsdetails::where('lectureid',$getid)->get();

        foreach($result as $res)
        {
            $row = array();
            $row[] = $res->lectures;
            $row[] = $res->mainpoints;
            $row[] = $res->lecturedetails;
            $row[] = "<input type='date' name='stardate' class='form-control'>";
            $row[] = "<input type='date' name='stardate' class='form-control'>";
            $row[] = "";
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $uids = Auth::user()->id;

        $bdetails =  assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->where('assignbatches.faculty',$uids)->where('assignbatches.id',$id)->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->get();

         //dd($chat);
        

        
            return view('faculty.batchlist.batchdetails',compact('bdetails'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
