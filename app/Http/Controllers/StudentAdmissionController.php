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

class StudentAdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

            $usersAdmissionsId = Auth::user()->ustusdentsadmssionsids;


            $studentsdata = admissionprocess::select('admissionprocesses.*','payments.*','admissionprocesses.id as aid')->join('payments', 'payments.inviceid', '=', 'admissionprocesses.id')->where('admissionprocesses.id',$usersAdmissionsId)->where('payments.studenterno','!=',null)->groupBy('payments.inviceid')->get(); 

       
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

            return view('students.admissions.manage',compact('studentsdata'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //

        $result = payment::where('inviceid',$id)->orderBy('id','DESC')->get();

      $admissionname = admissionprocess::find($id);

      return view('students.admissions.paymentreceiptlist',compact('result','admissionname'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id)
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
            

            
            

             return view('students.admissions.paymentreceipt',compact('aprocess','invvcoursed','univCourse','paymentdata','makepayment','installdata','selectID'));

        }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {

        $usersAdmissionsId = Auth::user()->ustusdentsadmssionsids;

        $invoicesdata = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.id',$usersAdmissionsId)->orderBy('payments.id','DESC')->get();

        return view('students.admissions.fees',compact('invoicesdata'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $usersAdmissionsId = Auth::user()->ustusdentsadmssionsids;

        $invoicesdata = payment::select('admissionprocesses.*','payments.*','payments.id as pids','admissionprocesses.id as admid')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.id',$usersAdmissionsId)->groupBy('payments.inviceid')->orderBy('payments.id','DESC')->get();

        return view('students.admissions.installmentsdetails',compact('invoicesdata'));
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
