<?php

namespace App\Http\Controllers;

use App\leads;
use App\leadsfollowups;
use App\Source;
use App\User;
use App\Branch;
use App\course;
use App\coursecategory;
use App\followup;
use Carbon\Carbon;
use DB;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeadImport;
use Illuminate\Http\Request;

class CenterManagerLeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $currrentmonths = date('m');
        
        $userId = Auth::user()->id;
         $userBranch = Auth::user()->branchs;
       $leadsdata =  leads::join('users', 'users.id', '=', 'leads.user_id')->select('leads.*','users.name')->where('user_id',$userId)->whereMonth('leads.leaddate', $currrentmonths)->orderBy('id','DESC')->get();

       $userdata = User::where('branchs',$userBranch)->get();

       //$cour = course::all();
    //sourcedata = Source::get();


        /*$leadsdata = DB::table('leads')
                 ->join('users', 'users.id', '=', 'leads.user_id')
                 ->where('leads.branch', '=', 'BITSJ')
                 ->select('leads.*','users.name')
                 ->get();*/

        $folss = followup::get();

        foreach($leadsdata as $leas)
        {
            $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

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

        
         $cour = course::all();
         $branchdata = Branch::where('branchname',$userBranch)->get();

        $folss = followup::get();
        $userdata = User::where('branchs',$userBranch)->get();
        $sourcedata = Source::get();
        $ccatall = coursecategory::get();
        
        return view('centremanager.leads.manage',compact('leadsdata','folss','dates','userdata','cour','sourcedata','ccatall','branchdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userBranch = Auth::user()->branchs;
          $sourcedata = Source::get();
        $userdata = User::where('branchs',$userBranch)->get();
        $branchdata = Branch::get();
        $coursedata = course::get();
        $fol = followup::get();
        return view('centremanager.leads.create',compact('sourcedata','userdata','branchdata','coursedata','fol'));
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
                  
         if (leads::where('phone', '=', $request->sphone)->exists() || leads::where('whatsappno', '=', $request->wno)->exists()) 
                    {
                         return redirect()->back()->with('error','Leads Already Exists');
                    }
                    else
                    {
       
                            $leadsmodel = new leads();
                            $leads = $leadsmodel->create([
                                'source'=> $request->sourcename,
                                'branch'=> $request->branches,
                                'tobranchs'=> $request->tobranches,
                                'studentname'=> $request->sname,
                                'institutions'=> $request->insititutesto,
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
                                'leaddate'=> $request->leaddates,
                                'user_id'=> $userId,
                  
                            ]);
                    }

        return redirect('/centre-manager-users-leads')->with('success','Leads Created Successfully!!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function show()
    {

    $currentmonths = date('m');
    
                $userId = Auth::user()->id;
         $userBranch = Auth::user()->branchs;
          $leadsdata = DB::table('leads')
                 ->join('users', 'users.id', '=', 'leads.user_id')
                 ->whereMonth('leads.leaddate', $currentmonths)
                 ->where('leads.tobranchs', '=', $userBranch)
                 ->orderBy('leads.leaddate','DESC')
                 ->select('leads.*','users.name')
                 ->get();

       //$userdata = User::where('branchs',$userBranch)->get();

       //$cour = course::all();
       //$sourcedata = Source::get();


        /*$leadsdata = DB::table('leads')
                 ->join('users', 'users.id', '=', 'leads.user_id')
                 ->where('leads.branch', '=', 'BITSJ')
                 ->select('leads.*','users.name')
                 ->get();*/

        //$folss = followup::get();

        foreach($leadsdata as $leas)
        {
            $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

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

        $cour = course::all();
         $branchdata = Branch::where('branchname',$userBranch)->get();

        $folss = followup::get();
        $userdata = User::where('branchs',$userBranch)->get();
        $sourcedata = Source::get();
        $ccatall = coursecategory::get();

        

        
        return view('centremanager.leads.marketingusersleads',compact('leadsdata','folss','dates','userdata','cour','sourcedata','ccatall','branchdata'));
        
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
        return view('centremanager.leads.edit',compact('sourcedata','userdata','branchdata','coursedata','leadsda','selectedcourse','fol'));
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
         $updated->institutions = $request->insititutesto;
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
         $updated->leaddate = $request->leaddates;
         $updated->user_id = $request->assignedto;
         $updated->save();

        return redirect('/centre-manager-users-leads')->with('success','Leads Updated Successfully!!');
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
    
   public function filtersdatas(Request $request)
    {   

       $userBranch = Auth::user()->branchs; 
       $userIds = Auth::user()->id; 
          $datesfor = "";
         $namedatas = "";
         $mobdatas = "";
         $coursedatas = "";
         $cmodes = "";
         $sources= "";
         $fsearch = "";
         $asearch = "";
         $bransdata = "";
         $categorydata = "";

        if($namedatas = $request->getstudentsnames)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leads.id as lid")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                        $leas->followupstatus ='';
                                        $leas->takenby ='';
                                        $leas->flfollwpdate ='';
                                        $leas->flremarsk = '';
                                        $leas->nxtfollowupdate = '';

                                        if($da)
                                        {
                                            $leas->followupstatus = $da->followupstatus;
                                            $leas->takenby = $da->takenby;
                                            $leas->flfollwpdate = $da->flfollwpdate;
                                            $leas->flremarsk = $da->flremarsk;
                                            $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                           
                                        }

                                      }


             return view('centremanager.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              

            $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->orwhere('whatsappno',$mobdatas)->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                        $leas->followupstatus ='';
                                        $leas->takenby ='';
                                        $leas->flfollwpdate ='';
                                        $leas->flremarsk = '';
                                        $leas->nxtfollowupdate = '';

                                        if($da)
                                        {
                                            $leas->followupstatus = $da->followupstatus;
                                            $leas->takenby = $da->takenby;
                                            $leas->flfollwpdate = $da->flfollwpdate;
                                            $leas->flremarsk = $da->flremarsk;
                                            $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                           
                                        }

                                      }

             return view('centremanager.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','mobdatas','namedatas','datesfor','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($datesfor = $request->DateFor)
        {   
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if($datesfor == "Lead Date")
            {


                $folss = followup::get();
                $userdata = User::where('branchs',$userBranch)->get();
                   $cour = course::all();
                    $sourcedata = Source::all();
                    $branchdata = Branch::where('branchname',$userBranch)->get();
                    $ccatall = coursecategory::get();

                   


                    $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereBetween('leads.leaddate',[$startdates,$enddats])->orderBy('leads.leaddate','DESC')->get();
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                                $leas->followupstatus ='';
                                                $leas->takenby ='';
                                                $leas->flfollwpdate ='';
                                                $leas->flremarsk = '';
                                                $leas->nxtfollowupdate = '';

                                                if($da)
                                                {
                                                    $leas->followupstatus = $da->followupstatus;
                                                    $leas->takenby = $da->takenby;
                                                    $leas->flfollwpdate = $da->flfollwpdate;
                                                    $leas->flremarsk = $da->flremarsk;
                                                    $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                                   
                                                }

                                              }

                     return view('centremanager.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
                }

            elseif($datesfor == "Followup Date")
            {


                $folss = followup::get();
                $userdata = User::where('branchs',$userBranch)->get();
                   $cour = course::all();
                   $sourcedata = Source::all();
                   $branchdata = Branch::where('branchname',$userBranch)->get();
                   $ccatall = coursecategory::get();

                    $namesfinds = leads::select("users.name","leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","leads.user_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->orderBy('leads.leaddate','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                                $leas->followupstatus ='';
                                                $leas->takenby ='';
                                                $leas->flfollwpdate ='';
                                                $leas->flremarsk = '';
                                                $leas->nxtfollowupdate = '';

                                                if($da)
                                                {
                                                    $leas->followupstatus = $da->followupstatus;
                                                    $leas->takenby = $da->takenby;
                                                    $leas->flfollwpdate = $da->flfollwpdate;
                                                    $leas->flremarsk = $da->flremarsk;
                                                    $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                                   
                                                }

                                              }

                            

                     return view('centremanager.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
                }

            

            elseif($datesfor == "Next Followup Date")
            {


                $folss = followup::get();  
                 $cour = course::all();
                $userdata = User::where('branchs',$userBranch)->get();
                $sourcedata = Source::all();
                $branchdata = Branch::where('branchname',$userBranch)->get();
                $ccatall = coursecategory::get();

                    $namesfinds = leads::select("users.name","leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","leads.user_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->orderBy('leads.leaddate','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                                $leas->followupstatus ='';
                                                $leas->takenby ='';
                                                $leas->flfollwpdate ='';
                                                $leas->flremarsk = '';
                                                $leas->nxtfollowupdate = '';

                                                if($da)
                                                {
                                                    $leas->followupstatus = $da->followupstatus;
                                                    $leas->takenby = $da->takenby;
                                                    $leas->flfollwpdate = $da->flfollwpdate;
                                                    $leas->flremarsk = $da->flremarsk;
                                                    $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                                   
                                                }

                                        }

                        
                     return view('centremanager.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
                }

            
            }

        elseif($coursedatas = $request->coursedatas)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

               $cstarstdates = $request->cdatestat;
               $cendatea = $request->cdateend;

            $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.leaddate',[$cstarstdates,$cendatea])->orderBy('leads.leaddate','DESC')->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                        $leas->followupstatus ='';
                                        $leas->takenby ='';
                                        $leas->flfollwpdate ='';
                                        $leas->flremarsk = '';
                                        $leas->nxtfollowupdate = '';

                                        if($da)
                                        {
                                            $leas->followupstatus = $da->followupstatus;
                                            $leas->takenby = $da->takenby;
                                            $leas->flfollwpdate = $da->flfollwpdate;
                                            $leas->flremarsk = $da->flremarsk;
                                            $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                           
                                        }

                                      }

                        
             return view('centremanager.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','coursedatas','cstarstdates','cendatea','namedatas','mobdatas','coursedatas','datesfor','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              

            $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.coursesmode',$cmodes)->orderBy('leads.leaddate','DESC')->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                        $leas->followupstatus ='';
                                        $leas->takenby ='';
                                        $leas->flfollwpdate ='';
                                        $leas->flremarsk = '';
                                        $leas->nxtfollowupdate = '';

                                        if($da)
                                        {
                                            $leas->followupstatus = $da->followupstatus;
                                            $leas->takenby = $da->takenby;
                                            $leas->flfollwpdate = $da->flfollwpdate;
                                            $leas->flremarsk = $da->flremarsk;
                                            $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                           
                                        }

                                      }

             return view('centremanager.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','cmodes','mobdatas','datesfor','coursedatas','namedatas','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($sources = $request->sourceSearch)
        {
            $starsdates = $request->sdatestat;
            $enssdates = $request->sdateend;

            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->orderBy('leads.leaddate','DESC')->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                        $leas->followupstatus ='';
                                        $leas->takenby ='';
                                        $leas->flfollwpdate ='';
                                        $leas->flremarsk = '';
                                        $leas->nxtfollowupdate = '';

                                        if($da)
                                        {
                                            $leas->followupstatus = $da->followupstatus;
                                            $leas->takenby = $da->takenby;
                                            $leas->flfollwpdate = $da->flfollwpdate;
                                            $leas->flremarsk = $da->flremarsk;
                                            $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                           
                                        }

                                      }

                

             return view('centremanager.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','sources','datesfor','namedatas','mobdatas','coursedatas','cmodes','starsdates','enssdates','fsearch','asearch','bransdata','categorydata'));
        }



        elseif($fsearch = $request->FollowupsSearch)
        {
            $fdates = $request->fsdate;
            $fenddates = $request->fedate;

            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("users.name","leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","leads.user_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.leaddate',[$fdates,$fenddates])->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                                $leas->followupstatus ='';
                                                $leas->takenby ='';
                                                $leas->flfollwpdate ='';
                                                $leas->flremarsk = '';
                                                $leas->nxtfollowupdate = '';

                                                if($da)
                                                {
                                                    $leas->followupstatus = $da->followupstatus;
                                                    $leas->takenby = $da->takenby;
                                                    $leas->flfollwpdate = $da->flfollwpdate;
                                                    $leas->flremarsk = $da->flremarsk;
                                                    $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                                   
                                                }

                                        }


                     return view('centremanager.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','fdates','fenddates','asearch','bransdata','categorydata'));
        }



        elseif($asearch = $request->AssignedToSearch)
        {
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$asearch)->whereBetween('leads.leaddate',[$asdates,$aenddates])->orderBy('leads.leaddate','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                                $leas->followupstatus ='';
                                                $leas->takenby ='';
                                                $leas->flfollwpdate ='';
                                                $leas->flremarsk = '';
                                                $leas->nxtfollowupdate = '';

                                                if($da)
                                                {
                                                    $leas->followupstatus = $da->followupstatus;
                                                    $leas->takenby = $da->takenby;
                                                    $leas->flfollwpdate = $da->flfollwpdate;
                                                    $leas->flremarsk = $da->flremarsk;
                                                    $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                                   
                                                }

                                        }

                                
                        

                     return view('centremanager.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','asdates','aenddates','bransdata','categorydata'));
        }


        elseif($bransdata = $request->branchSearchDatas)
        {
            $bstartdate = $request->BStartDate;
            $benddate = $request->BEnddate;

            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            $namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.leaddate','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                                $leas->followupstatus ='';
                                                $leas->takenby ='';
                                                $leas->flfollwpdate ='';
                                                $leas->flremarsk = '';
                                                $leas->nxtfollowupdate = '';

                                                if($da)
                                                {
                                                    $leas->followupstatus = $da->followupstatus;
                                                    $leas->takenby = $da->takenby;
                                                    $leas->flfollwpdate = $da->flfollwpdate;
                                                    $leas->flremarsk = $da->flremarsk;
                                                    $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                                   
                                                }

                                        }
                        
                        

                     return view('centremanager.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','bstartdate','benddate','categorydata'));
        }


        elseif($categorydata = $request->categorysDatas)
        {

            //dd($categorydata);
            $cstartdate = $request->CStartDate;
            $cenddate = $request->CEnddate;

            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

               $findcourse = course::where('cat_id',$categorydata)->pluck('coursename');
              //dd($findcourse);

              /* foreach($findcourse as $courses)
               {
                    $getourses = $courses->coursename;

               }*/
            
           
               $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('leads.user_id',$userIds)->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.leaddate','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

                                                $leas->followupstatus ='';
                                                $leas->takenby ='';
                                                $leas->flfollwpdate ='';
                                                $leas->flremarsk = '';
                                                $leas->nxtfollowupdate = '';

                                                if($da)
                                                {
                                                    $leas->followupstatus = $da->followupstatus;
                                                    $leas->takenby = $da->takenby;
                                                    $leas->flfollwpdate = $da->flfollwpdate;
                                                    $leas->flremarsk = $da->flremarsk;
                                                    $leas->nxtfollowupdate = $da->nxtfollowupdate;
                                                   
                                                }

                                        }
                        

                     return view('centremanager.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate')); 
            
        

            
        }

    }
}
