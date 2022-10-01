<?php

namespace App\Http\Controllers;
use App\students;
use App\course;
use App\Branch;
use App\leads;
use App\studentscourse;
use App\admissionprocess;

use Auth;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $userId = Auth::user()->id;
        //$studentsdata = admissionprocess::where('userId',$userId)->get();
        $studentsdata = admissionprocess::select("admissionprocesses.*","payments.paymentreceived","payments.paymentdate","payments.paymentmode","payments.remainingamount","courses.coursename")->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->join('leads', 'leads.phone', '=', 'admissionprocesses.sphone')->join('admissionprocesscourses', 'admissionprocesscourses.invid', '=', 'admissionprocesses.id')->join('courses', 'admissionprocesscourses.courseid', '=', 'courses.id')->where('leads.user_id',$userId)->get();
        //dd($studentsdata);
        return view('marketing.admission.studentsdetails',compact('studentsdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id,leads $leads,Branch $branch,course $course)
    {
        $alb = $branch::get();
        $directstudentsdata = leads::find($id);
        $cours = $course::get();
        $leadsdata = leads::get();
        return view('marketing.admission.direct',compact('alb','cours','leadsdata','directstudentsdata'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,students $students,studentscourse $studentscourse)
    {
        //

        $userId = Auth::user()->id;

        $mjerno ="0";
        $sjerno ="0";
        $wageron ="0";
        //dd($request->all());

        $studentname = $request->studentname;
        $birthdate = $request->dob;
        $email = $request->stuemail;
        $brnach = $request->bran;
        $erno = $request->studenterno;
        $mobile = $request->phoneno;
        $studentstreet =  $request->streets;
        $studentcity = $request->city;
        $studentstate = $request->state;
        $studentzipcode =  $request->zipcode;
        $ptime =  $request->preferrabletime;
        $rnote =  $request->remarknote;
        $cmode =  $request->coursemode;

        $enrollno = explode("/",$erno);
        if($enrollno[0] == 'BITSJ')
        {
            $sjerno = $enrollno[3];

            //dd($enrollno);
        }
        else if($enrollno[0] == 'BITMJ')
        {
            $mjerno = $enrollno[3];
         
        }
        elseif($enrollno[0] == 'BITWG')
        {
            $wageron = $enrollno[3];
        }

         $studentsmodel = new students();
        $students = $studentsmodel->create([
            'studentname'=> $studentname,
            'dateofbirth'=> $birthdate,
            'studemail'=> $email,
            'branch'=> $brnach,
            'brancherno'=> $erno,
            'sjerno'=> $sjerno,
            'mjerno'=> $mjerno,
            'wgerno'=> $wageron,
            'phoneno'=> $mobile,
            'street'=> $studentstreet,
            'city'=> $studentcity,
            'state'=> $studentstate,
            'zipcode'=> $studentzipcode,
            'preferrabletime'=> $ptime,
            'remarknote'=> $rnote,
            'userId'=> $userId,
            
        ]);




        $studentsid = $students->id;    
        
            

         return redirect('/create-Admission-invoice/'.$studentsid);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
