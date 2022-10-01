<?php

namespace App\Http\Controllers;

use App\ReAdmission;
use App\Readmissioncourses;
use App\readmissioninstallmentfees;
use App\admissionprocess;
use App\payment;
use App\course;
use App\PaymentSource;
use App\Branch;
use App\UnviersitiesCategory;
use App\Tax;
use App\User;
use App\leads;
use Illuminate\Http\Request;
use Auth;
use DB;

class ReAdmissionController extends Controller
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
         $studentsdata = ReAdmission::select('re_admissions.*','re_admissions.id as aid')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->whereMonth('payments.paymentdate',$currentMonth)->groupBy('payments.reinviceid')->orderBy('payments.id','DESC')->get(); 

       
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

        return view('superadmin.readmissions.manage',compact('studentsdata','brnagch','userALl'));
    }

     public function centremanagerreadmission()
    {

        $brnagch = Branch::all();
        $userALl = User::all();
        $userBranchs = Auth::user()->branchs;

             $currentMonth = date('m');
         $studentsdata = ReAdmission::select('re_admissions.*','re_admissions.id as aid','payments.id as pids')->join('payments', 'payments.reinviceid', '=', 're_admissions.id')->where('rsbrnanch',$userBranchs)->whereMonth('re_admissions.rsadate',$currentMonth)->groupBy('payments.reinviceid')->orderBy('payments.id','DESC')->get(); 

       
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

        return view('centremanager.admissionprocess.readmission',compact('studentsdata','brnagch','userALl'));
    }

    public function allreceipts(Request $request)
    {
        $admissionsId = $request->admissionid;

        $data= array();

        $result = payment::where('reinviceid',$admissionsId)->get();

        foreach($result as $res)
        {
            $row = array();
            $row[] = '<a href="/re-payment-recipt/'.$res->id.'" class="btn btn-primary"><i class="fas fa-file-invoice"></i></a>';
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);

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
        return view('superadmin.readmissions.filterwithreadmissions',compact('studentsdata','mobileno'));   
    }
    

    public function getOldAdmissionforCentreManager(Request $request)
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
            return view('centremanager.admissionprocess.filterreadmission',compact('studentsdata','mobileno'));   
        }


    public function getreadmissiondetails(Request $request)
    {
        $studentsids = $request->studentsid;

        $admissiondetails = admissionprocess::find($studentsids);
        $paymentsdetails = payment::where('inviceid',$studentsids)->groupBy('inviceid')->first();
        $course = course::get(); 
         $ucats = UnviersitiesCategory::all();
         $taxesna = Tax::get();

        return view('superadmin.readmissions.create',compact('admissiondetails','course','ucats','taxesna','paymentsdetails'));

    }

    public function getreerno($rerno)
    {
         $year = date("Y");
         $month = date("m");

               if($rerno == "BITSJ")
        {
            
            //$latests = admissionprocess::get()->pluck('sjerno');

            //$latests = admissionprocess::where('prefix_id', $current_prefix->id)->max('number') + 1;
            $latests = ReAdmission::where('rstobranches','=',$rerno)->latest()->get()->pluck('rsjerno');
            //dd($latests);
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITSJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
            
             /*return response()->json($value);*/
        }

        else if($rerno == "BITMJ") 
        {

            
            $latests = ReAdmission::where('rstobranches','=',$rerno)->latest()->get()->pluck('rmjerno');
            //$latests = admissionprocess::get()->pluck('mjerno')->toArray();
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITMJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
        }

          else if($rerno == "BITWG") 
        {

            
            $latests = ReAdmission::where('rstobranches','=',$rerno)->latest()->get()->pluck('rwgerno');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITWG/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
        }

         else if($rerno == "BITOL") 
        {
           
            $latests = ReAdmission::where('rstobranches','=',$rerno)->latest()->get()->pluck('rbitolerno');
            /*$lates = admissionprocess::get()->pluck('wgerno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'BITOL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if($rerno == "CVRU(BL)") 
        {
           
            $latests = ReAdmission::where('rstobranches','=',$rerno)->latest()->get()->pluck('rcvrublerno');
            /*$lates = admissionprocess::get()->pluck('wgerno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(BL)/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if($rerno == "CVRU (KH)") 
        {
           
            $latests = ReAdmission::where('rstobranches','=',$rerno)->latest()->get()->pluck('rcvrukherno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(KH)/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if($rerno == "RNTU") 
        {
           
            $latests = ReAdmission::where('rstobranches','=',$rerno)->latest()->get()->pluck('rrntuerno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'RNTU/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        else if($rerno == "MANIPAL") 
        {
           
            $latests = ReAdmission::where('rstobranches','=',$rerno)->latest()->get()->pluck('rmanipalerno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'MANIPAL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
    }

    public function getInvno($rebranchId)
    {

        $year = date("Y");
         $month = date("m");
         if($rebranchId == "1")
        {

               // $latest = DB::select("SELECT sjerno from students order by sjerno DESC LIMIT 1");
           
            $latests = ReAdmission::where('rIbranchs','=',$rebranchId)->latest()->get()->pluck('rIsjno');
            // $latests = admissionprocess::get()->pluck('Isjno')->toArray();
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'INV-BITSJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
            
             //return response()->json($value);
        }

        else if ($rebranchId == "2") 
        {
             $latests = ReAdmission::where('rIbranchs','=',$rebranchId)->latest()->get()->pluck('rImjno');
            /*$latests = admissionprocess::get()->pluck('Imjno')->toArray();*/
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'INV-BITMJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
        }

         else if ($rebranchId == "3") 
        {
            $latests = ReAdmission::where('rIbranchs','=',$rebranchId)->latest()->get()->pluck('rIwgno');
            /*$lates = admissionprocess::get()->pluck('Iwgno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-BITWG/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }

         else if ($rebranchId == "4") 
        {
            $latests = ReAdmission::where('rIbranchs','=',$rebranchId)->latest()->get()->pluck('rIbitolno');
            /*$lates = admissionprocess::get()->pluck('Ibitolno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-BITOL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if ($rebranchId == "5") 
        {
            $latests = ReAdmission::where('rIbranchs','=',$rebranchId)->latest()->get()->pluck('rIcvrublno');
            /*$lates = admissionprocess::get()->pluck('Icvrublno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-CVRU(BL)/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if ($rebranchId == "6") 
        {
            $latests = ReAdmission::where('rIbranchs','=',$rebranchId)->latest()->get()->pluck('rIcvrukhno');   
            /*$lates = admissionprocess::get()->pluck('Icvrukhno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-CVRU(KH)/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        else if ($rebranchId == "7") 
        {
            $latests = ReAdmission::where('rIbranchs','=',$rebranchId)->latest()->get()->pluck('rIrntuno');
            /*$lates = admissionprocess::get()->pluck('Irntuno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-RNTU/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        else if ($rebranchId == "8") 
        {
            $latests = ReAdmission::where('rIbranchs','=',$rebranchId)->latest()->get()->pluck('rImanipalno');
           /* $lates = admissionprocess::get()->pluck('Imanipalno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-MANIPAL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        
          
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


            return redirect('/create-re-payment/'.$invoicesid);

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

                 

             return redirect('/create-re-payment/'.$invoicesid);
                    

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ReAdmission  $reAdmission
     * @return \Illuminate\Http\Response
     */
    public function viewinvoice($id,ReAdmission $reAdmission)
    {
        $aprocess = ReAdmission::find($id);
       
          

        $invvcoursed = DB::select('SELECT * FROM  re_admissions a, courses c, readmissioncourses k WHERE c.id = k.recourseid AND a.id = k.reinvid AND a.id = "'.$id.'" ');
       

        $univCourse = DB::select('SELECT * FROM  re_admissions a, courses c, readmissioncourses k WHERE c.id = k.reunivecoursid AND a.id = k.reinvid AND a.id = "'.$id.'" ');

         $installmentfees = DB::select("SELECT * FROM readmissioninstallmentfees WHERE reinvoid = '$id' ORDER BY id DESC");
         
        return view('superadmin.readmissions.reinvoice',compact('aprocess','invvcoursed','installmentfees','univCourse'));
    }

     public function admisionforms($id,ReAdmission $reAdmission)
    {
        $aprocess = ReAdmission::find($id);
       
          

        $invvcoursed = DB::select('SELECT * FROM  re_admissions a, courses c, readmissioncourses k WHERE c.id = k.recourseid AND a.id = k.reinvid AND a.id = "'.$id.'" ');
       

        $univCourse = DB::select('SELECT * FROM  re_admissions a, courses c, readmissioncourses k WHERE c.id = k.reunivecoursid AND a.id = k.reinvid AND a.id = "'.$id.'" ');

         $installmentfees = DB::select("SELECT * FROM readmissioninstallmentfees WHERE reinvoid = '$id' ORDER BY id DESC");
         
        return view('superadmin.readmissions.admissionform',compact('aprocess','invvcoursed','installmentfees','univCourse'));
    }


    public function repayment($id,ReAdmission $reAdmission)
        {
             $paymentdetails = ReAdmission::find($id);
             $branc = Branch::all();
             $branc = Branch::all();
             $installmentfees = readmissioninstallmentfees::where('reinvoid',$id)->where('restatus',0)->orderBy('id','DESC')->get();
             $psource = PaymentSource::all();
      
             return view('superadmin.readmissions.makepayment',compact('paymentdetails','branc','installmentfees','psource'));
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

        return redirect('/re-payment-recipt/'.$paymentid)->with('success','Payment Successfully Done!!!');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ReAdmission  $reAdmission
     * @return \Illuminate\Http\Response
     */


    public function repaymentlist($id)
    {
        $aprocess = payment::where('reinviceid',$id)->get();
        $reaprocess = ReAdmission::find($id);
       return view('superadmin.student.repaymentlist',compact('aprocess','reaprocess'));
    }    
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
       
        

        return view('superadmin.readmissions.repaymentreceipt',compact('aprocess','invvcoursed','univCourse','paymentdata','makepayment','installdata'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ReAdmission  $reAdmission
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
        $directstudentsdata = ReAdmission::find($id);
        $ad = ReAdmission::all();
        $adcourses = Readmissioncourses::where('reinvid','=',$id)->get();
        $univcourses = Readmissioncourses::where('reinvid','=',$id)->get();
        $ademi = readmissioninstallmentfees::where('reinvoid','=',$id)->get();
        $ucats = UnviersitiesCategory::all();


        return view('superadmin.readmissions.edit',compact('alb','cours','leadsdata','branchdetails','course','taxesna','directstudentsdata','ad','adcourses','univcourses','ademi','ucats'));
    } 
    public function update($id,Request $request, ReAdmission $reAdmission)
    { 

       if($request->discounttype == "1")
        {
             $discoun = $request->discount1;
        }

        elseif($$request->discounttype == "2")
        {
            $discoun = $request->discount2;
        }
         
                                 if($request->universitiesss == 'BIT')
                                 {
                                    $dele = Readmissioncourses::where('reinvid',$id)->get();
                                    $dele->each->delete();
                                 }

                                 else
                                 {
                                     $deles = Readmissioncourses::where('reinvid',$id)->get();
                                     $deles->each->delete();
                                 }
                                
                                


              $updates = ReAdmission::find($id);
              $updates->rstudents = $request->studentname;
              $updates->rfnames = $request->fathersnames;
              $updates->rmnames = $request->mothersname;
              $updates->rsdobs = $request->dob;
              $updates->rsemails = $request->stuemail;
              $updates->rsphone = $request->phoneno;
              $updates->rswhatsappno = $request->whatsno;
              $updates->rsadate = $request->adate;
              $updates->rsbrnanch = $request->bran;
              $updates->rstobranches = $request->tobranchessw;
              $updates->rsuniversities = $request->universitiesss;
              $updates->rsstreet = $request->streets;
              $updates->rscity = $request->city;
              $updates->rsstate = $request->state;
              $updates->rszipcode = $request->zipcode;
              $updates->rspreferrabbletime = $request->preferrabletime;
              $updates->rrefeassignto = $request->assignto;
              $updates->rreferfrom = $request->refename;
              $updates->rrefername = $request->refrom;
              $updates->rsremarknotes = $request->remarknote;
              $updates->ripaymentmodes = $request->paymentmode;
              $updates->ridiscounttypes = $request->discounttype;
              $updates->risubtotal = $request->subtotal;
              $updates->rdiscounttotal = $request->discounttotal;
              $updates->ridiscount = $discoun;
              $updates->ritax = $request->tax;
              $updates->rgstprices = $request->gstprice;
              $updates->rinvtotal = $request->total;
              $updates->save();



              if($request->universitiesss == 'BIT')
                                 {

                                    $maincourse = $request->invcourse;
                                    $cmodes = $request->coursdataemode;
                                    $inmvsprice = $request->invprice;


                                   

                                     for($i=0; $i < (count($maincourse)); $i++)
                                        {
                                            
                                             $dakmsm = Readmissioncourses::updateOrCreate(['recourseid' => $maincourse[$i],'recoursemode' => $cmodes[$i],'recourseprice' => $inmvsprice[$i],'reinvid' => $id ]);

                                          
                  


                                        }
                                 }

                                 else
                                 {
                                    $univcourse = $request->unvicocurs;
                                    $admissfor = $request->admissionfor;
                                    $ufees = $request->univfees;

                                      for($i=0; $i < (count($univcourse)); $i++)
                                        {
                                            
                                             $dakmsm = Readmissioncourses::updateOrCreate(['reunivecoursid' => $univcourse[$i],'readmissionfor' => $admissfor[$i],'reunoverfeess' => $ufees[$i],'reinvid' => $id ]);

                                          
                  


                                        }
                                 }


                              return redirect('/all-re-admissions')->with('success','Admission Updated successfully!');
         

      


    } 

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ReAdmission  $reAdmission
     * @return \Illuminate\Http\Response
     */
    public function destroy(ReAdmission $reAdmission)
    {
        //
    }
}
