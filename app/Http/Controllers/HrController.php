<?php

namespace App\Http\Controllers;
use App\HrManag;
use App\usercategory;
use App\User;
use App\Branch;
use Illuminate\Http\Request;

class HrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Appointment Letter')->get();

        return view('superadmin.hrmanagements.appointmentletter',compact('uall'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Appreciation Letter For Employees')->get();

        return view('superadmin.hrmanagements.appreciationsletter',compact('uall'));
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

        return view('superadmin.hrmanagements.appreciationsletter',compact('uall'));
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

        return view('superadmin.hrmanagements.buspassletter',compact('uall'));
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

        return view('superadmin.hrmanagements.completionprojectletters',compact('uall'));
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

        return view('superadmin.hrmanagements.coursecompletion',compact('uall'));
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

        return view('superadmin.hrmanagements.cvrubonafiedletters',compact('uall'));
    }

    public function experienceletter()
    {
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Experience Letter')->get();

        return view('superadmin.hrmanagements.experienceletters',compact('uall'));
    }

    public function frequentlylatecoming()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Frequently Late Coming Notice')->get();
          //  dd($uall);
        return view('superadmin.hrmanagements.frequelatecoming',compact('uall'));
    }

    public function internshipcomplletter()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Internship Completion Letter')->get();
          //  dd($uall);
        return view('superadmin.hrmanagements.internshipcompletion',compact('uall'));
    }

     public function internshipconfirmations()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Internship Confirmation Letter')->get();
          //  dd($uall);
        return view('superadmin.hrmanagements.internshipcomfirmation',compact('uall'));
    }

    public function relievingletter()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Relieving Letter')->get();
          //  dd($uall);
        return view('superadmin.hrmanagements.relievingletter',compact('uall'));
    }

    public function saptraining()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','SAP Training Completion Letter')->get();
          //  dd($uall);
        return view('superadmin.hrmanagements.saptraining',compact('uall'));
    }

    public function warningletter()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Warning Letter')->get();
          //  dd($uall);
        return view('superadmin.hrmanagements.warningletter',compact('uall'));
    }


    public function othersletter()
    {

     //   dd('test');
        $uall = HrManag::join('users','users.id','=','hr_manags.lusers_id')->select('users.name','hr_manags.*')->where('hr_manags.lettertype','Other Letter')->get();
          //  dd($uall);
        return view('superadmin.hrmanagements.othersletters',compact('uall'));
    }
    
    public function edithrdocuments($id)
    {
        $edit = HrManag::find($id);

         $ucategory = usercategory::get();
          $branchsdetails = Branch::all();
          $usersdatas = User::where('usercategory',$edit->lucategory)->get();

        return view('superadmin.hrmanagements.edit',compact('edit','ucategory','branchsdetails','usersdatas'));
    }


    public function updatehrdocuments($id,Request $request)
    {
       
        if($request->ltypes == 'Appointment Letter')
        {
                $updates = HrManag::find($id);
                $updates->lettertype = $request->ltypes;
                $updates->usecompanys =  $request->compns;
                $updates->lucategory = $request->ccategorys;
                $updates->lusers_id = $request->candidatesnames;
                $updates->lissuingdates = $request->appointmentdates;
                $updates->ltexts = $request->appointmentslettermatters;
                $updates->save();
             
            return redirect('/appointment-letter')->with('success','Appointment Letter Updated Successfully!!');
        }


        else if($request->ltypes == 'Appreciation Letter For Employees')
        {

                $updates = HrManag::find($id);
                $updates->lettertype = $request->ltypes;
                $updates->usecompanys = $request->compns;
                $updates->lucategory =  $request->ccategorys;
                $updates->lusers_id =  $request->candidatesnames;
                $updates->lissuingdates = $request->appreciationsdates;
                $updates->ltexts = $request->aprrematters;
                $updates->save();

             
            return redirect('/appreciations-letter')->with('success','Appreciation Letter For Employees Updated Successfully!!');
        }

        else if($request->ltypes == 'Appreciation Letter For Students')
        {

                $updates = HrManag::find($id);
                $updates->lettertype = $request->ltypes;
                $updates->usecompanys = $request->compns;
                $updates->lucategory =  $request->ccategorys;
                $updates->lusers_id = $request->candidatesnames;
                $updates->lissuingdates = $request->apreciationsdatesforst;
                $updates->ltexts = $request->apprematteforstudents;
                $updates->save();

             
            return redirect('/appreciations-letter-for-students')->with('success','Appreciation Letter For Students Updated Successfully!!');
        }
        
        else if($request->ltypes == 'BUS Pass Permission')
                    {


                                        $updates = HrManag::find($id);
                                        $updates->lettertype = $request->ltypes;
                                        $updates->usecompanys = $request->compns;
                                        $updates->lucategory = $request->ccategorys;
                                        $updates->lusers_id =  $request->candidatesnames;
                                        $updates->lissuingdates = $request->bstrtdates;
                                        $updates->lissuingenddates = $request->benddates;
                                        $updates->ltexts = $request->bsmatters;
                                        $updates->save();

                        
                        return redirect('/bus-pass-letters')->with('success','Bus Pass Letter Updated Successfully!!');
                    }

                    else if($request->ltypes == 'Completion Of Project')
                    {


                                        $updates = HrManag::find($id);
                                        $updates->lettertype = $request->ltypes;
                                        $updates->usecompanys = $request->compns;
                                        $updates->lucategory = $request->ccategorys;
                                        $updates->lusers_id =  $request->candidatesnames;
                                        $updates->lissuingdates = $request->compstrtdate;
                                        $updates->lissuingenddates = $request->compenddate;
                                        $updates->ltexts = $request->compmatters;
                                        $updates->save();

                        
                        return redirect('/completion-project-letters')->with('success','Completion Project Letter Updated Successfully!!');
                    }


                     else if($request->ltypes == 'Course Completion')
                    {
                                        $updates = HrManag::find($id);
                                        $updates->lettertype = $request->ltypes;
                                        $updates->usecompanys = $request->compns;
                                        $updates->lucategory = $request->ccategorys;
                                        $updates->lusers_id =  $request->candidatesnames;
                                        $updates->ltexts = $request->courscommatters;
                                        $updates->save();

                         
                        return redirect('/course-completion')->with('success','Completion Course Letter Updated Successfully!!');
                    }


                    else if($request->ltypes == 'CVRU Bonafied Letter')
                    {


                                        $updates = HrManag::find($id);
                                        $updates->lettertype = $request->ltypes;
                                        $updates->usecompanys = $request->compns;
                                        $updates->lucategory = $request->ccategorys;
                                        $updates->lusers_id =  $request->candidatesnames;
                                        $updates->lissuingdates = $request->cvbldates;
                                        $updates->ltexts = $request->cvblmatters;
                                        $updates->save();
                        
                        return redirect('/cvru-bonafied-letter')->with('success','CVRU Bonafied Letter Updated Successfully!!');
                    }

                     else if($request->ltypes == 'Experience Letter')
                    {

                                        $updates = HrManag::find($id);
                                        $updates->lettertype = $request->ltypes;
                                        $updates->usecompanys = $request->compns;
                                        $updates->lucategory = $request->ccategorys;
                                        $updates->lusers_id =  $request->candidatesnames;
                                        $updates->lissuingdates = $request->expstartdates;
                                        $updates->lissuingenddates = $request->expenddates;
                                        $updates->ltexts = $request->explmatters;
                                        $updates->save();

                        
                        return redirect('/experience-letter')->with('success','Experience Letter Updated Successfully!!');
                    }

                     else if($request->ltypes == 'Frequently Late Coming Notice')
                    {

                                        $updates = HrManag::find($id);
                                        $updates->lettertype = $request->ltypes;
                                        $updates->usecompanys = $request->compns;
                                        $updates->lucategory = $request->ccategorys;
                                        $updates->lusers_id =  $request->candidatesnames;
                                        $updates->lissuingdates = $request->fqlisdate;
                                        $updates->ltexts = $request->fqlmatters;
                                        $updates->save();

                        
                        return redirect('/frequently-late-coming-notice')->with('success','Frequently Late Coming Notice Updated Successfully!!');
                    }
                     else if($request->ltypes == 'Internship Completion Letter')
                    {


                                        $updates = HrManag::find($id);
                                        $updates->lettertype = $request->ltypes;
                                        $updates->usecompanys = $request->compns;
                                        $updates->lucategory = $request->ccategorys;
                                        $updates->lusers_id =  $request->candidatesnames;
                                        $updates->lissuingdates = $request->insternshistartdat;
                                        $updates->lissuingenddates = $request->insternshienddate;
                                        $updates->ltexts = $request->intenshipcomplematter;
                                        $updates->save();

                     
                        return redirect('/internship-completion-letter')->with('success','Internship Completion Letter Created Successfully!!');
                    }

                    else if($request->ltypes == 'Internship Confirmation Letter')
                    {


                                        $updates = HrManag::find($id);
                                        $updates->lettertype = $request->ltypes;
                                        $updates->usecompanys = $request->compns;
                                        $updates->lucategory = $request->ccategorys;
                                        $updates->lusers_id =  $request->candidatesnames;
                                        $updates->lissuingdates = $request->internshipconfstartdtae;
                                        $updates->lissuingenddates = $request->internshipconfenddtae;
                                        $updates->ltexts = $request->internshipcomfirmatters;
                                        $updates->save();


                         
                        return redirect('/internship-confirmation-letter')->with('success','Internship Confirmations Letter Updated Successfully!!');
                    }

                     else if($request->ltypes == 'Relieving Letter')
                    {


                                        $updates = HrManag::find($id);
                                        $updates->lettertype = $request->ltypes;
                                        $updates->usecompanys = $request->compns;
                                        $updates->lucategory = $request->ccategorys;
                                        $updates->lusers_id =  $request->candidatesnames;
                                        $updates->lissuingdates = $request->realstartdates;
                                        $updates->lissuingenddates = $request->realenddates;
                                        $updates->ltexts = $request->releavingmatters;
                                        $updates->save();


                         
                        return redirect('/relieving-letter')->with('success','Relieving Letter Updated Successfully!!');
                    }

                     else if($request->ltypes == 'SAP Training Completion Letter')
                    {

                                        $updates = HrManag::find($id);
                                        $updates->lettertype = $request->ltypes;
                                        $updates->usecompanys = $request->compns;
                                        $updates->lucategory = $request->ccategorys;
                                        $updates->lusers_id =  $request->candidatesnames;
                                        $updates->lissuingdates = $request->sapsdates;
                                        $updates->ltexts = $request->saptraingingmatters;
                                        $updates->save();

                        
                        return redirect('/sap-taining-comp-letter')->with('success','SAP Training Completion Letter Updated Successfully!!');
                    }


                    

                     else if($request->ltypes == 'Warning Letter')
                    {


                                        $updates = HrManag::find($id);
                                        $updates->lettertype = $request->ltypes;
                                        $updates->usecompanys = $request->compns;
                                        $updates->lucategory = $request->ccategorys;
                                        $updates->lusers_id =  $request->candidatesnames;
                                        $updates->lissuingdates = $request->warnindates;
                                        $updates->ltexts = $request->warningmatters;
                                        $updates->save();

                         
                        return redirect('/warning-letter')->with('success','Warning Letter Updated Successfully!!');
                    }


                    else if($request->ltypes == 'Other Letter')
                    {

                                        $updates = HrManag::find($id);
                                        $updates->lettertype = $request->ltypes;
                                        $updates->usecompanys = $request->compns;
                                        $updates->lucategory = $request->ccategorys;
                                        $updates->lusers_id =  $request->candidatesnames;
                                        $updates->ltexts = $request->othersmatters;
                                        $updates->save();
                         
                        return redirect('/others-letter')->with('success','Others Letter Updated Successfully!!');
                    }
    }

    public function deletehrdocuments($id)
    {
        $deles = HrManag::find($id);
        $deles->delete();

        return redirect()->back()->with('success','Letter Has Been Deleted Successfully!!');
    }
}
