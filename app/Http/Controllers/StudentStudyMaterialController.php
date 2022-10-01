<?php

namespace App\Http\Controllers;
use App\students;
use App\course;
use App\Branch;
use App\leads;
use App\payment;
use App\studentscourse;
use App\Tax;
use App\User;
use App\admissionprocess;
use App\admissionprocesscourses;
use App\admissionprocessinstallmentfees;
use App\coursebunchlist;
use App\coursespecializationlist;
use App\UnviersitiesCategory;
use App\universititiesfeeslist;
use App\ReAdmission;
use App\Source;
use App\followup;
use App\PaymentSource;
use App\coursecategory;
use App\lecturereport;
use App\lecturereportsdetails;
use App\assignbatch;
use App\assignbatchesdetails;
use App\batch_lr_attendance_reports;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use DB;

class StudentStudyMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $usersAdmissionsId = Auth::user()->ustusdentsadmssionsids;


            $studentscourses = assignbatchesdetails::where('stusdentsadmssionsids',$usersAdmissionsId)->get();

                foreach($studentscourses as $coursesid)
                {

                    $coursesdetails = course::where('id',$coursesid->course)->get();
                    

                     return view('students.courseview.manage',compact('coursesdetails'));

                }
            


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $usersAdmissionsId = Auth::user()->ustusdentsadmssionsids;

          $studentscourses = assignbatchesdetails::where('stusdentsadmssionsids',$usersAdmissionsId)->get();

                foreach($studentscourses as $coursesid)
                {

                    $coursesdetails = course::where('id',$coursesid->course)->first();

                    $lctsdetails = lecturereport::join('lecturereportsdetails','lecturereportsdetails.lectureid','=','lecturereports.id')->select('lecturereports.*','lecturereportsdetails.*','lecturereports.id as lecid')->where('lecturereports.courses',$coursesdetails->coursename)->groupBy('lecturereportsdetails.lectureid')->get();
                    

                     return view('students.courseview.lecturereports',compact('lctsdetails'));

                }


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $usersAdmissionsId = Auth::user()->ustusdentsadmssionsids;
  
         $bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.stusdentsadmssionsids',$usersAdmissionsId)->get();

        return view('students.batchdetails.manage',compact('bdetails'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
         $usersAdmissionsId = Auth::user()->ustusdentsadmssionsids;

         $bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.stusdentsadmssionsids',$usersAdmissionsId)->get();

        $useall = User::where('usercategory','=','Instructor')->get();
        
        return view('students.batchattendances.lecturereports',compact('bdetails','useall'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $usersAdmissionsId = Auth::user()->ustusdentsadmssionsids;
          $bcourse = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.assignbatchid',$id)->where('assignbatchesdetails.stusdentsadmssionsids',$usersAdmissionsId)->get();
        $batchdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.assignbatchid',$id)->where('assignbatchesdetails.stusdentsadmssionsids',$usersAdmissionsId)->get();
        $btchs = assignbatch::where('id',$id)->first();
        $blctrprts = batch_lr_attendance_reports::where('abs_batch_id',$id)->groupBy('abslecids')->get();
       

         return view('students.batchattendances.views',compact('batchdetails','bcourse','btchs','blctrprts','id'));
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
