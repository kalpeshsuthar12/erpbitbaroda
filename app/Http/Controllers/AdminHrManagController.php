<?php

namespace App\Http\Controllers;
use App\HrManag;
use App\User;
use App\usercategory;
use App\Branch;
use Illuminate\Http\Request;

class AdminHrManagController extends Controller
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

        return view('admin.hrmanagements.create',compact('branchsdetails','uall','ucategory'));
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
            return redirect('/admin-appointment-letter')->with('success','Appointment Letter Created Successfully!!');
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
            return redirect('/admin-appreciations-letter')->with('success','Appreciation Letter For Employees Created Successfully!!');
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
            return redirect('/admin-appreciations-letter-for-students')->with('success','Appreciation Letter For Students Created Successfully!!');
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
                        return redirect('/admin-bus-pass-letters')->with('success','Bus Pass Letter Created Successfully!!');
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
                        return redirect('/admin-completion-project-letters')->with('success','Completion Project Letter Created Successfully!!');
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
                        return redirect('/admin-course-completion')->with('success','Completion Course Letter Created Successfully!!');
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
                        return redirect('/admin-cvru-bonafied-letter')->with('success','CVRU Bonafied Letter Created Successfully!!');
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
                        return redirect('/admin-experience-letter')->with('success','Experience Letter Created Successfully!!');
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
                        return redirect('/admin-frequently-late-coming-notice')->with('success','Frequently Late Coming Notice Created Successfully!!');
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
                        return redirect('/admin-internship-completion-letter')->with('success','Internship Completion Letter Created Successfully!!');
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
                        return redirect('/admin-internship-confirmation-letter')->with('success','Internship Confirmations Letter Created Successfully!!');
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
                        return redirect('/admin-relieving-letter')->with('success','Relieving Letter Created Successfully!!');
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
                        return redirect('/admin-sap-taining-comp-letter')->with('success','SAP Training Completion Letter Created Successfully!!');
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
                        return redirect('/admin-sap-taining-comp-letter')->with('success','SAP Training Completion Letter Created Successfully!!');
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
                        return redirect('/admin-warning-letter')->with('success','Warning Letter Created Successfully!!');
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
                        return redirect('/admin-others-letter')->with('success','Others Letter Created Successfully!!');
                    }

        //return redirect('/view-letters'.$HrManag->id);
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
