<?php

namespace App\Http\Controllers;
use App\Branch;
use App\banned;
use App\students;
use App\payment;
use App\leads;
use App\invoices;
use App\admissionprocess;
use App\admissionprocesscourses;
use App\admissionprocessinstallmentfees;
use App\bannedleads;
use DB;
use Auth;

use Illuminate\Http\Request;

class BannedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
           /* $bannesdata =DB::table('banneds')
                ->join('branches', 'branches.id', '=', 'banneds.branchsId')
                ->select('banneds.id','banneds.stuents', 'banneds.created_at','branches.branchname')
                ->get();*/

                $bannesdata = DB::select('SELECT * FROM bannedleads b, users u WHERE u.id = b.busersid');

            return view('superadmin.banned.manage',compact('bannesdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $brans = Branch::get();
         $studes = admissionprocess::get();

        return view('superadmin.banned.create',compact('brans','studes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,bannedleads $bannedleads)
    {

         $userName = Auth::user()->name;
         //dd($userName);
         $studentsdata = $request->students;
       
          $bannedmodel = new banned();
        $banned = $bannedmodel->create([
            'branchsId'=> $request->brnaches,
            'stuents'=> $request->students,
            
        ]);

        $studentsid =  admissionprocess::where('studentname',$studentsdata)->pluck('sphone');
        $invid =  admissionprocess::where('sphone',$studentsid)->pluck('id');

        $leadsdatadatabanned = leads::where('phone',$studentsid)->first();

        //dd($leadsdatadatabanned);

        $bannedleads->bsource = $leadsdatadatabanned->source;
        $bannedleads->bbranch = $leadsdatadatabanned->branch;
        $bannedleads->bstudentname = $leadsdatadatabanned->studentname;
        $bannedleads->baddress = $leadsdatadatabanned->address;
        $bannedleads->bemail = $leadsdatadatabanned->email;
        $bannedleads->bphone = $leadsdatadatabanned->phone;
        $bannedleads->bcourse = $leadsdatadatabanned->course;
        $bannedleads->bcoursesmode = $leadsdatadatabanned->coursesmode;
        $bannedleads->blvalue = $leadsdatadatabanned->lvalue;
        $bannedleads->bcity = $leadsdatadatabanned->city;
        $bannedleads->bstate = $leadsdatadatabanned->state;
        $bannedleads->bzipcode = $leadsdatadatabanned->zipcode;
        $bannedleads->bdescription = $leadsdatadatabanned->description;
        $bannedleads->bfollowupstatus = $leadsdatadatabanned->followupstatus;
        $bannedleads->bfollowupdate = $leadsdatadatabanned->followupdate;
        $bannedleads->bleadstatus = $leadsdatadatabanned->leadstatus;
        $bannedleads->bleaddate = $leadsdatadatabanned->leaddate;
        $bannedleads->busersid = $leadsdatadatabanned->user_id;
        $bannedleads->bbyname = $userName;
        $bannedleads->save();

        $paymentsdatabanne = payment::where('inviceid',$invid);

        $deled = $paymentsdatabanne->delete();

        $deleted = $leadsdatadatabanned->delete();

        $invoicesadatabanned = admissionprocess::where('id',$invid);

        $ds = $invoicesadatabanned->delete();

        $as = admissionprocesscourses::where('invid',$invid);
        $de = $as->delete();

        $asis = admissionprocessinstallmentfees::where('invoid',$invid);
        $desaa = $asis->delete();


       

        return redirect('/banned')->with('success','Data Banned Successfully!!!');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\banned  $banned
     * @return \Illuminate\Http\Response
     */
    public function show(banned $banned)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\banned  $banned
     * @return \Illuminate\Http\Response
     */
    public function edit(banned $banned)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\banned  $banned
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, banned $banned)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\banned  $banned
     * @return \Illuminate\Http\Response
     */
    public function destroy(banned $banned)
    {
        //
    }
}
