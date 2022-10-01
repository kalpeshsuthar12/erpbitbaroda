<?php

namespace App\Http\Controllers;
use App\UserPunching;
use App\HolidayList;
use App\PaymentSource;
use App\User;
use App\payment;
use App\Branch;
use App\Users_Salar_Data;
use App\CashExpense;
use App\SalaryCalculations;
use App\Leave_Management;
use App\AdvanceDeductions;
use App\User_Salary_Deductions;
use Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Http\Request;

class SalaryManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        

           $userBranch = Auth::user()->branchs;

           $userall = User::where('branchs',$userBranch)->where('usercategory','!=','Affiliate Marketing')->where('usercategory','!=','Franchise')->where('usercategory','!=','Past Admin')->where('usercategory','!=','Superadmin')->where('usercategory','!=','Student')->where('usercategory','!=','Instructor')->where('userstatus',1)->get();
           $facall = User::join('users_officials_timings_details','users_officials_timings_details.usersdetailsid','=','users.id')->select('users_officials_timings_details.*','users.*','users.id as uid')->where('users_officials_timings_details.usersdetailsbranchs',$userBranch)->where('users.usercategory','=','Instructor')->where('users.userstatus',1)->groupBy('users_officials_timings_details.usersdetailsid')->get();

           // dd($facall);

           $fromDate = Carbon::now()->subMonth()->startOfMonth();
        $tillDate = Carbon::now()->subMonth()->endOfMonth();
        $range = [$fromDate,$tillDate];

           $branchdata = Branch::get();
           $userdata = User::get();
        
        return view('superadmin.salarymanagement.manage',compact('userall','branchdata','facall','range','fromDate'));
    }


    public function filtersuserssalasyrs(Request $request)
    {
        $branch = $request->branchsdatas;
        $monthsdatas = $request->branchmonths;

         $userall = User::where('branchs',$branch)->where('usercategory','!=','Affiliate Marketing')->where('usercategory','!=','Franchise')->where('usercategory','!=','Past Admin')->where('usercategory','!=','Superadmin')->where('usercategory','!=','Student')->where('userstatus',1)->get();

          $facall = User::join('users_officials_timings_details','users_officials_timings_details.usersdetailsid','=','users.id')->select('users_officials_timings_details.*','users.*','users.id as uid')->where('users_officials_timings_details.usersdetailsbranchs',$branch)->where('users.usercategory','=','Instructor')->where('users.userstatus',1)->groupBy('users_officials_timings_details.usersdetailsid')->get();

         $branchdata = Branch::get();

         return view('superadmin.salarymanagement.filtersalarys',compact('branchdata','branch','monthsdatas','userall','monthsdatas','facall'));


    }

    public function getusersAllfromexpnse($getuId)
    {
        $usersdatas = User::whereRaw('FIND_IN_SET("'.$getuId.'",expcategories)')->first(); 

        //dd($usersdatas);

        return response()->json($usersdatas->id);
    }



    public function viewfullsalarydetails($id)
    {

        $salisid = SalaryCalculations::find($id);
        $pall = PaymentSource::all();
        return view('superadmin.salarymanagement.viewfullsalary',compact('salisid','pall'));
    }

    public function editfinalsalary($id)
    {
            $usde = User_Salary_Deductions::find($id);
            $pall = PaymentSource::all();

            return view('superadmin.salarymanagement.editfinalsalary',compact('usde','pall'));
    }


    public function updatefinalsalary($id,Request $request)
    {
        $usde = User_Salary_Deductions::find($id);
        $usde->salsworkingsalarys = $request->Salary;
        $usde->salsusersid = $request->saluseersid;
        $usde->salsfinalsalarys = $request->payablesalary;
        $usde->salspaidsalarys = $request->psalarys;
        $usde->salspendingsalarys = $request->pendinsalary;
        $usde->salspaymentdate = $request->pdate;
        $usde->salspaymoddes = $request->paymentsmodes;
        $usde->save();

        return redirect('/view-salary-details/'.$usde->salssalarysid)->with('success','Final Salary Updated!!!');

    }


    public function relaesefinalsalary ($id,$datesdat)
    {   
        $monthdat = $datesdat;
        $rfinalsal = User_Salary_Deductions::find($id);
         $pall = PaymentSource::all();
         $getchequedetails = payment::where('paymentmode','Bank (Cheque)')->where('chequestatus',0)->get();
         $getbanksdetails = payment::where('paymentmode','Bank (Cheque)')->where('chequestatus',0)->groupBy('bankname')->get();
         //dd($rfinalsal);

         $dates = date($datesdat.'-'.'01');
        $fromDate = Carbon::parse($dates)->startOfMonth();
        $tillDate = Carbon::parse($dates)->endOfMonth();
        $range = [$fromDate, $tillDate];

              $cperiod = CarbonPeriod::create($fromDate,$tillDate);
              $cdatesd = $cperiod->toArray();

              $ccounts = count($cdatesd); 

              $start = new DateTime($fromDate);
              $end = new DateTime($tillDate);
              $days = $start->diff($end, true)->days;

                $sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7);

                $period = CarbonPeriod::create($fromDate,$tillDate);
                $datesall = $period->toArray();



            return view('superadmin.salarymanagement.relaesefinalsalarys',compact('rfinalsal','pall','getchequedetails','getbanksdetails','datesall','ccounts','sundays','range','monthdat'));


    }

    public function getallcheques($getallcheques)
    {
        $getpaymentid = payment::find($getallcheques);

        if($getpaymentid->inviceid != null)
        {
                $responsesdatas = payment::select('admissionprocesses.studentname','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('payments.id',$getpaymentid->id)->get();
                 return response()->json($responsesdatas);
        }

        else
        {

                $responsesdatas = payment::select('re_admissions.rstudents','payments.*','payments.id as pids','re_admissions.id as reid')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('payments.id',$getpaymentid->id)->get();
                 return response()->json($responsesdatas);
        }
    }


    public function getrecordscheqdateswise($chqdates)
    {
       // $getpaymentid = payment::where('chequedate',$chqdates)->where('chequestatus',0)->get();

        $incvideddatea = payment::select('admissionprocesses.studentname','payments.*')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->whereDate('chequedate',$chqdates)->where('chequestatus',0)->get();

        $readresponsesdatas = payment::select('re_admissions.rstudents','payments.*')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->whereDate('chequedate',$chqdates)->where('chequestatus',0)->get();

       // dd($getpaymentid);
/*
            foreach($getpaymentid as $paysmene)
            {
                //$
            }*/

       

                    if($incvideddatea)
                    {
                            
                             return response()->json($incvideddatea);
                    }

                    elseif($readresponsesdatas)
                    {

                            
                             return response()->json($readresponsesdatas);
                    }

        

       
    }

    public function storereleasependingsalary($id,Request $request)
    {
       

        $getudaytas = User::find($request->saluseersid);

            $getsdtats = explode(',',$getudaytas->expcategories);


           
        if($request->pmode == 'Bank (Cheque)')
                {
                     $User_Salary_Deductionsmodel = new User_Salary_Deductions();
                        $User_Salary_Deductions = $User_Salary_Deductionsmodel->create([
                            'salsusersid'=> $request->saluseersid,
                            'salssalarysid'=> $id,
                            'salsworkingsalarys'=> $request->Salary,
                            'salsfinalsalarys'=> $request->payablesalary,
                            'salspaidsalarys'=> $request->psalarys,
                            'salspendingsalarys'=> $request->pendinsalary,
                            'salspaymentdate'=> $request->pdate,
                            'salspaymoddes'=> $request->pmode,
                            'schqdates'=> $request->chequedates,
                            'schqno'=> $request->chequenos,
                            'sbanknanmes'=> $request->banksnames,
                            'scheqof'=> $request->chequegivenby,
                            'schqamounts'=> $request->chqqmounts,
                            'smonthsdatas'=> $request->months_data,

                        ]);



                        $usedata = User::find($request->saluseersid);

                      $updateclear = payment::find($request->bankdetails);
                      $updateclear->chequestatus = 1;
                      $updateclear->chequedepositsto = $usedata->name.' '.'Salary';
                      $updateclear->save();



                }

          
                 else if($request->pmode == 'Cash')
                     {

                         $User_Salary_Deductionsmodel = new User_Salary_Deductions();
                        $User_Salary_Deductions = $User_Salary_Deductionsmodel->create([
                            'salsusersid'=> $request->saluseersid,
                            'salssalarysid'=> $id,
                            'salsworkingsalarys'=> $request->Salary,
                            'salsfinalsalarys'=> $request->payablesalary,
                            'salspaidsalarys'=> $request->psalarys,
                            'salspendingsalarys'=> $request->pendinsalary,
                            'salspaymentdate'=> $request->pdate,
                            'salspaymoddes'=> $request->pmode,
                            'smonthsdatas'=> $request->months_data,

                            
                        ]);



                        $usedata = User::find($request->saluseersid);

                        $CashExpense = new CashExpense([
                                               
                                                'expnsenewamounts'   => $request->psalarys,
                                                'expensefor'   => $usedata->name.' '. 'Salary',
                                                'cusersids'   =>$request->saluseersid,
                                                'exppaymendate'   => $request->pdate,
                                                'expensepaymode'   => $request->pmode,
                                                
                                                
                                            ]);
                                            $CashExpense->save();
                     } 

                else
                {   

                    $User_Salary_Deductions = $User_Salary_Deductionsmodel->create([
                            'salsusersid'=> $request->saluseersid,
                            'salssalarysid'=> $id,
                            'salsworkingsalarys'=> $request->Salary,
                            'salsfinalsalarys'=> $request->payablesalary,
                            'salspaidsalarys'=> $request->psalarys,
                            'salspendingsalarys'=> $request->pendinsalary,
                            'salspaymentdate'=> $request->pdate,
                            'salspaymoddes'=> $request->pmode,
                            'smonthsdatas'=> $request->months_data,
                            
                        ]);

                }



        return redirect('/final-salarys')->with('success','Salary Created Successfully !!!!');
    }


    public function updatereleasesalary($id,Request $request)
    {
            $usal = User_Salary_Deductions::find($id);
            $usal->salspaidsalarys = $request->payablesalary;
            $usal->totalrealeasesalary = $request->totalrealeasesalary;
            $usal->salspendingsalarys = $request->pendingsalarys;
            $usal->salspaymentdate = $request->pdates;
            $usal->salspaymoddes = $request->pmode;
            $usal->smonthsdatas = $request->months_data;
            $usal->save();

             $usedata = User::find($usal->salsusersid);


                if($request->pmode == 'Bank (Cheque)')
                {
                      $usal->schqdates = $request->chequedates;
                      $usal->schqno = $request->chequenos;
                      $usal->sbanknanmes = $request->banksnames;
                      $usal->scheqof = $request->chequegivenby;
                      $usal->schqamounts = $request->chqqmounts;
                      $usal->save();

                        $usedata = User::find($usal->salsusersid);

                      $updateclear = payment::find($request->bankdetails);
                      $updateclear->chequestatus = 1;
                      $updateclear->psusersId = $usal->id;
                      $updateclear->chequedepositsto = $usedata->name.' '.'Salary';
                      $updateclear->save();



                }

          
                 if($request->pmode == 'Cash')
                     {

                        $usedata = User::find($usal->salsusersid);

                        $CashExpense = new CashExpense([
                                               
                                                'expnsenewamounts'   => $request->payablesalary,
                                                'expensefor'   => $usedata->name.' '. 'Salary',
                                                'cusersids'   => $usedata->id,
                                                'exppaymendate'   => $request->pdates,
                                                'expensepaymode'   => $request->pmode,
                                                
                                                
                                            ]);
                                            $CashExpense->save();
                     }  



                     return redirect('/realease-pending-salary/'.$id.'/'.$request->months_data)->with('success','Master Salary Created For'.' '.$usedata->name);



    }


    public function releaesependingsalarys($id,$monthsdata)
    {
        $usde = User_Salary_Deductions::find($id);
        $pall = PaymentSource::all();


        $dates = date($monthsdata.'-'.'01');
        $fromDate = Carbon::parse($dates)->startOfMonth();
        $tillDate = Carbon::parse($dates)->endOfMonth();
       // dd($tillDate);

             $range = [$fromDate, $tillDate];

             $pall = PaymentSource::all();

             $period = CarbonPeriod::create($fromDate,$tillDate);
                $datesall = $period->toArray();

                 $cperiod = CarbonPeriod::create($fromDate,$tillDate);
              $cdatesd = $cperiod->toArray();

              $ccounts = count($cdatesd);


              $start = new DateTime($fromDate);
              $end = new DateTime($tillDate);
              $days = $start->diff($end, true)->days;

                $sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7); 

        return view('superadmin.salarymanagement.realeasependingsalarys',compact('usde','pall','datesall','ccounts','sundays','range','monthsdata'));
    }

    public function editadvance($id)
    {
        $addeddc = AdvanceDeductions::find($id);

        return view('superadmin.salarymanagement.editadvance',compact('addeddc'));

    }




   public function updatefinaladvances($id, Request $request)
   {
         $addeddc = AdvanceDeductions::find($id);
         $addeddc->addeusersid = $request->salusersid;
         $addeddc->paidadvance = $request->payableadvances;
         $addeddc->advededdate = $request->pdate;
         $addeddc->save();


         return redirect('/final-salarys')->with('success','Advance Updated Successfully!!!');
   }


   public function editsalaryscalcu($id, Request $request)
    {
         $salisid = SalaryCalculations::find($id);

         $pall = PaymentSource::all();



        $fromDate = Carbon::now()->subMonth()->startOfMonth();
        $tillDate = Carbon::now()->subMonth()->endOfMonth();
       // dd($tillDate);

             $range = [$fromDate, $tillDate];

              $cperiod = CarbonPeriod::create($fromDate,$tillDate);
              $cdatesd = $cperiod->toArray();

              $ccounts = count($cdatesd); 

              $start = new DateTime($fromDate);
              $end = new DateTime($tillDate);
              $days = $start->diff($end, true)->days;

                $sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7);


                return view('superadmin.salarymanagement.editsalarycalculations',compact('id','pall','range','ccounts','sundays','fromDate','salisid'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function create($usersId,Request $request)
    {

              $start = Carbon::now()->startOfMonth();
              $end =  Carbon::now()->endOfMonth();

               $startdate =  Carbon::parse($start)->format('Y-m-d');
              $enddate =  Carbon::parse($end)->format('Y-m-d');

               $period = CarbonPeriod::create($startdate,$enddate);
               $datesd = $period->toArray();

                

                        
                                   
                           // dd($request->start);
                        $usese = UserPunching::where('pusersid',$usersId)->get();


                      
                                    foreach ($usese as $key => $value) {
                                       $data[$key]['id'] = $value['id'];
                                       $data[$key]['title'] = $value['title'];
                                       $data[$key]['start'] = $value['puncdates'].' 00:00:00';
                                       $data[$key]['end'] = $value['puncdates'].' 23:59:59';
                                       $data[$key]['color'] = 'blue';
                                    }


                        $hlists = HolidayList::get();

                        foreach ($hlists as $key => $nvalue) {
                                       $data_k[$key]['id'] = $nvalue['id'];
                                       $data_k[$key]['title'] = 'Paid Leave';
                                       $data_k[$key]['start'] = $nvalue['hstartdates'].' 00:00:00';
                                       $data_k[$key]['end'] = $nvalue['henddates'].' 23:59:59';
                                       $data_k[$key]['color'] = 'green';
                                    }

                              //  foreach($usese as $key => $value)

                                    //$c = ['title' => 'sunday','dow' => [0]];
                                    //$data = $c;//array_merge($data, $c);
                                  

                                    //$data = array_push($data, $data_s);
                                      //  print_r($data);
                                    //print_r($data);exit;




                                     $data_s = 
                                            array(
                                              'id' => '1',
                                              'title' => 'Paid Leave',
                                              'start' => '00:00:00',
                                              'end' => '23:59:59',
                                              'dow' => [0],
                                              'color' => 'Red',

                                        
                                            
                                        );

                                            $data_ss = 
                                            array(
                                              'id' => '1',
                                              'title' => 'Paid Leave',
                                              'start' => '2022-06-12 00:00:00',
                                              'end' => '2022-06-12 23:59:59',
                                              'color' => 'green',

                                        
                                            
                                        );

                                        array_push($data, $data_s);
                                       // array_push($data, $data_ss);

                                        $data = array_merge($data,$data_k);
                                        return response()->json($data);
                        



                                   
  
             
        

      
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,SalaryCalculations $SalaryCalculations,Leave_Management $Leave_Management)
    {
          
            //dd(explode(',',$getudaytas->expcategories));


          $explodedates =   explode('-',$request->datesstores);

            $datesdat = $explodedates[0].'-'.$explodedates[1];
         //   dd($datesdat);

            $today = date('Y-m-d');


        $SalaryCalculations->datesofsalarys = $request->datesstores;
        $SalaryCalculations->user_details_id = $request->usersids;
        $SalaryCalculations->usersworkinghrs = $request->uswkhrs;
        $SalaryCalculations->users_salarys = $request->hutotalsalarys;
        $SalaryCalculations->upurecollections = $request->pcollections;
        $SalaryCalculations->user_months = $request->months;
        $SalaryCalculations->umsdays = $request->humonthsdays;
        $SalaryCalculations->workingdays = $request->huworkingdays;
        $SalaryCalculations->totalwrknghrs = $request->hutwh;
        $SalaryCalculations->ttlsphpl = $request->htsplphpl;
        $SalaryCalculations->ul = $request->hl;
        $SalaryCalculations->upl = $request->hpl;
        $SalaryCalculations->flh = $request->flh;
        $SalaryCalculations->fld = $request->fld;
        $SalaryCalculations->uwrkinghrs = $request->hwh;
        $SalaryCalculations->uwrkingsalary = $request->hws;
        $SalaryCalculations->uwrkingincentif = $request->hi;
        $SalaryCalculations->remarks = $request->hremarks;
        
        $SalaryCalculations->save();



        $salid = $SalaryCalculations->id;

             $fromDate = Carbon::now()->subMonth()->startOfMonth();

             $Leave_Management->leavuserid =  $request->usersids;
             $Leave_Management->leavesdate =  $fromDate;
             $Leave_Management->userstotalleave =  $request->hpl;
             $Leave_Management->save();




        return redirect('/SalaryCalculations/'.$salid.'/'.$datesdat)->with('success','Salary Generated Successfully !!!');

    }


    public function updatefinalsalarys($id,Request $request)
    {
            $today = date('Y-m-d');

            $deles = Leave_Management::where('leavuserid',$request->usersids)->get();

            $deles->each->delete();

            $SalaryCalculations = SalaryCalculations::find($id); 

        $SalaryCalculations->user_details_id = $request->usersids;
        $SalaryCalculations->usersworkinghrs = $request->uswkhrs;
        $SalaryCalculations->users_salarys = $request->hutotalsalarys;
        $SalaryCalculations->upurecollections = $request->pcollections;
        $SalaryCalculations->user_months = $request->months;
        $SalaryCalculations->umsdays = $request->humonthsdays;
        $SalaryCalculations->workingdays = $request->huworkingdays;
        $SalaryCalculations->totalwrknghrs = $request->hutwh;
        $SalaryCalculations->ttlsphpl = $request->htsplphpl;
        $SalaryCalculations->ul = $request->hl;
        $SalaryCalculations->upl = $request->hpl;
        $SalaryCalculations->flh = $request->flh;
        $SalaryCalculations->fld = $request->fld;
        $SalaryCalculations->uwrkinghrs = $request->hwh;
        $SalaryCalculations->uwrkingsalary = $request->hws;
        $SalaryCalculations->uwrkingincentif = $request->hi;
        $SalaryCalculations->remarks = $request->hremarks;
        
        $SalaryCalculations->save();



        $salid = $SalaryCalculations->id;

             $fromDate = Carbon::now()->subMonth()->startOfMonth();



               $dakmsm = Leave_Management::updateOrCreate(['leavuserid' => $request->usersids,'leavesdate' => $fromDate,'userstotalleave' => $request->hpl ]);

              return redirect('/view-salary-details/'.$id)->with('success','Salary Updated Successfully !!!');
        
    }


    public function getasalaryadvance($id,$datesdat)
    {
        $salisid = SalaryCalculations::find($id);
        
         $modatas = $datesdat;

        $dates = date($datesdat.'-'.'01');
        $fromDate = Carbon::parse($dates)->startOfMonth();
        $tillDate = Carbon::parse($dates)->endOfMonth();
       // dd($tillDate);

             $range = [$fromDate, $tillDate];

             $pall = PaymentSource::all();

             $period = CarbonPeriod::create($fromDate,$tillDate);
                $datesall = $period->toArray();

                 $cperiod = CarbonPeriod::create($fromDate,$tillDate);
              $cdatesd = $cperiod->toArray();

              $ccounts = count($cdatesd);


              $start = new DateTime($fromDate);
              $end = new DateTime($tillDate);
              $days = $start->diff($end, true)->days;

                $sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7); 

        return view('superadmin.salarymanagement.branchwithadvancesalary',compact('salisid','range','pall','dates','datesall','ccounts','sundays','datesdat','modatas'));
    }


    public function storefinalsalarys($id,$dates,Request $request)
    {
        $User_Salary_Deductionsmodel = new User_Salary_Deductions();
        $User_Salary_Deductions = $User_Salary_Deductionsmodel->create([
                'salsusersid'=> $request->saluseersid,
            'salssalarysid'=> $id,
            'salsworkingsalarys'=> $request->Salary,
            'salsfinalsalarys'=> $request->payablesalary,
            'salspaidsalarys'=> $request->psalarys,
            'salspendingsalarys'=> $request->pendinsalary,
            'salspaymentdate'=> $request->pdate,
            'salspaymoddes'=> $request->paymentsmodes,

        ]);

        $getudaytas = User::find($request->saluseersid);

            $getsdtats = explode(',',$getudaytas->expcategories);


           
         if($request->paymentsmodes == 'Cash')
         {

            $CashExpense = new CashExpense([
                                   
                                    'expnsenewamounts'   => $request->payablesalary,
                                    'expensefor'   => $getsdtats[0],
                                    'cusersids'   =>$request->saluseersid,
                                    'exppaymendate'   => $request->pdate,
                                    
                                    
                                ]);
                                $CashExpense->save();
         }  

        return redirect()->back()->with('success','Salary Created Successfully !!!!');
    }

    public function storeadvancedeductions(Request $request)
    {
            $AdvanceDeductionsmodel = new AdvanceDeductions();
        $AdvanceDeductions = $AdvanceDeductionsmodel->create([
            'addatses'=> $request->advandates,
            'addeusersid'=> $request->salusersid,
            'paidadvance'=> $request->payableadvances,
            'advededdate'=> $request->pdate,
            
        ]);  


        return redirect()->back()->with('success','Advance Deducted Successfully !!!!');
    }

    public function finalslarys()
    {
        //$usersall = Users_Salar_Data::join('users','users.id','=','users__salar__data.husalarys_id')->select('users.*','users__salar__data.*','users.id as uids')->get();

        $usersall = SalaryCalculations::all();
        $ball = Branch::all();
        $uall =  User::where('usercategory','!=','Affiliate Marketing')->where('usercategory','!=','Franchise')->where('usercategory','!=','Past Admin')->where('usercategory','!=','Superadmin')->where('usercategory','!=','Student')->where('userstatus',1)->get();

        return view('superadmin.salarymanagement.managesalary',compact('usersall','ball','uall'));

    }

    public function filterfinalsalarys(Request $request)
    {
        //$usersall = Users_Salar_Data::join('users','users.id','=','users__salar__data.husalarys_id')->select('users.*','users__salar__data.*','users.id as uids')->get();

        /*$usersall = SalaryCalculations::all();*/
        $ufilters = $request->usersfilters;
        $bfilters = $request->branchsfilters;
        $dofyears = $request->datasofyears;

            if($ufilters)
            {
                $usersall =  SalaryCalculations::whereYear('datesofsalarys',$dofyears)->where('user_details_id',$ufilters)->get();
                 $ball = Branch::all();
                $uall =  User::where('usercategory','!=','Affiliate Marketing')->where('usercategory','!=','Franchise')->where('usercategory','!=','Past Admin')->where('usercategory','!=','Superadmin')->where('usercategory','!=','Student')->where('userstatus',1)->get();
                $bfilters = "";

                return view('superadmin.salarymanagement.filterfinalsalary',compact('usersall','ball','uall','ufilters','dofyears','bfilters'));

            }
            elseif($bfilters)
            {

                $usersall =  SalaryCalculations::join('users','users.id','=','salary_calculations.user_details_id')->select('salary_calculations.*')->whereYear('salary_calculations.datesofsalarys',$dofyears)->where('users.branchs',$bfilters)->get();
                 $ball = Branch::all();
                $uall =  User::where('usercategory','!=','Affiliate Marketing')->where('usercategory','!=','Franchise')->where('usercategory','!=','Past Admin')->where('usercategory','!=','Superadmin')->where('usercategory','!=','Student')->where('userstatus',1)->get();
               $ufilters = "";

                return view('superadmin.salarymanagement.filterfinalsalary',compact('usersall','ball','uall','ufilters','dofyears','bfilters'));

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
            $pall = PaymentSource::all();

            return view('superadmin.salarymanagement.create',compact('id','pall'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
          $pall = PaymentSource::all();

            return view('superadmin.salarymanagement.adminsalary',compact('id','pall'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id,$monthsdata)
    {
         $pall = PaymentSource::all();

         $dates = date($monthsdata.'-'.'01');
        $fromDate = Carbon::parse($dates)->startOfMonth();
        $tillDate = Carbon::parse($dates)->endOfMonth();
        $range = [$fromDate, $tillDate];

              $cperiod = CarbonPeriod::create($fromDate,$tillDate);
              $cdatesd = $cperiod->toArray();

              $ccounts = count($cdatesd); 

              $start = new DateTime($fromDate);
              $end = new DateTime($tillDate);
              $days = $start->diff($end, true)->days;

                $sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7);

                $period = CarbonPeriod::create($fromDate,$tillDate);
                $datesall = $period->toArray();
               // dd($sundays);

            return view('superadmin.salarymanagement.branchsalarys',compact('id','pall','range','ccounts','sundays','fromDate','dates','monthsdata','datesall'));
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
