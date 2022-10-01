<?php

namespace App\Http\Controllers;
use App\students;
use App\course;
use App\Branch;
use App\leads;
use App\payment;
use App\studentscourse;
use App\Tax;
use Auth;
use App\admissionprocess;
use App\admissionprocesscourses;
use App\admissionprocessinstallmentfees;
use Illuminate\Http\Request;
use DB;
class CentreCoordinatorAdmissionProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         
           $userBranch = Auth::user()->branchs;
        $studentsdata = admissionprocess::Where('sbrnanch',$userBranch);
        return view('centrecoordinator.admissionprocess.studentsdetails',compact('studentsdata'));
    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $alb = branch::get();
        $directstudentsdata = leads::find($id);
        $cours = course::get();
        $leadsdata = leads::get();

        $studentdetails = students::get();
       
        $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();

         return view('centrecoordinator.admissionprocess.create',compact('alb','cours','leadsdata','directstudentsdata','studentdetails','branchdetails','course','taxesna'));
    }

    public function createnewadminssion()
    {
        $userId = Auth::user()->id;

        $alb = branch::get();
        $cours = course::get();
        $leadsdata = leads::where('user_id',$userId)->get();
        $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();
        return view('centrecoordinator.admissionprocess.newadmiison',compact('alb','cours','leadsdata','branchdetails','course','taxesna'));
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
                                'coursemode'   => $csmode[$i],
                                'courseprice'   => $courseprice[$i],
                                
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


            return redirect('/view-centre-cordinator-invoice/'.$invoicesid);

        }

        else
        {
            $admissionprocessmodel = new admissionprocess();
            $admissionprocess = $admissionprocessmodel->create([
            'studentname'=> $newstudents,
            'sdobs'=> $birthdate,
            'semails'=> $email,
            'sbrnanch'=> $brnach,
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
                                
                            ]);
                            $admissionprocesscourses->save();
                    }

             return redirect('/view-centre-cordinator-invoice/'.$invoicesid);
        }

    }

    public function viewinvoice($id)
      {

         
        $aprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");


        return view('centrecoordinator.admissionprocess.generalinvoices',compact('aprocess','invvcoursed','installmentfees'));
     }


     public function payment($id,admissionprocess $admissionprocess)
    {

        $paymentdetails = admissionprocess::find($id);
        return view('centrecoordinator.payments.create',compact('paymentdetails'));
    }


    public function paymentreceipt($id,admissionprocess $admissionprocess)
    {
        $aprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");

         $paymentdata = payment::where('inviceid',$id)->get();


        return view('centrecoordinator.admissionprocess.paymentreceipt',compact('aprocess','invvcoursed','installmentfees','paymentdata'));
    }


      public function paymentstore(Request $request,$id,admissionprocess $admissionprocess,payment $payment)
    {

        $userId = Auth::user()->id;
        $studentsdata = $request->students;

        $paymentmodel = new payment();
        $payment = $paymentmodel->create([
            'inviceid'=> $id,
            'totalamount'=> $request->totalamount,
            'paymentreceived'=> $request->paymentrecieved,
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



        $updatesid = admissionprocess::find($id);
        $updatesid->status = '1';
        $updatesid->save();

        $studentsphone = admissionprocess::where('id',$id)->pluck('sphone');
        $leadupodat = leads::where('phone',$studentsphone)->first();
        //dd($leadupodat);
        $leadupodat->conversationstatus = '1';
        $leadupodat->save();



        //$paymentid = $payment->id;


        return redirect('/centre-coordinator-paymentreceipt/'.$id)->with('success','Payment Successfully Done!!!');

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
