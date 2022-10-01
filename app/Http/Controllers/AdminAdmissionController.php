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
use App\admissionprocess;
use App\admissionprocesscourses;
use App\admissionprocessinstallmentfees;
use App\coursebunchlist;
use App\coursespecializationlist;
use App\UnviersitiesCategory;
use App\universititiesfeeslist;
use App\ReAdmission;
use App\Source;
use App\followup;
use App\PaymentSource;
use App\coursecategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use DB;



class AdminAdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $invoicesdata =DB::table('admissionprocesses')
                ->join('branches', 'branches.id', '=', 'admissionprocesses.Ibranchs')
                ->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                ->select('admissionprocesses.id','admissionprocesses.studentname','branches.branchname','admissionprocesses.created_at','admissionprocesses.studentname','admissionprocesses.semails','admissionprocesses.sphone','admissionprocesses.sdobs','admissionprocesses.serno','admissionprocesses.Invoiceno','admissionprocesses.invtotal','payments.paymentreceived','payments.remainingamount','admissionprocesses.status')
                ->orderBy('payments.id','DESC')
                ->get();

                return view('admin.invoice.manage',compact('invoicesdata'));
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

        /* return view('admin.admissionprocess.create',compact('alb','cours','leadsdata','directstudentsdata','studentdetails','branchdetails','course','taxesna'));*/
        return view('admin.admissionprocess.create',compact('alb','cours','leadsdata','directstudentsdata','studentdetails','branchdetails','course','taxesna','ucats'));
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


            return redirect('/admin-create-payment/'.$invoicesid);

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

                 

             return redirect('/admin-create-payment/'.$invoicesid);
                    

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
 
    public function show($id)
    {

         $aprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$id.'" ');
       

        $univCourse = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.univecoursid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");
         
        return view('admin.admissionprocess.generalinvoices',compact('aprocess','invvcoursed','installmentfees','univCourse'));
    }

    public function admissionform($id)
    {


        $aprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $univCourse = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.univecoursid AND a.id = k.invid AND a.id = "'.$id.'" ');

         //$installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");

         //$paymentdata = payment::where('inviceid',$id)->get();

        
        

        return view('admin.admissionprocess.admissionform',compact('aprocess','invvcoursed','univCourse'));

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

        
        

        return view('admin.invoice.paymentreceipt',compact('aprocess','invvcoursed','univCourse','paymentdata','makepayment','installdata','selectID'));

    }


    public function payment($id,admissionprocess $admissionprocess)
    {

      

        $paymentdetails = admissionprocess::find($id);
        $branc = Branch::all();
        $installmentfees = admissionprocessinstallmentfees::where('invoid',$id)->where('status',0)->orderBy('id','DESC')->get();
        $psource = PaymentSource::all();
        
        return view('admin.invoice.createpaymentsvie',compact('paymentdetails','branc','installmentfees','psource'));
    }


     public function changeemi($id)
    {

       $adm = admissionprocess::find($id); 
    $latestpaymentdata = payment::where('inviceid',$id)->orderBy('id','DESC')->first();
    $getemidata = payment::where('inviceid',$id)->get();
    foreach($getemidata as $emis)
       {
           $getinstallmentdata = admissionprocessinstallmentfees::where('id',$emis->installmentid)->get();
       }

     /*  $getinstallmentdata = admissionprocessinstallmentfees::select('admissionprocessinstallmentfees.*')->join('payments','payments.installmentid','=','admissionprocessinstallmentfees.id')->whereExist('admissionprocessinstallmentfees.id',$id)->get();*/

       //dd($getinstallmentdata);


     /*   dd($getinstallmentdata);*/

        return view('admin.invoice.changeemi',compact('getinstallmentdata','adm','latestpaymentdata'));
    }
     public function changenewemi($id,Request $request)
    {                        /* dd($request->all());*/      
                                $bids = $request->emimainid;
                            //        dd($bids);
                                $deles = admissionprocessinstallmentfees::where('invoid',$bids)->get();
                                $deles->each->delete();
                                  
                               


                                $idate = $request->installmentdate;
                                $iprice = $request->installmentprice;
                                $ipa = $request->pendingamount;


                                for($i=0; $i < (count($idate)); $i++)
                                        {
                                            
                                             $dakmsm = admissionprocessinstallmentfees::updateOrCreate(['invoicedate' => $idate[$i],'invoid' => $id,'installmentamount' => $iprice[$i],'pendinamount' => $ipa[$i] ]);

                                          
                  


                                        }

                                    DB::statement('update admissionprocessinstallmentfees a inner join payments c on a.invoid = c.inviceid and  a.installmentamount = c.totalamount set a.status = 1, c.installmentid = a.id;');

   
            return redirect('/admin-make-repayment/'.$id)->with('success','EMI Successfully Changed !!');

    }

   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
         $userBranch = Auth::user()->branchs;

         $studentsdata = admissionprocess::where('stobranches',$userBranch)->get();
         //dd($userBranch);
        return view('admin.admission.studentsdetails',compact('studentsdata'));
    }

    public function paymentstore(Request $request,$id,admissionprocess $admissionprocess,payment $payment)
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
        $elrecno = "0";

        $sjernocs = "0";
        $mjernocs = "0";
        $wagernocs = "0";
        $bitolernocs = "0";
        $cvrublernocs = "0";
        $cvrukhernocs = "0";
        $rntuernocs = "0";
        $manipalernocs = "0";
        $elernocs = "0";
       

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
        
        elseif($receptsno[0] == 'BITEL')
        {
            $elrecno = $receptsno[1];
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
        
        elseif($newerno[0] == 'BITEL')
        {
            $elernocs = $newerno[3];
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
            'transactionsids'=> $request->transactionsids,
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
            'elrecpno'=> $elrecno,
            'bitolrecpno'=> $bitolrecno,
            'cvrublrecpno'=> $cvrublrecno,
            'cvrukhrecpno'=> $cvrukhrecno,
            'rnturecpno'=> $rnturecno,
            'manipalrecpno'=> $manipalrecno,
            'studenterno'=> $ernos,
            'sjerno'=> $sjernocs,
            'mjerno'=> $mjernocs,
            'wgerno'=> $wagernocs,
            'elernos'=> $elernocs,
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
        



        return redirect('/admin-paymentreceipt/'.$paymentid)->with('success','Payment Successfully Done!!!');
    }


    public function pendingamountstores(Request $request,$id,admissionprocess $admissionprocess,payment $payment)
    {

        $userId = Auth::user()->id;
        $studentsdata = $request->students;

        $paymentmodel = new payment();
        $payment = $paymentmodel->create([
            'inviceid'=> $id,
            'totalamount'=> $request->totalamount,
            'paymentreceived'=> $request->paymentrecieved,
            'studentadmissiionstatus'=> 'Old Student',
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
            'branchs'=> $request->branchs,
        ]);

        $updatepayem = payment::where('inviceid',$id)->first();
        $updatepayem->remainingamount = 0;
        $updatepayem->studentadmissiionstatus = 'Old Student';
        $updatepayem->save();

        


        //$paymentid = $payment->id;


        return redirect('/admin-paymentreceipt/'.$id)->with('success','Payment Successfully Done!!!');

    }

    public function admission()
    {
        $alb = branch::get();
        $cours = course::get();
        $leadsdata = leads::get();
         $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();
        return view('admin.admission.create',compact('alb','cours','leadsdata','branchdetails','course','taxesna'));

    }


    public function storeadmission(Request $request)
    {
       $userId = Auth::user()->id;
     

         /*$sjinvno = "0";
        $mjinvno = "0";
        $waginvno = "0";
        $bitolinvno = "0";
        $cvrublinvno = "0";
        $cvrukhinvno = "0";
        $rntuinvno = "0";
        $manipalinvno = "0";*/

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
        

        $enrollno = explode("/",$erno);
       

        if($enrollno[0] == 'BITSJ')
        {
            $sjerno = $enrollno[3];

            
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


            return redirect('/admin-create-payment/'.$invoicesid);

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

             return redirect('/admin-create-payment/'.$invoicesid);
                    

        }
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
    public function destroy()
    {
        $userBranch = Auth::user()->branchs;

       // dd($userBranch);
       
     
        
        $brnagch = Branch::all();
        $userALl = User::all();
             $currentMonth = date('m');
         /*$studentsdata = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->whereMonth('admissionprocesses.sadate',$currentMonth)->orderBy('admissionprocesses.sadate','DESC')->groupBy('payments.inviceid')->get(); */
         
         $studentsdata = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->whereMonth('payments.paymentdate',$currentMonth)->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 


       
         foreach($studentsdata as $studentpaymen)
         {
            $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

            $studentpaymen->receiptno ='';
            $studentpaymen->paymentreceived ='';
            $studentpaymen->remainingamount ='';
           
            
             if($das){
                $studentpaymen->receiptno = $das->receiptno;
                $studentpaymen->paymentreceived = $das->paymentreceived;
                $studentpaymen->remainingamount = $das->remainingamount;
                
                
            }

         }

          $newstudentsdata = ReAdmission::select('re_admissions.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->whereMonth('re_admissions.rsadate',$currentMonth)->where('re_admissions.rstobranches',$userBranch)->orderBy('payments.id','ASC')->groupBy('payments.reinviceid')->get(); 

       
         foreach($newstudentsdata as $studentpaymen)
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


         $invototal = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereMonth('admissionprocesses.sadate',$currentMonth)->sum('invtotal'); 
         
         $retotal = ReAdmission::select('re_admissions.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->whereMonth('re_admissions.rsadate',$currentMonth)->sum('rinvtotal'); 
          
         $sumtotal =  $invototal +  $retotal;

         $pamenreceived = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereMonth('admissionprocesses.sadate',$currentMonth)->sum('paymentreceived'); 
         

         
         $repaymreceived = ReAdmission::select('re_admissions.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->whereMonth('re_admissions.rsadate',$currentMonth)->sum('paymentreceived'); 
         
          
            $totslreceived = $pamenreceived + $repaymreceived;

            $remainingamount = $sumtotal - $totslreceived;

             $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
           $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        return view('admin.admissionprocess.studentsdetails',compact('studentsdata','brnagch','userALl','newstudentsdata','sumtotal','totslreceived','remainingamount','folss','userdata','cour','sourcedata','branchdata','ccatall'));
    }

    public function pendingamount()
    {
    
        $pendamount = admissionprocess::select('admissionprocesses.*', 'payments.remainingamount','payments.paymentreceived')->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('payments.remainingamount','!=',0)
         ->get();
                      //  dd($pendamount);

           // dd($pendamount);


        return view('admin.invoice.pendingamount',compact('pendamount'));

    }

     public function getpendingamount($id)
    {

        //$paymentdetails = admissionprocess::find($id);
        $paymentdetails = admissionprocess::find($id);
        $paymentsse = payment::where('inviceid',$id)->sum('remainingamount');



       // dd($paymentsse);
        return view('admin.invoice.getpendingamount',compact('paymentdetails','paymentsse'));
    }

    public function branchwiseAdmission(Request $request)
    {

        $userBranch = Auth::user()->branchs;

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
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

       //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->where('payments.studenterno','!=',null)->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 
            foreach($namesfinds as $studentpaymen)
               {
                  $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                  $studentpaymen->receiptno ='';
                  $studentpaymen->paymentreceived ='';
                  $studentpaymen->remainingamount ='';
                 
                  
                   if($das){
                      $studentpaymen->receiptno = $das->receiptno;
                      $studentpaymen->paymentreceived = $das->paymentreceived;
                      $studentpaymen->remainingamount = $das->remainingamount;
                      
                      
                  }

               } 

               $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('re_admissions.rstobranches',$userBranch)->Where('re_admissions.rstudents', 'like', '%' .$namedatas. '%')->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
         foreach($newstudentsdata as $studentpaymen)
         {
            $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

            $studentpaymen->receiptno ='';
            $studentpaymen->paymentreceived ='';
            $studentpaymen->remainingamount ='';
           
            
             if($das){
                $studentpaymen->receiptno = $das->receiptno;
                $studentpaymen->paymentreceived = $das->paymentreceived;
                $studentpaymen->remainingamount = $das->remainingamount;
                
                
            }

         }

        

          return view('admin.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','ramesfinds'));
      }

      elseif($mobdatas = $request->getMobilesno)
      {
         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->where('payments.studenterno','!=',null)->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

         foreach($namesfinds as $studentpaymen)
               {
                  $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                  $studentpaymen->receiptno ='';
                  $studentpaymen->paymentreceived ='';
                  $studentpaymen->remainingamount ='';
                 
                  
                   if($das){
                      $studentpaymen->receiptno = $das->receiptno;
                      $studentpaymen->paymentreceived = $das->paymentreceived;
                      $studentpaymen->remainingamount = $das->remainingamount;
                      
                      
                  }

               } 


          $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('re_admissions.rstobranches',$userBranch)->Where('re_admissions.rsphone', $mobdatas)->orwhere('re_admissions.rswhatsappno',$mobdatas)->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
                  foreach($newstudentsdata as $studentpaymen)
                  {
                     $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  }

       

          return view('admin.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','ramesfinds'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         

          if($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->whereBetween('payments.paymentdate',[$startdates,$enddats])->groupBy('payments.inviceid')->orderBy('payments.studenterno','DESC')->get(); 
              
              $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->whereBetween('payments.paymentdate',[$startdates,$enddats])->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

              foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 

                  $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
                     foreach($newstudentsdata as $studentpaymen)
                     {
                        $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                        $studentpaymen->receiptno ='';
                        $studentpaymen->paymentreceived ='';
                        $studentpaymen->remainingamount ='';
                       
                        
                         if($das){
                            $studentpaymen->receiptno = $das->receiptno;
                            $studentpaymen->paymentreceived = $das->paymentreceived;
                            $studentpaymen->remainingamount = $das->remainingamount;
                            
                            
                        }

                     }

               
               

                return view('admin.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

         

        

         
         }

      elseif($coursedatas = $request->coursedatas)
      {
         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();
            $cstartsdates = $request->cdatestat;
            $cendsdates = $request->cdateend;
         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->orderBy('leads.leaddate','DESC')->get();

         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->where('payments.studenterno','!=',null)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

         foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 

            $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('readmissioncourses','readmissioncourses.reinvid','=','readmissioncourses.id')->join('payments', 'payments.inviceid', '=', 'readmissioncourses.id')->where('re_admissions.rstobranches',$userBranch)->where('readmissioncourses.recourseid',$coursedatas)->orWhere('readmissioncourses.reunivecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

       
                     foreach($newstudentsdata as $studentpaymen)
                     {
                        $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                        $studentpaymen->receiptno ='';
                        $studentpaymen->paymentreceived ='';
                        $studentpaymen->remainingamount ='';
                       
                        
                         if($das){
                            $studentpaymen->receiptno = $das->receiptno;
                            $studentpaymen->paymentreceived = $das->paymentreceived;
                            $studentpaymen->remainingamount = $das->remainingamount;
                            
                            
                        }

                     }

         

          return view('admin.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
      }

    


      elseif($sources = $request->sourceSearch)
      {
         $starsdates = $request->sdatestat;
         $enssdates = $request->sdateend;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admsisource',$sources)->where('admissionprocesses.stobranches',$userBranch)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 


           foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 


                  $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('re_admissions.radmsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
                     foreach($newstudentsdata as $studentpaymen)
                     {
                        $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                        $studentpaymen->receiptno ='';
                        $studentpaymen->paymentreceived ='';
                        $studentpaymen->remainingamount ='';
                       
                        
                         if($das){
                            $studentpaymen->receiptno = $das->receiptno;
                            $studentpaymen->paymentreceived = $das->paymentreceived;
                            $studentpaymen->remainingamount = $das->remainingamount;
                            
                            
                        }

                     }
         

          return view('admin.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
      }






      elseif($asearch = $request->AssignedToSearch)
      {
         $asdates = $request->AstartDate;
         $aenddates = $request->AEndDate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

  

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$asearch)->where('admissionprocesses.stobranches',$userBranch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

          foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 
          $newstudentsdata =""; 
               
             

                return view('admin.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
      }


      elseif($bransdata = $request->branchSearchDatas)
      {
         $bstartdate = $request->BStartDate;
         $benddate = $request->BEnddate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

        // $namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$bransdata)->where('payments.studenterno','!=',null)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 

          foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 


                  $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('re_admissions.rstobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get(); 

       
                     foreach($newstudentsdata as $studentpaymen)
                     {
                        $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                        $studentpaymen->receiptno ='';
                        $studentpaymen->paymentreceived ='';
                        $studentpaymen->remainingamount ='';
                       
                        
                         if($das){
                            $studentpaymen->receiptno = $das->receiptno;
                            $studentpaymen->paymentreceived = $das->paymentreceived;
                            $studentpaymen->remainingamount = $das->remainingamount;
                            
                            
                        }

                     }
               
             

                return view('admin.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
      }


      

      elseif($categorydata = $request->categorysDatas)
      {

         //dd($categorydata);
         $cstartdate = $request->CStartDate;
         $cenddate = $request->CEnddate;

         $folss = followup::get();
         $userdata = User::where('branchs',$userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where('branchname',$userBranch)->get();
            $ccatall = coursecategory::get();

            $findcourse = course::where('cat_id',$categorydata)->pluck('id');
            $susfindcourse = course::where('cat_id',$categorydata)->pluck('byuniversitites');
          
         if($susfindcourse = 'BIT Institute')
                  {
                    

         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereIn('admissionprocesscourses.courseid',$findcourse)->where('admissionprocesses.stobranches',$userBranch)->where('payments.studenterno','!=',null)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 


         foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 


                  $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('readmissioncourses','readmissioncourses.reinvid','=','re_admissions.id')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('re_admissions.rstobranches',$userBranch)->whereIn('readmissioncourses.recourseid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get();
       
                     foreach($newstudentsdata as $studentpaymen)
                     {
                        $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                        $studentpaymen->receiptno ='';
                        $studentpaymen->paymentreceived ='';
                        $studentpaymen->remainingamount ='';
                       
                        
                         if($das){
                            $studentpaymen->receiptno = $das->receiptno;
                            $studentpaymen->paymentreceived = $das->paymentreceived;
                            $studentpaymen->remainingamount = $das->remainingamount;
                            
                            
                        }

                     }
               
              

                return view('admin.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate','ramesfinds'));
                
                  }
                  
                  
                  else
                  {
                      
                      
         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereIn('admissionprocesscourses.univecoursid',$findcourse)->where('admissionprocesses.stobranches',$userBranch)->where('payments.studenterno','!=',null)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get(); 


         foreach($namesfinds as $studentpaymen)
                  {
                     $das = payment::where('inviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                     $studentpaymen->receiptno ='';
                     $studentpaymen->paymentreceived ='';
                     $studentpaymen->remainingamount ='';
                    
                     
                      if($das){
                         $studentpaymen->receiptno = $das->receiptno;
                         $studentpaymen->paymentreceived = $das->paymentreceived;
                         $studentpaymen->remainingamount = $das->remainingamount;
                         
                         
                     }

                  } 


                  $newstudentsdata = ReAdmission::select('re_admissions.*','payments.*','re_admissions.id as aid')->join('readmissioncourses','readmissioncourses.reinvid','=','re_admissions.id')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('re_admissions.rstobranches',$userBranch)->whereIn('readmissioncourses.reunivecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->groupBy('payments.reinviceid')->get();
       
                     foreach($newstudentsdata as $studentpaymen)
                     {
                        $das = payment::where('reinviceid',$studentpaymen->aid)->orderBy('id','ASC')->first();

                        $studentpaymen->receiptno ='';
                        $studentpaymen->paymentreceived ='';
                        $studentpaymen->remainingamount ='';
                       
                        
                         if($das){
                            $studentpaymen->receiptno = $das->receiptno;
                            $studentpaymen->paymentreceived = $das->paymentreceived;
                            $studentpaymen->remainingamount = $das->remainingamount;
                            
                            
                        }

                     }
               
              

                return view('admin.admissionprocess.filterAdmission',compact('newstudentsdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate','ramesfinds'));
                      
                      
                      
                  }
      }

   }

        public function pendingAdmission()
     {
         $currentMonth = date('m');
         $brnagch = Branch::all();
        $userALl = User::all();

        $userBranc = Auth::user()->branchs;
        /* $studentsdata = admissionprocess::select('admissionprocesses.*','admissionprocesses.id as aid')->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereNull('payments.inviceid')->groupBy('payments.inviceid')->get();*/
          
        $studentsdata = \DB::table('admissionprocesses')->where('stobranches',$userBranc)->select('admissionprocesses.*','admissionprocesses.id as aid')
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



         $newStudents = \DB::table('re_admissions')->where('rstobranches',$userBranc)->select('re_admissions.*','re_admissions.id as aid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('re_admissions.id = payments.reinviceid');
                                                            })->get(); 

        foreach($newStudents as $studentpaymen)
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



  

        $invototal = $studentsdata->sum('invtotal');
        
        $retotal = $newStudents->sum('rinvtotal');

        //dd($invototal);

        $sumtotal = $invototal + $retotal;
          
         $pamenreceived = 0; 
         

         
         $repaymreceived = 0; 
         
          
            $totslreceived = $pamenreceived + $repaymreceived;

            $remainingamount = $sumtotal - $totslreceived;
          
           
           // $total = $invototal->sum('invtotal');

        //    dd($sumtotal);

           return view('admin.admissionprocess.pendingadmission',compact('studentsdata','brnagch','userALl','newStudents','sumtotal','totslreceived','remainingamount'));
        
    }

    public function totalinvoice()
    {
        $student = admissionprocess::all();
        
        return view('admin.invoice.totalinvoice',compact('student'));
    }

     public function totalfees()
    {
        $userId = Auth::user()->id;

        $cour = course::all();
        $sourcedata = Source::get();
        $folss = followup::get();
        $userBranch = Auth::user()->branchs;
        $userdata = User::where("branchs", $userBranch)->get();

        $userBranch = Auth::user()->branchs;
        $currentMonth = date("m");

        $invoicesdata = payment::select(
            "admissionprocesses.*",
            "payments.*",
            "payments.id as pids",
            "admissionprocesses.id as admid"
        )
            ->join(
                "admissionprocesses",
                "admissionprocesses.id",
                "=",
                "payments.inviceid"
            )
            ->where("admissionprocesses.stobranches", $userBranch)
            ->whereMonth("payments.paymentdate", $currentMonth)
            ->orderBy("payments.id", "DESC")
            ->get();

        $reinvoicesdata = payment::select(
            "re_admissions.*",
            "payments.*",
            "payments.id as pids",
            "re_admissions.id as reid"
        )
            ->join(
                "re_admissions",
                "re_admissions.id",
                "=",
                "payments.reinviceid"
            )
            ->whereMonth("payments.paymentdate", $currentMonth)
            ->where("re_admissions.rstobranches", $userBranch)
            ->orderBy("payments.id", "DESC")
            ->get();

        $cour = course::all();
        $branchdata = Branch::where("branchname", $userBranch)->get();
        $userdata = User::where("branchs", $userBranch)->get();
        $sourcedata = Source::get();
        $ccatall = coursecategory::get();
        $folss = followup::get();

        return view(
            "admin.invoice.invoicesdata",
            compact(
                "invoicesdata",
                "reinvoicesdata",
                "cour",
                "sourcedata",
                "folss",
                "userdata",
                "cour",
                "sourcedata",
                "folss",
                "branchdata",
                "ccatall",
                "userdata"
            )
        );
    }

     public function filterfees(Request $request)
    {
        $userBranch = Auth::user()->branchs;
        $datesfor = "";
        $namedatas = "";
        $mobdatas = "";
        $coursedatas = "";
        $cmodes = "";
        $sources = "";
        $fsearch = "";
        $asearch = "";
        $bransdata = "";
        $categorydata = "";

        if ($namedatas = $request->getstudentsnames) {
            $folss = followup::get();
            $userdata = User::where("branchs", $userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where("branchname", $userBranch)->get();
            $ccatall = coursecategory::get();

            $namesfinds = payment::select(
                "admissionprocesses.*",
                "payments.*",
                "payments.id as pids",
                "admissionprocesses.id as aid"
            )
                ->join(
                    "admissionprocesses",
                    "admissionprocesses.id",
                    "=",
                    "payments.inviceid"
                )
                ->where("admissionprocesses.stobranches", $userBranch)
                ->Where(
                    "admissionprocesses.studentname",
                    "like",
                    "%" . $namedatas . "%"
                )
                ->orderBy("payments.id", "DESC")
                ->get();

            // $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as reid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->whereMonth('payments.paymentdate',$currentMonth)->where('re_admissions.rstobranches',$userBranch)->orderBy('payments.id','DESC')->get();

            $reinvoicesdata = payment::select(
                "re_admissions.*",
                "payments.*",
                "payments.id as pids",
                "re_admissions.id as rid"
            )
                ->join(
                    "re_admissions",
                    "re_admissions.id",
                    "=",
                    "payments.reinviceid"
                )
                ->where("re_admissions.rstobranches", $userBranch)
                ->Where(
                    "re_admissions.rstudents",
                    "like",
                    "%" . $namedatas . "%"
                )
                ->orderBy("payments.id", "DESC")
                ->get();

            return view(
                "admin.invoice.filterfees",
                compact(
                    "namesfinds",
                    "reinvoicesdata",
                    "folss",
                    "userdata",
                    "cour",
                    "sourcedata",
                    "branchdata",
                    "ccatall",
                    "datesfor",
                    "namedatas",
                    "mobdatas",
                    "coursedatas",
                    "cmodes",
                    "sources",
                    "fsearch",
                    "asearch",
                    "bransdata",
                    "categorydata"
                )
            );
        } elseif ($mobdatas = $request->getMobilesno) {
            $folss = followup::get();
            $userdata = User::where("branchs", $userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where("branchname", $userBranch)->get();
            $ccatall = coursecategory::get();

            $namesfinds = payment::join(
                "admissionprocesses",
                "admissionprocesses.id",
                "=",
                "payments.inviceid"
            )
                ->select(
                    "admissionprocesses.*",
                    "payments.*",
                    "admissionprocesses.id as aid",
                    "payments.id as pids"
                )
                ->where("admissionprocesses.stobranches", $userBranch)
                ->Where("admissionprocesses.sphone", $mobdatas)
                ->orwhere("admissionprocesses.swhatsappno", $mobdatas)
                ->orderBy("payments.id", "DESC")
                ->get();

            $reinvoicesdata = payment::select(
                "re_admissions.*",
                "payments.*",
                "payments.id as pids",
                "re_admissions.id as rid"
            )
                ->join(
                    "re_admissions",
                    "re_admissions.id",
                    "=",
                    "payments.reinviceid"
                )
                ->where("re_admissions.rstobranches", $userBranch)
                ->where("re_admissions.rsphone", $mobdatas)
                ->orWhere("re_admissions.rswhatsappno", $mobdatas)
                ->orderBy("payments.id", "DESC")
                ->get();

            return view(
                "admin.invoice.filterfees",
                compact(
                    "reinvoicesdata",
                    "namesfinds",
                    "folss",
                    "userdata",
                    "cour",
                    "sourcedata",
                    "branchdata",
                    "ccatall",
                    "datesfor",
                    "namedatas",
                    "mobdatas",
                    "coursedatas",
                    "cmodes",
                    "sources",
                    "fsearch",
                    "asearch",
                    "bransdata",
                    "categorydata"
                )
            );
        } elseif ($datesfor = $request->DateFor) {
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if ($datesfor == "Admission Date") {
                $folss = followup::get();
                $userdata = User::where("branchs", $userBranch)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $branchdata = Branch::where("branchname", $userBranch)->get();
                $ccatall = coursecategory::get();

                $namesfinds = payment::join(
                    "admissionprocesses",
                    "admissionprocesses.id",
                    "=",
                    "payments.inviceid"
                )
                    ->select(
                        "admissionprocesses.*",
                        "payments.*",
                        "admissionprocesses.id as aid",
                        "payments.id as pids"
                    )
                    ->where("admissionprocesses.stobranches", $userBranch)
                    ->whereBetween("payments.paymentdate", [
                        $startdates,
                        $enddats,
                    ])
                    ->orderBy("payments.id", "DESC")
                    ->get();

                $reinvoicesdata = payment::select(
                    "re_admissions.*",
                    "payments.*",
                    "payments.id as pids",
                    "re_admissions.id as rid"
                )
                    ->join(
                        "re_admissions",
                        "re_admissions.id",
                        "=",
                        "payments.reinviceid"
                    )
                    ->where("re_admissions.rstobranches", $userBranch)
                    ->whereBetween("payments.paymentdate", [
                        $startdates,
                        $enddats,
                    ])
                    ->orderBy("payments.id", "DESC")
                    ->get();

                return view(
                    "admin.invoice.filterfees",
                    compact(
                        "reinvoicesdata",
                        "namesfinds",
                        "folss",
                        "userdata",
                        "cour",
                        "sourcedata",
                        "branchdata",
                        "ccatall",
                        "datesfor",
                        "namedatas",
                        "mobdatas",
                        "coursedatas",
                        "cmodes",
                        "sources",
                        "fsearch",
                        "asearch",
                        "bransdata",
                        "categorydata",
                        "startdates",
                        "enddats"
                    )
                );
            } elseif ($datesfor == "Payment Date") {
                $folss = followup::get();
                $userdata = User::where("branchs", $userBranch)->get();
                $cour = course::all();
                $sourcedata = Source::all();
                $branchdata = Branch::where("branchname", $userBranch)->get();
                $ccatall = coursecategory::get();

                $namesfinds = payment::join(
                    "admissionprocesses",
                    "admissionprocesses.id",
                    "=",
                    "payments.inviceid"
                )
                    ->select(
                        "admissionprocesses.*",
                        "payments.*",
                        "admissionprocesses.id as aid",
                        "payments.id as pids"
                    )
                    ->where("admissionprocesses.stobranches", $userBranch)
                    ->whereBetween("payments.paymentdate", [
                        $startdates,
                        $enddats,
                    ])
                    ->orderBy("payments.id", "DESC")
                    ->get();

                $reinvoicesdata = payment::select(
                    "re_admissions.*",
                    "payments.*",
                    "payments.id as pids",
                    "re_admissions.id as rid"
                )
                    ->join(
                        "re_admissions",
                        "re_admissions.id",
                        "=",
                        "payments.reinviceid"
                    )
                    ->where("re_admissions.rstobranches", $userBranch)
                    ->whereBetween("payments.paymentdate", [
                        $startdates,
                        $enddats,
                    ])
                    ->orderBy("payments.id", "DESC")
                    ->get();

                return view(
                    "admin.invoice.filterfees",
                    compact(
                        "reinvoicesdata",
                        "namesfinds",
                        "folss",
                        "userdata",
                        "cour",
                        "sourcedata",
                        "branchdata",
                        "ccatall",
                        "datesfor",
                        "namedatas",
                        "mobdatas",
                        "coursedatas",
                        "cmodes",
                        "sources",
                        "fsearch",
                        "asearch",
                        "bransdata",
                        "categorydata",
                        "startdates",
                        "enddats"
                    )
                );
            }
        } 
         elseif($coursedatas = $request->coursedatas)
      {
         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();
            $cstartsdates = $request->cdatestat;
            $cendsdates = $request->cdateend;
         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->orderBy('leads.leaddate','DESC')->get();
             $susfindcourse = course::where('id',$coursedatas)->pluck('byuniversitites');


             if($susfindcourse = 'BIT Institute')
               {
                
                  $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesscourses.courseid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();


                    $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->where('readmissioncourses.recourseid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();
               

                return view('admin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));

               }


               else
               {
                     $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->where('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();


                    $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->where('readmissioncourses.reunivecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();
               

                return view('admin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
               }


      }

        elseif ($sources = $request->sourceSearch) {
            $starsdates = $request->sdatestat;
            $enssdates = $request->sdateend;

            $folss = followup::get();
            $userdata = User::where("branchs", $userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where("branchname", $userBranch)->get();
            $ccatall = coursecategory::get();

            $namesfinds = payment::join(
                "admissionprocesses",
                "admissionprocesses.id",
                "=",
                "payments.inviceid"
            )
                ->select(
                    "admissionprocesses.*",
                    "payments.*",
                    "admissionprocesses.id as aid",
                    "payments.id as pids"
                )
                ->where("admissionprocesses.stobranches", $userBranch)
                ->where("admissionprocesses.admsisource", $sources)
                ->whereBetween("payments.paymentdate", [
                    $starsdates,
                    $enssdates,
                ])
                ->orderBy("payments.id", "DESC")
                ->get();

            $reinvoicesdata = payment::select(
                "re_admissions.*",
                "payments.*",
                "payments.id as pids",
                "re_admissions.id as rid"
            )
                ->join(
                    "re_admissions",
                    "re_admissions.id",
                    "=",
                    "payments.reinviceid"
                )
                ->where("re_admissions.rstobranches", $userBranch)
                ->where("re_admissions.radmsisource", $sources)
                ->whereBetween("payments.paymentdate", [
                    $starsdates,
                    $enssdates,
                ])
                ->orderBy("payments.id", "DESC")
                ->get();

            return view(
                "admin.invoice.filterfees",
                compact(
                    "reinvoicesdata",
                    "namesfinds",
                    "folss",
                    "userdata",
                    "cour",
                    "sourcedata",
                    "branchdata",
                    "ccatall",
                    "datesfor",
                    "namedatas",
                    "mobdatas",
                    "coursedatas",
                    "cmodes",
                    "sources",
                    "fsearch",
                    "asearch",
                    "bransdata",
                    "categorydata",
                    "starsdates",
                    "enssdates"
                )
            );
        } elseif ($asearch = $request->AssignedToSearch) {
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
            $userdata = User::where("branchs", $userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where("branchname", $userBranch)->get();
            $ccatall = coursecategory::get();

            $namesfinds = payment::leftjoin(
                "admissionprocesses",
                "admissionprocesses.id",
                "=",
                "payments.inviceid"
            )
                ->select(
                    "admissionprocesses.*",
                    "payments.*",
                    "admissionprocesses.id as aid",
                    "payments.id as pids"
                )
                ->where("admissionprocesses.stobranches", $userBranch)
                ->where("admissionprocesses.admissionsusersid", $asearch)
                ->whereBetween("payments.paymentdate", [$asdates, $aenddates])
                ->orderBy("payments.id", "DESC")
                ->get();

            $reinvoicesdata = "";

            return view(
                "admin.invoice.filterfees",
                compact(
                    "reinvoicesdata",
                    "namesfinds",
                    "folss",
                    "userdata",
                    "cour",
                    "sourcedata",
                    "branchdata",
                    "ccatall",
                    "datesfor",
                    "namedatas",
                    "mobdatas",
                    "coursedatas",
                    "cmodes",
                    "sources",
                    "fsearch",
                    "asearch",
                    "bransdata",
                    "categorydata",
                    "asdates",
                    "aenddates"
                )
            );
        } elseif ($bransdata = $request->branchSearchDatas) {
            $bstartdate = $request->BStartDate;
            $benddate = $request->BEnddate;

            $folss = followup::get();
            $userdata = User::where("branchs", $userBranch)->get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::where("branchname", $userBranch)->get();
            $ccatall = coursecategory::get();

            $namesfinds = payment::join(
                "admissionprocesses",
                "admissionprocesses.id",
                "=",
                "payments.inviceid"
            )
                ->select(
                    "admissionprocesses.*",
                    "payments.*",
                    "admissionprocesses.id as aid",
                    "payments.id as pids"
                )
                ->whereBetween("payments.paymentdate", [$bstartdate, $benddate])
                ->where("admissionprocesses.stobranches", $bransdata)
                ->orderBy("payments.id", "DESC")
                ->get();

            $reinvoicesdata = payment::select(
                "re_admissions.*",
                "payments.*",
                "payments.id as pids",
                "re_admissions.id as rid"
            )
                ->join(
                    "re_admissions",
                    "re_admissions.id",
                    "=",
                    "payments.reinviceid"
                )
                ->whereBetween("payments.paymentdate", [$bstartdate, $benddate])
                ->where("re_admissions.rstobranches", $bransdata)
                ->orderBy("payments.id", "DESC")
                ->get();

            return view(
                "admin.invoice.filterfees",
                compact(
                    "reinvoicesdata",
                    "namesfinds",
                    "folss",
                    "userdata",
                    "cour",
                    "sourcedata",
                    "branchdata",
                    "ccatall",
                    "datesfor",
                    "namedatas",
                    "mobdatas",
                    "coursedatas",
                    "cmodes",
                    "sources",
                    "fsearch",
                    "asearch",
                    "bransdata",
                    "categorydata",
                    "bstartdate",
                    "benddate"
                )
            );
        } 

       elseif($categorydata = $request->categorysDatas)
         {

            //dd($categorydata);
            $cstartdate = $request->CStartDate;
            $cenddate = $request->CEnddate;

            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

                  $findcourse = course::where('cat_id',$categorydata)->pluck('id');
                  $susfindcourse = course::where('cat_id',$categorydata)->pluck('byuniversitites');

                  if($susfindcourse = 'BIT Institute')
                  {
                     //dd('test');

                     
                     $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->whereIn('admissionprocesscourses.courseid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get();



                     $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->whereIn('readmissioncourses.recourseid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get(); 
                     
                    

                      return view('admin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
                  }


                  else
                  {
                    

                     $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.stobranches',$userBranch)->whereIn('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get();



                     $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->whereIn('readmissioncourses.reunivecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get(); 
                     
                    

                      return view('admin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
                  }
           
          }  
    }

      public function pendingfees()
     {

       
      
        $currentMonth = date('m');
          $userBranch = Auth::user()->branchs;


        $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid','payments.id as pids')
         ->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('admissionprocesses.stobranches',$userBranch)
         ->groupBy('payments.inviceid')
         ->orderBy('payments.id','DESC')
         ->get();


 

        $ReWiPayment = ReAdmission::select('re_admissions.*','payments.id as pids','payments.*','re_admissions.id as reid')
         ->join('payments', 'payments.reinviceid', '=', 're_admissions.id')
         ->orderBy('payments.id','DESC')
         ->where('re_admissions.rstobranches',$userBranch)
         ->groupBy('payments.reinviceid')
         ->get();
        
        $pendamount = $WiPayment;
        $rependamount = $ReWiPayment;

        $invototal = $pendamount->sum('invtotal');
        
        $retotal = $rependamount->sum('rinvtotal');



        $sumtotal = $invototal + $retotal;
          
         $pamenreceived = $pendamount->sum('paymentreceived');
         

         
         $repaymreceived = $rependamount->sum('paymentreceived'); 
         
          
            $totslreceived = $pamenreceived + $repaymreceived;

            $remainingamount = $sumtotal - $totslreceived;

              $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();



      //  dd($pendamount);
        return view('admin.invoice.pendingfees',compact('pendamount','rependamount','sumtotal','totslreceived','remainingamount','folss','userdata','cour','sourcedata','branchdata','ccatall'));

    }



    public function totalinvociess()
    {
        $UserBranch = Auth::user()->branchs;
        $currentMonth = date('m');
          $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->where('admissionprocesses.stobranches',$UserBranch)
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->get(); 


        $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('admissionprocesses.stobranches',$UserBranch)
         ->groupBy('payments.inviceid')
         ->get();
        
        $pendamount = $NewPayment->merge($WiPayment);


      //  dd($pendamount);
        //return view('superadmin.invoice.pendingamount',compact('pendamount'));
        
        return view('admin.invoice.totalinvocies',compact('pendamount'));
    }

    public function paymentreceiptlist($id)
    {
        $selectID = payment::where('inviceid',$id)->orderBy('id','DESC')->get();
        $admissiondet = admissionprocess::find($id);
        return view('admin.invoice.receiptlist',compact('selectID','admissiondet'));
    }
    
     public function repaymentreceiptlist($id)
    {
        $selectID = payment::where('reinviceid',$id)->get();
        $admissiondet = ReAdmission::find($id);
        return view('admin.invoice.rereceiptlist',compact('selectID','admissiondet'));
    }
    
    public function repayment($id)
    {

        $paymentdetails = admissionprocess::find($id);
        $paymentsse = payment::where('inviceid',$id)->orderBy('id','DESC')->take(1)->get();
        $branc = Branch::all();
        $installmentfees = admissionprocessinstallmentfees::where('invoid',$id)->where('status',0)->orderBy('id','DESC')->get();
        $psource = PaymentSource::all();
        return view('admin.invoice.repayment',compact('paymentdetails','branc','installmentfees','paymentsse','psource'));
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
        return view(
            "admin.readmissions.readrepayment",
            compact(
                "paymentdetails",
                "branc",
                "paymentsse",
                "psource"
            )
        );
    }

    function filterpendingfees(Request $request)
    {
        {
        $students = $request->studentname;
        $mobile = $request->mobilesearch;
         $userBranch = Auth::user()->branchs;


            if($students)
            {

                $studentsfilter='Student Name';

                    $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
                     ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                     ->Where('studentname', 'like', '%' .$students. '%')
                     ->where('admissionprocesses.stobranches',$userBranch)
                     ->groupBy('payments.inviceid')
                     ->get();

                    


                    $ReWiPayment = ReAdmission::select('re_admissions.*', 'payments.*','re_admissions.id as remid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
                     ->Join('payments', 'payments.reinviceid', '=', 're_admissions.id')
                     ->Where('rstudents', 'like', '%' .$students. '%')
                     ->Where('re_admissions.rstobranches',$userBranch)
                     ->groupBy('payments.reinviceid')
                     ->get();
                    
                    $pendamount = $WiPayment;
                    $rependamount = $ReWiPayment;

                    $invototal = $pendamount->sum('invtotal');
                    
                    $retotal = $rependamount->sum('rinvtotal');



                    $sumtotal = $invototal + $retotal;
                      
                     $pamenreceived = $pendamount->sum('paymentreceived');; 
                     

                     
                     $repaymreceived = $rependamount->sum('paymentreceived');; 
                     
                      
                        $totslreceived = $pamenreceived + $repaymreceived;

                        $remainingamount = $sumtotal - $totslreceived;

                 return view('admin.invoice.filterpendingamount',compact('pendamount','rependamount','sumtotal','totslreceived','remainingamount','studentsfilter','students'));
            }


            else if($mobile)
            {
                $students ="";
                 $mobilefilter ='Mobile No.';

                    $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
                     ->Join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                     ->Where('sphone',$mobile)
                     ->orWhere('swhatsappno', 'like',$mobile)
                     ->groupBy('payments.inviceid')
                     ->get();

                    


                    $ReWiPayment = ReAdmission::select('re_admissions.*', 'payments.*','re_admissions.id as remid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
                     ->Join('payments', 'payments.reinviceid', '=', 're_admissions.id')
                     ->Where('rsphone',$mobile)
                     ->orWhere('rswhatsappno',$mobile)
                     ->groupBy('payments.reinviceid')
                     ->get();
                    
                    $pendamount = $WiPayment;
                    $rependamount = $ReWiPayment;

                    $invototal = $pendamount->sum('invtotal');
                    
                    $retotal = $rependamount->sum('rinvtotal');



                    $sumtotal = $invototal + $retotal;
                      
                     $pamenreceived = $pendamount->sum('paymentreceived');; 
                     

                     
                     $repaymreceived = $rependamount->sum('paymentreceived');; 
                     
                      
                        $totslreceived = $pamenreceived + $repaymreceived;

                        $remainingamount = $sumtotal - $totslreceived;

                 return view('admin.invoice.filterpendingamount',compact('pendamount','rependamount','sumtotal','totslreceived','remainingamount','mobilefilter','mobile','students'));
            }
        
    }
    }

     public function restorepayment(Request $request,$id)
    {

        $latestincrme = payment::where('inviceid',$id)->latest()->get()->pluck('instid');
         $counts = isset($latestincrme[0]) ? $latestincrme[0] : false;
           // $counts = $mj + 1;
        $incrementid = $counts + 1;
        /*dd($incrementid);*/
       
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

        $paymentmodel = new payment();
        $payment = $paymentmodel->create([
            'inviceid'=> $id,
            'instid'=> $incrementid,
            'totalamount'=> $tmamount,
            'paymentreceived'=> $preceived,
            'transactionsids'=> $request->transactionsids,
            'remainingamount'=> $request->ramount,
            'paymentdate'=> $request->paymentdate,
            'paymentmode'=> $request->paymentmode,
            'bankname'=> $request->bankname,
            'paymentype' => $request->ptypes,
            'nexamountdate'=> $request->remindersdates,
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
        



        return redirect('/admin-paymentreceipt/'.$paymentid)->with('success','Payment Successfully Done!!!');
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
            'transactionsids'=> $request->transactionsids,
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

        return redirect("/admins-re-payment-recipt/" . $paymentid)->with(
            "success",
            "Payment Successfully Done!!!"
        );
    }
}
