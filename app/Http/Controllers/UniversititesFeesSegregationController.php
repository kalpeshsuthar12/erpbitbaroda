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
use App\UnviersitiesCategory;
use Illuminate\Http\Request;
use DB;
use Mail;
use PDF;
use Auth;

class UniversititesFeesSegregationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admissions = admissionprocess::join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->select('admissionprocesscourses.*','admissionprocesses.*','admissionprocesses.id as admid','admissionprocesscourses.id as acid')->where('admissionprocesses.suniversities','!=','BIT')->where('admissionprocesses.suniversities','!=','BIT (RM)')->get();
        

        $readmissions = ReAdmission::join('readmissioncourses','readmissioncourses.reinvid','=','re_admissions.id')->select('readmissioncourses.*','re_admissions.*','re_admissions.id as remid','readmissioncourses.id as reacid')->where('re_admissions.rsuniversities','!=','BIT')->where('re_admissions.rsuniversities','!=','BIT (RM)')->get();

        $ucale = UnviersitiesCategory::get();


        return view('superadmin.universitiesfeessegregations.manage',compact('admissions','readmissions','ucale'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $admsissionsfor = $request->admissionsfors;
        //dd($admsissionsfor);

        $admissions = admissionprocess::join('admissionprocesscourses','admissionprocesscourses.invid','=','admissionprocesses.id')->select('admissionprocesscourses.*','admissionprocesses.*','admissionprocesses.id as admid','admissionprocesscourses.id as acid')->where('admissionprocesses.suniversities','!=','BIT')->where('admissionprocesses.suniversities','!=','BIT (RM)')->where('admissionprocesscourses.admissionfor',$admsissionsfor)->get();
        

        $readmissions = ReAdmission::join('readmissioncourses','readmissioncourses.reinvid','=','re_admissions.id')->select('readmissioncourses.*','re_admissions.*','re_admissions.id as remid','readmissioncourses.id as reacid')->where('re_admissions.rsuniversities','!=','BIT')->where('re_admissions.rsuniversities','!=','BIT (RM)')->where('readmissioncourses.readmissionfor',$admsissionsfor)->get();

        $ucale = UnviersitiesCategory::get();

         return view('superadmin.universitiesfeessegregations.filtersdata',compact('admissions','readmissions','ucale','admsissionsfor'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($receifde)
    {
        $chequwcollectsstatus = Readmissioncourses::find($receifde);
         $chequwcollectsstatus->restatus = 1;
         $chequwcollectsstatus->recollecdates = date('Y-m-d');
         $chequwcollectsstatus->save();

         return response()->json(
                    [
                        'success' => true,
                        'message' => 'University Fees Transfers Successfully!'
                    ]
                ); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($univdre)
    {
        $chequwcollectsstatus = admissionprocesscourses::find($univdre);
         $chequwcollectsstatus->acsstatus = 1;
         $chequwcollectsstatus->ucollectiondates = date('Y-m-d');
         $chequwcollectsstatus->save();

         return response()->json(
                    [
                        'success' => true,
                        'message' => 'University Fees Transfers Successfully!'
                    ]
                ); 
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
