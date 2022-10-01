<?php

namespace App\Http\Controllers;
use App\payment;
use App\admissionprocess;
use App\ReAdmission;
use App\Branch;
use Auth;
use Illuminate\Http\Request;

class CvruController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $currentmonths = date('m');
        $usersbranchs = Auth::user()->branchs;
        $branall = Branch::all();

            $admision = admissionprocess::where('suniversities','!=','BIT')->pluck('id');
            $readmision = ReAdmission::where('rsuniversities','!=','BIT')->pluck('id');


        //$getalldatas = payment::where('branchs',$usersbranchs)->whereMonth('paymentdate',$currentmonths)->orderBy('id','DESC')->get();

              $admissionsdata = payment::select('payments.*')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.suniversities','!=','BIT')->where('payments.branchs',$usersbranchs)->whereMonth('payments.paymentdate',$currentmonths)->orderBy('id','DESC')->get();
        $readmissionsdata = payment::select('payments.*')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rsuniversities','!=','BIT')->where('payments.branchs',$usersbranchs)->whereMonth('payments.paymentdate',$currentmonths)->orderBy('id','DESC')->get();

        //dd($readmissionsdata);
        $merged = $readmissionsdata->merge($admissionsdata);

        $getalldatas = $merged->sortByDesc('id')->all();

        //$getalldatas = collect($getvale)->sortBy('id'); 

        return view('superadmin.cvruadmissionslist.manage',compact('getalldatas','branall'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $months = $request->allmonthsdata;

        $month = explode('-',$months);
        $branch = $request->allbranchs;

           
        $admissionsdata = payment::select('payments.*')->join('admissionprocesses','admissionprocesses.id','=','payments.inviceid')->where('admissionprocesses.suniversities','!=','BIT')->where('payments.branchs',$branch)->whereMonth('payments.paymentdate',$month[1])->whereYear('payments.paymentdate', '=', $month[0])->get();

        //dd($admissionsdata);

        $readmissionsdata = payment::select('payments.*')->join('re_admissions','re_admissions.id','=','payments.reinviceid')->where('re_admissions.rsuniversities','!=','BIT')->where('payments.branchs',$branch)->whereMonth('payments.paymentdate',$month[1])->whereYear('payments.paymentdate', '=', $month[0])->get();

        //dd($readmissionsdata);
        $merged = $readmissionsdata->merge($admissionsdata);

        $getalldatas = $merged->sortByDesc('id')->all();

         $branall = Branch::all();

        return view('superadmin.cvruadmissionslist.filtercvrusadmissionslist',compact('getalldatas','branall','months','branch'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
