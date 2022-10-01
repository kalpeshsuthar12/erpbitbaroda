<?php

namespace App\Http\Controllers;
use App\HrManag;
use App\User;
use Illuminate\Http\Request;

class AdminHrController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Appointment Letter')->get();

        return view('admin.hrmanagements.appointmentletter',compact('uall'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Appreciation Letter For Employees')->get();

        return view('admin.hrmanagements.appreciationsletter',compact('uall'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Appreciation Letter For Students')->get();

        return view('admin.hrmanagements.appreciationsletter',compact('uall'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','BUS Pass Permission')->get();

        return view('admin.hrmanagements.buspassletter',compact('uall'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
         $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Completion Of Project')->get();

        return view('admin.hrmanagements.completionprojectletters',compact('uall'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Course Completion')->get();

        return view('admin.hrmanagements.coursecompletion',compact('uall'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','CVRU Bonafied Letter')->get();

        return view('admin.hrmanagements.cvrubonafiedletters',compact('uall'));
    }

    public function experienceletter()
    {
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Experience Letter')->get();

        return view('admin.hrmanagements.experienceletters',compact('uall'));
    }

    public function frequentlylatecoming()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Frequently Late Coming Notice')->get();
          //  dd($uall);
        return view('admin.hrmanagements.frequelatecoming',compact('uall'));
    }

    public function internshipcomplletter()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Internship Completion Letter')->get();
          //  dd($uall);
        return view('admin.hrmanagements.internshipcompletion',compact('uall'));
    }

     public function internshipconfirmations()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Internship Confirmation Letter')->get();
          //  dd($uall);
        return view('admin.hrmanagements.internshipcomfirmation',compact('uall'));
    }

    public function relievingletter()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Relieving Letter')->get();
          //  dd($uall);
        return view('admin.hrmanagements.relievingletter',compact('uall'));
    }

    public function saptraining()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','SAP Training Completion Letter')->get();
          //  dd($uall);
        return view('admin.hrmanagements.saptraining',compact('uall'));
    }

    public function warningletter()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Warning Letter')->get();
          //  dd($uall);
        return view('admin.hrmanagements.warningletter',compact('uall'));
    }


    public function othersletter()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Other Letter')->get();
          //  dd($uall);
        return view('admin.hrmanagements.othersletters',compact('uall'));
    }
}
