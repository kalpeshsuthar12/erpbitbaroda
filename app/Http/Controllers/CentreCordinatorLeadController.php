<?php

namespace App\Http\Controllers;
use App\leads;
use App\leadsfollowups;
use App\Source;
use App\User;
use App\Branch;
use App\course;
use App\followup;
use Carbon\Carbon;
use DB;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeadImport;
use Illuminate\Http\Request;


class CentreCordinatorLeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

         $userId = Auth::user()->id;
         $userBranch = Auth::user()->branchs;
       $leadsdata =  leads::where('user_id',$userId)->get();

       $userdata = User::where('branchs',$userBranch)->get();

        /*$leadsdata = DB::table('leads')
                 ->join('users', 'users.id', '=', 'leads.user_id')
                 ->where('leads.branch', '=', 'BITSJ')
                 ->select('leads.*','users.name')
                 ->get();*/

        $folss = followup::get();

        foreach($leadsdata as $leas)
        {
            $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

            //dd($da);

                $leas->followupstatus ='';
                $leas->takenby ='';
                $leas->flfollwpdate ='';
                $leas->flremarsk = '';
                $leas->nxtfollowupdate = '';
            //dd($da);
            if($da){
                $leas->followupstatus = $da->followupstatus;
                $leas->takenby = $da->takenby;
                $leas->flfollwpdate = $da->flfollwpdate;
                $leas->flremarsk = $da->flremarsk;
                $leas->nxtfollowupdate = $da->nxtfollowupdate;
                //$foldate = date('d-m-Y',strtotime($leas->flfollwpdate));

                        //dd($foldate);
            }
        }

        $dates = date('Y-m-d');
        
          //dd($leadspermis);

        
        return view('centrecoordinator.leads.manage',compact('leadsdata','folss','dates','userdata'));
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
        return view('centrecoordinator.leads.create',compact('sourcedata','userdata','branchdata','coursedata','fol'));
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

        return redirect('/centre-ordinator-leads')->with('success','Leads Created Successfully!!');
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
         $sourcedata = Source::all();
        $userdata = User::all();
        $branchdata = Branch::all();
        $coursedata = course::all();
        $leadsda = leads::find($id);
        $selectedcourse = explode(',', $leadsda->course);
        $fol = followup::get();
        return view('centrecoordinator.leads.edit',compact('sourcedata','userdata','branchdata','coursedata','leadsda','selectedcourse','fol'));
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

        return redirect('/centre-ordinator-leads')->with('success','Leads Updated Successfully!!');
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
