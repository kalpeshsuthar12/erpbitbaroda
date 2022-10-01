<?php

namespace App\Http\Controllers;
use App\invoices;
use App\students;
use App\Branch;
use App\course;
use App\invoicescourses;
use App\invoicesinstallmentfees;
use App\payment;
use App\leads;
use App\Source;
use App\followup;
use App\User;
use App\Tax;
use App\admissionprocess;
use App\coursecategory;
use App\admissionprocesscourses;
use App\admissionprocessinstallmentfees;
use App\ReAdmission;
use App\Readmissioncourses;
use App\readmissioninstallmentfees;
use App\PaymentSource;
use Illuminate\Http\Request;
use DB;
use Mail;
use PDF;
use Auth;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        
                 $userId = Auth::user()->id;
        
        $cour = course::all();
       $sourcedata = Source::get();
        $folss = followup::get();
         $userBranch = Auth::user()->branchs;
         $userdata = User::all();


         $userBranch = Auth::user()->branchs;
        $currentMonth = date('m');
        
       /* ->where('re_admissions.rstobranches',$userBranch)
        ->where('admissionprocesses.stobranches',$userBranch)*/

        
        $invoicesdata = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.stobranches',$userBranch)->whereMonth('payments.paymentdate',$currentMonth)->orderBy('payments.id','DESC')->get();

        $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as reid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rstobranches',$userBranch)->whereMonth('payments.paymentdate',$currentMonth)->orderBy('payments.id','DESC')->get();
                                                  
                    
                     
                           $cour = course::all();
                 $branchdata = Branch::get();
                 $userdata = User::get();
              $sourcedata = Source::get();
              $ccatall = coursecategory::get();
            
            return view('superadmin.invoice.manage',compact('reinvoicesdata','invoicesdata','folss','cour','branchdata','userdata','sourcedata','ccatall','currentMonth'));
    }


    public function maildetails($id)
    {
        $data = array();
        $getadmissionsdetails = admissionprocess::find($id);
        $getadmissionscoursesdetails = admissionprocesscourses::select('courses.coursename','admissionprocesscourses.*')->Join('courses','courses.id','=','admissionprocesscourses.courseid')->where('admissionprocesscourses.invid',$id)->get();

        $data["StudentsName"] = $getadmissionsdetails->studentname;
        $data["Studentserno"] = $getadmissionsdetails->serno;
        $data["studentsemails"] = $getadmissionsdetails->semails;
        $data["Courses"] = $getadmissionscoursesdetails;
        
        $pdf = PDF::loadView('superadmin.invoice.mailinvoice',compact('getadmissionsdetails','getadmissionscoursesdetails'));
                     
                     Mail::send('superadmin.invoice.invoicesmails', $data, function ($message) use ($data, $pdf) {
            $data;
            $message->to($data["studentsemails"],$data["studentsemails"])
                ->from('bitadmisson@gmail.com','BIT Baroda Institute Of Technology')
                ->cc('support@bitbaroda.com','Admission BIT')
                ->subject("Welcome letter to New Students.")
                ->attachData($pdf->output(),"Invoices.pdf");
        });

         
        if (Mail::failures()) {
                    dd('mailerror');
                } else {

                    return redirect()->back()->with('success','Invoice Sent Successfully!!!');

                }
    }

       public function mailreceipts($id)
    {   


             $data = array();
             $selectID = payment::find($id);
            $newId = $selectID->inviceid;
          $aprocess = admissionprocess::find($newId);

          $getadmissionscoursesdetails = admissionprocesscourses::select('courses.coursename','admissionprocesscourses.*')->Join('courses','courses.id','=','admissionprocesscourses.courseid')->where('admissionprocesscourses.invid',$aprocess->id)->get();

         $paymentdata = payment::where('inviceid',$newId)->first();

         $makepayment = DB::select('SELECT * FROM  admissionprocesses a, payments p WHERE a.id = p.inviceid AND a.id = "'.$newId.'" ');

         $installdata = admissionprocessinstallmentfees::leftJoin('payments', 'payments.installmentid', '=', 'admissionprocessinstallmentfees.id')->where('admissionprocessinstallmentfees.invoid',$newId)->orderBy('admissionprocessinstallmentfees.id','DESC')->get();  

            $data["StudentsName"] = $aprocess->studentname;
            $data["Studentserno"] = $aprocess->serno;
            $data["studentsemails"] = $aprocess->semails;
            $data["Courses"] = $getadmissionscoursesdetails;  

          $pdf = PDF::loadView('superadmin.invoice.paymentreceiptpdf',compact('aprocess','getadmissionscoursesdetails','paymentdata','installdata','makepayment'));
                     
                     Mail::send('superadmin.invoice.paymentreceiptsmails', $data, function ($message) use ($data, $pdf) {
            $data;
            $message->to($data["studentsemails"],$data["studentsemails"])
                ->from('bitadmisson@gmail.com','BIT Baroda Institute Of Technology')
                ->cc('support@bitbaroda.com','Admission BIT')
                ->subject("Admission details from BIT")
                ->attachData($pdf->output(),"paymentreceipts.pdf");
        });

         
        if (Mail::failures()) {
                    dd('mailerror');
                } else {

                    return redirect()->back()->with('success','Payment Receipts Sent Successfully!!!');

                }    
         

    }


    
     public function filterfees(Request $request)
      {
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
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

       //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();

         $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')->orderBy('payments.id','DESC')->get();


           $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->Where('re_admissions.rstudents', 'like', '%' .$namedatas. '%')->orderBy('payments.id','DESC')->get();


        

          return view('superadmin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }

      elseif($mobdatas = $request->getMobilesno)
      {
         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
        $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->orderBy('payments.id','DESC')->get();

          $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rsphone',$mobdatas)->orWhere('re_admissions.rswhatsappno',$mobdatas)->orderBy('payments.id','DESC')->get();

       

          return view('superadmin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Admission Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::get();
                  $ccatall = coursecategory::get();

               

                $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.id','DESC')->get();


                 $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.id','DESC')->get();

               

                return view('superadmin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

          elseif($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

             
                $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.id','DESC')->get();


                 $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.id','DESC')->get();
               

                return view('superadmin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
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
                   
                   //dd("test");
                
                  $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesscourses.courseid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();


                    $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->where('readmissioncourses.recourseid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();
               

                return view('superadmin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));

               }


               else
               {
                     $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();


                    $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->where('readmissioncourses.reunivecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->orderBy('payments.id','DESC')->get();
               

                return view('superadmin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
               }


      }

    


      elseif($sources = $request->sourceSearch)
      {
         $starsdates = $request->sdatestat;
         $enssdates = $request->sdateend;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

        

          
          $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->orderBy('payments.id','DESC')->get();



         $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.radmsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->orderBy('payments.id','DESC')->get();
         
         

          return view('superadmin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
      }



     
      elseif($asearch = $request->AssignedToSearch)
      {
         $asdates = $request->AstartDate;
         $aenddates = $request->AEndDate;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

  

          $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])->orderBy('payments.id','DESC')->get();
               
             $reinvoicesdata = "";

                return view('superadmin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
      }


      elseif($bransdata = $request->branchSearchDatas)
      {
         $bstartdate = $request->BStartDate;
         $benddate = $request->BEnddate;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

       
          $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->where('admissionprocesses.stobranches',$bransdata)->orderBy('payments.id','DESC')->get();
         
         
         $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->where('re_admissions.rstobranches', $bransdata)->orderBy('payments.id','DESC')->get();

           
        
                //  dd($rmems);
                 
                   
               

                            

                return view('superadmin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
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

                     
                     $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->whereIn('admissionprocesscourses.courseid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get();



                     $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->whereIn('readmissioncourses.recourseid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get(); 
                     
                    

                      return view('superadmin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
                  }


                  else
                  {
                    

                     $namesfinds = payment::join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->select('admissionprocesses.*','payments.*','admissionprocesses.id as aid','payments.id as pids')->whereIn('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get();



                     $reinvoicesdata = payment::select('re_admissions.*','payments.*','payments.id as pids','re_admissions.id as rid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->whereIn('readmissioncourses.reunivecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.id','DESC')->get(); 
                     
                    

                      return view('superadmin.invoice.filterfees',compact('reinvoicesdata','namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
                  }
           
          }  
      }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($studentsid,students $students,Branch $branch,course $course,Tax $tax)
    {
        //
        $studentdetails = students::get();
        $getstudents = students::find($studentsid);
        $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();
        return view('superadmin.invoice.create',compact('studentdetails','branchdetails','course','taxesna','getstudents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,invoicescourses $invoicescourses,invoicesinstallmentfees $invoicesinstallmentfees)
    {
        //

       //dd($request->all());

        $sjinvno = "0";
        $mjinvno = "0";
        $waginvno = "0";

        
        $discoun = "NULL";

        $studentname = $request->sname;
        $idate = $request->invoicedate;
        $ddate = $request->duedate;
        $branchdata = $request->brnach;
        $invno = $request->invno;
        $pmode = $request->paymentmode;
        $dtype = $request->discounttype;
        $subto = $request->subtotal;
        $tot = $request->total;
        $raxe = $request->taxs;
       
        $discoun2 = $request->discount2;

        $inoviceno = explode("/",$invno);
       
        if($inoviceno[0] == 'Inv-BITSJ')
        {
            $sjinvno = $inoviceno[3];

           
        }
        else if($inoviceno[0] == 'Inv-BITMJ')
        {
            $mjinvno = $inoviceno[3];
         
        }
        elseif($inoviceno[0] == 'Inv-BITWG')
        {
            $waginvno = $inoviceno[3];
        }


        if($dtype == "2")
        {
             $discoun = $request->discount1;
        }

        elseif($dtype == "1")
        {
            $discoun = $request->discount2;
        }

        if($pmode == "EMI") 

        {
            $invoicesmodel = new invoices();
                    $invoices = $invoicesmodel->create([
                        'studentid' => $studentname,
                        'branchId' => $branchdata,
                        'branchInvno' => $invno,
                        'sjIno' => $sjinvno,
                        'mjIno' => $mjinvno,
                        'wgIno' => $waginvno,
                        'discounttype' => $dtype,
                        'paymentmode' => $pmode,
                        'invdate' => $idate,
                        'duedate' => $ddate,
                        'subtotal' => $subto,
                        'invtotal' => $tot,
                        'discount' => $discoun,
                        'taxes' => $raxe,

                    ]);

                    $invoicesid = $invoices->id;
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
                                $invoicescourses = new invoicescourses([
                                
                                'invid' => $invoicesid,
                                'courseid'   => $coursesdata[$i],
                                'coursemode'   => $csmode[$i],
                                'courseprice'   => $courseprice[$i],
                                
                            ]);
                            $invoicescourses->save();
                    }

                    for($k=0; $k <(count($installdate)); $k++)
                    {
                        $invoicesinstallmentfees = new invoicesinstallmentfees([
                            
                            'invoid' => $invoicesid,
                            'invoicedate'   => $installdate[$k],
                            'installmentamount'   => $installprice[$k],
                            'pendinamount'   => $pamount[$k],

                        ]);

                         $invoicesinstallmentfees->save();  
                    }


         return redirect('/create-payment/'.$invoicesid);

        }

        else
        {


                    $invoicesmodel = new invoices();
                    $invoices = $invoicesmodel->create([
                        'studentid' => $studentname,
                        'branchId' => $branchdata,
                        'branchInvno' => $invno,
                        'sjIno' => $sjinvno,
                        'mjIno' => $mjinvno,
                        'wgIno' => $waginvno,
                        'discounttype' => $dtype,
                        'paymentmode' => $pmode,
                        'invdate' => $idate,
                        'duedate' => $ddate,
                        'subtotal' => $subto,
                        'invtotal' => $tot,
                        'discount' => $discoun,
                        'taxes' => $raxe,

                    ]);

                    $invoicesid = $invoices->id;
                    $coursesdata = $request->invcourse;
                    $courseprice = $request->invprice;
                    $cd = $request->duration;
                    $ct = $request->tax;

                    for($i=0; $i < (count($coursesdata)); $i++)
                    {
                                $invoicescourses = new invoicescourses([
                                
                                'invid' => $invoicesid,
                                'courseid'   => $coursesdata[$i],
                                'courseprice'   => $courseprice[$i],
                                'durations'   => $cd[$i],
                                'tax'   => $ct[$i],
                            ]);
                            $invoicescourses->save(); 
                    }

                 
                  
                return redirect('/create-payment/'.$invoicesid);


        }


    }


    public function viewinvoice($id)
    {

         $aprocess = admissionprocess::find($id);
       
            //$reaprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$id.'" ');
       

        $univCourse = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.univecoursid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");
         
        return view('superadmin.invoice.newinvoice',compact('aprocess','invvcoursed','installmentfees','univCourse'));
    }

     

    public function admissionform($id)
    {


        $aprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $univCourse = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.univecoursid AND a.id = k.invid AND a.id = "'.$id.'" ');

         //$installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");

         //$paymentdata = payment::where('inviceid',$id)->get();

        
        

        return view('superadmin.invoice.admissionform',compact('aprocess','invvcoursed','univCourse'));

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

        
        

        return view('superadmin.invoice.paymentreceipt',compact('aprocess','invvcoursed','univCourse','paymentdata','makepayment','installdata','selectID'));

    }
    
    public function idcards($id)
     {
        $selectID = payment::find($id);
            $newId = $selectID->inviceid;

        $aprocess = admissionprocess::find($id);

            //dd($aprocess);

        $invvcoursed = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.courseid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $installmentfees = DB::select("SELECT * FROM admissionprocessinstallmentfees WHERE invoid = '$id' ORDER BY id DESC");

         $univCourse = DB::select('SELECT * FROM  admissionprocesses a, courses c, admissionprocesscourses k WHERE c.id = k.univecoursid AND a.id = k.invid AND a.id = "'.$id.'" ');

         $paymentdata = payment::where('inviceid',$id)->first();

         $makepayment = DB::select('SELECT * FROM  admissionprocesses a, payments p WHERE a.id = p.inviceid AND a.id = "'.$newId.'" ');

         /*$installmentdata = DB::SELECT('SELECT * FROM  admissionprocesses a, payments p WHERE a.id = p.inviceid AND a.id = "'.$id.'" ');*/

         /*$installdata = DB::select("SELECT * FROM admissionprocessinstallmentfees f, admissionprocesses a, payments p  WHERE a.id = p.inviceid AND a.id = f.invoid AND a.id = '$id' ORDER BY f.id DESC");*/

         $installdata = admissionprocessinstallmentfees::leftJoin('payments', 'payments.installmentid', '=', 'admissionprocessinstallmentfees.id')->where('admissionprocessinstallmentfees.invoid',$id)->orderBy('admissionprocessinstallmentfees.id','DESC')->get();        
         

        

         /*$payments = payment::where('inviceid',$id)->first();*/

        // dd($payments);
      //   dd($payments);

        
        

        return view('superadmin.invoice.idcards',compact('aprocess','invvcoursed','univCourse','paymentdata','makepayment','installdata','selectID'));

    }
    
    
    public function uploadstudentsimages($id)
        {

            return view('superadmin.invoice.uploadimages',compact('id'));

        }

   public function updatestudentsimages(Request $request,$id)
         {
                $aprocess = admissionprocess::find($id);


             /*  $image = $request->file('image');
                            $imageName = $image->getClientOriginalName();
                            $name = time().'.'.$image->getClientOriginalExtension();
                            $destinationPath = public_path('/studentsimges');

                            $image->move($destinationPath, $imageName);
                        $imgs = base64_encode(file_get_contents($image));*/

                         /*$base64Image = explode(";base64,", $request->image);
                         $explodeImage = explode("image/", $base64Image[0]);
                          $imageName = $explodeImage[1];
                          $image_base64 = base64_decode($base64Image[1]);*/
                          //$file = $folderPath . uniqid() . '. '.$imageName;


                      /*  $path = $request->file('image')->getRealPath();
            $imgs = file_get_contents($path);
            $base64 = base64_encode($imgs);*/
            /*$account->logo = $base64;*/

                         //dd($base64);
            $image = $request->file('image');
             $imagedata = file_get_contents($image);
            $base64 = base64_encode($imagedata);
           /* $oUser->avatar = $base64;
            $oUser->update();*/

                  $aprocess->studentsimgs = $base64;
                  $aprocess->save();

                  return redirect('/student')->with('success','Students Images Is Uploaded Successfully!!');
        }


 

    

    /**
     * Display the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show(invoices $invoices)
    {   

        /*$student = admissionprocess::all();*/
        //   $admiId = admissionprocess::pluck('id');
      
         $currentMonth = date('m');
          $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('admissionprocesses.id','DESC')->get(); 


        $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->groupBy('payments.inviceid')
         ->orderBy('admissionprocesses.id','DESC')
         ->get();


         $ReNewPayment = \DB::table('re_admissions')
          ->leftJoin('payments', 'payments.reinviceid', '=', 're_admissions.id')
          ->whereMonth('re_admissions.rsadate',$currentMonth)
          ->select('re_admissions.*','payments.*','re_admissions.id as remid')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('re_admissions.id = payments.reinviceid')
                                                                 ->groupBy('payments.reinviceid');
        
                                                            })->orderBy('re_admissions.id','DESC')->get(); 


        $ReWiPayment = ReAdmission::select('re_admissions.*', 'payments.*','re_admissions.id as remid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.reinviceid', '=', 're_admissions.id')
         ->whereMonth('payments.paymentdate',$currentMonth)
         ->groupBy('payments.reinviceid')
         ->orderBy('re_admissions.id','DESC')
         ->get();
        
        $pendamount = $NewPayment->merge($WiPayment);
        $rependamount = $ReNewPayment->merge($ReWiPayment);

        $invototal = $pendamount->sum('invtotal');
        
        $retotal = $rependamount->sum('rinvtotal');

        //dd($invototal);

        $sumtotal = $invototal + $retotal;
          
         $pamenreceived = $pendamount->sum('paymentreceived');; 
         

         
         $repaymreceived = $rependamount->sum('paymentreceived');; 
         
          
            $totslreceived = $pamenreceived + $repaymreceived;

            $remainingamount = $sumtotal - $totslreceived;


      //  dd($pendamount);
        //return view('superadmin.invoice.pendingamount',compact('pendamount'));

                $cour = course::all();
                 $branchdata = Branch::get();
                 $userdata = User::get();
              $sourcedata = Source::get();
              $ccatall = coursecategory::get();
              $folss = followup::get();

        
        return view('superadmin.invoice.totalinvoice',compact('pendamount','rependamount','sumtotal','totslreceived','remainingamount','cour','sourcedata','folss','branchdata','userdata','ccatall'));
    }
    
     public function filtertotalinvoices(Request $request)
     {
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
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

          //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();

             


              $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('payments.paymentdate','DESC')->get(); 


              $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
               ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
               ->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')
               ->groupBy('payments.inviceid')
               ->orderBy('payments.paymentdate','DESC')
               ->get();

              $namesfinds = $NewPayment->merge($WiPayment);
           

             return view('superadmin.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
         }

         elseif($mobdatas = $request->getMobilesno)
         {
            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
            //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)->orderBy('payments.paymentdate','DESC')->get();

             $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('payments.paymentdate','DESC')->get(); 


              $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
               ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
               ->Where('admissionprocesses.sphone', $mobdatas)->orwhere('admissionprocesses.swhatsappno',$mobdatas)
               ->groupBy('payments.inviceid')
               ->orderBy('payments.paymentdate','DESC')
               ->get();

              $namesfinds = $NewPayment->merge($WiPayment); 

          

             return view('superadmin.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
         }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Admission Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::get();
                  $ccatall = coursecategory::get();

               

              // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])->orderBy('payments.paymentdate','DESC')->get(); 

                  $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('payments.paymentdate','DESC')->get(); 


              $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
               ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
               ->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])
               ->groupBy('payments.inviceid')
               ->orderBy('payments.paymentdate','DESC')
               ->get();

              $namesfinds = $NewPayment->merge($WiPayment); 
               

                return view('superadmin.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

             elseif($datesfor == "Payment Date")
            {


               $folss = followup::get();
               $userdata = User::get();
                  $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::get();
                  $ccatall = coursecategory::get();

                 //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->whereBetween('payments.paymentdate',[$startdates,$enddats])->orderBy('payments.paymentdate','DESC')->get(); 

                    $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->whereBetween('payments.paymentdate',[$startdates,$enddats])
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('payments.paymentdate','DESC')->get(); 


              $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
               ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
               ->whereBetween('payments.paymentdate',[$startdates,$enddats])
               ->groupBy('payments.inviceid')
               ->orderBy('payments.paymentdate','DESC')
               ->get();

              $namesfinds = $NewPayment->merge($WiPayment); 
                  

                   return view('superadmin.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
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

           // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get();

              $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->leftJoin('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('payments.paymentdate','DESC')->get(); 


              $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
               ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
               ->leftJoin('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
               ->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
               ->groupBy('payments.inviceid')
               ->orderBy('payments.paymentdate','DESC')
               ->get();

              $namesfinds = $NewPayment->merge($WiPayment);  
            

             return view('superadmin.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
         }




         elseif($sources = $request->sourceSearch)
         {
            $starsdates = $request->sdatestat;
            $enssdates = $request->sdateend;

            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

           

             //$namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])->orderBy('payments.paymentdate','DESC')->get(); 

                $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('payments.paymentdate','DESC')->get(); 


              $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
               ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
               ->where('admissionprocesses.admsisource',$sources)->whereBetween('payments.paymentdate',[$starsdates,$enssdates])
               ->groupBy('payments.inviceid')
               ->orderBy('payments.paymentdate','DESC')
               ->get();

              $namesfinds = $NewPayment->merge($WiPayment);
            

             return view('superadmin.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
         }



      


         elseif($asearch = $request->AssignedToSearch)
         {
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

     

               // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])->groupBy('payments.inviceid')->orderBy('payments.paymentdate','DESC')->get(); 
                    $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('payments.paymentdate','DESC')->get(); 


              $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
               ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
               ->where('admissionprocesses.admissionsusersid',$asearch)->whereBetween('payments.paymentdate',[$asdates,$aenddates])
               ->groupBy('payments.inviceid')
               ->orderBy('payments.paymentdate','DESC')
               ->get();

              $namesfinds = $NewPayment->merge($WiPayment);
                

                   return view('superadmin.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
         }


         elseif($bransdata = $request->branchSearchDatas)
         {
            $bstartdate = $request->BStartDate;
            $benddate = $request->BEnddate;

            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

           // $namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();

            // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.stobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])->orderBy('payments.paymentdate','DESC')->get();

             $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesses.stobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('payments.paymentdate','DESC')->get(); 


              $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
               ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
               ->where('admissionprocesses.stobranches',$bransdata)->whereBetween('payments.paymentdate',[$bstartdate,$benddate])
               ->groupBy('payments.inviceid')
               ->orderBy('payments.paymentdate','DESC')
               ->get();

              $namesfinds = $NewPayment->merge($WiPayment); 
                  
                

                   return view('superadmin.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
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
              //dd($findcourse);

              /* foreach($findcourse as $courses)
               {
                     $getourses = $courses->coursename;

               }*/

             //  dd($findcourse);

         

            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.id','DESC')->get();

           // $namesfinds = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])->orderBy('payments.paymentdate','DESC')->get(); 

                 $NewPayment = \DB::table('admissionprocesses')
          ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
          ->leftJoin('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
          ->select('admissionprocesses.*','payments.*','admissionprocesses.id as admid')->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])
                                                        ->whereNotExists( function ($query) {
                                                        $query->select(DB::raw(1))
                                                                ->from('payments')
                                                                ->whereRaw('admissionprocesses.id = payments.inviceid')
                                                                 ->groupBy('payments.inviceid');
        
                                                            })->orderBy('payments.paymentdate','DESC')->get(); 


              $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
               ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
               ->leftJoin('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
               ->where('admissionprocesscourses.courseid',$findcourse)->orWhere('admissionprocesscourses.univecoursid',$findcourse)->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])
               ->groupBy('payments.inviceid')
               ->orderBy('payments.paymentdate','DESC')
               ->get();

              $namesfinds = $NewPayment->merge($WiPayment); 
                  
                 

                   return view('superadmin.invoice.filtertotalinvoices',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
         }  
      }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $studentsdata = $request->studentsname;

         $data= array();

        $leagues = DB::table('admissionprocesses')
                    ->select('admissionprocesses.*','payments.paymentreceived')
                    ->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
                    ->where('admissionprocesses.studentname', $studentsdata)
                    ->get();

        foreach($leagues as $res)
        {
            $row = array();
            $row[] = $res->studentname;
            $row[] = $res->semails;
            $row[] = $res->sphone;
            $row[] = $res->sbrnanch;
            $row[] = $res->stobranches;
            $row[] = $res->serno;
            $row[] = $res->Invoiceno;
            $row[] = $res->paymentreceived;
            $row[] = '<a href="/paymentreceipt/'.$res->id.'" class="btn btn-outline-primary"><i class="fa fa-eye"></i></a>';
           
           
            
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);

            /*dd($leagues);*/

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        $deletesdata = admissionprocess::find($id);
        $deletesdata->delete();

        return redirect('/invoice')->with('success','Invoice Deleted Successfully!!'); 
    }


    public function ajaxstudentaddress($studentId,students $students)
    {
        $studentdata = students::where('id',$studentId)->get();
       

          return response()->json($studentdata);
    }

    public function branchinvoice($branchId,Branch $branch)
    {

        $year = date("Y");
         $month = date("m");
         if($branchId == "1")
        {

               // $latest = DB::select("SELECT sjerno from students order by sjerno DESC LIMIT 1");
           
            $latests = admissionprocess::where('Ibranchs','=',$branchId)->latest()->get()->pluck('Isjno');
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
             $latests = admissionprocess::where('Ibranchs','=',$branchId)->latest()->get()->pluck('Imjno');
            /*$latests = admissionprocess::get()->pluck('Imjno')->toArray();*/
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'INV-BITMJ/'.$year.'/'.$month.'/'.$code_nos;
            return response()->json($value);
        }

         else if ($branchId == "3") 
        {
            $latests = admissionprocess::where('Ibranchs','=',$branchId)->latest()->get()->pluck('Iwgno');
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
            $latests = admissionprocess::where('Ibranchs','=',$branchId)->latest()->get()->pluck('Ibitolno');
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
            $latests = admissionprocess::where('Ibranchs','=',$branchId)->latest()->get()->pluck('Icvrublno');
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
            $latests = admissionprocess::where('Ibranchs','=',$branchId)->latest()->get()->pluck('Icvrukhno');   
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
            $latests = admissionprocess::where('Ibranchs','=',$branchId)->latest()->get()->pluck('Irntuno');
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
            $latests = admissionprocess::where('Ibranchs','=',$branchId)->latest()->get()->pluck('Imanipalno');
           /* $lates = admissionprocess::get()->pluck('Imanipalno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'INV-MANIPAL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        
          
    }

    public function coursedetail(course $course,$corsemodeve,$cours)
    {
        if($corsemodeve == 'Online Mode')
        {   

            //$selectedprice = DB::select("SELECT courseonlineprice,coursedurations FROM courses WHERE id = '".$cours."'");
            $selectedprice = course::where('id',$cours)->pluck('courseonlineprice');
            return response()->json($selectedprice);
        }

        elseif($corsemodeve == 'Offline Mode')
        {
            //$selectedprice = DB::select("SELECT courseprice,coursedurations FROM courses WHERE id = '".$cours."'");
            $selectedprice = course::where('id',$cours)->pluck('courseprice');

            return response()->json($selectedprice);
        }
        
           else if($corsemodeve == "Exam Fees")
        {
            $selectedprice = 5000;

             return response()->json($selectedprice);
        }

        else if($corsemodeve == "Project Fees")
        {
            $selectedprice = 5000;
             return response()->json($selectedprice);
        }

        else if($corsemodeve == "Transfer Fees")
        {
            $selectedprice = 5000;
             return response()->json($selectedprice);
        }
        else if($corsemodeve == "Rejoining Fees")
        {
            $selectedprice = 5000;
             return response()->json($selectedprice);
        }
        
        else if($corsemodeve == "RM")
        {
            $selectedprice = course::where('id',$cours)->pluck('courseprice');
             return response()->json($selectedprice);
        }
        
           // return response()->json($dataprice);
    }



   public function payment($id)
    {

        $paymentdetails = admissionprocess::find($id);
        $branc = Branch::all();
        $installmentfees = admissionprocessinstallmentfees::where('invoid',$id)->where('status',0)->orderBy('id','DESC')->get();
        $psource = PaymentSource::all();
      
        return view('superadmin.invoice.create',compact('paymentdetails','branc','installmentfees','psource'));
    }

    public function repayment($id)
    {

        $paymentdetails = admissionprocess::find($id);
        $paymentsse = payment::where('inviceid',$id)->orderBy('id','DESC')->take(1)->get();
        $branc = Branch::all();
        $installmentfees = admissionprocessinstallmentfees::where('invoid',$id)->where('status',0)->orderBy('id','DESC')->get();
        
      $psource = PaymentSource::all();
        return view('superadmin.invoice.repayment',compact('paymentdetails','branc','installmentfees','paymentsse','psource'));
    }
    public function getpendingamount($id)
    {

        //$paymentdetails = admissionprocess::find($id);
        $paymentdetails = admissionprocess::find($id);
        $paymentsse = payment::where('inviceid',$id)->sum('remainingamount');

       // dd($paymentsse);
        return view('superadmin.invoice.getpendingamount',compact('paymentdetails','paymentsse'));
    }


     public function paymentstore(Request $request,$id,payment $payment)
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
        



        return redirect('/paymentreceipt/'.$paymentid)->with('success','Payment Successfully Done!!!');
    }
    public function restorepayment(Request $request,$id,payment $payment)
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
             'instatus'=> '1',
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
        



        return redirect('/paymentreceipt/'.$paymentid)->with('success','Payment Successfully Done!!!');
    }

    public function multipleprice($cour,$coursmode,course $course)

    {
        if($coursmode == "Online Mode")

        {

            $corprice = course::where('id',$cour)->pluck('courseonlineprice');
                return response()->json($corprice);
        }
        elseif($coursmode == "Offline Mode")

        {

            $corprice = course::where('id',$cour)->pluck('courseprice');
                return response()->json($corprice);
        }
         else if($coursmode == "Exam Fees")
        {
            $selectedprice = 5000;

             return response()->json($selectedprice);
        }

        else if($coursmode == "Project Fees")
        {
            $selectedprice = 5000;
             return response()->json($selectedprice);
        }

        else if($coursmode == "Transfer Fees")
        {
            $selectedprice = 5000;
             return response()->json($selectedprice);
        }
        
        else if($coursmode == "Rejoining Fees")
        {
            $selectedprice = 5000;
             return response()->json($selectedprice);
        }
        
       
    }

    public function pendingamount()
    {

       
      
        $currentMonth = date('m');
         


        $WiPayment = admissionprocess::select('admissionprocesses.*', 'payments.*','admissionprocesses.id as admid','payments.id as pids')
         ->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->groupBy('payments.inviceid')
         ->orderBy('payments.id','DESC')
         ->get();


 

        $ReWiPayment = ReAdmission::select('re_admissions.*','payments.id as pids','payments.*','re_admissions.id as reid')
         ->join('payments', 'payments.reinviceid', '=', 're_admissions.id')
         ->orderBy('payments.id','DESC')
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
        return view('superadmin.invoice.pendingamount',compact('pendamount','rependamount','sumtotal','totslreceived','remainingamount','folss','userdata','cour','sourcedata','branchdata','ccatall'));

    }


     public function filterpendingfees(Request $request)
     {
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
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

       //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();

          $namesfinds =  admissionprocess::select('admissionprocesses.*','payments.id as pids','payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->Where('admissionprocesses.studentname', 'like', '%' .$namedatas. '%')
         ->groupBy('payments.inviceid')
         ->get();
        

          return view('superadmin.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }

      elseif($mobdatas = $request->getMobilesno)
      {
         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

         //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
         $namesfinds =  admissionprocess::select('admissionprocesses.*','payments.id as pids','payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
        ->Where('admissionprocesses.sphone', $mobdatas)
        ->orwhere('admissionprocesses.swhatsappno',$mobdatas)
         ->groupBy('payments.inviceid')
         ->get();
       

          return view('superadmin.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
      }


      elseif($datesfor = $request->DateFor)
      {  
         $startdates = $request->datestat;
         $enddats = $request->dateend;

         if($datesfor == "Admission Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
                  $sourcedata = Source::all();
                  $branchdata = Branch::get();
                  $ccatall = coursecategory::get();

               

               $namesfinds = admissionprocess::select('admissionprocesses.*', 'payments.*','payments.id as pids','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
       ->whereBetween('admissionprocesses.sadate',[$startdates,$enddats])
         ->groupBy('payments.inviceid')
         ->get();
               

                return view('superadmin.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
            }

          elseif($datesfor == "Payment Date")
         {


            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

              $namesfinds = admissionprocess::select('admissionprocesses.*','payments.id as pids','payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
       ->whereBetween('payments.paymentdate',[$startdates,$enddats])
         ->groupBy('payments.inviceid')
         ->get(); 
               

                return view('superadmin.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
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

         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.id as pids','payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->leftJoin('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
        ->where('admissionprocesscourses.courseid',$coursedatas)->orWhere('admissionprocesscourses.univecoursid',$coursedatas)->whereBetween('payments.paymentdate',[$cstartsdates,$cendsdates])
         ->groupBy('payments.inviceid')
         ->get(); 
         

          return view('superadmin.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
      }

    

      elseif($sources = $request->sourceSearch)
      {
         $starsdates = $request->sdatestat;
         $enssdates = $request->sdateend;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

        
        $namesfinds = admissionprocess::select('admissionprocesses.*','payments.id as pids','payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('admissionprocesses.admsisource',$sources)
         ->whereBetween('payments.paymentdate',[$starsdates,$enssdates])
         ->groupBy('payments.inviceid')
         ->get(); 
         

          return view('superadmin.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
      }


      elseif($asearch = $request->AssignedToSearch)
      {
         $asdates = $request->AstartDate;
         $aenddates = $request->AEndDate;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

  

          

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.id as pids','payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('admissionprocesses.admissionsusersid',$asearch)
         ->whereBetween('payments.paymentdate',[$asdates,$aenddates])
         ->groupBy('payments.inviceid')
         ->get(); 
               
             

                return view('superadmin.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
      }


      elseif($bransdata = $request->branchSearchDatas)
      {
         $bstartdate = $request->BStartDate;
         $benddate = $request->BEnddate;

         $folss = followup::get();
         $userdata = User::get();
            $cour = course::all();
            $sourcedata = Source::all();
            $branchdata = Branch::get();
            $ccatall = coursecategory::get();

        // $namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();

          

          $namesfinds = admissionprocess::select('admissionprocesses.*','payments.id as pids','payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->where('admissionprocesses.stobranches',$bransdata)
         ->whereBetween('payments.paymentdate',[$bstartdate,$benddate])
         ->groupBy('payments.inviceid')
         ->get();
               
             

                return view('superadmin.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
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
           //dd($findcourse);

           /* foreach($findcourse as $courses)
            {
                  $getourses = $courses->coursename;

            }*/

          //  dd($findcourse);

         $namesfinds = admissionprocess::select('admissionprocesses.*','payments.id as pids','payments.*','admissionprocesses.id as admid', DB::raw('SUM(payments.paymentreceived) As paymentreceived'))
         ->leftJoin('payments', 'payments.inviceid', '=', 'admissionprocesses.id')
         ->leftJoin('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')
         ->where('admissionprocesscourses.courseid',$findcourse)
         ->orWhere('admissionprocesscourses.univecoursid',$findcourse)
         ->whereBetween('payments.paymentdate',[$cstartdate,$cenddate])
         ->groupBy('payments.inviceid')
         ->get(); 
               
              

                return view('superadmin.invoice.filterpendingamount',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
      }  

  }

    public function generatereceiptno($brancgs)
    {
         //$year = date("Y");
        // $month = date("m");

         if($brancgs == "BITSJ")
        {
            
            //$latests = admissionprocess::get()->pluck('sjerno');

            //$latests = admissionprocess::where('prefix_id', $current_prefix->id)->max('number') + 1;
            $latests = payment::where('branchs','=',$brancgs)->latest()->get()->pluck('sjrecpno');
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

            
            $latests = payment::where('branchs','=',$brancgs)->latest()->get()->pluck('mjrecpno');
            //$latests = admissionprocess::get()->pluck('mjerno')->toArray();
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITMJ/'.$code_nos;
            return response()->json($value);
        }

          else if($brancgs == "BITWG") 
        {

            
            $latests = payment::where('branchs','=',$brancgs)->latest()->get()->pluck('wgrecpno');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITWG/'.$code_nos;
            return response()->json($value);
        }

          else if($brancgs == "BITEL") 
        {

            
            $latests = payment::where('branchs','=',$brancgs)->latest()->get()->pluck('elrecpno');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITEL/'.$code_nos;
            return response()->json($value);
        }

         else if($brancgs == "BITOL") 
        {
           
            $latests = payment::where('branchs','=',$brancgs)->latest()->get()->pluck('bitolrecpno');
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
           
            $latests = payment::where('stobranches','=',$brancgs)->latest()->get()->pluck('cvrublrecpno');
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
           
            $latests = payment::where('branchs','=',$brancgs)->latest()->get()->pluck('cvrukhrecpno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(KH)/'.$kode;
            return response()->json($value);
        }
         else if($brancgs == "RNTU") 
        {
           
            $latests = payment::where('branchs','=',$brancgs)->latest()->get()->pluck('rnturecpno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'RNTU/'.$kode;
            return response()->json($value);
        }
        else if($brancgs == "MANIPAL") 
        {
           
            $latests = payment::where('branchs','=',$brancgs)->latest()->get()->pluck('manipalrecpno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'MANIPAL/'.$kode;
            return response()->json($value);
        }
        

    }
    
     public function changeemi($id)
    {

       $adm = admissionprocess::find($id); 
      // $allinstallmentdata = admissionprocessinstallmentfees::where('invoid',$id)->orderBy('id','DESC')->get();
       $latestpaymentdata = payment::where('inviceid',$id)->orderBy('id','DESC')->first();
       //dd($latestpaymentdata);
       $getemidata = payment::where('inviceid',$id)->get();
       
       foreach($getemidata as $emis)
       {
           $getinstallmentdata = admissionprocessinstallmentfees::where('id',$emis->installmentid)->get();
       }
       

        return view('superadmin.invoice.changeemi',compact('getinstallmentdata','adm','latestpaymentdata'));
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

   
            return redirect('/re-payment/'.$id)->with('success','EMI Successfully Changed !!');
            
            /* return redirect('/paymentreceipt/'.$paymentid)->with('success','Payment Successfully Done!!!');*/

    }
    
    
    public function generatenrollmentno($branchsw)
    {
         $year = date("Y");
        $month = date("m");

         if($branchsw == "BITSJ")
        {
            
        $latests = payment::whereNotNull('studenterno')->where('branchs','=',$branchsw)->latest()->get()->pluck('sjerno');
            //$latests = admissionprocess::get()->pluck('sjerno')->toArray();
            $sj = isset($latests[0]) ? $latests[0] : false;
            $counts = $sj + 1;
            $kode = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITSJ/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
                
            
        
        
        }

        else if($branchsw == "BITMJ") 
        {

            
               /*    $latests = payment::where('branchs','=',$branchsw)->latest()->get()->pluck('mjerno');*/
             $latests = payment::whereNotNull('studenterno')->where('branchs','=',$branchsw)->latest()->get()->pluck('mjerno');
            //$latests = admissionprocess::get()->pluck('mjerno')->toArray();
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $kode = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITMJ/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }

          else if($branchsw == "BITWG") 
        {

                   
                   /*  $latests = payment::where('branchs','=',$branchsw)->latest()->get()->pluck('wgerno');*/
             $latests = payment::whereNotNull('studenterno')->where('branchs','=',$branchsw)->latest()->get()->pluck('wgerno');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $kode = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITWG/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }

        else if($branchsw == "BITEL") 
        {
       // dd($branchsw);
            
              
           /*$latests = payment::whereNotNull('studenterno')->where('branchs','=',$branchsw)->latest()->get()->pluck('elernos');
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $kode = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITEL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);*/
            
             $latests = payment::whereNotNull('studenterno')->where('branchs','=',$branchsw)->latest()->get()->pluck('elernos');
            //$latests = admissionprocess::get()->pluck('sjerno')->toArray();
            $sj = isset($latests[0]) ? $latests[0] : false;
            $counts = $sj + 1;
            $kode = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BITEL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }

         else if($branchsw == "BITOL") 
        {
           
            /*$latests = payment::where('branchs','=',$branchsw)->latest()->get()->pluck('bitolerno');*/
            $latests = payment::whereNotNull('studenterno')->where('branchs','=',$branchsw)->latest()->get()->pluck('bitolerno');
            /*$lates = admissionprocess::get()->pluck('wgerno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'BITOL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if($branchsw == "CVRU(BL)") 
        {
            $latests = payment::whereNotNull('studenterno')->where('branchs','=',$branchsw)->latest()->get()->pluck('cvrublerno');
            /*            $latests = payment::where('stobranches','=',$branchsw)->latest()->get()->pluck('cvrublerno');*/
            /*$lates = admissionprocess::get()->pluck('wgerno')->toArray();*/
            //dd($lates);
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(BL)/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if($branchsw == "CVRU (KH)") 
        {
           $latests = payment::whereNotNull('studenterno')->where('branchs','=',$branchsw)->latest()->get()->pluck('cvrukherno');
           // $latests = payment::where('branchs','=',$branchsw)->latest()->get()->pluck('cvrukherno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'CVRU(KH)/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
         else if($branchsw == "RNTU") 
        {
           $latests = payment::whereNotNull('studenterno')->where('branchs','=',$branchsw)->latest()->get()->pluck('rntuerno');
            //$latests = payment::where('branchs','=',$branchsw)->latest()->get()->pluck('rntuerno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value = 'RNTU/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        else if($branchsw == "MANIPAL") 
        {
           $latests = payment::whereNotNull('studenterno')->where('branchs','=',$branchsw)->latest()->get()->pluck('manipalerno');
            //$latests = payment::where('branchs','=',$branchsw)->latest()->get()->pluck('manipalerno');
            $wg = isset($lates[0]) ? $lates[0] : false;
            $counted = $wg + 1;
            $kode = str_pad($counted, 4, "0", STR_PAD_LEFT);
            $value =  'MANIPAL/'.$year.'/'.$month.'/'.$kode;
            return response()->json($value);
        }
        

    }


}
