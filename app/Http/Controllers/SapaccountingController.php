<?php

namespace App\Http\Controllers;

use App\course;
use App\coursecategory;
use App\payment;
use App\sapaccounting;
use App\Branch;
use Illuminate\Http\Request;
use Auth;

class SapaccountingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentmonths = date('m');
        $userBranch = Auth::user()->branchs;

        //$ccats = coursecategory::where('coursecategoryname','SAP Training')->first();

        //$gsapcourse = course::where('cat_id',$ccats->id)->get();

        $findcourse = course::where('cat_id',12)->pluck('id');

        $invoicesdata = payment::select('payments.*','payments.id as pids')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->whereIn('admissionprocesscourses.courseid',$findcourse)->where('payments.branchs',$userBranch)->whereMonth('payments.paymentdate',$currentmonths)->orderBy('payments.id','DESC')->get();

       // dd($invoicesdata);

        $reinvoicesdata = payment::select('payments.*','payments.id as pids')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->whereIn('readmissioncourses.recourseid',$findcourse)->where('payments.branchs',$userBranch)->whereMonth('payments.paymentdate',$currentmonths)->orderBy('payments.id','DESC')->get();

        $branchdata = Branch::all();
        //dd($gsapcourse);

        return view('superadmin.sapacc.manage',compact('invoicesdata','reinvoicesdata','branchdata'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $months = $request->months;

        $month = explode('-',$months);
        $branch = $request->branchdatas;

        $findcourse = course::where('cat_id',12)->pluck('id');

        $invoicesdata = payment::select('payments.*','payments.id as pids')->join('admissionprocesscourses','admissionprocesscourses.invid','=','payments.inviceid')->whereIn('admissionprocesscourses.courseid',$findcourse)->where('payments.branchs',$branch)->whereMonth('paymentdate',$month[1])->whereYear('paymentdate', '=', $month[0])->orderBy('payments.id','DESC')->get();

       // dd($invoicesdata);

        $reinvoicesdata = payment::select('payments.*','payments.id as pids')->join('readmissioncourses','readmissioncourses.reinvid','=','payments.reinviceid')->whereIn('readmissioncourses.recourseid',$findcourse)->where('payments.branchs',$branch)->whereMonth('paymentdate',$month[1])->whereYear('paymentdate', '=', $month[0])->orderBy('payments.id','DESC')->get();

        $branchdata = Branch::all();
        //dd($gsapcourse);

        return view('superadmin.sapacc.filtersapadmissions',compact('invoicesdata','reinvoicesdata','branchdata','branch','months'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id,Request $request)
    {
        $getpayments = payment::find($id);

        return view('superadmin.sapacc.saptranferfees',compact('getpayments','id'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\sapaccounting  $sapaccounting
     * @return \Illuminate\Http\Response
     */
    public function show($id,sapaccounting $sapaccounting,Request $request)
    {
         $sapaccountingmodel = new sapaccounting();
        $sapaccounting = $sapaccountingmodel->create([

            'sapstudentsname'=> $request->studentsnames,
            'sapcourses'=> $request->coursenames,
            'sapadmissionsfors'=> $request->admissionfors,
            'saptotfees'=> $request->totaalsfees,
            'sapbalfees'=> $request->blacncessfees,
            'sappayablefees'=> $request->payabdlefees,
            'sapreceiptnos'=> $request->receiptesnos,
            'sapid'=> $id,
            'sappaydates'=> $request->paymentsdate,
            'sapenrollno'=> $request->studenterono,
            'sapfees'=> $request->cvrufees,
            'sapbitfees'=> $request->bitfees,
        ]);

      return redirect('/sap-admissions')->with('success','SAP Fees Transfer Successfully');
      //  return redirect('/cvrufees-details/'.$request->paymentsdate.'/'.$barnchs)->with('success','Fees Transfer Successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\sapaccounting  $sapaccounting
     * @return \Illuminate\Http\Response
     */
    public function edit($id,sapaccounting $sapaccounting)
    {
        $getcvrufees = sapaccounting::where('sapid',$id)->first();
      //  $getpaymentdate = payment::find($getcvrufees->sapid);

        return view('superadmin.sapacc.edit',compact('getcvrufees'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\sapaccounting  $sapaccounting
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, sapaccounting $sapaccounting)
    {
        $updatesfees = sapaccounting::find($id);
        $updatesfees->sappaydates  = $request->paymentsdate;
        $updatesfees->sapenrollno  = $request->studenterono;
        $updatesfees->sapfees  = $request->cvrufees;
        $updatesfees->sapbitfees  = $request->bitfees;
        $updatesfees->sapstudentsname  = $request->studentsnames;
        $updatesfees->sapcourses  = $request->coursenames;
        $updatesfees->sapadmissionsfors  = $request->admissionfors;
        $updatesfees->saptotfees  = $request->totaalsfees;
        $updatesfees->sapbalfees  = $request->blacncessfees;
        $updatesfees->sappayablefees  = $request->payabdlefees;
        $updatesfees->sapreceiptnos  = $request->receiptesnos;
       
        $updatesfees->save();

        return redirect('/sap-admissions')->with('success','SAP Fees Updated!!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\sapaccounting  $sapaccounting
     * @return \Illuminate\Http\Response
     */
    public function destroy(sapaccounting $sapaccounting)
    {
        //
    }
}
