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
use Auth;
use App\admissionprocess;
use App\admissionprocesscourses;
use App\admissionprocessinstallmentfees;
use App\UnviersitiesCategory;
use App\coursecategory;
use App\followup;
use App\Source;
use App\PaymentSource;

use Illuminate\Http\Request;
use DB;


class AdmissionprocessController extends Controller
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
    public function create(Request $request)
    {
       $id = $request->getadmissions;
        $userBranch = Auth::user()->branchs;

        $alb = branch::Where('branchname',$userBranch)->get();
        $directstudentsdata = leads::find($id);
        $cours = course::get();
        $leadsdata = leads::get();

        $studentdetails = students::get();
       
        $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();
           $ucats = UnviersitiesCategory::all();
         return view('marketing.admissionprocess.create',compact('alb','cours','leadsdata','directstudentsdata','studentdetails','branchdetails','course','taxesna','ucats'));
        
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

       


        if($pmode == "EMI") 

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
            'Ibranchs'=> $branchdata,
            'Invoiceno'=> $request->invno,
            'Isjno'=> $sjinvno,
            'Imjno'=> $mjinvno,
            'Iwgno'=> $waginvno,
            'Ibitolno'=> $bitolinvno,
            'Icvrublno'=> $cvrublinvno,
            'Icvrukhno'=> $cvrukhinvno,
            'Irntuno'=> $rntuinvno,
            'Imanipalno'=> $manipalinvno,
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
            'oldtotalpice' => $oldpricess,
            'admissionstatus'=> 'New Student',
            'admissionsusersid'=> $request->admissioonsusersid,
            'discounttotal'=> $request->discounttotal,
            'admsisource'=> $request->admisources,
            'fnames'=> $request->fathersnames,
            'mnames'=> $request->mothersname,
            'suniversities'=> $request->universitiesss,
            
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
                                    'subcourses'   => $subcoursesdata[$i],
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
                    for($k=0; $k <(count($installdate)); $k++)
                    {
                        $admissionprocessinstallmentfees = new admissionprocessinstallmentfees([
                            
                            'invoid' => $invoicesid,
                            'invoicedate'   => $installdate[$k],
                            'installmentamount'   => $installprice[$k],
                            'pendinamount'   => $pamount[$k],

                        ]);

                         $admissionprocessinstallmentfees->save();  
                    }


            return redirect('/general-invoice-marketing/'.$invoicesid);

        }

        else
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
            'Ibranchs'=> $branchdata,
            'Invoiceno'=> $request->invno,
            'Isjno'=> $sjinvno,
            'Imjno'=> $mjinvno,
            'Iwgno'=> $waginvno,
            'Ibitolno'=> $bitolinvno,
            'Icvrublno'=> $cvrublinvno,
            'Icvrukhno'=> $cvrukhinvno,
            'Irntuno'=> $rntuinvno,
            'Imanipalno'=> $manipalinvno,
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
            'oldtotalpice' => $request->oldtotalpice,
            'admissionstatus'=> 'New Student',
            'admissionsusersid'=> $request->admissioonsusersid,
            'discounttotal'=> $request->discounttotal,
            'admsisource'=> $request->admisources,
            'fnames'=> $request->fathersnames,
            'mnames'=> $request->mothersname,
            'suniversities'=> $request->universitiesss,
            
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

                    if($admissionprocess->suniversities == 'BIT')
                    {
                            for($i=0; $i < (count($coursesdata)); $i++)
                        {
                                    $admissionprocesscourses = new admissionprocesscourses([
                                    
                                    'invid' => $invoicesid,
                                    'courseid'   => $coursesdata[$i],
                                    'subcourses'   => $subcoursesdata[$i],
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

                 

             return redirect('/general-invoice-marketing/'.$invoicesid);
                    

        }
    }


    
    /**
     * Display the specified resource.
     *
     * @param  \App\admissionprocess  $admissionprocess
     * @return \Illuminate\Http\Response
     */
    public function show($id,admissionprocess $admissionprocess)
    {
        $aprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");


        return view('marketing.admissionprocess.generalinvoices',compact('aprocess','invvcoursed','installmentfees'));
    }

     public function admissionform($id)
    {


        $aprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $univCourse = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.univecoursid AND a.id = k.invid AND a.id = "'.$id.'" ');

         //$installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");

         //$paymentdata = payment::where('inviceid',$id)->get();

        
        

        return view('marketing.admissionprocess.admissionform',compact('aprocess','invvcoursed','univCourse'));

    }

     public function paymentstore(Request $request,$id,admissionprocess $admissionprocess,payment $payment)
     {

         $userId = Auth::user()->id;
        $studentsdata = $request->students;
        $rcepno = $request->receiptno;
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

        //dd($request->all());

        $paymentmodel = new payment();
        $payment = $paymentmodel->create([
            'inviceid'=> $id,
            'totalamount'=> $tmamount,
            'paymentreceived'=> $preceived,
            'remainingamount'=> $request->ramount,
            'paymentdate'=> $request->paymentdate,
            'paymentmode'=> $request->paymentmode,
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
            'studentadmissiionstatus'=> 'New Student',
            'installmentid'=> $request->installid,
            'paymentype'=>$rptype,
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
        $updatesid->save();

        $studentsphone = admissionprocess::where('id',$id)->pluck('sphone');
        $leadupodat = leads::where('phone',$studentsphone)->first();
      
         //dd($leadupodat);
       if($leadupodat)
       {
            $leadupodat->conversationstatus = '1';
            $leadupodat->save();
        
       }


       


       return redirect('/marketing-user-paymentreceipt/'.$id)->with('success','Payment Successfully Done!!!');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\admissionprocess  $admissionprocess
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $alb = branch::get();
        $cours = course::get();
        $leadsdata = leads::get();
         $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();
        $studad = admissionprocess::find($id);
        $ad = admissionprocess::all();
        $adcourses = admissionprocesscourses::where('invid','=',$id)->get();
        $ademi = admissionprocessinstallmentfees::where('invoid','=',$id)->get();
        //dd($adcourses);
        
        return view('marketing.admissionprocess.edit',compact('alb','cours','leadsdata','branchdetails','course','taxesna','studad','ad','adcourses','ademi'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\admissionprocess  $admissionprocess
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, admissionprocess $admissionprocess)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\admissionprocess  $admissionprocess
     * @return \Illuminate\Http\Response
     */
     public function destroy(admissionprocess $admissionprocess)
    {
        $userBranch = Auth::user()->id;

       // dd($userBranch);
        
        $brnagch = Branch::all();
        $userALl = User::all();
             $currentMonth = date('m');
        
        $studentsdata = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereMonth('admissionprocesses.sadate',$currentMonth)->where('admissionprocesses.admissionsusersid',$userBranch)->groupBy('payments.inviceid')->get(); 

       
         foreach($studentsdata as $studentpaymen)
         {
            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

            $studentpaymen->receiptno ='';
            $studentpaymen->paymentreceived ='';
            $studentpaymen->remainingamount ='';
           
            
             if($das){
                $studentpaymen->receiptno = $das->receiptno;
                $studentpaymen->paymentreceived = $das->paymentreceived;
                $studentpaymen->remainingamount = $das->remainingamount;
                
                
            }

         }

      


         $invototal = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$userBranch)->whereMonth('admissionprocesses.sadate',$currentMonth)->sum('invtotal'); 
         
     

         $sumtotal =  $invototal;

         $pamenreceived = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$userBranch)->whereMonth('admissionprocesses.sadate',$currentMonth)->sum('paymentreceived'); 
         

         
          
            $totslreceived = $pamenreceived;

            $remainingamount = $sumtotal - $totslreceived;
                $BranchUser = Auth::user()->branchs;

               $folss = followup::get();
         $userdata = User::where('id',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
           $branchdata = Branch::where('branchname',$BranchUser)->get();
            $ccatall = coursecategory::get();

         

        return view('marketing.admissionprocess.studentsdetails',compact('studentsdata','brnagch','userALl','sumtotal','totslreceived','remainingamount','folss','userdata','cour','sourcedata','branchdata','ccatall'));
    }

    public function fitermarketingtotaladmission(Request $request)
     {

        $userBranch = Auth::user()->branchs;
        $usersId = Auth::user()->id;

          $datesfor = "";
         $namedatas = "";
         $mobdatas = "";
         $coursedatas = "";
         $cmodes = "";
         $sources= "";
         $fsearch = "";
         $asearch = "";
         $bransdata = "";
         $categorydata = "";
          $namesfinds = "";
          $ramesfinds = "";

      if($namedatas = $request->getstudentsnames)
      {
         $folss = followup::get();
         $userdata = User::where('id',$usersId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

       //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$usersId)->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->groupBy('payments.inviceid')->orderBy('admissionprocesses.sadate','DESC')->get(); 
        

          return view('marketing.admissionprocess.filterAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','ramesfinds'));
      }

      elseif($mobdatas = $request->getMobilesno)
      {
         $folss = followup::get();
         $userdata = User::where('id',$usersId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$usersId)->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->groupBy('payments.inviceid')->orderBy('admissionprocesses.sadate','DESC')->get(); 

       

          return view('marketing.admissionprocess.filterAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','ramesfinds'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Admission Date")
         {


            $folss = followup::get();
            $userdata = User::where('id',$usersId)->get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                  $ccatall = coursecategory::get();

               

               $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$usersId)->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])->groupBy('payments.inviceid')->orderBy('payments.studenterno','DESC')->get(); 
               

                return view('marketing.admissionprocess.filterAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats','ramesfinds'));
            }

          elseif($datesfor == "Payment Date")
         {


            $folss = followup::get();
           $userdata = User::where('id',$usersId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$usersId)->whereBetween('payments.paymentdate',[$startdates,$enddats])->groupBy('payments.inviceid')->orderBy('payments.studenterno','DESC')->get(); 
               

                return view('marketing.admissionprocess.filterAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats','ramesfinds'));
            }

         

        

         
         }

      elseif($coursedatas = $request->coursedatas)
      {
         $folss = followup::get();
        $userdata = User::where('id',$usersId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();
            $cstartsdates = $request->cdatestat;
            $cendsdates = $request->cdateend;
         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->orderBy('leads.leaddate','DESC')->get();

         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$usersId)->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('admissionprocesses.sadate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 
         

          return view('marketing.admissionprocess.filterAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates','ramesfinds'));
      }

    


      elseif($sources = $request->sourceSearch)
      {
         $starsdates = $request->sdatestat;
         $enssdates = $request->sdateend;

         $folss = followup::get();
        $userdata = User::where('id',$usersId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admsisource',$sources)->where('admissionprocesses.admissionsusersid',$usersId)->whereBetween('admissionprocesses.sadate',[$starsdates,$enssdates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 
         

          return view('marketing.admissionprocess.filterAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates','ramesfinds'));
      }






      elseif($asearch = $request->AssignedToSearch)
      {
         $asdates = $request->AstartDate;
         $aenddates = $request->AEndDate;

         $folss = followup::get();
        $userdata = User::where('id',$usersId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

  

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$asearch)->where('admissionprocesses.admissionsusersid',$usersId)->whereBetween('admissionprocesses.sadate',[$asdates,$aenddates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 
              
       // $ramesfinds = 0; 
             

                return view('marketing.admissionprocess.filterAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
      }


      elseif($bransdata = $request->branchSearchDatas)
      {
         $bstartdate = $request->BStartDate;
         $benddate = $request->BEnddate;

         $folss = followup::get();
        $userdata = User::where('id',$usersId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        // $namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$bransdata)->whereBetween('admissionprocesses.sadate',[$bstartdate,$benddate])->groupBy('payments.inviceid')->orderBy('payments.studenterno','DESC')->get(); 
               
             

                return view('marketing.admissionprocess.filterAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate','ramesfinds'));
      }


     


      elseif($categorydata = $request->categorysDatas)
      {

         //dd($categorydata);
         $cstartdate = $request->CStartDate;
         $cenddate = $request->CEnddate;

         $folss = followup::get();
         $userdata = User::where('id',$usersId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

            $findcourse = course::where('cat_id',$categorydata)->pluck('id');
           //dd($findcourse);

           /* foreach($findcourse as $courses)
            {
                  $getourses = $courses->coursename;

            }*/

          //  dd($findcourse);

      

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.id','DESC')->get();

         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->where('admissionprocesses.admissionsusersid',$usersId)->whereBetween('admissionprocesses.sadate',[$cstartdate,$cenddate])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 
               
              

                return view('marketing.admissionprocess.filterAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate','ramesfinds'));
      }

   }
    
         public function pendingAdmission()
     {
         $currentMonth = date('m');
         $brnagch = Branch::all();
        $userALl = User::all();

        $userBranc = Auth::user()->id;
        $userBranchs = Auth::user()->branchs;
           $studentsdata = \DB::table('admissionprocesses')->where('admissionsusersid',$userBranc)->whereMonth('admissionprocesses.sadate',$currentMonth)->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 

        foreach($studentsdata as $studentpaymen)
         {
            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

            $studentpaymen->receiptno ='';
            $studentpaymen->paymentreceived ='';
            $studentpaymen->remainingamount ='';
           
            
             if($das){
                $studentpaymen->receiptno = $das->receiptno;
                $studentpaymen->paymentreceived = $das->paymentreceived;
                $studentpaymen->remainingamount = $das->remainingamount;
                
                
            }
        }




  

        $invototal = $studentsdata->sum('invtotal');
        
        

        //dd($invototal);

        $sumtotal = $invototal;
          
         $pamenreceived = 0; 
         

         
        
         
          
            $totslreceived = $pamenreceived;

            $remainingamount = $sumtotal - $totslreceived;
            

              $folss = followup::get();
         $userdata = User::where('id',$userBranc)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranchs)->get();
            $ccatall = coursecategory::get();
            
           // $total = $invototal->sum('invtotal');


        //    dd($sumtotal);

           return view('marketing.admissionprocess.pendingadmission',compact('studentsdata','brnagch','userALl','sumtotal','totslreceived','remainingamount','folss','userdata','cour','sourcedata','branchdata','ccatall'));
        
    }

    public function filtermarketingpendingadmissions(Request $request)
     {

        $userBranch = Auth::user()->branchs;
        $userId = Auth::user()->id;

      $datesfor = "";
      $namedatas = "";
      $mobdatas = "";
      $coursedatas = "";
      $cmodes = "";
      $sources= "";
      $fsearch = "";
      $asearch = "";
      $bransdata = "";
      $categorydata = "";

      if($namedatas = $request->getstudentsnames)
      {
         $folss = followup::get();
         $userdata = User::where('id',$userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

           $namesfinds = \DB::table('admissionprocesses')->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->select('admissionprocesses.*','admissionprocesses.id as aid')->where('admissionprocesses.admissionsusersid',$userId)
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         }
        

           return view('marketing.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }

      elseif($mobdatas = $request->getMobilesno)
      {
         $folss = followup::get();
         $userdata = User::where('id',$userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
         $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.admissionsusersid',$userId)->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         }

       

           return view('marketing.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Admission Date")
         {


            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                  $ccatall = coursecategory::get();

               

               $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.admissionsusersid',$userId)->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         } 
               

                 return view('marketing.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

          elseif($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 

               $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.admissionsusersid',$userId)->whereBetween('payments.paymentdate',[$startdates,$enddats])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         } 
               

                 return view('marketing.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

         

        

         
         }

      elseif($coursedatas = $request->coursedatas)
      {
         $folss = followup::get();
         $userdata = User::where('id',$userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();
            $cstartsdates = $request->cdatestat;
            $cendsdates = $request->cdateend;
        

        // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('admissionprocesses.sadate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 

             $namesfinds = \DB::table('admissionprocesses')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->where('admissionprocesses.admissionsusersid',$userId)->whereBetween('admissionprocesses.sadate',[$cstartsdates,$cendsdates])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         } 
         

           return view('marketing.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
      }


      elseif($sources = $request->sourceSearch)
      {
         $starsdates = $request->sdatestat;
         $enssdates = $request->sdateend;

         $folss = followup::get();
         $userdata = User::where('id',$userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        

         
           $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.admissionsusersid',$userId)->where('admissionprocesses.admsisource',$sources)->whereBetween('admissionprocesses.sadate',[$starsdates,$enssdates])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists(function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         } 
         

           return view('marketing.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
      }





      elseif($asearch = $request->AssignedToSearch)
      {
         $asdates = $request->AstartDate;
         $aenddates = $request->AEndDate;

         $folss = followup::get();
         $userdata = User::where('id',$userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

  

      
          $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.admissionsusersid',$userId)->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('admissionprocesses.sadate',[$asdates,$aenddates])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         }
               
             

                 return view('marketing.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
      }


      elseif($bransdata = $request->branchSearchDatas)
      {
         $bstartdate = $request->BStartDate;
         $benddate = $request->BEnddate;

         $folss = followup::get();
         $userdata = User::where('id',$userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

       

          $namesfinds = \DB::table('admissionprocesses')->where('admissionprocesses.admissionsusersid',$userId)->whereBetween('admissionprocesses.sadate',[$bstartdate,$benddate])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         }
               
             

                 return view('marketing.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
      }


      elseif($categorydata = $request->categorysDatas)
      {

         //dd($categorydata);
         $cstartdate = $request->CStartDate;
         $cenddate = $request->CEnddate;

         $folss = followup::get();
         $userdata = User::where('id',$userId)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

            $findcourse = course::where('cat_id',$categorydata)->pluck('id');
           

         $namesfinds = \DB::table('admissionprocesses')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$userId)->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('admissionprocesses.sadate',[$cstartdate,$cenddate])->select('admissionprocesses.*','admissionprocesses.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid');
                                                            })->get(); 


                       

                         foreach($namesfinds as $studentpaymen)
                         {
                            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

                            $studentpaymen->receiptno ='';
                            $studentpaymen->paymentreceived ='';
                            $studentpaymen->remainingamount ='';
                           
                            
                             if($das){
                                $studentpaymen->receiptno = $das->receiptno;
                                $studentpaymen->paymentreceived = $das->paymentreceived;
                                $studentpaymen->remainingamount = $das->remainingamount;
                                
                                
                            }

                         } 
               
              

                 return view('marketing.admissionprocess.filterPendingAdmission',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
      }

   }

      public function payment($id,admissionprocess $admissionprocess)
    {

       $paymentdetails = admissionprocess::find($id);
         $branc = Branch::all();
         $installmentfees = admissionprocessinstallmentfees::where('invoid',$id)->where('status',0)->orderBy('id','DESC')->get();
         $psource = PaymentSource::all();
        return view('marketing.payments.create',compact('paymentdetails','branc','installmentfees','psource'));
    }


  

    public function paymentreceipt($id)
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
         

        

         /*$payments = payment::where('inviceid',$id)->first();*/

        // dd($payments);
      //   dd($payments);

        
        

        return view('marketing.admissionprocess.paymentreceipt',compact('aprocess','invvcoursed','univCourse','paymentdata','makepayment','installdata','selectID'));
    }
}
