<?php

namespace App\Http\Controllers;

use App\course;
use App\Source;
use App\followup;
use App\User;
use App\Accounting;
use App\payment;
use App\TargetAlloted;
use App\assigntarget;
use App\IncentiveReleasePayments;
use App\PaymentSource;
use App\ExpenseCategory;
use App\ReAdmission;
use App\admissionprocess;
use App\CashExpense;
use App\admissionprocesscourses;
use App\CvruFees;
use App\clearaccounting;
use App\User_Salary_Deductions;
use App\SalaryCalculations;
use App\ChequeAgainstMoney;
use App\Branch;
use Carbon\CarbonPeriod;
use DB;
use Auth;
use Illuminate\Http\Request;

class BranchAccountingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

         $userBrnachs = Auth::user()->branchs;

            $accountincomes = payment::where('branchs',$userBrnachs)->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->orderBy('id','DESC')->get();



            $excollec = payment::where('branchs',$userBrnachs)->where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->groupBy('paymentdate')->orderBy('id','DESC')->get();
            
             $today = date('Y-m-d');

            $period = CarbonPeriod::create(date('Y').'-01-03', $today);
            $dated = $period->toArray();

             $datesd = array_reverse($dated);

             $branall = Branch::all();

            return view('admin.accounting.income',compact('accountincomes','excollec','datesd','userBrnachs','branall'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userBrnachs = Auth::user()->branchs;

        $branall = Branch::all();

        $excollec = payment::where('branchs',$userBrnachs)->where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->groupBy('paymentdate')->orderBy('id','DESC')->get();

         $cashepxs = CashExpense::where('ebranchs',$userBrnachs)->groupBy('exppaymendate')->get();

          return view('admin.accounting.totalexpense',compact('excollec','cashepxs','userBrnachs','branall'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($expdats,$barnchs)
    {
        $casheexpdet = CashExpense::where('ebranchs',$barnchs)->whereDate('exppaymendate',$expdats)->get();

            return view('admin.accounting.expensesdetails',compact('casheexpdet','expdats','barnchs'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $userBrnachs = Auth::user()->branchs;

            if($userBrnachs == 'BITSJ')
            {
                    $accountincomes = payment::where('branchs',$userBrnachs)->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->orderBy('id','DESC')->get();



                    $excollec = payment::where('branchs',$userBrnachs)->where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->groupBy('paymentdate')->orderBy('id','DESC')->get();
                    
                     $today = date('Y-m-d');

                    $period = CarbonPeriod::create(date('Y').'-01-03', $today);
                    $dated = $period->toArray();

                     $datesd = array_reverse($dated);

                     $branall = Branch::all();

                    return view('centremanager.accounting.income',compact('accountincomes','excollec','datesd','userBrnachs','branall')); 
            }

            else
            {

                 $accountincomes = payment::where('branchs',$userBrnachs)->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->orderBy('id','DESC')->get();



                    $excollec = payment::where('branchs',$userBrnachs)->where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->groupBy('paymentdate')->orderBy('id','DESC')->get();
                    
                     $today = date('Y-m-d');

                    $period = CarbonPeriod::create(date('Y').'-06-01', $today);
                    $dated = $period->toArray();

                     $datesd = array_reverse($dated);

                     $branall = Branch::all();

                    return view('centremanager.accounting.income',compact('accountincomes','excollec','datesd','userBrnachs','branall'));

            }

            
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $userBrnachs = Auth::user()->branchs;

        $branall = Branch::all();

        $excollec = payment::where('branchs',$userBrnachs)->where('paymentmode','Cash')->OrWhere('paymentmode','Cheque Against Cash')->groupBy('paymentdate')->selectRaw('*, sum(paymentreceived) as paymentreceived')->groupBy('paymentdate')->orderBy('id','DESC')->get();

         $cashepxs = CashExpense::where('ebranchs',$userBrnachs)->groupBy('exppaymendate')->get();

          return view('centremanager.accounting.totalexpense',compact('excollec','cashepxs','userBrnachs','branall'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($expdats,$barnchs)
    {
         $casheexpdet = CashExpense::where('ebranchs',$barnchs)->whereDate('exppaymendate',$expdats)->get();

            return view('centremanager.accounting.expensesdetails',compact('casheexpdet','expdats','barnchs'));
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
