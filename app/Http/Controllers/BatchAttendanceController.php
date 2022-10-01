<?php

namespace App\Http\Controllers;
use App\times;
use App\days;
use App\assignbatch;
use App\assignbatchesdetails;
use App\coursebunchlist;
use App\coursespecializationlist;
use App\admissionprocess;
use App\admissionprocesscourses;
use App\payment;
use App\Branch;
use App\course;
use App\User;
use App\Batchs_logs;
use App\BatchCourseDurations;
use App\batch_lr_attendance_reports;
use App\AttendanceLectureReportsDetails;
use Illuminate\Http\Request;
use Auth;

class BatchAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->get();

        $useall = User::where('usercategory','=','Instructor')->get();
        $timedata = times::all();

        return view('superadmin.batchattendances.manage',compact('bdetails','useall','timedata'));
    }

    public function blreports()
    {
         $bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->get();

        $useall = User::where('usercategory','=','Instructor')->get();
        $timedata = times::all();

        return view('superadmin.batchattendances.lecturereports',compact('bdetails','useall','timedata'));
    }

    public function vlrs($id)
    {

         //$userId = Auth::user()->id;

         $userId = Auth::user()->id;
          $bcourse = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.assignbatchid',$id)->get();
        $batchdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.assignbatchid',$id)->get();
        $btchs = assignbatch::where('id',$id)->first();
        $blctrprts = batch_lr_attendance_reports::where('abs_batch_id',$id)->groupBy('abslecids')->get();
       

         return view('superadmin.batchattendances.view',compact('batchdetails','bcourse','btchs','blctrprts','id'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $bcourse = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.assignbatchid',$id)->get();
        $batchdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.assignbatchid',$id)->get();
        $btchs = assignbatch::find($id);

        return view('superadmin.batchattendances.create',compact('batchdetails','bcourse','btchs'));
     
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = Auth::user()->id;

         $bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatches.faculty',$userId)->get();

        $useall = User::where('usercategory','=','Instructor')->get();
        $timedata = times::all();

        return view('faculty.batchattendances.manage',compact('bdetails','useall','timedata'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $userId = Auth::user()->id;
          $bcourse = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.assignbatchid',$id)->where('assignbatches.faculty',$userId)->get();
        $batchdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.assignbatchid',$id)->where('assignbatches.faculty',$userId)->get();
        $btchs = assignbatch::where('id',$id)->where('faculty',$userId)->first();

        return view('faculty.batchattendances.create',compact('batchdetails','bcourse','btchs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id,Request $request)
    {
            $lecids = $request->leccheckboxs;
            $mdates = $request->datesof;
            $epoints = $request->extrapoints;
            $sids = $request->studentsusersId;
            $attendance = $request->attndna;


          //  dd($mdates);

               
                       // dd(is_array($mdates));

                                    if (is_array($request->leccheckboxs)) {

                                        $mlecs = implode(',', $request->leccheckboxs);
                                        // code...
                                    }

                                    else
                                    {
                                        $mlecs = $request->leccheckboxs;
                                    }


                                    if (is_array($request->datesof)) {

                                        $mmdates = implode(',', $request->datesof);
                                        // code...
                                    }

                                    else
                                    {
                                        $mmdates = $request->datesof;
                                    }

                                for($i = 0; $i < count($sids); $i++)
                                {
                                   // $atns = $attendance[$i];

                                    $batch_lr_attendance_reportsmodel = new batch_lr_attendance_reports();

                                    $batch_lr_attendance_reports = $batch_lr_attendance_reportsmodel->create([ 

                                            'abs_batch_id' => $id,
                                            'absstudentsid' => $sids[$i],
                                            'absdates' => $mmdates,
                                            'abslecids' => $mlecs,
                                            'absextrapoints' => $epoints,
                                            'absstudentsattendance' => $attendance[$i],

                                    ]);

                                 
                                }


               

         
           // dd($mlecs);
           
        

            return redirect('/view-attendance-reports/'.$id)->with('success','Attendance Reports Generated Successfully!!');

         
            

       // dd($attendance);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
         $userId = Auth::user()->id;
          $bcourse = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.assignbatchid',$id)->where('assignbatches.faculty',$userId)->get();
        $batchdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatchesdetails.assignbatchid',$id)->where('assignbatches.faculty',$userId)->get();
        $btchs = assignbatch::where('id',$id)->where('faculty',$userId)->first();
        $blctrprts = batch_lr_attendance_reports::where('abs_batch_id',$id)->groupBy('abslecids')->get();
       

         return view('faculty.batchattendances.view',compact('batchdetails','bcourse','btchs','blctrprts','id'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {

          $userId = Auth::user()->id;

         $bdetails = assignbatch::join('assignbatchesdetails','assignbatchesdetails.assignbatchid','=','assignbatches.id')->groupBy('assignbatchesdetails.assignbatchid')->orderBy('assignbatches.id','DESC')->select('assignbatchesdetails.*','assignbatches.*','assignbatches.id as batchids')->where('assignbatches.faculty',$userId)->get();

        $useall = User::where('usercategory','=','Instructor')->get();
        $timedata = times::all();

        return view('faculty.batchattendances.lecturereports',compact('bdetails','useall','timedata'));
        
    }
}
