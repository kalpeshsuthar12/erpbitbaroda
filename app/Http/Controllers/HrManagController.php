<?php

namespace App\Http\Controllers;
use App\HrManag;
use App\User;
use App\usercategory;
use App\Branch;
use Illuminate\Http\Request;
use PDF;

class HrManagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $uall = User::all();
         $ucategory = usercategory::get();
        $branchsdetails = Branch::all();

        return view('superadmin.hrmanagements.create',compact('branchsdetails','uall','ucategory'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       

        if($request->ltypes == 'Appointment Letter')
        {


             $HrManagmodel = new HrManag();
                $HrManag = $HrManagmodel->create([
                    'lettertype'=> $request->ltypes,
                    'usecompanys'=> $request->compns,
                    'lucategory'=> $request->ccategorys,
                    'lusers_id'=> $request->candidatesnames,
                    'lissuingdates'=> $request->appointmentdates,
                    'ltexts'=> $request->appointmentslettermatters,
                ]);
            return redirect('/appointment-letter')->with('success','Appointment Letter Created Successfully!!');
        }


        else if($request->ltypes == 'Appreciation Letter For Employees')
        {


             $HrManagmodel = new HrManag();
                $HrManag = $HrManagmodel->create([
                    'lettertype'=> $request->ltypes,
                    'usecompanys'=> $request->compns,
                    'lucategory'=> $request->ccategorys,
                    'lusers_id'=> $request->candidatesnames,
                    'lissuingdates'=> $request->appreciationsdates,
                    'ltexts'=> $request->aprrematters,
                ]);
            return redirect('/appreciations-letter')->with('success','Appreciation Letter For Employees Created Successfully!!');
        }

        else if($request->ltypes == 'Appreciation Letter For Students')
        {


             $HrManagmodel = new HrManag();
                $HrManag = $HrManagmodel->create([
                    'lettertype'=> $request->ltypes,
                    'usecompanys'=> $request->compns,
                    'lucategory'=> $request->ccategorys,
                    'lusers_id'=> $request->candidatesnames,
                    'lissuingdates'=> $request->apreciationsdatesforst,
                    'ltexts'=> $request->apprematteforstudents,
                ]);
            return redirect('/appreciations-letter-for-students')->with('success','Appreciation Letter For Students Created Successfully!!');
        }
            else if($request->ltypes == 'BUS Pass Permission')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'lissuingdates'=> $request->bstrtdates,
                                'lissuingenddates'=> $request->benddates,
                                'ltexts'=> $request->bsmatters,
                            ]);
                        return redirect('/bus-pass-letters')->with('success','Bus Pass Letter Created Successfully!!');
                    }

                    else if($request->ltypes == 'Completion Of Project')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'lissuingdates'=> $request->compstrtdate,
                                'lissuingenddates'=> $request->compenddate,
                                'ltexts'=> $request->compmatters,
                            ]);
                        return redirect('/completion-project-letters')->with('success','Completion Project Letter Created Successfully!!');
                    }


                     else if($request->ltypes == 'Course Completion')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'ltexts'=> $request->courscommatters,
                            ]);
                        return redirect('/course-completion')->with('success','Completion Course Letter Created Successfully!!');
                    }


                    else if($request->ltypes == 'CVRU Bonafied Letter')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'lissuingdates'=> $request->cvbldates,
                                'ltexts'=> $request->cvblmatters,
                            ]);
                        return redirect('/cvru-bonafied-letter')->with('success','CVRU Bonafied Letter Created Successfully!!');
                    }

                     else if($request->ltypes == 'Experience Letter')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'lissuingdates'=> $request->expstartdates,
                                'lissuingenddates'=> $request->expenddates,
                                'ltexts'=> $request->explmatters,
                            ]);
                        return redirect('/experience-letter')->with('success','Experience Letter Created Successfully!!');
                    }

                     else if($request->ltypes == 'Frequently Late Coming Notice')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'lissuingdates'=> $request->fqlisdate,
                                'ltexts'=> $request->fqlmatters,
                            ]);
                        return redirect('/frequently-late-coming-notice')->with('success','Frequently Late Coming Notice Created Successfully!!');
                    }
                     else if($request->ltypes == 'Internship Completion Letter')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'lissuingdates'=> $request->insternshistartdat,
                                'lissuingenddates'=> $request->insternshienddate,
                                'ltexts'=> $request->intenshipcomplematter,
                            ]);
                        return redirect('/internship-completion-letter')->with('success','Internship Completion Letter Created Successfully!!');
                    }

                    else if($request->ltypes == 'Internship Confirmation Letter')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'lissuingdates'=> $request->internshipconfstartdtae,
                                'lissuingenddates'=> $request->internshipconfenddtae,
                                'ltexts'=> $request->internshipcomfirmatters,
                            ]);
                        return redirect('/internship-confirmation-letter')->with('success','Internship Confirmations Letter Created Successfully!!');
                    }

                     else if($request->ltypes == 'Relieving Letter')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'lissuingdates'=> $request->realstartdates,
                                'lissuingenddates'=> $request->realenddates,
                                'ltexts'=> $request->releavingmatters,
                            ]);
                        return redirect('/relieving-letter')->with('success','Relieving Letter Created Successfully!!');
                    }

                     else if($request->ltypes == 'SAP Training Completion Letter')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'lissuingdates'=> $request->sapsdates,
                                'ltexts'=> $request->saptraingingmatters,
                            ]);
                        return redirect('/sap-taining-comp-letter')->with('success','SAP Training Completion Letter Created Successfully!!');
                    }


                     else if($request->ltypes == 'SAP Training Completion Letter')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'lissuingdates'=> $request->sapsdates,
                                'ltexts'=> $request->saptraingingmatters,
                            ]);
                        return redirect('/sap-taining-comp-letter')->with('success','SAP Training Completion Letter Created Successfully!!');
                    }

                     else if($request->ltypes == 'Warning Letter')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'lissuingdates'=> $request->warnindates,
                                'ltexts'=> $request->warningmatters,
                            ]);
                        return redirect('/warning-letter')->with('success','Warning Letter Created Successfully!!');
                    }


                    else if($request->ltypes == 'Other Letter')
                    {


                         $HrManagmodel = new HrManag();
                            $HrManag = $HrManagmodel->create([
                                'lettertype'=> $request->ltypes,
                                'usecompanys'=> $request->compns,
                                'lucategory'=> $request->ccategorys,
                                'lusers_id'=> $request->candidatesnames,
                                'ltexts'=> $request->othersmatters,
                            ]);
                        return redirect('/others-letter')->with('success','Others Letter Created Successfully!!');
                    }

        //return redirect('/view-letters'.$HrManag->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\HrManag  $hrManag
     * @return \Illuminate\Http\Response
     */
    public function show($id,HrManag $hrManag)
    {
        $hrdetails = HrManag::find($id);

        $userdetails = User::find($hrdetails->lusers_id);

      //  return view('superadmin.hrmanagements.viewletter',compact('hrdetails','userdetails'));
         $pdf =  pdf::loadview('superadmin.hrmanagements.viewletter',compact('hrdetails','userdetails'));

         return $pdf->stream('result.pdf');      
    }

    public function jobbbitdocuments($id)
    {
         /*$pdf =  pdf::loadview('superadmin.hrmanagements.jobitletterhear');

         return $pdf->stream('result.pdf'); */

         //return view('superadmin.hrmanagements.jobitletterhear');

         $viewdatas = HrManag::find($id);

        return view('superadmin.hrmanagements.jobitletterhear',compact('viewdatas'));

    }

     public function bitinfotechdocuments($id)
    {

         $viewdatas = HrManag::find($id);

        return view('superadmin.hrmanagements.bitinfotech',compact('viewdatas'));

    }

    public function bitbarodadocuments($id)
    {
        $viewdatas = HrManag::find($id);

        return view('superadmin.hrmanagements.bitbarodaletterhear',compact('viewdatas'));

         /*$pdf =  pdf::loadview('superadmin.hrmanagements.bitbarodaletterhear',compact('viewdatas'));

         return $pdf->stream('result.pdf');*/ 

         //return view('superadmin.hrmanagements.jobitletterhear');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrManag  $hrManag
     * @return \Illuminate\Http\Response
     */
    public function getUsers($candicat)
    {
        $userdetails = User::where('usercategory',$candicat)->get();

         return response()->json($userdetails);
    }


     public function edit(HrManag $hrManag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrManag  $hrManag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HrManag $hrManag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrManag  $hrManag
     * @return \Illuminate\Http\Response
     */
    public function destroy(HrManag $hrManag)
    {
        //
    }
}
