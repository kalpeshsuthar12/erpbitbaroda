<?php

namespace App\Http\Controllers;


use App\ReAdmission;
use App\Readmissioncourses;
use App\readmissioninstallmentfees;
use App\admissionprocess;
use App\payment;
use App\course;
use App\Branch;
use App\UnviersitiesCategory;
use App\Tax;
use App\User;
use App\PaymentSource;
use Illuminate\Http\Request;
use Auth;
use DB;

class AdminReadmissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $brnagch = Branch::all();
        $userALl = User::all();
             $currentMonth = date('m');
         $studentsdata = ReAdmission::select('re_admissions.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->whereMonth('re_admissions.rsadate',$currentMonth)->groupBy('payments.reinviceid')->orderBy('payments.id','DESC')->get(); 

       
         foreach($studentsdata as $studentpaymen)
         {
            $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','DESC')->first();

            $studentpaymen->receiptno ='';
            $studentpaymen->paymentreceived ='';
            $studentpaymen->remainingamount ='';
           
            
             if($das){
                $studentpaymen->receiptno = $das->receiptno;
                $studentpaymen->paymentreceived = $das->paymentreceived;
                $studentpaymen->remainingamount = $das->remainingamount;
                
                
            }

         }

        return view('admin.readmissions.manage',compact('studentsdata','brnagch','userALl'));
    }

     public function getOldAdmission(Request $request)
    {
        $mobileno = $request->Mobilrbneo;

        //dd($mobileno);
        
         $studentsdata = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.sphone',$mobileno)->orWhere('admissionprocesses.swhatsappno',$mobileno)->groupBy('payments.inviceid')->get(); 

       
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

         //dd($installdata);
        return view('admin.readmissions.filterwithreadmissions',compact('studentsdata','mobileno'));   
    }

      public function getreadmissiondetails(Request $request)
    {
        $studentsids = $request->studentsid;

        $admissiondetails = admissionprocess::find($studentsids);
         $paymentsdetails = payment::where('inviceid',$studentsids)->groupBy('inviceid')->first();
        $course = course::get(); 
         $ucats = UnviersitiesCategory::all();
         $taxesna = Tax::get();

        return view('admin.readmissions.create',compact('admissiondetails','course','ucats','taxesna','paymentsdetails'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $userBranhcs = Auth::user()->branchs;
            $ds = User::where('usercategory','Centre Manager')->where('branchs',$userBranhcs)->first();
        
        $userId = $ds->id;
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

            

            $ReAdmissionmodel = new ReAdmission();
            $ReAdmission = $ReAdmissionmodel->create([
            'rstudents'=> $newstudents,
            'rsdobs'=> $birthdate,
            'rsemails'=> $email,
            'rsbrnanch'=> $brnach,
            'rstobranches'=> $request->tobranchessw,
            'rserno'=> $erno,
            'rsjerno'=> $sjerno,
            'rmjerno'=> $mjerno,
            'rwgerno'=> $wageron,
            'rbitolerno'=> $bitolerno,
            'rcvrublerno'=> $cvrublerno,
            'rcvrukherno'=> $cvrukherno,
            'rrntuerno'=> $rntuerno,
            'rmanipalerno'=> $manipalerno,
            'rsphone'=> $mobile,
            'rswhatsappno'=> $stuwhatsapp,
            'rsadate'=> $admidate,
            'rsstreet'=> $studentstreet,
            'rscity'=> $studentcity,
            'rsstate'=> $studentstate,
            'rszipcode'=> $studentzipcode,
            'rspreferrabbletime'=> $ptime,
            'refeassignto'=> $refassignto,
            'rreferfrom'=> $refrom,
            'rrefername'=> $refename,
            'rsremarknotes'=> $rnote,
            'rIbranchs'=> $branchdata,
            'rInvoiceno'=> $request->invno,
            'rIsjno'=> $sjinvno,
            'rImjno'=> $mjinvno,
            'rIwgno'=> $waginvno,
            'rIbitolno'=> $bitolinvno,
            'rIcvrublno'=> $cvrublinvno,
            'rIcvrukhno'=> $cvrukhinvno,
            'rIrntuno'=> $rntuinvno,
            'rImanipalno'=> $manipalinvno,
            'rinvdate'=> $idate,
            'rduedate'=> $ddate,
            'ripaymentmodes'=> $pmode,
            'ridiscounttypes'=> $dtype,
            'risubtotal'=> $subto,
            'ridiscount'=> $discoun,
            'ritax'=> $request->tax,
            'rinvtotal'=> $tot,
            'ruserid' => $userId,
            'rgstprices' => $request->gstprice,
            'roldtotalpice' => $oldpricess,
            'radmissionstatus'=> 'New Student',
            'radmissionsusersid'=> '',
            'rdiscounttotal'=> $request->discounttotal,
            'radmsisource'=> $request->admisources,
            'rfnames'=> $request->fathersnames,
            'rmnames'=> $request->mothersname,
            'rsuniversities'=> $request->universitiesss,
            
            ]);

            $invoicesid = $ReAdmission->id;
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

                    if($ReAdmission->rsuniversities == 'BIT')
                    {
                           for($i=0; $i < (count($coursesdata)); $i++)
                        {
                                    $Readmissioncourses = new Readmissioncourses([
                                    
                                    'reinvid' => $invoicesid,
                                    'recourseid'   => $coursesdata[$i],
                                    'resubcourses'   => $subcoursesdata[$i],
                                    'recoursemode'   => $csmode[$i],
                                    'recourseprice'   => $courseprice[$i],
                                    'restudentsin'   => 'New Student',
                                    
                                ]);
                                $Readmissioncourses->save();
                        }
                    }

                    else

                    {
                          for($i=0; $i < (count($uniccourse)); $i++)
                        {
                                    $Readmissioncourses = new Readmissioncourses([
                                    
                                    'reinvid' => $invoicesid,
                                    'reunivecoursid'   => $uniccourse[$i],
                                    'readmissionfor'   => $adforss[$i],
                                    'reunoverfeess'   => $ufees[$i],
                                    'restudentsin'   => 'New Student',
                                    
                                ]);
                                $Readmissioncourses->save();
                        }
                    }
                    for($k=0; $k <(count($installdate)); $k++)
                    {
                        $readmissioninstallmentfees = new readmissioninstallmentfees([
                            
                            'reinvoid' => $invoicesid,
                            'reinvoicedate'   => $installdate[$k],
                            'reinstallmentamount'   => $installprice[$k],
                            'rependinamount'   => $pamount[$k],

                        ]);

                         $readmissioninstallmentfees->save();  
                    }


            return redirect('/admin-create-re-payment/'.$invoicesid);

        }

        else
        {
              $ReAdmissionmodel = new ReAdmission();
            $ReAdmission = $ReAdmissionmodel->create([
            'rstudents'=> $newstudents,
            'rsdobs'=> $birthdate,
            'rsemails'=> $email,
            'rsbrnanch'=> $brnach,
            'rstobranches'=> $request->tobranchessw,
            'rserno'=> $erno,
            'rsjerno'=> $sjerno,
            'rmjerno'=> $mjerno,
            'rwgerno'=> $wageron,
            'rbitolerno'=> $bitolerno,
            'rcvrublerno'=> $cvrublerno,
            'rcvrukherno'=> $cvrukherno,
            'rrntuerno'=> $rntuerno,
            'rmanipalerno'=> $manipalerno,
            'rsphone'=> $mobile,
            'rswhatsappno'=> $stuwhatsapp,
            'rsadate'=> $admidate,
            'rsstreet'=> $studentstreet,
            'rscity'=> $studentcity,
            'rsstate'=> $studentstate,
            'rszipcode'=> $studentzipcode,
            'rspreferrabbletime'=> $ptime,
            'refeassignto'=> $refassignto,
            'rreferfrom'=> $refrom,
            'rrefername'=> $refename,
            'rsremarknotes'=> $rnote,
            'rIbranchs'=> $branchdata,
            'rInvoiceno'=> $request->invno,
            'rIsjno'=> $sjinvno,
            'rImjno'=> $mjinvno,
            'rIwgno'=> $waginvno,
            'rIcvrublno'=> $bitolinvno,
            'rIcvrukhno'=> $cvrublinvno,
            'Icvrukhno'=> $cvrukhinvno,
            'rIrntuno'=> $rntuinvno,
            'rImanipalno'=> $manipalinvno,
            'rinvdate'=> $idate,
            'rduedate'=> $ddate,
            'ripaymentmodes'=> $pmode,
            'ridiscounttypes'=> $dtype,
            'risubtotal'=> $subto,
            'ridiscount'=> $discoun,
            'ritax'=> $request->tax,
            'rinvtotal'=> $tot,
            'ruserid' => $userId,
            'rgstprices' => $request->gstprice,
            'roldtotalpice' => $oldpricess,
            'radmissionstatus'=> 'New Student',
            'radmissionsusersid'=> '',
            'rdiscounttotal'=> $request->discounttotal,
            'radmsisource'=> $request->admisources,
            'rfnames'=> $request->fathersnames,
            'rmnames'=> $request->mothersname,
            'rsuniversities'=> $request->universitiesss,
            
            ]);

            $invoicesid = $ReAdmission->id;
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

                   if($ReAdmission->rsuniversities == 'BIT')
                    {
                           for($i=0; $i < (count($coursesdata)); $i++)
                        {
                                    $Readmissioncourses = new Readmissioncourses([
                                    
                                    'reinvid' => $invoicesid,
                                    'recourseid'   => $coursesdata[$i],
                                    'resubcourses'   => $subcoursesdata[$i],
                                    'recoursemode'   => $csmode[$i],
                                    'recourseprice'   => $courseprice[$i],
                                    'restudentsin'   => 'New Student',
                                    
                                ]);
                                $Readmissioncourses->save();
                        }
                    }

                    else

                    {
                          for($i=0; $i < (count($uniccourse)); $i++)
                        {
                                    $Readmissioncourses = new Readmissioncourses([
                                    
                                    'reinvid' => $invoicesid,
                                    'reunivecoursid'   => $uniccourse[$i],
                                    'readmissionfor'   => $adforss[$i],
                                    'reunoverfeess'   => $ufees[$i],
                                    'restudentsin'   => 'New Student',
                                    
                                ]);
                                $Readmissioncourses->save();
                        }
                    }

                 

             return redirect('/admin-create-re-payment/'.$invoicesid);
                    

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function viewinvoice($id,ReAdmission $reAdmission)
    {
        $aprocess = ReAdmission::find($id);
       
          

        $invvcoursed = DB::select('SELECT * FROM  re_admissions a, courses c, readmissioncourses k WHERE c.id = k.recourseid AND a.id = k.reinvid AND a.id = "'.$id.'" ');
       

        $univCourse = DB::select('SELECT * FROM  re_admissions a, courses c, readmissioncourses k WHERE c.id = k.reunivecoursid AND a.id = k.reinvid AND a.id = "'.$id.'" ');

         $installmentfees = DB::select("SELECT * FROM readmissioninstallmentfees WHERE reinvoid = '$id' ORDER BY id DESC");
         
        return view('admin.readmissions.reinvoice',compact('aprocess','invvcoursed','installmentfees','univCourse'));
    }

    public function admisionforms($id,ReAdmission $reAdmission)
    {
        $aprocess = ReAdmission::find($id);
       
          

        $invvcoursed = DB::select('SELECT * FROM  re_admissions a, courses c, readmissioncourses k WHERE c.id = k.recourseid AND a.id = k.reinvid AND a.id = "'.$id.'" ');
       

        $univCourse = DB::select('SELECT * FROM  re_admissions a, courses c, readmissioncourses k WHERE c.id = k.reunivecoursid AND a.id = k.reinvid AND a.id = "'.$id.'" ');

         $installmentfees = DB::select("SELECT * FROM readmissioninstallmentfees WHERE reinvoid = '$id' ORDER BY id DESC");
         
        return view('admin.readmissions.admissionform',compact('aprocess','invvcoursed','installmentfees','univCourse'));
    }

     public function repayment($id,ReAdmission $reAdmission)
     {
            $userBranch = Auth::user()->branchs;

             $paymentdetails = ReAdmission::find($id);
             $branc = Branch::where('branchname',$userBranch)->get();
             $psource = PaymentSource::all();
             $installmentfees = readmissioninstallmentfees::where('reinvoid',$id)->where('restatus',0)->orderBy('id','DESC')->get();
      
             return view('admin.readmissions.makepayment',compact('paymentdetails','branc','installmentfees','psource'));
     }

     public function repaymentstore($id,Request $request)
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
            'reinviceid'=> $id,
            'totalamount'=> $tmamount,
            'paymentreceived'=> $preceived,
            'transactionsids'=> $request->transactionsids,
            'remainingamount'=> $request->ramount,
            'paymentdate'=> $request->paymentdate,
            'nexamountdate'=> $request->remindersdates,
            'paymentmode'=> $request->paymentmode,
            'bankname'=> $request->bankname,
            'chequeno'=> $request->chequeno,
            'chequedate'=> $request->chequedate,
            'chequetype'=> $request->chequetype,
            'remarknoe'=> $request->remarknote,
            'userid'=> $userId,
            'studentsid'=> $request->students,
            'restudentsernos'=> $request->resernos,
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
            'reinstallmentid'=> $request->installid,
        ]);


        $insid = $request->installid;

        $paymentid = $payment->id;

        $updatenew = readmissioninstallmentfees::find($insid);

        if($updatenew)
       {
            $updatenew->restatus = 1;
            $updatenew->save();
        }

        return redirect('/admins-re-payment-recipt/'.$paymentid)->with('success','Payment Successfully Done!!!');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ReAdmission  $reAdmission
     * @return \Illuminate\Http\Response
     */
    public function paymentreceipt($id,ReAdmission $reAdmission)
    {
        $selectID = payment::find($id);
            $newId = $selectID->reinviceid;

        $aprocess = ReAdmission::find($newId);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  re_admissions a, courses c, readmissioncourses k WHERE  c.id = k.recourseid AND a.id = k.reinvid AND a.id = "'.$newId.'" ');

         $installmentfees = DB::select("SELECT * FROM readmissioninstallmentfees WHERE reinvoid = '$id' ORDER BY id DESC");

         $univCourse = DB::select('SELECT * FROM  re_admissions a, courses c, readmissioncourses k WHERE c.id = k.reunivecoursid AND a.id = k.reinvid AND a.id = "'.$newId.'" ');

         $paymentdata = payment::where('reinviceid',$newId)->first();

         $makepayment = DB::select('SELECT * FROM  re_admissions a, payments p WHERE a.id = p.reinviceid AND a.id = "'.$newId.'" ');

         //dd($makepayment);

         $installdata = readmissioninstallmentfees::leftJoin('payments', 'payments.reinstallmentid', '=', 'readmissioninstallmentfees.id')->where('readmissioninstallmentfees.reinvoid',$newId)->get();        
       
        

        return view('admin.readmissions.repaymentreceipt',compact('aprocess','invvcoursed','univCourse','paymentdata','makepayment','installdata'));
    }

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
