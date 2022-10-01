<?php

namespace App\Http\Controllers;

use App\userpermission;
use App\User;
use DB;
use Illuminate\Http\Request;

class UserpermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(userpermission $userpermission)
    {


        $usersdata =DB::table('userpermissions')
                ->join('users', 'users.id', '=', 'userpermissions.usersid')
                ->select('userpermissions.id','userpermissions.leads','userpermissions.coursecategory','userpermissions.course','userpermissions.coursesubcategory', 'users.name','userpermissions.created_at','userpermissions.invoice','userpermissions.admission','userpermissions.directadmission','userpermissions.source','userpermissions.assigntarget','userpermissions.followup','userpermissions.generatepaymentreciept')
                ->get();
        $uses = userpermission::pluck('leads');
        $cpirs = userpermission::pluck('coursesubcategory');
        $cocat = userpermission::pluck('coursecategory');
        $cours = userpermission::pluck('course');
        $invoi = userpermission::pluck('invoice');
        $admsion = userpermission::pluck('admission');
        $dadmision = userpermission::pluck('directadmission');
        $srces = userpermission::pluck('source');
        $atarget = userpermission::pluck('assigntarget');
        $fllowp = userpermission::pluck('followup');
        $grsp = userpermission::pluck('generatepaymentreciept');



                /*if($uses->count() > 0)
                {
                    dd('condition worls');
                }
                else
                {
                    dd('notworking');
                }*/
        //$usersdata = userpermission::get();
        return view('superadmin.userpermissions.manage',compact('usersdata','uses','cpirs','cocat','cours','invoi','admsion','dadmision','srces','atarget','fllowp','grsp'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($userid,User $user)
    {
        $selecteduser = User::find($userid);
        $allusers = User::all();

        return view('superadmin.userpermissions.create',compact('allusers','selecteduser'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,userpermission $userpermission)
    {

        $cats  = $request->ccategorys;
        $subcats  = $request->csubcategorys;
        $cs  = $request->courses;
        $inv  = $request->invoices;
        $adms  = $request->admissions;
        $srcs  = $request->sources;
        $agrs  = $request->assintarges;
        $flps  = $request->followups;
        $gps  = $request->gpr;
        $lea  = $request->leads;
          if(is_array($lea)) 
                 {
                    
                  
                     $leadsdatss  = implode(',',$lea);

                  }

                  else
                  {
                    $leadsdatss = $request->leads;

                  }

                  if(is_array($cats)) 
                 {
                    
                  
                     $categ  = implode(',',$cats);

                  }

                  else
                  {
                    $categ  = $request->ccategorys;

                  }
                  if(is_array($subcats)) 
                 {
                    
                  
                     $subcateg  = implode(',',$subcats);

                  }

                  else
                  {
                    $subcateg  = $request->csubcategorys;

                  }
                   if(is_array($cs)) 
                 {
                    
                  
                     $cours  = implode(',',$cs);

                  }

                  else
                  {
                    $cours  = $request->courses;

                  }
                    if(is_array($inv)) 
                 {
                    
                  
                     $invs  = implode(',',$inv);

                  }

                  else
                  {
                    $invs  = $request->invoices;

                  }
                  if(is_array($adms)) 
                 {
                    
                  
                     $admison  = implode(',',$adms);

                  }

                  else
                  {
                    $admison  = $request->admissions;

                  }
                  if(is_array($srcs)) 
                 {
                    
                  
                     $soru  = implode(',',$srcs);

                  }

                  else
                  {
                    $soru  = $request->sources;

                  }
                   if(is_array($agrs)) 
                 {
                    
                  
                     $argt  = implode(',',$agrs);

                  }

                  else
                  {
                    $argt  = $request->assintarges;

                  }
                  if(is_array($flps)) 
                 {
                    
                  
                     $foll  = implode(',',$flps);

                  }

                  else
                  {
                    $foll  = $request->followups;

                  }
                  if(is_array($gps)) 
                 {
                    
                  
                     $payment  = implode(',',$gps);

                  }

                  else
                  {
                    $payment  = $request->gpr;

                  }

        $userpermissionmodel = new userpermission();
        $userpermission = $userpermissionmodel->create([
            'usersid'=> $request->userper,
            'coursecategory'=> $categ,
            'coursesubcategory'=> $subcateg,
            'course'=> $cours,
            'leads'=> $leadsdatss,
            'invoice'=> $invs,
            'admission'=> $admison,
            'directadmission'=> $request->directadmissions,
            'source'=> $soru,
            'assigntarget'=> $argt,
            'followup'=> $foll,
            'generatepaymentreciept'=> $payment,
        ]);

        return redirect('/permission')->with('success','Permission Assign Successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\userpermission  $userpermission
     * @return \Illuminate\Http\Response
     */
    public function show(userpermission $userpermission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\userpermission  $userpermission
     * @return \Illuminate\Http\Response
     */
    public function edit($id,userpermission $userpermission,User $user)
    {
        $edits = userpermission::find($id);
        $allusers = User::all();
        return view('superadmin.userpermissions.edit',compact('edits','allusers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\userpermission  $userpermission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, userpermission $userpermission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\userpermission  $userpermission
     * @return \Illuminate\Http\Response
     */
    public function destroy(userpermission $userpermission)
    {
        //
    }
}
