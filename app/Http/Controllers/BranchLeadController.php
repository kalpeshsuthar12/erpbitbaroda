<?php

namespace App\Http\Controllers;
use App\leads;
use App\Source;
use App\User;
use App\Branch;
use App\course;
use App\followup;
use App\leadsfollowups;
use App\userpermission;
use Auth;
use Illuminate\Http\Request;

class BranchLeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $userId = Auth::user()->id;
       $leadsdata =  leads::where('user_id',$userId)->orderBy('created_at','DESC')->get();

      //  $leadsdata = DB::select('SELECT * FROM `leads` WHERE `user_id` = "'.$userId.'" ORDER BY created_at DESC');
        $folss = followup::get();

        foreach($leadsdata as $leas)
        {
            $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

            //dd($da);

            $leas->flfollwpdate ='';
                $leas->flremarsk = '';
                $leas->nxtfollowupdate = '';
            //dd($da);
            if($da){
                $leas->flfollwpdate = $da->flfollwpdate;
                $leas->flremarsk = $da->flremarsk;
                $leas->nxtfollowupdate = $da->nxtfollowupdate;
                //$foldate = date('d-m-Y',strtotime($leas->flfollwpdate));

                        //dd($foldate);
            }
        }

        $dates = date('Y-m-d');
        
          //dd($leadspermis);

        

        
        return view('branchs.leads.manage',compact('leadsdata','folss','dates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $sourcedata = Source::get();
        $userdata = User::get();
        $branchdata = Branch::get();
        $coursedata = course::get();
        $fol = followup::get();
        return view('branchs.leads.create',compact('sourcedata','userdata','branchdata','coursedata','fol'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $data =  $request->leadcourse;
        $userId = Auth::user()->id;

       if(is_array($data)) 
                 {
                    
                  
                     $courses  = implode(',',$data);

                  }

                  else
                  {
                    $courses = $request->leadcourse;

                  } 
       
        $leadsmodel = new leads();
        $leads = $leadsmodel->create([
            'source'=> $request->sourcename,
            'branch'=> $request->branches,
            'tobranchs'=> $request->tobranches,
            'studentname'=> $request->sname,
            'email'=> $request->semail,
            'phone'=> $request->sphone,
            'whatsappno'=> $request->wno,
            'course'=> $courses,
            'coursesmode'=> $request->coursemode,
            'coursesmode'=> $request->coursemode,
            'lvalue'=> $request->lvalue,
            'reffrom'=> $request->rfrom,
            'refname'=> $request->rname,
            'refassignto'=> $request->rassignto,
            'address'=> $request->ladress,
            'city'=> $request->lcity,
            'state'=> $request->lstate,
            'zipcode'=> $request->lzipcode,
            'description'=> $request->ldescript,
            'followupstatus'=> $request->followupstatus,
            'leadduration'=> $request->lduaration,
            'followupdate'=> $request->fdate,
            'user_id'=> $userId,
        ]);

        return redirect('/branches-leads')->with('success','Leads Created Successfully!!');
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
        $sourcedata = Source::all();
        $userdata = User::all();
        $branchdata = Branch::all();
        $coursedata = course::all();
        $leadsda = leads::find($id);
        $selectedcourse = explode(',', $leadsda->course);
        $fol = followup::get();
        return view('branchs.leads.edit',compact('sourcedata','userdata','branchdata','coursedata','leadsda','selectedcourse','fol'));
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
                $data =  $request->leadcourse;

      
    
        

       $data =  $request->leadcourse;
       if(is_array($data)) 
                 {
                
                     $courses  = implode(',',$data);

                  }

                  else
                  {
                    $courses = $request->leadcourse;

                  }

         $updated = leads::find($id);
         $updated->source =  $request->sourcename;
         $updated->branch = $request->branches;
         $updated->tobranchs = $request->tpnvbranches;
         $updated->studentname =  $request->sname;
         $updated->email = $request->semail;
         $updated->phone = $request->sphone;
         $updated->whatsappno = $request->wno;
         $updated->course = $courses;
         $updated->coursesmode = $request->coursemode;
         $updated->lvalue = $request->lvalue;
         $updated->reffrom = $request->rfrom;
         $updated->refname = $request->rname;
         $updated->refassignto = $request->rassignto;
         $updated->address = $request->ladress;
         $updated->city = $request->lcity;
         $updated->state = $request->lstate;
         $updated->zipcode = $request->lzipcode;
         $updated->description = $request->ldescript;
         $updated->followupstatus = $request->followupstatus;
         $updated->followupdate = $request->fdate;
         $updated->save();

        return redirect('/branches-leads')->with('success','Leads Updated Successfully!!');
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
