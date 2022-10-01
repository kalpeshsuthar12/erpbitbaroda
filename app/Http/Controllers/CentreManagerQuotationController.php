<?php

namespace App\Http\Controllers;
use App\leads;
use App\leadsfollowups;
use App\CompanyQuotation;
use App\quotation_courses_details;
use App\course;
use App\studentsQuotation;
use App\studentsQuotationCourses;
use App\QuotationInvoices;
use App\QuotationInvoicesCourses;
use App\Branch;
use App\PaymentSource;
use App\UnviersitiesCategory;
use App\admissionprocess;
use App\admissionprocesscourses;
use App\Companys_Admissions;
use App\payment;
use App\admissionprocessinstallmentfees;
use App\Tax;
use Mail;
use PDF;
use Auth;
use DB;
use Illuminate\Http\Request;

class CentreManagerQuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   

        $userBranchss = Auth::user()->branchs;

        $quodetails = studentsQuotation::join('students_quotation_courses','students_quotation_courses.stucompyid','=','students_quotations.id')->select('students_quotations.*','students_quotation_courses.*','students_quotations.id as admid')->where('students_quotations.subranchse',$userBranchss)->groupBy('students_quotation_courses.stucompyid')->orderBy('students_quotations.quotationsdates','desc')->get();
        return view('centremanager.quotation.manage',compact('quodetails'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $usrBranch = Auth::user()->branchs;
         $year = date("Y");
         $month = date("m");
    
        $getid = $request->getquotation;
        
       // dd($getid);
        $quotationdetails = leads::find($getid);
        $cours = course::get();

        $latests = CompanyQuotation::latest()->get()->pluck('quotenos');
            //dd($latests);
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BIT-Quo/'.$year.'/'.$month.'/'.$code_nos;

            $brnachdata = Branch::where('branchname',$usrBranch)->get();

        return view('centremanager.quotation.create',compact('quotationdetails','cours','value','brnachdata'));

    }
    
    public function readrepayment($id)
    {
        $paymentdetails = ReAdmission::find($id);
        $paymentsse = payment::where("reinviceid", $id)
            ->orderBy("id", "DESC")
            ->take(1)
            ->get();
        $branc = Branch::all();
        $psource = PaymentSource::all();
        return view("centremanager.readmissions.readrepayment",compact("paymentdetails","branc","paymentsse","psource"));
    }

    public function restoreadrepayment(Request $request, $id)
    {
       
        $userId = Auth::user()->id;
        $studentsdata = $request->students;
        $rcepno = $request->receiptno;
        $ernos = $request->erno;
        $rptype = $request->ptypes;

        if ($rptype == "LumpSum") {
            $tmamount = $request->totalamount;
            $preceived = $request->paymentrecieved;
        } elseif ($rptype == "EMI") {
            $tmamount = $request->instam;
            $preceived = $request->instam;
        }

        $receptsno = explode("/", $rcepno);
        $newerno = explode("/", $ernos);
        // dd($receptsno);

        $sjrecno = "0";
        $mjrecno = "0";
        $wagrecno = "0";
        $bitolrecno = "0";
        $cvrublrecno = "0";
        $cvrukhrecno = "0";
        $rnturecno = "0";
        $manipalrecno = "0";

        if ($receptsno[0] == "BITSJ") {
            $sjrecno = $receptsno[1];

            //dd($sjrecno);
        } elseif ($receptsno[0] == "BITMJ") {
            $mjrecno = $receptsno[1];
        } elseif ($receptsno[0] == "BITWG") {
            $wagrecno = $receptsno[1];
        } elseif ($receptsno[0] == "BITOL") {
            $bitolrecno = $receptsno[1];
        } elseif ($receptsno[0] == "CVRU(BL)") {
            $cvrublrecno = $receptsno[1];
        } elseif ($receptsno[0] == "CVRU (KH)") {
            $cvrukhrecno = $receptsno[1];
        } elseif ($receptsno[0] == "RNTU") {
            $rnturecno = $receptsno[1];
        } elseif ($receptsno[0] == "MANIPAL") {
            $manipalrecno = $receptsno[1];
        }

        $paymentmodel = new payment();
        $payment = $paymentmodel->create([
            "reinviceid" => $id,
            "totalamount" => $tmamount,
            "paymentreceived" => $preceived,
            "remainingamount" => $request->ramount,
            "paymentdate" => $request->paymentdate,
            "paymentmode" => $request->paymentmode,
            "bankname" => $request->bankname,
            "paymentype" => $request->ptypes,
            "nexamountdate" => $request->remindersdates,
            "chequeno" => $request->chequeno,
            "chequedate" => $request->chequedate,
            "chequetype" => $request->chequetype,
            "remarknoe" => $request->remarknote,
            "userid" => $userId,
            "studentsid" => $request->students,
            "branchs" => $request->brnavhc,
            "receiptno" => $rcepno,
            "sjrecpno" => $sjrecno,
            "mjrecpno" => $mjrecno,
            "wgrecpno" => $wagrecno,
            "bitolrecpno" => $bitolrecno,
            "cvrublrecpno" => $cvrublrecno,
            "cvrukhrecpno" => $cvrukhrecno,
            "rnturecpno" => $rnturecno,
            "manipalrecpno" => $manipalrecno,
            "studentadmissiionstatus" => "New Student",
            "installmentid" => $request->installid,
        ]);

       

        $paymentid = $payment->id;

        return redirect("/c-m-re-payment-recipt/" . $paymentid)->with("success","Payment Successfully Done!!!");
    }

    public function editquotations($id,Request $request)
    {

        $quotationdetails = studentsQuotation::find($id);

        $sccourses = studentsQuotationCourses::where('stucompyid',$id)->get();
        //dd($quotationdetails);


        $cours = course::get();
        $brnachdata = Branch::get();



            $brnachdata = Branch::get();

            return view('centremanager.quotation.edit',compact('quotationdetails','cours','brnachdata','sccourses'));
    }


    public function updateQuotations($id,Request $request)
    {
            $quotationdetails = studentsQuotation::find($id);

              $deles = studentsQuotationCourses::where('stucompyid',$id)->get();
                                     $deles->each->delete();

              $dtype = $request->ccddiscotypes;
            

             if($dtype == "2")
            {
                 $discoun = $request->pdiscounts;
            }

            elseif($dtype == "1")
            {
                $discoun = $request->fdiscounts;
            }

            else
            {
                $discoun = "";
            }

            $sjqno ="0";
            $mjqno ="0";
            $wagqno ="0";
            $bitolqno ="0";
            $bitelqno = "0";
            $cvrublqno ="0";
            $cvrukhqno ="0";
            $cvrukhqno ="0";
            $rntuqno ="0";
            $manipalqno ="0";


            $qno = $request->qnos;
            $scqnos = explode("/",$qno);

             if($scqnos[0] == 'BITSJ')
            {
                $sjqno = $scqnos[3];

                //dd($enrollno);
            }
            else if($scqnos[0] == 'BITMJ')
            {
                $mjqno = $scqnos[3];
             
            }
            elseif($scqnos[0] == 'BITWG')
            {
                $wagqno = $scqnos[3];
            }

             elseif($scqnos[0] == 'BITOL')
            {
                $bitolqno = $scqnos[3];
            }


            elseif($scqnos[0] == 'BITEL')
            {
                $bitelqno = $scqnos[3];
            }

            elseif($scqnos[0] == 'CVRU(BL)')
            {
                $cvrublqno = $scqnos[3];
            }

            elseif($scqnos[0] == 'CVRU (KH)')
            {
                $cvrukhqno = $scqnos[3];
            }

            elseif($scqnos[0] == 'RNTU')
            {
                $rntuqno = $scqnos[3];
            }

            elseif($scqnos[0] == 'MANIPAL')
            {
                $manipalqno = $scqnos[3];
            }


            $qcategorys = $request->quotationcategory;

            if($qcategorys == "Students")
            {
              //  dd($qcategorys);
           $quotationdetails->studentscategorys = $qcategorys;
             $quotationdetails->quotationsdates = $request->qdate;
             $quotationdetails->quotationsduedates = $request->ddates;
             $quotationdetails->contactperson = $request->cperson;
            $quotationdetails->scemail= $request->cemaiels;
             $quotationdetails->scphones= $request->cmobileno;
            $quotationdetails->scwhatsappno= $request->cwhatsappno;
             $quotationdetails->scsubtotal= $request->subtotals;
             $quotationdetails->scdiscounttypes= $dtype;
             $quotationdetails->scdiscountstotals= $discoun;
             $quotationdetails->scgstamounts= $request->gstamounts;
             $quotationdetails->ssgstamounts= $request->sgstamounts;
             $quotationdetails->scfinaltotal= $request->finaltotals;
             $quotationdetails->scbranch= $request->frombranch;
             $quotationdetails->scquonos= $request->qnos;
             $quotationdetails->squsersid = $request->leadsuserid;
             $quotationdetails->qleaddates= $request->leaddates;
                $quotationdetails->leadids = $request->leadid;
             $quotationdetails->subranchse=  $request->frombranch;
             $quotationdetails->save();



            $studcompid = $id;
                    $scoursesdata = $request->sinvcourses;
                    $scoursesubcourse = $request->sinvsubcourses;
                    $scsmode = $request->scoursdataemode;
                    $scsfeess = $request->scoursesFees;
                    //$nstudents = $request->nofstudents;

                    //dd($request->sinvsubcourses);
                     for($i=0; $i < (count($scoursesdata)); $i++)
                    {
                                $studentsQuotationCourses = new studentsQuotationCourses([
                                
                                'stucompyid' => $studcompid,
                                'studecompcourse'   => $scoursesdata[$i],
                                'studecoursemode'   => $scsmode[$i],
                                'studecoursefeess'   => $scsfeess[$i],
                                
                                
                            ]);
                            $studentsQuotationCourses->save();
                    }
            }

            elseif($qcategorys == "Company")
            {

               $quotationdetails->studentscategorys = $qcategorys;
             $quotationdetails->quotationsdates = $request->qdate;
             $quotationdetails->quotationsduedates = $request->ddates;
             $quotationdetails->contactperson = $request->cperson;
            $quotationdetails->scemail= $request->cemaiels;
             $quotationdetails->scphones= $request->cmobileno;
            $quotationdetails->scwhatsappno= $request->cwhatsappno;
             $quotationdetails->scsubtotal= $request->subtotals;
             $quotationdetails->scdiscounttypes= $dtype;
             $quotationdetails->scdiscountstotals= $discoun;
             $quotationdetails->scgstamounts= $request->gstamounts;
             $quotationdetails->ssgstamounts= $request->sgstamounts;
             $quotationdetails->scfinaltotal= $request->finaltotals;
             $quotationdetails->scbranch= $request->frombranch;
             $quotationdetails->scquonos= $request->qnos;
             $quotationdetails->squsersid = $request->leadsuserid;
             $quotationdetails->qleaddates= $request->leaddates;
                $quotationdetails->leadids = $request->leadid;
             $quotationdetails->subranchse=  $request->frombranch;
             $quotationdetails->save();

            $studcompid = $id;
                    $coursesdata = $request->invcourse;
                    $coursesubcourse = $request->invsubcourses;
                    $csmode = $request->coursdataemode;
                    $csfeess = $request->coursesFees;
                    $nstudents = $request->nofstudents;
                     for($i=0; $i < (count($coursesdata)); $i++)
                    {
                                $studentsQuotationCourses = new studentsQuotationCourses([
                                
                                'stucompyid' => $studcompid,
                                'studecompcourse'   => $coursesdata[$i],
                                'studecoursemode'   => $csmode[$i],
                                'studecoursefeess'   => $csfeess[$i],
                                'compnystudents'   => $nstudents[$i],
                                
                                
                            ]);
                            $studentsQuotationCourses->save();
                    }

            }


                if($quotationdetails->studentscategorys == "Company")
                {

                            $data = array();
                $getUserQuotationDetails = studentsQuotation::find($id);
                
                $getCourseDetasils = studentsQuotationCourses::select('courses.coursename','students_quotation_courses.*')->Join('courses','courses.id','=','students_quotation_courses.studecompcourse')->where('students_quotation_courses.stucompyid',$id)->get();
                    
                         $Comapanyname = $getUserQuotationDetails->studentsocompanyname;
                         $contactperson = $getUserQuotationDetails->contactperson;
                         $inquirydate = date('d-m-Y',strtotime($getUserQuotationDetails->qleaddates));
                     

                     $pdf = PDF::loadView('superadmin.quotation.viewquotations',compact('getUserQuotationDetails','getCourseDetasils'));

                     $data = array('CompanyName' => $Comapanyname, 'ContactPerson' => $contactperson, 'QuoteDate' => $inquirydate); 
                     $data["email"] = $getUserQuotationDetails->scemail;
                     $data["title"] = "Quotations For " .$Comapanyname;

                     $data["course"] = $getCourseDetasils;
                     
                       
                     
                     Mail::send('superadmin.quotation.quotationmail', $data, function ($message) use ($data, $pdf) {
                        $data;
                        $message->to($data["email"], $data["email"])
                           ->from('bitadmisson@gmail.com','BIT Baroda Institute Of Technology')
                           ->cc('support@bitbaroda.com','Admission BIT')
                            ->subject($data["title"])
                            ->attachData($pdf->output(),"Quotations.pdf");
                            });


                            if (Mail::failures()) {
                                        dd('mailerror');
                                    } else {

                                        return redirect('/centre-manager-quotation')->with('success','Quotation Update SuccessFully and Email Sent Successfully!!!');

                                    }

                    }




                if($quotationdetails->studentscategorys == "Students")
                {

                            $data = array();
                $getUserQuotationDetails = studentsQuotation::find($id);
                
                $getCourseDetasils = studentsQuotationCourses::select('courses.coursename','students_quotation_courses.*')->Join('courses','courses.id','=','students_quotation_courses.studecompcourse')->where('students_quotation_courses.stucompyid',$id)->get();
                    
                         $Comapanyname = $getUserQuotationDetails->contactperson;
                         $contactperson = $getUserQuotationDetails->contactperson;
                         $inquirydate = date('d-m-Y',strtotime($getUserQuotationDetails->qleaddates));
                     

                     $pdf = PDF::loadView('superadmin.quotation.viewquotations',compact('getUserQuotationDetails','getCourseDetasils'));

                     $data = array('CompanyName' => $Comapanyname, 'ContactPerson' => $contactperson, 'QuoteDate' => $inquirydate); 
                     $data["email"] = $getUserQuotationDetails->scemail;
                     $data["title"] = "Quotations For " .$Comapanyname;

                     $data["course"] = $getCourseDetasils;
                     
                       
                     
                     Mail::send('superadmin.quotation.quotationmail', $data, function ($message) use ($data, $pdf) {
                        $data;
                        $message->to($data["email"], $data["email"])
                           ->from('bitadmisson@gmail.com','BIT Baroda Institute Of Technology')
                           ->cc('support@bitbaroda.com','Admission BIT')
                            ->subject($data["title"])
                            ->attachData($pdf->output(),"Quotations.pdf");
                            });


                            if (Mail::failures()) {
                                        dd('mailerror');
                                    } else {

                                        return redirect('/centre-manager-quotation')->with('success','Quotation Update SuccessFully and Email Sent Successfully!!!');

                                    }

                    }

        //return redirect('/centre-manager-quotation')->with('success','Quotations Updated Successfully!!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
        $userId = Auth::user()->id;
     */
    public function store(Request $request)
   {
        $userBranchs = Auth::user()->branchs;
           

             $dtype = $request->ccddiscotypes;
            

             if($dtype == "2")
            {
                 $discoun = $request->pdiscounts;
            }

            elseif($dtype == "1")
            {
                $discoun = $request->fdiscounts;
            }

            else
            {
                $discoun = "";
            }

            $sjqno ="0";
            $mjqno ="0";
            $wagqno ="0";
            $bitolqno ="0";
            $bitelqno = "0";
            $cvrublqno ="0";
            $cvrukhqno ="0";
            $cvrukhqno ="0";
            $rntuqno ="0";
            $manipalqno ="0";


            $qno = $request->qnos;
            $scqnos = explode("/",$qno);

             if($scqnos[0] == 'BITSJ')
            {
                $sjqno = $scqnos[3];

                //dd($enrollno);
            }
            else if($scqnos[0] == 'BITMJ')
            {
                $mjqno = $scqnos[3];
             
            }
            elseif($scqnos[0] == 'BITWG')
            {
                $wagqno = $scqnos[3];
            }

             elseif($scqnos[0] == 'BITOL')
            {
                $bitolqno = $scqnos[3];
            }


            elseif($scqnos[0] == 'BITEL')
            {
                $bitelqno = $scqnos[3];
            }

            elseif($scqnos[0] == 'CVRU(BL)')
            {
                $cvrublqno = $scqnos[3];
            }

            elseif($scqnos[0] == 'CVRU (KH)')
            {
                $cvrukhqno = $scqnos[3];
            }

            elseif($scqnos[0] == 'RNTU')
            {
                $rntuqno = $scqnos[3];
            }

            elseif($scqnos[0] == 'MANIPAL')
            {
                $manipalqno = $scqnos[3];
            }


            $qcategorys = $request->quotationcategory;

            if($qcategorys == "Students")
            {
                 $studentsQuotationmodel = new studentsQuotation();
            $studentsQuotation = $studentsQuotationmodel->create([
            'studentscategorys'=> $qcategorys,
            'quotationsdates'=> $request->qdate,
            'quotationsduedates'=> $request->ddates,
            'contactperson'=> $request->cperson,
            'scemail'=> $request->cemaiels,
            'scphones'=> $request->cmobileno,
            'scwhatsappno'=> $request->cwhatsappno,
            'scsubtotal'=> $request->subtotals,
            'scdiscounttypes'=> $dtype,
            'scdiscountstotals'=> $discoun,
            'scgstamounts'=> $request->gstamounts,
            'ssgstamounts'=> $request->sgstamounts,
            'scfinaltotal'=> $request->finaltotals,
            'scbranch'=> $request->frombranch,
            'scquonos'=> $request->qnos,
            'sjqnos'=> $sjqno,
            'mjqnos'=> $mjqno,
            'wgqnos'=> $wagqno,
            'bitolqnos'=> $bitolqno,
            'elqnos'=> $bitelqno,
            'cvrublqnos'=> $cvrublqno,
            'cvrukhqnos'=> $cvrukhqno,
            'rntuqnos'=> $rntuqno,
            'manipalnos'=> $manipalqno,
            'squsersid'=> $request->leadsuserid,
            'subranchse'=> $userBranchs,
            'leadids'=> $request->leadid,

                ]);

            $studcompid = $studentsQuotation->id;
                    $scoursesdata = $request->sinvcourses;
                    $scoursesubcourse = $request->sinvsubcourses;
                    $scsmode = $request->scoursdataemode;
                    $scsfeess = $request->scoursesFees;
                    //$nstudents = $request->nofstudents;

                    //dd($request->sinvsubcourses);
                     for($i=0; $i < (count($scoursesdata)); $i++)
                    {
                                $studentsQuotationCourses = new studentsQuotationCourses([
                                
                                'stucompyid' => $studcompid,
                                'studecompcourse'   => $scoursesdata[$i],
                                'studecoursemode'   => $scsmode[$i],
                                'studecoursefeess'   => $scsfeess[$i],
                                
                                
                            ]);
                            $studentsQuotationCourses->save();
                    }
            }

            elseif($qcategorys == "Company")
            {

                  //dd($qcategorys);
                 $studentsQuotationmodel = new studentsQuotation();
            $studentsQuotation = $studentsQuotationmodel->create([
            'studentscategorys'=> $qcategorys,
            'quotationsdates'=> $request->qdate,
            'quotationsduedates'=> $request->ldates,
            'studentsocompanyname'=> $request->ccname,
            'contactperson'=> $request->cperson,
            'scemail'=> $request->cemaiels,
            'scaddress'=> $request->caddress,
            'scgstnos'=> $request->gstnos,
            'scphones'=> $request->cmobileno,
            'scwhatsappno'=> $request->cwhatsappno,
            'scsubtotal'=> $request->subtotals,
            'scdiscounttypes'=> $dtype,
            'scdiscountstotals'=> $discoun,
            'scgstamounts'=> $request->gstamounts,
            'ssgstamounts'=> $request->sgstamounts,
            'scfinaltotal'=> $request->finaltotals,
            'scbranch'=> $request->frombranch,
            'scquonos'=> $request->qnos,
            'sjqnos'=> $sjqno,
            'mjqnos'=> $mjqno,
            'wgqnos'=> $wagqno,
            'bitolqnos'=> $bitolqno,
            'elqnos'=> $bitelqno,
            'cvrublqnos'=> $cvrublqno,
            'cvrukhqnos'=> $cvrukhqno,
            'rntuqnos'=> $rntuqno,
            'manipalnos'=> $manipalqno,
            'squsersid'=> $request->leadsuserid,
            'subranchse'=> $userBranchs,
            'leadids'=> $request->leadid,

                ]);

            $studcompid = $studentsQuotation->id;
                    $coursesdata = $request->invcourse;
                    $coursesubcourse = $request->invsubcourses;
                    $csmode = $request->coursdataemode;
                    $csfeess = $request->coursesFees;
                    $nstudents = $request->nofstudents;
                     for($i=0; $i < (count($coursesdata)); $i++)
                    {
                                $studentsQuotationCourses = new studentsQuotationCourses([
                                
                                'stucompyid' => $studcompid,
                                'studecompcourse'   => $coursesdata[$i],
                                'studecoursemode'   => $csmode[$i],
                                'studecoursefeess'   => $csfeess[$i],
                                'compnystudents'   => $nstudents[$i],
                                
                                
                            ]);
                            $studentsQuotationCourses->save();
                    }

            }

                   // return redirect('/centre-manager-quotation')->with('success','Quotation Create SuccessFully!!!');


             if($studentsQuotation->studentscategorys == "Company")
                {

                            $data = array();
                $getUserQuotationDetails = studentsQuotation::find($studentsQuotation->id);
                
                $getCourseDetasils = studentsQuotationCourses::select('courses.coursename','students_quotation_courses.*')->Join('courses','courses.id','=','students_quotation_courses.studecompcourse')->where('students_quotation_courses.stucompyid',$studentsQuotation->id)->get();
                    
                         $Comapanyname = $getUserQuotationDetails->studentsocompanyname;
                         $contactperson = $getUserQuotationDetails->contactperson;
                         $inquirydate = date('d-m-Y',strtotime($getUserQuotationDetails->qleaddates));
                     

                     $pdf = PDF::loadView('superadmin.quotation.viewquotations',compact('getUserQuotationDetails','getCourseDetasils'));

                     $data = array('CompanyName' => $Comapanyname, 'ContactPerson' => $contactperson, 'QuoteDate' => $inquirydate); 
                     $data["email"] = $getUserQuotationDetails->scemail;
                     $data["title"] = "Quotations For " .$Comapanyname;

                     $data["course"] = $getCourseDetasils;
                     
                       
                     
                     Mail::send('superadmin.quotation.quotationmail', $data, function ($message) use ($data, $pdf) {
                        $data;
                        $message->to($data["email"], $data["email"])
                           ->from('bitadmisson@gmail.com','BIT Baroda Institute Of Technology')
                           ->cc('support@bitbaroda.com','Admission BIT')
                            ->subject($data["title"])
                            ->attachData($pdf->output(),"Quotations.pdf");
                            });


                            if (Mail::failures()) {
                                        dd('mailerror');
                                    } else {

                                        return redirect('/centre-manager-quotation')->with('success','Quotation Create SuccessFully and Email Sent Successfully!!!');

                                    }

                    }




                if($studentsQuotation->studentscategorys == "Students")
                {

                            $data = array();
                $getUserQuotationDetails = studentsQuotation::find($studentsQuotation->id);
                
                $getCourseDetasils = studentsQuotationCourses::select('courses.coursename','students_quotation_courses.*')->Join('courses','courses.id','=','students_quotation_courses.studecompcourse')->where('students_quotation_courses.stucompyid',$studentsQuotation->id)->get();
                    
                         $Comapanyname = $getUserQuotationDetails->contactperson;
                         $contactperson = $getUserQuotationDetails->contactperson;
                         $inquirydate = date('d-m-Y',strtotime($getUserQuotationDetails->qleaddates));
                     

                     $pdf = PDF::loadView('superadmin.quotation.viewquotations',compact('getUserQuotationDetails','getCourseDetasils'));

                     $data = array('CompanyName' => $Comapanyname, 'ContactPerson' => $contactperson, 'QuoteDate' => $inquirydate); 
                     $data["email"] = $getUserQuotationDetails->scemail;
                     $data["title"] = "Quotations For " .$Comapanyname;

                     $data["course"] = $getCourseDetasils;
                     
                       
                     
                     Mail::send('superadmin.quotation.quotationmail', $data, function ($message) use ($data, $pdf) {
                        $data;
                        $message->to($data["email"], $data["email"])
                           ->from('bitadmisson@gmail.com','BIT Baroda Institute Of Technology')
                           ->cc('support@bitbaroda.com','Admission BIT')
                            ->subject($data["title"])
                            ->attachData($pdf->output(),"Quotations.pdf");
                            });


                            if (Mail::failures()) {
                                        dd('mailerror');
                                    } else {

                                        return redirect('/centre-manager-quotation')->with('success','Quotation Create SuccessFully and Email Sent Successfully!!!');

                                    }

                    }


    }


       public function viewquotations($id)
    {
        $getdetails = studentsQuotation::find($id);

         $coursedetailsv = studentsQuotationCourses::Join('courses','courses.id','=','students_quotation_courses.studecompcourse')->select('students_quotation_courses.*','courses.coursename')->where('students_quotation_courses.stucompyid',$id)->get();

        return view('centremanager.quotation.quoteview',compact('getdetails','coursedetailsv'));

      //  return view('centremanager.quotation.quoteview',compact('getdetails','coursedetailsv'));
    }


    public function createinvoice($id)
    {
        
        $cours = course::get();
        $getdetails = studentsQuotation::find($id);

        $branhanch = $getdetails->scbranch;

         if($branhanch == "BITSJ")
        {
            
            $latests = QuotationInvoices::where('invscbranch','=',$branhanch)->latest()->get()->pluck('invsjqnos');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITSJ-Inv/'.$code_nos;
            
        }

        else if($branhanch == "BITMJ") 
        {

            
            $latests = QuotationInvoices::where('invscbranch','=',$branhanch)->latest()->get()->pluck('invmjqnos');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITMJ-Inv/'.$code_nos;
            
        }

          else if($branhanch == "BITWG") 
        {

            
            $latests = QuotationInvoices::where('invscbranch','=',$branhanch)->latest()->get()->pluck('invwgqnos');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITWG-Inv/'.$code_nos;
            
        }

         else if($branhanch == "BITOL") 
        {
           
            $latests = QuotationInvoices::where('invscbranch','=',$banch)->latest()->get()->pluck('invbitolqnos');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'BITOL-Inv/'.$kode;
           
        }

        else if($branhanch == "BITEL") 
        {
           
            $latests = QuotationInvoices::where('invscbranch','=',$branhanch)->latest()->get()->pluck('invelqnos');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'BITEL-Inv/'.$kode;
           
        }
         else if($branhanch == "CVRU(BL)") 
        {
           
            $latests = QuotationInvoices::where('invscbranch','=',$branhanch)->latest()->get()->pluck('invcvrublqnos');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(BL)-Inv/'.$kode;
            
        }
         else if($branhanch == "CVRU (KH)") 
        {
           
            $latests = QuotationInvoices::where('invscbranch','=',$branhanch)->latest()->get()->pluck('invcvrukhqnos');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(KH)-Inv/'.$kode;
            
        }
         else if($branhanch == "RNTU") 
        {
           
            $latests = QuotationInvoices::where('invscbranch','=',$banch)->latest()->get()->pluck('invrntuqnos');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'RNTU-Inv/'.$kode;
            
        }
        else if($branhanch == "MANIPAL") 
        {
           
            $latests = QuotationInvoices::where('invscbranch','=',$banch)->latest()->get()->pluck('invmanipalnos');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'MANIPAL-Inv/'.$kode;
            
        } 



         $invsjqno ="0";
            $invmjqno ="0";
            $invwagqno ="0";
            $invbitolqno ="0";
            $invbitelqno = "0";
            $invcvrublqno ="0";
            $invcvrukhqno ="0";
            $invrntuqno ="0";
            $invmanipalqno ="0";


            $invsno = $value;
            $invsnos = explode("/",$value);
             if($invsnos[0] == 'BITSJ')
            {
                $invsjqno  = $invsnos[3];

               
            }
            else if($invsnos[0] == 'BITMJ')
            {
                $invmjqno  = $invsnos[3];
             
            }
            elseif($invsnos[0] == 'BITWG')
            {
                $invwagqno = $invsnos[3];
            }

             elseif($invsnos[0] == 'BITOL')
            {
                $invbitolqno = $invsnos[3];
            }


            elseif($invsnos[0] == 'BITEL')
            {
                $invbitelqno = $invsnos[3];
            }

            elseif($invsnos[0] == 'CVRU(BL)')
            {
                $invcvrublqno = $invsnos[3];
            }

            elseif($invsnos[0] == 'CVRU (KH)')
            {
               $invcvrukhqno = $invsnos[3];
            }

            elseif($invsnos[0] == 'RNTU')
            {
               $invrntuqno = $invsnos[3];
            }

            elseif($invsnos[0] == 'MANIPAL')
            {
                $invmanipalqno = $invsnos[3];
            }



            $QuotationInvoicesmodel = new QuotationInvoices();
            $QuotationInvoices = $QuotationInvoicesmodel->create([
            'quotationsid'=> $id,
            'invdates'=> date("Y-m-d"),
            'invscbranch'=> $branhanch,
            'invscquonos'=> $invsno,
            'invsjqnos'=> $invsjqno,
            'invmjqnos'=> $invmjqno,
            'invwgqnos'=>  $invwagqno,
            'invbitolqnos'=>  $invbitolqno,
            'invelqnos'=> $invbitelqno,
            'invcvrublqnos'=> $invcvrublqno,
            'invcvrukhqnos'=>$invcvrukhqno,
            'invrntuqnos'=> $invrntuqno,
            'invmanipalnos'=> $invmanipalqno,

                ]);

             return redirect('/centre-manager-show-invoices-details/'.$id)->with('success','Invoices Created SuccessFully!!!');  


      
    }


     public function invoiceslist($id)
    {
        $invlists = QuotationInvoices::where('quotationsid',$id)->orderBy('id','DESC')->first();
        $invoicessname = studentsQuotation::find($id);

        return view('centremanager.quotation.invoiceslists',compact('invlists','invoicessname'));
    }

    public function shwoinvociesdetails($id)
    {

        $userBranchs = Auth::user()->branchs;
            //dd($);
        //$aprocess = QuotationInvoices::find($id);

       // $invocicescoursedetails = QuotationInvoicesCourses::Join('courses','courses.id','=','quotation_invoices_courses.invstudecompcourse')->select('quotation_invoices_courses.*','courses.coursename')->where('quotation_invoices_courses.invstucompyid',$id)->get(); 'subranchse'=> $userBranchs,
        
        $aprocess = studentsQuotation::join('quotation_invoices','quotation_invoices.quotationsid','=','students_quotations.id')->select('students_quotations.*','quotation_invoices.*','students_quotations.id as sid')->where('students_quotations.id',$id)->where('students_quotations.subranchse',$userBranchs)->first();

          // dd($aprocess);

        $invocicescoursedetails = studentsQuotationCourses::Join('courses','courses.id','=','students_quotation_courses.studecompcourse')->select('students_quotation_courses.*','courses.coursename')->where('students_quotation_courses.stucompyid',$id)->get();

     

        return view('centremanager.quotation.invoicesview',compact('aprocess','invocicescoursedetails','id'));

    }

    public function convertoadmissionproccess($id)
    {
       // dd($id);

        //$alb = branch::get();
        $directstudentsdata = studentsQuotation::find($id);

       // dd($directstudentsdata);
        $cours = course::get();
        $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();
        $ucats = UnviersitiesCategory::all();
        return view('centremanager.quotation.convertoadmissionproccess',compact('cours','branchdetails','cours','course','taxesna','ucats','directstudentsdata'));
    }  

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
         $findmobileno = $request->CompanyQuotataion;

        $leadsMob = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.phone',$findmobileno)->orWhere('leads.whatsappno',$findmobileno)->get();
        foreach($leadsMob as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

                                        $leas->followupstatus ='';
                                        $leas->takenby ='';
                                        $leas->flfollwpdate ='';
                                        $leas->flremarsk = '';
                                        $leas->nxtfollowupdate = '';

                                        if($da)
                                        {
                                            $leas->followupstatus = $da->followupstatus;
                                            $leas->takenby = $da->takenby;
                                            $leas->flfollwpdate = $da->flfollwpdate;
                                            $leas->flremarsk = $da->flremarsk;
                                            $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                           
                                        }

                                      }

        return view('centremanager.quotation.filterquotations',compact('leadsMob'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyQuotation $companyQuotation,Request $request)
    {
       $userId = Auth::user()->id;
       /*dd($request->all());*/

        /*$sjinvno = "0";
        $mjinvno = "0";
        $waginvno = "0";
        $bitolinvno = "0";
        $cvrublinvno = "0";
        $cvrukhinvno = "0";
        $rntuinvno = "0";
        $manipalinvno = "0"*/;

        $mjerno ="0";
        $sjerno ="0";
        $wageron ="0";
        $bitolerno ="0";
        $cvrublerno ="0";
        $cvrukherno ="0";
        $rntuerno ="0";
        $manipalerno ="0";
        
       
        
        $discoun = "NULL";

        $studentnames = $request->studentname;
        $sstudentnames = $request->studentnamesd;
        $birthdate = $request->dob;
        $email = $request->stuemail;
        $brnach = $request->bran;
        $erno = $request->studenterno;
        $mobile = $request->phoneno;
        $stuwhatsapp = $request->whatsno;
        $admidate = $request->adate;
        $studentstreet =  $request->streets;
        $studentcity = $request->city;
        $studentstate = $request->state;
        $studentzipcode =  $request->zipcode;
        $ptime =  $request->preferrabletime;
        $refassignto =  $request->refassignto;
        $refename =  $request->refename;
        $refrom =  $request->refrom;
        $rnote =  $request->remarknote;

      

        $idate = $request->invoicedate;
        $ddate = $request->duedate;
        $branchdata = $request->brnach;
        $invno = $request->invno;
        $pmode = $request->paymentmode;
        $dtype = $request->discounttype;
        $subto = $request->subtotal;
        $tot = $request->total;
        $raxe = $request->taxs;
        $oldpricess = $request->oldtotalpice;
        //$cmode =  $request->coursemode;

        $acategory  = $request->admissionscateogrys;

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

         elseif($enrollno[0] == 'BITOL')
        {
            $bitolerno = $enrollno[3];
        }

        elseif($enrollno[0] == 'CVRU(BL)')
        {
            $cvrublerno = $enrollno[3];
        }

        elseif($enrollno[0] == 'CVRU (KH)')
        {
            $cvrukherno = $enrollno[3];
        }

        elseif($enrollno[0] == 'RNTU')
        {
            $rntuerno = $enrollno[3];
        }

        elseif($enrollno[0] == 'MANIPAL')
        {
            $manipalerno = $enrollno[3];
        }

        
       

         if($dtype == "2")
        {
             $discoun = $request->discount1;
        }

        elseif($dtype == "1")
        {
            $discoun = $request->discount2;
        }

        $discoun2 = $request->discount2;

        $inoviceno = explode("/",$invno);

         if($sstudentnames)
        {
            $newstudents = $sstudentnames;
        }

        elseif($studentnames)
        {
            $newstudents = $studentnames;
        }

       


        if($acategory == "Students") 
         {

            

            $admissionprocessmodel = new admissionprocess();
            $admissionprocess = $admissionprocessmodel->create([
            'studentname'=> $newstudents,
            'sdobs'=> $birthdate,
            'semails'=> $email,
            'sbrnanch'=> $brnach,
            'stobranches'=> $request->tobranchessw,
            'serno'=> $erno,
            'sjerno'=> $sjerno,
            'mjerno'=> $mjerno,
            'wgerno'=> $wageron,
            'bitolerno'=> $bitolerno,
            'cvrublerno'=> $cvrublerno,
            'cvrukherno'=> $cvrukherno,
            'rntuerno'=> $rntuerno,
            'manipalerno'=> $manipalerno,
            'sphone'=> $mobile,
            'swhatsappno'=> $stuwhatsapp,
            'sadate'=> $admidate,
            'sstreet'=> $studentstreet,
            'scity'=> $studentcity,
            'sstate'=> $studentstate,
            'szipcode'=> $studentzipcode,
            'spreferrabbletime'=> $ptime,
            'refeassignto'=> $refassignto,
            'referfrom'=> $refrom,
            'refername'=> $refename,
            'sremarknotes'=> $rnote,
            'ipaymentmodes'=> $pmode,
            'idiscounttypes'=> $dtype,
            'isubtotal'=> $subto,
            'idiscount'=> $discoun,
            'itax'=> $request->tax,
            'invtotal'=> $tot,
            'userid' => $userId,
            'gstprices' => $request->gstprice,
            'sgstprics' => $request->sgst,
            'oldtotalpice' => $oldpricess,
            'admissionstatus'=> 'New Student',
            'admissionsusersid'=> $request->admissioonsusersid,
            'discounttotal'=> $request->discounttotal,
            'admsisource'=> $request->admisources,
            'fnames'=> $request->fathersnames,
            'mnames'=> $request->mothersname,
            'suniversities'=> $request->universitiesss,
             'admissionsusersid'=> $request->admissionsid,
            
            ]);

            $invoicesid = $admissionprocess->id;
                    $coursesdata = $request->invcourse;
                    $courseprice = $request->invprice;
                    $csmode = $request->coursdataemode;
                    $cd = $request->duration;
                    $ct = $request->tax;
                    $installdate = $request->installmentdate;
                    $installprice = $request->installmentprice;
                    $pamount = $request->pendingamount;
                     $adforss= $request->admissionfor;

                    $adforss= $request->admissionfor;
                    $uniccourse= $request->unvicocurs;
                    $ufees= $request->univfees;
                    $subcoursesdata = $request->invsubcourses;

                    if($admissionprocess->suniversities == 'BIT')
                    {
                           for($i=0; $i < (count($coursesdata)); $i++)
                        {
                                    $admissionprocesscourses = new admissionprocesscourses([
                                    
                                    'invid' => $invoicesid,
                                    'courseid'   => $coursesdata[$i],
                                    'coursemode'   => $csmode[$i],
                                    'courseprice'   => $courseprice[$i],
                                    'studentsin'   => 'New Student',
                                    
                                ]);
                                $admissionprocesscourses->save();
                        }
                    }

                    else

                    {
                          for($i=0; $i < (count($uniccourse)); $i++)
                        {
                                    $admissionprocesscourses = new admissionprocesscourses([
                                    
                                    'invid' => $invoicesid,
                                    'univecoursid'   => $uniccourse[$i],
                                    'admissionfor'   => $adforss[$i],
                                    'unoverfeess'   => $ufees[$i],
                                    'studentsin'   => 'New Student',
                                    
                                ]);
                                $admissionprocesscourses->save();
                        }
                    }
                    


              return redirect('/centre-manager-create-company-payment/'.$invoicesid);

        }

        else
        {
            $admissionprocessmodel = new admissionprocess();
            $admissionprocess = $admissionprocessmodel->create([
            'studentname'=> $newstudents,
            'companynames'=> $request->companyname,
            'admissionscategorys'=> $request->admissionscateogrys,
            'sdobs'=> $birthdate,
            'semails'=> $email,
            'sbrnanch'=> $brnach,
            'stobranches'=> $request->tobranchessw,
            'serno'=> $erno,
            'sjerno'=> $sjerno,
            'mjerno'=> $mjerno,
            'wgerno'=> $wageron,
            'bitolerno'=> $bitolerno,
            'cvrublerno'=> $cvrublerno,
            'cvrukherno'=> $cvrukherno,
            'rntuerno'=> $rntuerno,
            'manipalerno'=> $manipalerno,
            'sphone'=> $mobile,
            'swhatsappno'=> $stuwhatsapp,
            'sadate'=> $admidate,
            'sstreet'=> $studentstreet,
            'scity'=> $studentcity,
            'sstate'=> $studentstate,
            'szipcode'=> $studentzipcode,
            'spreferrabbletime'=> $ptime,
            'refeassignto'=> $refassignto,
            'referfrom'=> $refrom,
            'refername'=> $refename,
            'sremarknotes'=> $rnote,
            'invdate'=> $idate,
            'duedate'=> $ddate,
            'ipaymentmodes'=> $pmode,
            'idiscounttypes'=> $dtype,
            'isubtotal'=> $subto,
            'idiscount'=> $discoun,
            'itax'=> $request->tax,
            'invtotal'=> $tot,
            'userid' => $userId,
            'gstprices' => $request->gstprice,
            'sgstprics' => $request->sgst,
            'oldtotalpice' => $request->oldtotalpice,
            'admissionstatus'=> 'New Student',
            'admissionsusersid'=> $request->admissioonsusersid,
            'discounttotal'=> $request->discounttotal,
            'admsisource'=> $request->admisources,
            'fnames'=> $request->fathersnames,
            'mnames'=> $request->mothersname,
            'suniversities'=> $request->universitiesss,
             'admissionsusersid'=> $request->admissionsid,
            
            ]);

            $invoicesid = $admissionprocess->id;
                    $coursesdata = $request->invcourse;
                    $subcoursesdata = $request->invsubcourses;
                    $courseprice = $request->invprice;
                    $csmode = $request->coursdataemode;
                    $cd = $request->duration;
                    $ct = $request->tax;
                    $installdate = $request->installmentdate;
                    $installprice = $request->installmentprice;
                    $pamount = $request->pendingamount;
                    

                    $adforss= $request->admissionfor;
                    $uniccourse= $request->unvicocurs;
                    $ufees= $request->univfees;
                    $subcoursesdata = $request->invsubcourses;

                    $cstudentsnames = $request->csnames;
                    $cstudentsemails = $request->csemails;
                    $csphoneno = $request->csphoneno;
                    $cswhatsappno = $request->cswhatsappno;



                    $cinvcourse = $request->cinvcourse;
                    $cinvsubcourses = $request->cinvsubcourses;
                    $ccoursdataemode = $request->ccoursdataemode;
                    $ccoursesFees = $request->ccoursesFees;
                    $cnosofstudents = $request->cnofstudents;

                 
                            for($k=0; $k < (count($cinvcourse)); $k++)
                        {

             


                                    $admissionprocesscourses = new admissionprocesscourses([
                                    
                                    'invid' => $invoicesid,
                                    'courseid'   => $cinvcourse[$k],
                                    'coursemode'   => $ccoursdataemode[$k],
                                    'courseprice'   => $ccoursesFees[$k],
                                    'nofstudetns'   => $cnosofstudents[$k],
                                    'studentsin'   => 'New Student',
                                    
                                ]);
                                $admissionprocesscourses->save();
                        }






                             for($m=0; $m < (count($cstudentsnames)); $m++)
                        {


                                               $branhanch = $request->tobranchessw;

                                                         if($branhanch == "BITSJ")
                                                        {
                                                            
                                                            $latests = Companys_Admissions::where('cbranchs','=',$branhanch)->latest()->get()->pluck('csjerno');
                                                            $mj = isset($latests[0]) ? $latests[0] : false;
                                                            $counts = $mj + 1;
                                                            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
                                                            $value = 'BITSJ-COMP/'.$code_nos;
                                                            
                                                        }

                                                        else if($branhanch == "BITMJ") 
                                                        {

                                                            
                                                            $latests = Companys_Admissions::where('cbranchs','=',$branhanch)->latest()->get()->pluck('cmjerno');
                                                            $mj = isset($latests[0]) ? $latests[0] : false;
                                                            $counts = $mj + 1;
                                                            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
                                                            $value = 'BITMJ-COMP/'.$code_nos;
                                                            
                                                        }

                                                          else if($branhanch == "BITWG") 
                                                        {

                                                            
                                                            $latests = Companys_Admissions::where('cbranchs','=',$branhanch)->latest()->get()->pluck('cwgerno');
                                                            $mj = isset($latests[0]) ? $latests[0] : false;
                                                            $counts = $mj + 1;
                                                            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
                                                            $value = 'BITWG-COMP/'.$code_nos;
                                                            
                                                        }

                                                         else if($branhanch == "BITOL") 
                                                        {
                                                           
                                                            $latests = Companys_Admissions::where('cbranchs','=',$banch)->latest()->get()->pluck('cbitolerno');
                                                            $wg = isset($lates[0]) ? $lates[0] : false;
                                                            $counted = $wg + 1;
                                                            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
                                                            $value = 'BITOL-COMP/'.$kode;
                                                           
                                                        }

                                                        else if($branhanch == "BITEL") 
                                                        {
                                                           
                                                            $latests = Companys_Admissions::where('cbranchs','=',$branhanch)->latest()->get()->pluck('celerno');
                                                            $wg = isset($lates[0]) ? $lates[0] : false;
                                                            $counted = $wg + 1;
                                                            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
                                                            $value = 'BITEL-COMP/'.$kode;
                                                           
                                                        }
                                                         else if($branhanch == "CVRU(BL)") 
                                                        {
                                                           
                                                            $latests = Companys_Admissions::where('cbranchs','=',$branhanch)->latest()->get()->pluck('ccvrublerno');
                                                            $wg = isset($lates[0]) ? $lates[0] : false;
                                                            $counted = $wg + 1;
                                                            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
                                                            $value = 'CVRU(BL)-COMP/'.$kode;
                                                            
                                                        }
                                                         else if($branhanch == "CVRU (KH)") 
                                                        {
                                                           
                                                            $latests = Companys_Admissions::where('cbranchs','=',$branhanch)->latest()->get()->pluck('ccvrukherno');
                                                            $wg = isset($lates[0]) ? $lates[0] : false;
                                                            $counted = $wg + 1;
                                                            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
                                                            $value = 'CVRU(KH)-COMP/'.$kode;
                                                            
                                                        }
                                                         else if($branhanch == "RNTU") 
                                                        {
                                                           
                                                            $latests = Companys_Admissions::where('cbranchs','=',$branhanch)->latest()->get()->pluck('crntuerno');
                                                            $wg = isset($lates[0]) ? $lates[0] : false;
                                                            $counted = $wg + 1;
                                                            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
                                                            $value = 'RNTU-COMP/'.$kode;
                                                            
                                                        }
                                                        else if($branhanch == "MANIPAL") 
                                                        {
                                                           
                                                            $latests = Companys_Admissions::where('cbranchs','=',$branhanch)->latest()->get()->pluck('cmanipalerno');
                                                            $wg = isset($lates[0]) ? $lates[0] : false;
                                                            $counted = $wg + 1;
                                                            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
                                                            $value = 'MANIPAL-COMP/'.$kode;
                                                            
                                                        } 



                                             $invsjqno ="0";
                                                $invmjqno ="0";
                                                $invwagqno ="0";
                                                $invbitolqno ="0";
                                                $invbitelqno = "0";
                                                $invcvrublqno ="0";
                                                $invcvrukhqno ="0";
                                                $invrntuqno ="0";
                                                $invmanipalqno ="0";


                                                $invsno = $value;
                                                $invsnos = explode("/",$invsno);
                                                 if($invsnos[0] == 'BITSJ')
                                                {
                                                    $invsjqno  = $invsnos[3];

                                                   
                                                }
                                                else if($invsnos[0] == 'BITMJ')
                                                {
                                                    $invmjqno  = $invsnos[3];
                                                 
                                                }
                                                elseif($invsnos[0] == 'BITWG')
                                                {
                                                    $invwagqno = $invsnos[3];
                                                }

                                                 elseif($invsnos[0] == 'BITOL')
                                                {
                                                    $invbitolqno = $invsnos[3];
                                                }


                                                elseif($invsnos[0] == 'BITEL')
                                                {
                                                    $invbitelqno = $invsnos[3];
                                                }

                                                elseif($invsnos[0] == 'CVRU(BL)')
                                                {
                                                    $invcvrublqno = $invsnos[3];
                                                }

                                                elseif($invsnos[0] == 'CVRU (KH)')
                                                {
                                                   $invcvrukhqno = $invsnos[3];
                                                }

                                                elseif($invsnos[0] == 'RNTU')
                                                {
                                                   $invrntuqno = $invsnos[3];
                                                }

                                                elseif($invsnos[0] == 'MANIPAL')
                                                {
                                                    $invmanipalqno = $invsnos[3];
                                                }
                                                                        $Companys_Admissions = new Companys_Admissions([
                                    
                                    'cadmissionsid' => $invoicesid,
                                    'cstudentsnames'   => $cstudentsnames[$m],
                                    'cemails'   => $cstudentsemails[$m],
                                    'cphones'   => $csphoneno[$m],
                                    'cwhatsappnos'   => $cswhatsappno[$m],
                                    'cbranchs'   => $branhanch,
                                    'cernos'   => $value,
                                    'csjerno'   => $invsjqno,
                                    'cwgerno'   => $invwagqno,
                                    'cmjerno'   => $invmjqno,
                                    'celerno'   => $invbitelqno,
                                    'cbitolerno'   => $invbitolqno,
                                    'ccvrublerno'   => $invcvrublqno,
                                    'ccvrukherno'   => $invcvrukhqno,
                                    'crntuerno'   => $invrntuqno,
                                    'cmanipalerno'   => $invmanipalqno,
                                    
                                
                                    
                                ]);
                                $Companys_Admissions->save();
                        
                    }

                    
        }

                 

             return redirect('/centre-manager-create-company-payment/'.$invoicesid);
                    

        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function update($id,Request $request, CompanyQuotation $companyQuotation)
    {

        $userBranch = Auth::user()->branchs;
        $paymentdetails = admissionprocess::find($id);
        $branc = Branch::where('branchname',$userBranch)->get();
         $psource = PaymentSource::all();
      
        return view('centremanager.quotation.createpayment',compact('paymentdetails','branc','psource'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CompanyQuotation  $companyQuotation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,CompanyQuotation $companyQuotation,Request $request)
     {

        
        $userId = Auth::user()->id;
        $studentsdata = $request->students;
        $rcepno = $request->receiptno;
        $ernos = $request->erno;
        $rptype = $request->ptypes;

            if($rptype == 'LumpSum')
            {
                $tmamount = $request->totalamount;
                $preceived = $request->paymentrecieved;
            }
            else if($rptype == 'EMI')
            {
                $tmamount = $request->instam;
                $preceived = $request->instam;
            }

        $receptsno = explode("/",$rcepno);
        $newerno = explode("/",$ernos);
       // dd($receptsno);

        $sjrecno = "0";
        $mjrecno = "0";
        $wagrecno = "0";
        $bitolrecno = "0";
        $cvrublrecno = "0";
        $cvrukhrecno = "0";
        $rnturecno = "0";
        $manipalrecno = "0";

        $sjernocs = "0";
        $mjernocs = "0";
        $wagernocs = "0";
        $bitolernocs = "0";
        $cvrublernocs = "0";
        $cvrukhernocs = "0";
        $rntuernocs = "0";
        $manipalernocs = "0";
       

        if($receptsno[0] == 'BITSJ')
        {
            $sjrecno = $receptsno[1];

            //dd($sjrecno);
        }
        else if($receptsno[0] == 'BITMJ')
        {
            $mjrecno = $receptsno[1];
         
        }
        elseif($receptsno[0] == 'BITWG')
        {
            $wagrecno = $receptsno[1];
        }

         elseif($receptsno[0] == 'BITOL')
        {
            $bitolrecno = $receptsno[1];
        }

        elseif($receptsno[0] == 'CVRU(BL)')
        {
            $cvrublrecno = $receptsno[1];
        }

        elseif($receptsno[0] == 'CVRU (KH)')
        {
            $cvrukhrecno = $receptsno[1];
        }

        elseif($receptsno[0] == 'RNTU')
        {
            $rnturecno = $receptsno[1];
        }

        elseif($receptsno[0] == 'MANIPAL')
        {
            $manipalrecno = $receptsno[1];
        }



         if($newerno[0] == 'BITSJ')
        {
            $sjernocs = $newerno[3];
            
        }
        else if($newerno[0] == 'BITMJ')
        {
            $mjernocs = $newerno[3];
         
        }
        elseif($newerno[0] == 'BITWG')
        {
            $wagernocs = $newerno[3];
        }

         elseif($newerno[0] == 'BITOL')
        {
            $bitolernocs   = $newerno[3];
        }

        elseif($newerno[0] == 'CVRU(BL)')
        {
            $cvrublernocs  = $newerno[3];
        }

        elseif($newerno[0] == 'CVRU (KH)')
        {
           $cvrukhernocs  = $newerno[3];
        }

        elseif($newerno[0] == 'RNTU')
        {
            $rntuernocs  = $newerno[3];
        }

        elseif($newerno[0] == 'MANIPAL')
        {
            $manipalernocs    = $newerno[3];
        }

        //dd($request->all());

        $paymentmodel = new payment();
        $payment = $paymentmodel->create([
            'inviceid'=> $id,
            'totalamount'=> $tmamount,
            'paymentreceived'=> $preceived,
            'remainingamount'=> $request->ramount,
            'paymentdate'=> $request->paymentdate,
            'paymentmode'=> $request->paymentmode,
            'nexamountdate'=> $request->remindersdates,
            'bankname'=> $request->bankname,
            'chequeno'=> $request->chequeno,
            'chequedate'=> $request->chequedate,
            'chequetype'=> $request->chequetype,
            'remarknoe'=> $request->remarknote,
            'userid'=> $userId,
            'studentsid'=> $request->students,
            'branchs'=> $request->brnavhc,
            'receiptno'=> $rcepno,
            'sjrecpno'=> $sjrecno,
            'mjrecpno'=> $mjrecno,
            'wgrecpno'=> $wagrecno,
            'bitolrecpno'=> $bitolrecno,
            'cvrublrecpno'=> $cvrublrecno,
            'cvrukhrecpno'=> $cvrukhrecno,
            'rnturecpno'=> $rnturecno,
            'manipalrecpno'=> $manipalrecno,
            'studenterno'=> $ernos,
            'sjerno'=> $sjernocs,
            'mjerno'=> $mjernocs,
            'wgerno'=> $wagernocs,
            'cvrublerno'=> $cvrublernocs,
            'cvrukherno'=> $cvrukhernocs,
            'bitolerno'=> $bitolernocs,
            'rntuerno'=> $manipalernocs,
            'manipalerno'=> $manipalernocs,
            'studentadmissiionstatus'=> 'New Student',
            'installmentid'=> $request->installid,
            'paymentype' => $request->ptypes
        ]);

        $insid = $request->installid;

        $paymentid = $payment->id;

        $updatenew = admissionprocessinstallmentfees::find($insid);

        if($updatenew)
       {
            $updatenew->status = 1;
            $updatenew->save();
        }
        




        $updatesid = admissionprocess::find($id);
        $updatesid->status = '1';
        $updatesid->serno = $payment->studenterno;
        $updatesid->save();

        $studentsphone = admissionprocess::where('id',$id)->pluck('sphone');
        $leadupodat = leads::where('phone',$studentsphone)->first();
      
         //dd($leadupodat);
       if($leadupodat)
       {
            $leadupodat->conversationstatus = '1';
            $leadupodat->save();
        
       }
        



        return redirect('/centre-manager-company-paymentreceipt/'.$paymentid)->with('success','Payment Successfully Done!!!');
    }


    public function companypaymentreceipts($id)
    {
        $selectID = payment::find($id);
            $newId = $selectID->inviceid;

        $aprocess = admissionprocess::find($newId);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$newId.'" ');

         $installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");

         $univCourse = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.univecoursid AND a.id = k.invid AND a.id = "'.$newId.'" ');

         $paymentdata = payment::where('inviceid',$newId)->first();

         $makepayment = DB::select('SELECT * FROM  admissionprocesses a, payments p WHERE a.id = p.inviceid AND a.id = "'.$newId.'" ');

         /*$installmentdata = DB::SELECT('SELECT * FROM  admissionprocesses a, payments p WHERE a.id = p.inviceid AND a.id = "'.$id.'" ');*/

         /*$installdata = DB::select("SELECT * FROM admissionprocessinstallmentfees f, admissionprocesses a, payments p  WHERE a.id = p.inviceid AND a.id = f.invoid AND a.id = '$id' ORDER BY f.id DESC");*/

         $installdata = admissionprocessinstallmentfees::leftJoin('payments', 'payments.installmentid', '=', 'admissionprocessinstallmentfees.id')->where('admissionprocessinstallmentfees.invoid',$newId)->orderBy('admissionprocessinstallmentfees.id','DESC')->get();        
        
         $compadmissiondetails = Companys_Admissions::join('admissionprocesses', 'admissionprocesses.id', '=', 'companys__admissions.cadmissionsid')->where('companys__admissions.cadmissionsid',$newId)->get();        
         

        

         /*$payments = payment::where('inviceid',$id)->first();*/

        // dd($payments);
      //   dd($payments);

        
        

        return view('centremanager.quotation.paymentreceipt',compact('aprocess','invvcoursed','univCourse','paymentdata','makepayment','installdata','selectID','compadmissiondetails'));

    }
}
