<?php
namespace App\Http\Controllers;
use App\PastAdmission;
use App\pastadmissioncourse;
use App\pastadmissioninstallmentfees;
use App\pastpayments;
use App\students;
use App\Source;
use App\followup;
use App\course;
use App\Branch;
use App\leads;
use App\studentscourse;
use App\Tax;
use App\User;
use App\admissionprocess;
use App\admissionprocesscourses;
use App\admissionprocessinstallmentfees;
use App\ReAdmission;
use App\coursebunchlist;
use App\coursespecializationlist;
use App\UnviersitiesCategory;
use App\universititiesfeeslist;
use App\PaymentSource;
use Auth;
use DB;
use Illuminate\Http\Request;

class PastAdminAdmissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

      public function getcourseslist(Request $request)
    {
        $admissionsId = $request->admissionid;

       // dd($admissionsId);

        $data= array();

        $result = pastadmissioncourse::select('courses.coursename')->join('courses','courses.id','=','pastadmissioncourses.pcourseid')->where('pastadmissioncourses.pinvid',$admissionsId)->get();

        foreach($result as $res)
        {
            $row = array();
            $row[] = $res->coursename;
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);

    }

      public function repayment($id)
     {

        $paymentdetails = PastAdmission::find($id);
        $paymentsse = pastpayments::where('pinviceid',$id)->orderBy('id','DESC')->take(1)->get();
        $branc = Branch::all();
        $installmentfees = pastadmissioninstallmentfees::where('pinvoid',$id)->where('pstatus',0)->orderBy('id','DESC')->get();
        $psource = PaymentSource::all();
      
        return view('pastadmin.pastadmissions.repayment',compact('paymentdetails','branc','installmentfees','paymentsse','psource'));
     }
      public function restorepayment(Request $request,$id)
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

        $pastpaymentsmodel = new pastpayments();
        $pastpayments = $pastpaymentsmodel->create([
            'pinviceid'=> $id,
            'ptotalamount'=> $tmamount,
            'ppaymentreceived'=> $preceived,
            'premainingamount'=> $request->ramount,
            'ppaymentdate'=> $request->paymentdate,
            'ppaymentmode'=> $request->paymentmode,
            'pbankname'=> $request->bankname,
            'pchequeno'=> $request->chequeno,
            'pchequedate'=> $request->chequedate,
            'pchequetype'=> $request->chequetype,
            'premarknoe'=> $request->remarknote,
            'puserid'=> $userId,
            'pbranchs'=> $request->brnavhc,
            'preceiptno'=> $rcepno,
            'psjrecpno'=> $sjrecno,
            'pmjrecpno'=> $mjrecno,
            'pwgrecpno'=> $wagrecno,
            'pbitolrecpno'=> $bitolrecno,
            'pcvrublrecpno'=> $cvrublrecno,
            'pcvrukhrecpno'=> $cvrukhrecno,
            'prnturecpno'=> $rnturecno,
            'pmanipalrecpno'=> $manipalrecno,
            'pstudentadmissiionstatus'=> 'New Student',
            'pinstallmentid'=> $request->installid,
        ]);

        $insid = $request->installid;

        $paymentid = $pastpayments->id;

        $updatenew = pastadmissioninstallmentfees::find($insid);

        if($updatenew)
       {
            $updatenew->pstatus = 1;
            $updatenew->save();
        }
        




        $updatesid = PastAdmission::find($id);
        $updatesid->pstatus = '1';
        $updatesid->save();

        $studentsphone = PastAdmission::where('id',$id)->pluck('psphone');
        $leadupodat = leads::where('phone',$studentsphone)->first();
      
         //dd($leadupodat);
       if($leadupodat)
       {
            $leadupodat->conversationstatus = '1';
            $leadupodat->save();
        
       }
        



        return redirect('/past-admin-paymentreceipt/'.$paymentid)->with('success','Payment Successfully Done!!!');
    }
    public function index()
    {
        
        $brnagch = Branch::all();
        $userALl = User::all();
            // $currentMonth = date('m');
         $studentsdata = PastAdmission::select('past_admissions.*','pastpayments.*','past_admissions.id as aid')->join('pastpayments', 'pastpayments.pinviceid', '=', 'past_admissions.id')->groupBy('pastpayments.pinviceid')->get(); 

       
         foreach($studentsdata as $studentpaymen)
         {
            $das = pastpayments::where('pinviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

            $studentpaymen->preceiptno ='';
            $studentpaymen->ppaymentreceived ='';
            $studentpaymen->premainingamount ='';
           
            
             if($das){
                $studentpaymen->preceiptno = $das->preceiptno;
                $studentpaymen->ppaymentreceived = $das->ppaymentreceived;
                $studentpaymen->premainingamount = $das->premainingamount;
                
                
            }

         }

         return view('pastadmin.pastadmissions.manage',compact('studentsdata','brnagch','userALl'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $alb = branch::get();
        $cours = course::get();
        $leadsdata = leads::get();

        $studentdetails = students::get();
       
        $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();
        $ucats = UnviersitiesCategory::all();
        return view('pastadmin.pastadmissions.create',compact('alb','cours','leadsdata','studentdetails','branchdetails','course','taxesna','ucats'));   
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
   {
     // dd($request->all());
       $userId = Auth::user()->id;
       /*dd($request->all());*/

        $sjinvno = "0";
        $mjinvno = "0";
        $waginvno = "0";
        $bitolinvno = "0";
        $cvrublinvno = "0";
        $cvrukhinvno = "0";
        $rntuinvno = "0";
        $manipalinvno = "0";

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

       

        $inoviceno = explode("/",$invno);
       //dd($inoviceno);
        if($inoviceno[0] == 'INV-BITSJ')
        {
            $sjinvno = $inoviceno[3];

           
        }
        else if($inoviceno[0] == 'INV-BITMJ')
        {
            $mjinvno = $inoviceno[3];
         
        }
        elseif($inoviceno[0] == 'INV-BITWG')
        {
            $waginvno = $inoviceno[3];
        }

         elseif($inoviceno[0] == 'INV-BITOL')
        {
            $bitolinvno = $inoviceno[3];
        }

         elseif($inoviceno[0] == 'INV-CVRU(BL)')
        {
            $cvrublinvno = $inoviceno[3];
        }
        elseif($inoviceno[0] == 'INV-CVRU(KH)')
        {
            $cvrukhinvno = $inoviceno[3];
        }

         elseif($inoviceno[0] == 'INV-RNTU')
        {
            $rntuinvno = $inoviceno[3];
        }
        elseif($inoviceno[0] == 'INV-MANIPAL')
        {
            $manipalinvno = $inoviceno[3];
        }

         if($dtype == "2")
        {
             $discoun = $request->discount1;
        }

        elseif($dtype == "1")
        {
            $discoun = $request->discount2;
        }

    

        $inoviceno = explode("/",$invno);

         if($sstudentnames)
        {
            $newstudents = $sstudentnames;
        }

        elseif($studentnames)
        {
            $newstudents = $studentnames;
        }

       
        //dd($discoun);

        if($pmode == "EMI") 

        {

            

            $PastAdmissionmodel = new PastAdmission();
            $PastAdmission = $PastAdmissionmodel->create([
            'pstudentname'=> $newstudents,
            'psdobs'=> $birthdate,
            'psemails'=> $email,
            'psbrnanch'=> $brnach,
            'pstobranches'=> $request->tobranchessw,
            'psphone'=> $mobile,
            'pswhatsappno'=> $stuwhatsapp,
            'psadate'=> $admidate,
            'psstreet'=> $studentstreet,
            'pscity'=> $studentcity,
            'psstate'=> $studentstate,
            'pszipcode'=> $studentzipcode,
            'pspreferrabbletime'=> $ptime,
            'prefeassignto'=> $refassignto,
            'preferfrom'=> $refrom,
            'prefername'=> $refename,
            'psremarknotes'=> $rnote,
            'pIpbranchs'=> $branchdata,
            'pInvoiceno'=> $request->invno,
            'pIsjno'=> $sjinvno,
            'pImjno'=> $mjinvno,
            'pIwgno'=> $waginvno,
            'pIbitolno'=> $bitolinvno,
            'pIcvrublno'=> $cvrublinvno,
            'pIcvrukhno'=> $cvrukhinvno,
            'pIrntuno'=> $rntuinvno,
            'pImanipalno'=> $manipalinvno,
            'pinvdate'=> $idate,
            'pduedate'=> $ddate,
            'pipaymentmodes'=> $pmode,
            'pidiscounttypes'=> $dtype,
            'pisubtotal'=> $subto,
            'pdiscount'=> $discoun,
            'pitax'=> $request->tax,
            'pinvtotal'=> $tot,
            'puserid' => $userId,
            'pgstprices' => $request->gstprice,
            'poldtotalpice' => $oldpricess,
            'padmissionstatus'=> 'New Student',
            'pdiscounttotal'=> $request->discounttotal,
            'pfnames'=> $request->fathersnames,
            'pmnames'=> $request->mothersname,
            'psuniversities'=> $request->universitiesss,
            
            ]);

            $invoicesid = $PastAdmission->id;
                    $coursesdata = $request->invcourse;
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

                  /*  for($i=0; $k <(count($installdate)); $i++)
                    {
                        $pastadmissioninstallmentfees = new pastadmissioninstallmentfees([
                            
                            'pinvoid' => $invoicesid,
                            'pinvoicedate'   => $installdate[$i],
                            'pinstallmentamount'   => $installprice[$i],
                            'ppendinamount'   => $ppendinamount[$i],

                        ]);

                         $pastadmissioninstallmentfees->save();  
                    }*/

                //    dd($installdate);

                    for($i=0; $i < (count($installdate)); $i++)
                                        {
                                            
                                             $dakmsm = pastadmissioninstallmentfees::updateOrCreate(['pinvoicedate' => $installdate[$i],'pinvoid' => $invoicesid,'pinstallmentamount' => $installprice[$i],'ppendinamount' => $pamount[$i] ]);

                                          
                  


                                        }

                    if($PastAdmission->psuniversities == 'BIT')
                    {
                           for($i=0; $i < (count($coursesdata)); $i++)
                        {
                                    $pastadmissioncourse = new pastadmissioncourse([
                                    
                                    'pinvid' => $invoicesid,
                                    'pcourseid'   => $coursesdata[$i],
                                    'psubcourses'   => $subcoursesdata[$i],
                                    'pcoursemode'   => $csmode[$i],
                                    'pcourseprice'   => $courseprice[$i],
                                    'pstudentsin'   => 'New Student',
                                    
                                ]);
                                $pastadmissioncourse->save();
                        }
                    }

                    else

                    {
                          for($i=0; $i < (count($uniccourse)); $i++)
                        {
                                    $pastadmissioncourse = new pastadmissioncourse([
                                    
                                    'pinvid' => $invoicesid,
                                    'punivecoursid'   => $uniccourse[$i],
                                    'padmissionfor'   => $adforss[$i],
                                    'punoverfeess'   => $ufees[$i],
                                    'pstudentsin'   => 'New Student',
                                    
                                ]);
                                $pastadmissioncourse->save();
                        }
                    }
                  


            return redirect('/view-admin-past-invoices/'.$invoicesid);

        }

        else
        {
            $PastAdmissionmodel = new PastAdmission();
            $PastAdmission = $PastAdmissionmodel->create([
            'pstudentname'=> $newstudents,
            'psdobs'=> $birthdate,
            'psemails'=> $email,
            'psbrnanch'=> $brnach,
            'pstobranches'=> $request->tobranchessw,
            'psphone'=> $mobile,
            'pswhatsappno'=> $stuwhatsapp,
            'psadate'=> $admidate,
            'psstreet'=> $studentstreet,
            'pscity'=> $studentcity,
            'psstate'=> $studentstate,
            'pszipcode'=> $studentzipcode,
            'pspreferrabbletime'=> $ptime,
            'prefeassignto'=> $refassignto,
            'preferfrom'=> $refrom,
            'prefername'=> $refename,
            'psremarknotes'=> $rnote,
            'pIbranchs'=> $branchdata,
            'pInvoiceno'=> $request->invno,
            'pIsjno'=> $sjinvno,
            'pImjno'=> $mjinvno,
            'pIwgno'=> $waginvno,
            'pIbitolno'=> $bitolinvno,
            'pIcvrublno'=> $cvrublinvno,
            'pIcvrukhno'=> $cvrukhinvno,
            'pIrntuno'=> $rntuinvno,
            'pImanipalno'=> $manipalinvno,
            'pinvdate'=> $idate,
            'pduedate'=> $ddate,
            'pipaymentmodes'=> $pmode,
            'pidiscounttypes'=> $dtype,
            'pisubtotal'=> $subto,
            'pdiscount'=> $discoun,
            'pitax'=> $request->tax,
            'pinvtotal'=> $tot,
            'puserid' => $userId,
            'pgstprices' => $request->gstprice,
            'poldtotalpice' => $oldpricess,
            'padmissionstatus'=> 'New Student',
            'pdiscounttotal'=> $request->discounttotal,
            'pfnames'=> $request->fathersnames,
            'pmnames'=> $request->mothersname,
            'psuniversities'=> $request->universitiesss,
            
            ]);

            $invoicesid = $PastAdmission->id;
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

                    if($PastAdmission->psuniversities == 'BIT')
                    {
                           for($i=0; $i < (count($coursesdata)); $i++)
                        {
                                    $pastadmissioncourse = new pastadmissioncourse([
                                    
                                    'pinvid' => $invoicesid,
                                    'pcourseid'   => $coursesdata[$i],
                                    'psubcourses'   => $subcoursesdata[$i],
                                    'pcoursemode'   => $csmode[$i],
                                    'pcourseprice'   => $courseprice[$i],
                                    'pstudentsin'   => 'New Student',
                                    
                                ]);
                                $pastadmissioncourse->save();
                        }
                    }

                    else

                    {
                          for($i=0; $i < (count($uniccourse)); $i++)
                        {
                                    $pastadmissioncourse = new pastadmissioncourse([
                                    
                                    'pinvid' => $invoicesid,
                                    'punivecoursid'   => $uniccourse[$i],
                                    'padmissionfor'   => $adforss[$i],
                                    'punoverfeess'   => $ufees[$i],
                                    'pstudentsin'   => 'New Student',
                                    
                                ]);
                                $pastadmissioncourse->save();
                        }
                    }

                 

             return redirect('/view-admin-past-invoices/'.$invoicesid);
                    

        }
    }


   public function paymentreceiptlist($id)
    {
      $result = pastpayments::where('pinviceid',$id)->get();

      $admissionname = PastAdmission::find($id);

      return view('pastadmin.pastadmissions.paymentreceiptlist',compact('result','admissionname'));

    }
    /**
     * Display the specified resource.
     *
     * @param  \App\PastAdmission  $pastAdmission
     * @return \Illuminate\Http\Response
     */
    public function show(PastAdmission $pastAdmission)
   {
         $currentMonth = date('m');
         $brnagch = Branch::all();
        $userALl = User::all();
        /* $studentsdata = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereNull('payments.inviceid')->groupBy('payments.inviceid')->get();*/
          
        $pendamount = \DB::table('past_admissions')->whereMonth('past_admissions.psadate',$currentMonth)->select('past_admissions.*','past_admissions.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('pastpayments')
                                                                ->whereRaw('past_admissions.id = pastpayments.pinviceid');
                                                            })->get(); 

        foreach($pendamount as $studentpaymen)
         {
            $das = pastpayments::where('pinviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

            $studentpaymen->preceiptno ='';
            $studentpaymen->ppaymentreceived ='';
            $studentpaymen->premainingamount ='';
           
            
             if($das){
                $studentpaymen->preceiptno = $das->preceiptno;
                $studentpaymen->ppaymentreceived = $das->ppaymentreceived;
                $studentpaymen->premainingamount = $das->premainingamount;
                
                
            }
        }



        

            $invototal = $pendamount->sum('pinvtotal');
        
        

        //dd($invototal);

        $sumtotal = $invototal;
          
         $pamenreceived = 0; 
         

         
        
         
          
            $totslreceived = $pamenreceived;

            $remainingamount = $sumtotal - $totslreceived;


           return view('pastadmin.pastadmissions.pendingamount',compact('pendamount','brnagch','userALl','sumtotal','totslreceived','remainingamount'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PastAdmission  $pastAdmission
     * @return \Illuminate\Http\Response
     */
    public function edit(PastAdmission $pastAdmission)
    {
         
                 $userId = Auth::user()->id;
        
        $cour = course::all();
       $sourcedata = Source::get();
        $folss = followup::get();
         $userBranch = Auth::user()->branchs;
         $userdata = User::all();


         $userBranch = Auth::user()->branchs;
        $currentMonth = date('m');

        $invoicesdata = PastAdmission::select('past_admissions.*','pastpayments.*','past_admissions.id as admid')->join('pastpayments','pastpayments.pinviceid','=','past_admissions.id')->whereMonth('pastpayments.ppaymentdate',$currentMonth)->get();
        
        

        $invototal = $invoicesdata->sum('pinvtotal');
        
        //dd($invototal);

        $sumtotal = $invototal;
          
         $pamenreceived = $invoicesdata->sum('ppaymentreceived');
          
            $totslreceived = $pamenreceived;

            $remainingamount = $sumtotal - $totslreceived;

                
            
            return view('pastadmin.pastadmissions.totalfees',compact('invoicesdata','cour','sourcedata','folss','userdata','sumtotal','totslreceived','remainingamount'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PastAdmission  $pastAdmission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PastAdmission $pastAdmission)
   {

       //   $admiId = admissionprocess::pluck('id');
      
        $currentMonth = date('m');
          /*$NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->whereMonth('admissionprocesses.sadate',$currentMonth)
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->get(); */


        $WiPayment = PastAdmission::select('past_admissions.*', 'pastpayments.*','past_admissions.id as admid', DB::raw('SUM(pastpayments.ppaymentreceived) As ppaymentreceived'))
         ->leftJoin('pastpayments', 'pastpayments.pinviceid', '=', 'past_admissions.id')
         ->whereMonth('pastpayments.ppaymentdate',$currentMonth)
         ->groupBy('pastpayments.pinviceid')
         ->get();


       

        
        $pendamount = $WiPayment;
       
         $invototal = $pendamount->sum('pinvtotal');
        
        $sumtotal = $invototal;
          
         $pamenreceived = $pendamount->sum('ppaymentreceived');; 
         

         
            $totslreceived = $pamenreceived;

            $remainingamount = $sumtotal - $totslreceived;



      //  dd($pendamount);
        return view('pastadmin.pastadmissions.pendingfees',compact('pendamount','sumtotal','totslreceived','remainingamount'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PastAdmission  $pastAdmission
     * @return \Illuminate\Http\Response
     */
    public function destroy(PastAdmission $pastAdmission)
    {
        //
    }

    public function viewinvoice($id)
    {

         $aprocess = PastAdmission::find($id);
       
            //$reaprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  past_admissions a, courses c, pastadmissioncourses k WHERE c.id = k.pcourseid AND a.id = k.pinvid AND a.id = "'.$id.'" ');
       

        $univCourse = DB::select('SELECT * FROM  past_admissions a, courses c, pastadmissioncourses k WHERE c.id = k.punivecoursid AND a.id = k.pinvid AND a.id = "'.$id.'" ');

         $installmentfees = DB::select("SELECT * FROM pastadmissioninstallmentfees WHERE pinvoid = '$id' ORDER BY id DESC");
         
        return view('pastadmin.pastadmissions.newinvoice',compact('aprocess','invvcoursed','installmentfees','univCourse'));
    }

    public function changeemi($id)
    {

                   $adm = PastAdmission::find($id); 
                  // $allinstallmentdata = admissionprocessinstallmentfees::where('invoid',$id)->orderBy('id','DESC')->get();
                   $latestpaymentdata = pastpayments::where('pinviceid',$id)->orderBy('id','DESC')->first();
                $getemidata = pastpayments::where('pinviceid',$id)->get();

                foreach($getemidata as $emis)
                {

                     $getinstallmentdata = pastadmissioninstallmentfees::where('id',$emis->pinstallmentid)->get();
                }

                return view('pastadmin.pastadmissions.changeemi',compact('getinstallmentdata','adm','latestpaymentdata'));
    }

     public function changenewemi($id,Request $request)
    {                        /* dd($request->all());*/      
                                $bids = $request->emimainid;
                            //        dd($bids);
                                $deles = pastadmissioninstallmentfees::where('pinvoid',$bids)->get();
                                $deles->each->delete();
                                  
                               


                                $idate = $request->installmentdate;
                                $iprice = $request->installmentprice;
                                $ipa = $request->pendingamount;


                                for($i=0; $i < (count($idate)); $i++)
                                        {
                                            
                                             $dakmsm = pastadmissioninstallmentfees::updateOrCreate(['pinvoicedate' => $idate[$i],'pinvoid' => $id,'pinstallmentamount' => $iprice[$i],'ppendinamount' => $ipa[$i] ]);

                                          
                  


                                        }

                                    DB::statement('update pastadmissioninstallmentfees a inner join pastpayments c on a.pinvoid = c.pinviceid and  a.pinstallmentamount = c.ptotalamount set a.pstatus = 1, c.pinstallmentid = a.id;');

   
            return redirect('/make-past-admin-re-payment/'.$id)->with('success','EMI Successfully Changed !!');

    }

    public function payment($id)
    {

        $paymentdetails  = PastAdmission::find($id);
        $branc = Branch::all();
        $installmentfees = pastadmissioninstallmentfees::where('pinvoid',$id)->where('pstatus',0)->orderBy('id','DESC')->get();
        $psource = PaymentSource::all();
        //  dd($installmentfees);
      
        return view('pastadmin.pastadmissions.makepayment',compact('paymentdetails','branc','installmentfees','psource'));
    }

    public function paymentstore(Request $request,$id)
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

        $pastpaymentsmodel = new pastpayments();
        $pastpayments = $pastpaymentsmodel->create([
            'pinviceid'=> $id,
            'ptotalamount'=> $tmamount,
            'ppaymentreceived'=> $preceived,
            'premainingamount'=> $request->ramount,
            'ppaymentdate'=> $request->paymentdate,
            'ppaymentmode'=> $request->paymentmode,
            'pbankname'=> $request->bankname,
            'pchequeno'=> $request->chequeno,
            'pchequedate'=> $request->chequedate,
            'pchequetype'=> $request->chequetype,
            'premarknoe'=> $request->remarknote,
            'puserid'=> $userId,
            'pbranchs'=> $request->brnavhc,
            'preceiptno'=> $rcepno,
            'psjrecpno'=> $sjrecno,
            'pmjrecpno'=> $mjrecno,
            'pwgrecpno'=> $wagrecno,
            'pbitolrecpno'=> $bitolrecno,
            'pcvrublrecpno'=> $cvrublrecno,
            'cvrukhrecpno'=> $cvrukhrecno,
            'prnturecpno'=> $rnturecno,
            'pmanipalrecpno'=> $manipalrecno,
            'pstudenterno'=> $ernos,
            'psjerno'=> $sjernocs,
            'pmjerno'=> $mjernocs,
            'pwgerno'=> $wagernocs,
            'pcvrublerno'=> $cvrublernocs,
            'pcvrukherno'=> $cvrukhernocs,
            'pbitolerno'=> $bitolernocs,
            'prntuerno'=> $manipalernocs,
            'pmanipalerno'=> $manipalernocs,
            'pstudentadmissiionstatus'=> 'New Student',
            'pinstallmentid'=> $request->installid,
        ]);

        $insid = $request->installid;

        $paymentid = $pastpayments->id;

        $updatenew = pastadmissioninstallmentfees::find($insid);

        if($updatenew)
       {
            $updatenew->pstatus = 1;
            $updatenew->save();
        }
        




        $updatesid = PastAdmission::find($id);
        $updatesid->pstatus = '1';
        $updatesid->pserno = $pastpayments->pstudenterno;
        $updatesid->save();

        $studentsphone = PastAdmission::where('id',$id)->pluck('psphone');
        $leadupodat = leads::where('phone',$studentsphone)->first();
      
         //dd($leadupodat);
       if($leadupodat)
       {
            $leadupodat->conversationstatus = '1';
            $leadupodat->save();
        
       }
        



        return redirect('/past-admin-paymentreceipt/'.$paymentid)->with('success','Payment Successfully Done!!!');
    }

    public function paymentreceipt($id)
    {
        $selectID = pastpayments::find($id);
            $newId = $selectID->pinviceid;

        $aprocess = PastAdmission::find($newId);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  past_admissions a, courses c, pastadmissioncourses k WHERE c.id = k.pcourseid AND a.id = k.pinvid AND a.id = "'.$newId.'" ');

         $installmentfees = DB::select("SELECT * FROM pastadmissioninstallmentfees WHERE pinvoid = '$id' ORDER BY id DESC");

         $univCourse = DB::select('SELECT * FROM  past_admissions a, courses c, pastadmissioncourses k WHERE c.id = k.punivecoursid AND a.id = k.pinvid AND a.id = "'.$newId.'" ');

         $paymentdata = pastpayments::where('pinviceid',$newId)->first();

         $makepayment = DB::select('SELECT * FROM  past_admissions a, pastpayments p WHERE a.id = p.pinviceid AND a.id = "'.$newId.'" ');

         $installdata = pastadmissioninstallmentfees::join('pastpayments', 'pastpayments.pinstallmentid', '=', 'pastadmissioninstallmentfees.id')->where('pastadmissioninstallmentfees.pinvoid',$newId)->orderBy('pastadmissioninstallmentfees.id','DESC')->get();        
         


        return view('pastadmin.pastadmissions.pastpaymentreceipt',compact('aprocess','invvcoursed','univCourse','paymentdata','makepayment','installdata','selectID'));

    }

    public function getreceiptno($brancgs)
    {
        
          if($brancgs == "BITSJ")
        {
            
          
            $latests = pastpayments::where('pbranchs','=',$brancgs)->latest()->get()->pluck('psjrecpno');
            //dd($latests);
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITSJ/'.$code_nos;
            return response()->json($value);
            
             /*return response()->json($value);*/
        }

        else if($brancgs == "BITMJ") 
        {

            
            $latests = pastpayments::where('pbranchs','=',$brancgs)->latest()->get()->pluck('pmjrecpno');
            //$latests = admissionprocess::get()->pluck('mjerno')->toArray();
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITMJ/'.$code_nos;
            return response()->json($value);
        }

          else if($brancgs == "BITWG") 
        {

            
            $latests = pastpayments::where('pbranchs','=',$brancgs)->latest()->get()->pluck('pwgrecpno');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITWG/'.$code_nos;
            return response()->json($value);
        }

         else if($brancgs == "BITOL") 
        {
           
            $latests = pastpayments::where('pbranchs','=',$brancgs)->latest()->get()->pluck('pbitolrecpno');
            /*$lates = admissionprocess::get()->pluck('wgerno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'BITOL/'.$kode;
            return response()->json($value);
        }
         else if($brancgs == "CVRU(BL)") 
        {
           
            $latests = pastpayments::where('stobranches','=',$brancgs)->latest()->get()->pluck('pcvrublrecpno');
            /*$lates = admissionprocess::get()->pluck('wgerno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(BL)/'.$kode;
            return response()->json($value);
        }
         else if($brancgs == "CVRU (KH)") 
        {
           
            $latests = pastpayments::where('pbranchs','=',$brancgs)->latest()->get()->pluck('pcvrukhrecpno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(KH)/'.$kode;
            return response()->json($value);
        }
         else if($brancgs == "RNTU") 
        {
           
            $latests = pastpayments::where('pbranchs','=',$brancgs)->latest()->get()->pluck('prnturecpno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'RNTU/'.$kode;
            return response()->json($value);
        }
        else if($brancgs == "MANIPAL") 
        {
           
            $latests = pastpayments::where('pbranchs','=',$brancgs)->latest()->get()->pluck('pmanipalrecpno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'MANIPAL/'.$kode;
            return response()->json($value);
        }
        

    
    }


    public function getenrollmentno($pbranchsw)
    {

                     $year = date("Y");
                    $month = date("m");

                     if($pbranchsw == "BITSJ")
                    {
                        
                        //$latests = admissionprocess::get()->pluck('sjerno');

                        //$latests = admissionprocess::where('prefix_id', $current_prefix->id)->max('number') + 1;
                        $latests = pastpayments::whereNotNull('pstudenterno')->where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('psjerno');
                    
                        //dd($latests);
                        $mj = isset($latests[0]) ? $latests[0] : false;
                        $counts = $mj + 1;
                        $kode = str_pad($counts, 4, "0", STR_PAD_LEFT);
                        $value = 'BITSJ/'.$year.'/'.$month.'/'.$kode;
                        return response()->json($value);
                        
                         /*return response()->json($value);*/
                    }

                    else if($pbranchsw == "BITMJ") 
                    {

                        
                    /*    $latests = pastpayments::where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('mjerno');*/
                         $latests = pastpayments::whereNotNull('pstudenterno')->where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('pmjerno');
                        //$latests = admissionprocess::get()->pluck('mjerno')->toArray();
                        $mj = isset($latests[0]) ? $latests[0] : false;
                        $counts = $mj + 1;
                        $kode = str_pad($counts, 4, "0", STR_PAD_LEFT);
                        $value = 'BITMJ/'.$year.'/'.$month.'/'.$kode;
                        return response()->json($value);
                    }

                      else if($pbranchsw == "BITWG") 
                    {

                        
                      /*  $latests = pastpayments::where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('wgerno');*/
                         $latests = pastpayments::whereNotNull('pstudenterno')->where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('pwgerno');
                        $mj = isset($latests[0]) ? $latests[0] : false;
                        $counts = $mj + 1;
                        $kode = str_pad($counts, 4, "0", STR_PAD_LEFT);
                        $value = 'BITWG/'.$year.'/'.$month.'/'.$kode;
                        return response()->json($value);
                    }

                     else if($pbranchsw == "BITOL") 
                    {
                       
                        /*$latests = pastpayments::where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('bitolerno');*/
                        $latests = pastpayments::whereNotNull('pstudenterno')->where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('pbitolerno');
                        /*$lates = admissionprocess::get()->pluck('wgerno')->toArray();*/
                        //dd($lates);
                        $wg = isset($lates[0]) ? $lates[0] : false;
                        $counted = $wg + 1;
                        $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
                        $value = 'BITOL/'.$year.'/'.$month.'/'.$kode;
                        return response()->json($value);
                    }
                     else if($pbranchsw == "CVRU(BL)") 
                    {
                        $latests = pastpayments::whereNotNull('pstudenterno')->where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('pcvrublerno');
         
                        $wg = isset($lates[0]) ? $lates[0] : false;
                        $counted = $wg + 1;
                        $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
                        $value = 'CVRU(BL)/'.$year.'/'.$month.'/'.$kode;
                        return response()->json($value);
                    }
                     else if($pbranchsw == "CVRU (KH)") 
                    {
                       $latests = pastpayments::whereNotNull('studenterno')->where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('pcvrukherno');
                       // $latests = pastpayments::where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('cvrukherno');
                        $wg = isset($lates[0]) ? $lates[0] : false;
                        $counted = $wg + 1;
                        $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
                        $value = 'CVRU(KH)/'.$year.'/'.$month.'/'.$kode;
                        return response()->json($value);
                    }
                     else if($pbranchsw == "RNTU") 
                    {
                       $latests = pastpayments::whereNotNull('pstudenterno')->where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('prntuerno');
                        //$latests = pastpayments::where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('rntuerno');
                        $wg = isset($lates[0]) ? $lates[0] : false;
                        $counted = $wg + 1;
                        $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
                        $value = 'RNTU/'.$year.'/'.$month.'/'.$kode;
                        return response()->json($value);
                    }
                    else if($pbranchsw == "MANIPAL") 
                    {
                       $latests = pastpayments::whereNotNull('pstudenterno')->where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('pmanipalerno');
                        //$latests = pastpayments::where('pbranchs','=',$pbranchsw)->latest()->get()->pluck('manipalerno');
                        $wg = isset($lates[0]) ? $lates[0] : false;
                        $counted = $wg + 1;
                        $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
                        $value =  'MANIPAL/'.$year.'/'.$month.'/'.$kode;
                        return response()->json($value);
                    }
                    

                

    }


    public function branchinvoice($branchId)
    {

        $year = date("Y");
         $month = date("m");
         if($branchId == "1")
        {

               // $latest = DB::select("SELECT sjerno from students order by sjerno DESC LIMIT 1");
           
            $latests = PastAdmission::where('pIbranchs','=',$branchId)->latest()->get()->pluck('pIsjno');
            // $latests = admissionprocess::get()->pluck('Isjno')->toArray();
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'INV-BITSJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
            
             //return response()->json($value);
        }

        else if ($branchId == "2") 
        {
             $latests = PastAdmission::where('pIbranchs','=',$branchId)->latest()->get()->pluck('pImjno');
            /*$latests = admissionprocess::get()->pluck('Imjno')->toArray();*/
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'INV-BITMJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
        }

         else if ($branchId == "3") 
        {
            $latests = PastAdmission::where('pIbranchs','=',$branchId)->latest()->get()->pluck('pIwgno');
            /*$lates = admissionprocess::get()->pluck('Iwgno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-BITWG/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }

         else if ($branchId == "4") 
        {
            $latests = PastAdmission::where('pIbranchs','=',$branchId)->latest()->get()->pluck('pIbitolno');
            /*$lates = admissionprocess::get()->pluck('Ibitolno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-BITOL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if ($branchId == "5") 
        {
            $latests = PastAdmission::where('pIbranchs','=',$branchId)->latest()->get()->pluck('pIcvrublno');
            /*$lates = admissionprocess::get()->pluck('Icvrublno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-CVRU(BL)/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if ($branchId == "6") 
        {
            $latests = PastAdmission::where('pIbranchs','=',$branchId)->latest()->get()->pluck('pIcvrukhno');   
            /*$lates = admissionprocess::get()->pluck('Icvrukhno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-CVRU(KH)/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        else if ($branchId == "7") 
        {
            $latests = PastAdmission::where('pIbranchs','=',$branchId)->latest()->get()->pluck('pIrntuno');
            /*$lates = admissionprocess::get()->pluck('Irntuno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-RNTU/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        else if ($branchId == "8") 
        {
            $latests = PastAdmission::where('pIbranchs','=',$branchId)->latest()->get()->pluck('pImanipalno');
           /* $lates = admissionprocess::get()->pluck('Imanipalno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-MANIPAL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        
          
    }

    public function changestudents($id)
    {
        $alb = branch::get();
        $cours = course::get();
        $leadsdata = leads::get();
         $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();
        $directstudentsdata = PastAdmission::find($id);
        $ad = PastAdmission::all();
        $adcourses = pastadmissioncourse::where('pinvid','=',$id)->get();
        $univcourses = pastadmissioncourse::where('pinvid','=',$id)->get();
        $ademi = pastadmissioninstallmentfees::where('pinvoid','=',$id)->get();
        $ucats = UnviersitiesCategory::all();
        //dd($adcourses);
        
        return view('pastadmin.pastadmissions.edit',compact('alb','cours','leadsdata','branchdetails','course','taxesna','directstudentsdata','ad','adcourses','ademi','ucats'));
    }

    
     public function modifystudents($id, Request $request)
    { 

        $uodates = pastadmissioninstallmentfees::where('pinvoid',$id)->update(['pstatus'=> 0 ]);
        //dd($uodates);
        /*if($uodates)
        {
            $uodates->pstatus = 0;
            $uodates->save();

        }*/
       /* if($uodates->pinstallmentid)
        {
            $uodates->pinstallmentid = 
        }*/

       if($request->discounttype == "2")
        {
             $discoun = $request->discount1;
        }

        elseif($request->discounttype == "1")
        {
            $discoun = $request->discount2;
        }
         
         if($request->paymentmode == "EMI")
         {

                                 if($request->universitiesss == 'BIT')
                                 {
                                    $dele = pastadmissioncourse::where('pinvid',$id)->get();
                                    $dele->each->delete();
                                 }

                                 else
                                 {
                                     $deles = pastadmissioncourse::where('pinvid',$id)->get();
                                     $deles->each->delete();
                                 }
                                
                                $deleted = pastadmissioninstallmentfees::where('pinvoid',$id)->get();
                                $deleted->each->delete();



               $updates = PastAdmission::find($id);
              $updates->pstudentname = $request->studentname;
              $updates->pfnames = $request->fathersnames;
              $updates->pmnames = $request->mothersname;
              $updates->psdobs = $request->dob;
              $updates->psemails = $request->stuemail;
              $updates->psphone = $request->phoneno;
              $updates->pswhatsappno = $request->whatsno;
              $updates->psadate = $request->adate;
              $updates->psbrnanch = $request->bran;
              $updates->pstobranches = $request->tobranchessw;
              $updates->psuniversities = $request->universitiesss;
              $updates->psstreet = $request->streets;
              $updates->pscity = $request->city;
              $updates->psstate = $request->state;
              $updates->pszipcode = $request->zipcode;
              $updates->pspreferrabbletime = $request->preferrabletime;
              $updates->prefeassignto = $request->assignto;
              $updates->preferfrom = $request->refename;
              $updates->prefername = $request->refrom;
              $updates->psremarknotes = $request->remarknote;
              $updates->pIbranchs = $request->brnach;
              $updates->pInvoiceno = $request->invno;
              $updates->pinvdate = $request->invoicedate;
              $updates->pduedate = $request->duedate;
              $updates->pipaymentmodes = $request->paymentmode;
              $updates->pidiscounttypes = $request->discounttype;
              $updates->pisubtotal = $request->subtotal;
              $updates->pdiscounttotal = $request->discounttotal;
              $updates->pdiscount = $discoun;
              $updates->pitax = $request->tax;
              $updates->pgstprices = $request->gstprice;
              $updates->pinvtotal = $request->total;
              $updates->save();



              if($request->universitiesss == 'BIT')
                                 {

                                    $maincourse = $request->invcourse;
                                    $cmodes = $request->coursdataemode;
                                    $inmvsprice = $request->invprice;


                                   

                                     for($i=0; $i < (count($maincourse)); $i++)
                                        {
                                            
                                             $dakmsm = pastadmissioncourse::updateOrCreate(['pcourseid' => $maincourse[$i],'pcoursemode' => $cmodes[$i],'pcourseprice' => $inmvsprice[$i],'pinvid' => $id ]);

                                          
                  


                                        }
                                 }

                                 else
                                 {
                                    $univcourse = $request->unvicocurs;
                                    $admissfor = $request->admissionfor;
                                    $ufees = $request->univfees;

                                      for($i=0; $i < (count($univcourse)); $i++)
                                        {
                                            
                                             $dakmsm = pastadmissioncourse::updateOrCreate(['punivecoursid' => $univcourse[$i],'padmissionfor' => $admissfor[$i],'punoverfeess' => $ufees[$i],'pinvid' => $id ]);

                                          
                  


                                        }
                                 }

                                 $idates = $request->installmentdate;
                                 $iprice = $request->installmentprice;
                                 $pprice = $request->pendingamount;
                                  for($j=0; $j < (count($idates)); $j++)
                                        {
                                            
                                             $dakmsm = pastadmissioninstallmentfees::updateOrCreate(['pinvoicedate' => $idates[$j],'pinstallmentamount' => $iprice[$j],'ppendinamount' => $pprice[$j],'pinvoid' => $id ]);


                                             DB::statement('update pastadmissioninstallmentfees a inner join pastpayments c on a.pinvoid = c.pinviceid and  a.pinstallmentamount = c.ptotalamount set a.pstatus = 1, c.pinstallmentid = a.id;');

                                            
                  


                                        }


                              return redirect('/past-admin-student')->with('success','Past Admission Updated successfully!');



         }


         else
         {
                        if($request->universitiesss == 'BIT')
                                 {
                                    $dele = pastadmissioncourse::where('pinvid',$id)->get();
                                    $dele->each->delete();
                                 }

                                 else
                                 {
                                     $deles = pastadmissioncourse::where('pinvid',$id)->get();
                                     $deles->each->delete();
                                 }
                                
                                $deleted = pastadmissioninstallmentfees::where('pinvoid',$id)->get();
                                $deleted->each->delete();



              $updates = PastAdmission::find($id);
              $updates->pstudentname = $request->studentname;
              $updates->pfnames = $request->fathersnames;
              $updates->pmnames = $request->mothersname;
              $updates->psdobs = $request->dob;
              $updates->psemails = $request->stuemail;
              $updates->psphone = $request->phoneno;
              $updates->pswhatsappno = $request->whatsno;
              $updates->psadate = $request->adate;
              $updates->psbrnanch = $request->bran;
              $updates->pstobranches = $request->tobranchessw;
              $updates->psuniversities = $request->universitiesss;
              $updates->psstreet = $request->streets;
              $updates->pscity = $request->city;
              $updates->psstate = $request->state;
              $updates->pszipcode = $request->zipcode;
              $updates->pspreferrabbletime = $request->preferrabletime;
              $updates->prefeassignto = $request->assignto;
              $updates->preferfrom = $request->refename;
              $updates->prefername = $request->refrom;
              $updates->psremarknotes = $request->remarknote;
              $updates->pIbranchs = $request->brnach;
              $updates->pInvoiceno = $request->invno;
              $updates->pinvdate = $request->invoicedate;
              $updates->pduedate = $request->duedate;
              $updates->pipaymentmodes = $request->paymentmode;
              $updates->pidiscounttypes = $request->discounttype;
              $updates->pisubtotal = $request->subtotal;
              $updates->pdiscounttotal = $request->discounttotal;
              $updates->pdiscount = $discoun;
              $updates->pitax = $request->tax;
              $updates->pgstprices = $request->gstprice;
              $updates->pinvtotal = $request->total;
              $updates->save();



              if($request->universitiesss == 'BIT')
                                 {

                                    $maincourse = $request->invcourse;
                                    $cmodes = $request->coursdataemode;
                                    $inmvsprice = $request->invprice;


                                   

                                     for($i=0; $i < (count($maincourse)); $i++)
                                        {
                                            
                                              $dakmsm = pastadmissioncourse::updateOrCreate(['pcourseid' => $maincourse[$i],'pcoursemode' => $cmodes[$i],'pcourseprice' => $inmvsprice[$i],'pinvid' => $id ]);

                                          
                  


                                        }
                                 }

                                 else
                                 {
                                    $univcourse = $request->unvicocurs;
                                    $admissfor = $request->admissionfor;
                                    $ufees = $request->univfees;

                                      for($i=0; $i < (count($univcourse)); $i++)
                                        {
                                            
                                              $dakmsm = pastadmissioncourse::updateOrCreate(['punivecoursid' => $univcourse[$i],'padmissionfor' => $admissfor[$i],'punoverfeess' => $ufees[$i],'pinvid' => $id ]);

                                          
                  


                                        }
                                 }


                              return redirect('/past-admin-student')->with('success','Past Admission Updated successfully!');
         }

      


    } 

}