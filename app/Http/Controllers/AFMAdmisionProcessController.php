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
use App\ReAdmission;
use App\coursebunchlist;
use App\coursespecializationlist;
use App\UnviersitiesCategory;
use App\universititiesfeeslist;
use App\PaymentSource;
use App\AffiliatesCategory; 
use App\affiliatestrainingcategory; 
use Illuminate\Http\Request;
use DB;
use Razorpay\Api\Api;
use Session;
use Redirect;
use Auth;



class AFMAdmisionProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
         $userId = Auth::user()->id;
        $studentsdata = admissionprocess::where('userId',$userId)->get();
        return view('affiliatesmarketing.admission.studentsdetails',compact('studentsdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
          $id = $request->getadmissions;
         //dd($id);

        $alb = branch::get();
        $directstudentsdata = leads::find($id);
        $cours = course::get();
        $leadsdata = leads::get();

        $studentdetails = students::get();
       
        $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();
        $ucats = UnviersitiesCategory::all();
        $actsa = affiliatestrainingcategory::all();
        return view('affiliatesmarketing.admissionprocess.create',compact('alb','cours','leadsdata','directstudentsdata','studentdetails','branchdetails','course','taxesna','ucats','actsa'));
        
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
            'afficategory'=> $request->acategories,
            
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


            return redirect('/general-Affiliates-invoice-marketing/'.$invoicesid);

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
            'afficategory'=> $request->acategories,
            
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

                 

             return redirect('/general-Affiliates-invoice-marketing/'.$invoicesid);
                    

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

         $installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");


        return view('affiliatesmarketing.admissionprocess.generalinvoices',compact('aprocess','invvcoursed','installmentfees'));
    }

    public function admissionform($id)
    {
          $aprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $univCourse = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.univecoursid AND a.id = k.invid AND a.id = "'.$id.'" ');

         //$installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");

         //$paymentdata = payment::where('inviceid',$id)->get();

        
        

        return view('affiliatesmarketing.admissionprocess.admissionform',compact('aprocess','invvcoursed','univCourse'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
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
        
        return view('affiliatesmarketing.admissionprocess.edit',compact('alb','cours','leadsdata','branchdetails','course','taxesna','studad','ad','adcourses','ademi'));
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


     public function payment($id,admissionprocess $admissionprocess)
    {

        $paymentdetails = admissionprocess::find($id);  
         $paymentdetails = admissionprocess::find($id);
         $branc = Branch::where('branchname','bitol')->get();
         $installmentfees = admissionprocessinstallmentfees::where('invoid',$id)->where('status',0)->orderBy('id','DESC')->get();
         $psource = PaymentSource::all();
        return view('affiliatesmarketing.payments.create',compact('paymentdetails','branc','installmentfees','psource'));
    }

    public function paymentprocess($id,Request $request)
    {   
         $paymentdetails = admissionprocess::find($id);
         $branc = Branch::where('branchname','bitol')->get();
         $installmentfees = admissionprocessinstallmentfees::where('invoid',$id)->where('status',0)->orderBy('id','DESC')->get();
         $psource = PaymentSource::all();
       return view('affiliatesmarketing.payments.razorpaypayment',compact('paymentdetails','branc','installmentfees','psource'));
    }


    public function makepayment($id,Request $request)
    {
        $paymentdetails = admissionprocess::find($id);
        $ctotalpayment = $request->totalamount;
        $branchsdata = $request->brnavhc;
        $cpaymentrecieved = $request->paymentrecieved;
        $cremainingamount = $request->ramount;
        $cpaymentdate = $request->paymentdate;
        $cremarknote = $request->remarknote;
        $insid = $request->installid;
        $paytype = $request->ptypes;
        $rrno = $request->receiptno;
        $enrol = $request->erno;


       return view('affiliatesmarketing.payments.makepayment',compact('paymentdetails','ctotalpayment','branchsdata','cpaymentrecieved','cremainingamount','cpaymentdate','cremarknote','insid','paytype','rrno','enrol'));   
    }

    public function paymentprodw($id,Request $request)
    {    

        //dd($id);    
       /* $input = $request->all();        
        $api = new Api('rzp_live_GVLAcXSlRcULoB','lWlfTn6fcz0PA70R6nSRqNwc');
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if(count($input)  && !empty($input['razorpay_payment_id'])) 
        {
            try 
            {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$payment['amount'])); 

            } 
            catch (\Exception $e) 
            {
                return  $e->getMessage();
               
                 return redirect('/Affiliates-marketing-user-create-payment/'.$id)->with('error',$e->getMessage());
               
            }            
        }*/
        
           $userId = Auth::user()->id;
        $studentsdata = $request->students;
        $ernos = $request->enrollmen;
        $rcepno = $request->recep;
       
        $rptype = $request->ptype;
            if($rptype == 'LumpSum')
            {
                $tvamount = $request->tamount;
                $preceived = $request->tprecieved;
            }
            else if($rptype == 'EMI')
            {
                $tvamount = $request->tinstamount;
                $preceived = $request->tprecieved;
            }


           // dd($tvamount);
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
            'totalamount'=> $tvamount,
            'paymentreceived'=> $preceived,
            'remainingamount'=> $request->premainingamount,
            'paymentdate'=> $request->pdates,
            'paymentmode'=> "Razorpay",
            'remarknoe'=> $request->premarknotes,
            'userid'=> $userId,
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
        ]);

        $inid = $request->instid;

        $paymentid = $payment->id;

        $updatenew = admissionprocessinstallmentfees::find($inid);

        if($updatenew)
       {
            $updatenew->status = 1;
            $updatenew->save();
        }
        




        $updatesid = admissionprocess::find($id);
        $updatesid->status = '1';
        $updatesid->serno = $ernos;
        $updatesid->save();

        $studentsphone = admissionprocess::where('id',$id)->pluck('sphone');
        $leadupodat = leads::where('phone',$studentsphone)->first();
      
         //dd($leadupodat);
       if($leadupodat)
       {
            $leadupodat->conversationstatus = '1';
            $leadupodat->save();
        
       }


       // \Session::put('success', 'Payment successful, your order will be despatched in the next 48 hours.');
        return redirect('/Affiliates-marketing-user-paymentreceipt/'.$paymentid)->with('success','Payment Successfully Done !!');
        //return redirect()->back();
    }

    public function paymentreceipt($id,admissionprocess $admissionprocess)
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

         $installdata = admissionprocessinstallmentfees::leftJoin('payments', 'payments.installmentid', '=', 'admissionprocessinstallmentfees.id')->where('admissionprocessinstallmentfees.invoid',$newId)->orderBy('admissionprocessinstallmentfees.id','DESC')->get();


        return view('affiliatesmarketing.payments.paymentreceipt',compact('aprocess','invvcoursed','univCourse','paymentdata','makepayment','installdata','selectID'));
    }

     public function fees()
    {
        $userId = Auth::user()->id;

       
        $invoicesdata = DB::select('SELECT * FROM admissionprocesses  a, branches b, payments p WHERE b.id = a.Ibranchs AND  a.id = p.inviceid AND a.userid = "'.$userId.'"');

                return view('affiliatesmarketing.invoice.invoicesdata',compact('invoicesdata'));
    }

    public function targets()
    {

         $userId = Auth::user()->name;

         $targetsdata = assigntarget::where('tassignuser',$userId)->get();
         //$targetsdata = DB::select("SELECT * FROM assigntargets a, users u WHERE u.id");
        /*$targetsdata =DB::se('assigntargets')
                ->join('users', 'users.id', '=', 'assigntargets.tassignuser')
                ->select('assigntargets.id','assigntargets.targtname','assigntargets.tmonth','assigntargets.created_at','assigntargets.usercategory','assigntargets.targetamount','assigntargets.incentivepercent','users.name')
                ->get();*/



                return view('affiliatesmarketing.target.manage',compact('targetsdata'));
    }

}
