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
use App\AffiliatesCategory; 
use App\affiliatestrainingcategory;
use Auth;
use Carbon\Carbon;
use DateTime;

use Illuminate\Http\Request;

class AffiliateMarketingUsersLeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    public function transferusersleads()
      {
                $uSerId = Auth::user()->id;
                $cour = course::all();
               $sourcedata = Source::get();
                   $userBranch = Auth::user()->branchs;

                $leadsdata = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.transferto',$uSerId)->get();

                   $userdata = User::where('branchs',$userBranch)->get();
               // dd($leadsdata);

                $folss = followup::get();

                $branchdata = Branch::where('branchname',$userBranch)->get();

                foreach($leadsdata as $leas)
                {
                    $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->orderBy('id','DESC')->first();

                    $leas->followupstatus ='';
                    $leas->takenby ='';
                    $leas->flfollwpdate ='';
                        $leas->flremarsk = '';
                        $leas->nxtfollowupdate = '';

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
                
               
                return view('affiliatesmarketing.leads.transfertoleads',compact('leadsdata','folss','dates','userdata','cour','sourcedata','branchdata'));
      }
      
    public function index(leads $leads,course $course,followup $followup,userpermission $userpermission)
    {
        $userId = Auth::user()->id;
         
       $leadsdata =  leads::where('user_id',$userId)->orderBy('id','DESC')->get();
       $folss = followup::get();
       $userdata = User::where('id',$userId)->get();
       $cour = course::all();
       $sourcedata = Source::get();


        foreach($leadsdata as $leas)
        {
            $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

            //dd($da);

            $leas->followupstatus ='';
            $leas->takenby ='';
            $leas->flfollwpdate ='';
                $leas->flremarsk = '';
                $leas->nxtfollowupdate = '';
            
             if($da){
                $leas->followupstatus = $da->followupstatus;
                $leas->takenby = $da->takenby;
                $leas->flfollwpdate = $da->flfollwpdate;
                $leas->flremarsk = $da->flremarsk;
                $leas->nxtfollowupdate = $da->nxtfollowupdate;
                
            }
        }

        $dates = date('Y-m-d');
        
          //dd($leadspermis);
      

        

        
        return view('affiliatesmarketing.leads.manage',compact('leadsdata','folss','dates','userdata','cour','sourcedata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Source $source,User $user,Branch $branch,course $course,followup $followup)
    {
        $sourcedata = Source::get();
        $userdata = User::get();
        $branchdata = Branch::where('branchname','BITOL')->get();
        $coursedata = course::get();
        $fol = followup::get();
        $afcreates = affiliatestrainingcategory::get();
        return view('affiliatesmarketing.leads.create',compact('sourcedata','userdata','branchdata','coursedata','fol','afcreates'));
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
                  
          if (leads::where('phone', '=', $request->sphone)->exists() || leads::where('whatsappno', '=', $request->wno)->exists() || leads::where('whatsappno', '=', $request->sphone)->exists() || leads::where('phone', '=', $request->wno)->exists()) 
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
                                'institutions'=> $request->insititutesto,
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
                                'leaddate'=> $request->leaddates,
                                'affiliatescategorynames'=> $request->acategorynames,
                                'user_id'=> $userId,
                            ]);
                    }

        return redirect('/affiliate-users-leads')->with('success','Leads Created Successfully!!');
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
        $branchdata = Branch::where('branchname','BITOL')->get();
        $coursedata = course::all();
        $leadsda = leads::find($id);
        $selectedcourse = explode(',', $leadsda->course);
        $fol = followup::get();
        $afcreates = affiliatestrainingcategory::get();
        return view('affiliatesmarketing.leads.edit',compact('sourcedata','userdata','branchdata','coursedata','leadsda','selectedcourse','fol','afcreates'));
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
         $updated->affiliatescategorynames = $request->acategorynames;
         $updated->save();

       return redirect('/affiliate-users-leads')->with('success','Leads Updated Successfully!!');
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

    public function updateleadsfollowups($followup,$followupsId,leads $leads)
    {
        $updates = leads::find($followupsId);
        $updates->followupstatus = $followup; 
        $updates->save(); 
        

        return response()->json($updates);
    }


    public function pendingleads()
     {

        $Cdates = date('Y-m-d');
         $userBranch = Auth::user()->branchs;
         $UserId = Auth::user()->id;

      $leadsdata = leads::select("leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leads.user_id',$UserId)->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leadsfollowups.id','DESC')->get();

     // dd();

         $userdata = User::where('branchs',$userBranch)->get();
    
        $folss = followup::get();

        foreach($leadsdata as $leas)
        {
            $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

            $leas->followupstatus ='';
            $leas->takenby ='';
            $leas->flfollwpdate ='';
                $leas->flremarsk = '';
                $leas->nxtfollowupdate = '';

            if($da){
                $leas->followupstatus = $da->followupstatus;
                $leas->takenby = $da->takenby;
                $leas->flfollwpdate = $da->flfollwpdate;
                $leas->flremarsk = $da->flremarsk;
                $leas->nxtfollowupdate = $da->nxtfollowupdate;

         
            }
        }

        $dates = date('Y-m-d');
        
       
        return view('affiliatesmarketing.leads.pendingleads',compact('leadsdata','folss','dates','userdata','Cdates'));
    }

    public function coldsleads(Request $request)
    {
        $userBranch = Auth::user()->branchs;

         $userId = Auth::user()->id;
          $leadsdata = leads::select("leads.*","users.name","leadsfollowups.leadsfrom","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$userId)->where('leadsfollowups.followupstatus','Cold Follow-ups')->where('leadsfollowups.fstatus',0)->get();


         $userdata = User::where('id',$userId)->get();

          $cour = course::all();

        $folss = followup::get();
               
         foreach($leadsdata as $leas)
        {
            $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

             $leas->followupstatus ='';
            $leas->takenby ='';
            $leas->flfollwpdate ='';
                $leas->flremarsk = '';
                $leas->nxtfollowupdate = '';
            
             if($da){
                $leas->followupstatus = $da->followupstatus;
                $leas->takenby = $da->takenby;
                $leas->flfollwpdate = $da->flfollwpdate;
                $leas->flremarsk = $da->flremarsk;
                $leas->nxtfollowupdate = $da->nxtfollowupdate;
                
            }

            else
            {

                $leas->followupstatus ='';
            $leas->takenby ='';
            $leas->flfollwpdate ='';
                $leas->flremarsk = '';
                $leas->nxtfollowupdate = '';

            }
        }


        /*$da = leadsfollowups::where('leadsfrom','=',$leadsss)->get();*/

        $dates = date('Y-m-d');

        $le = leads::all();

        
        
        return view('affiliatesmarketing.leads.coldleads',compact('leadsdata','folss','dates','cour','le','userdata'));

                //dd($leadsdata);
    }


    public function todaysfollowup()
    {
       
         $userBranch = Auth::user()->branchs;

        $userId = Auth::user()->id;

        $leadsdata = leads::select("leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leads.user_id',$userId)->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->where('leadsfollowups.fstatus',0)->get();

         $userdata = User::where('id',$userId)->latest()->get();
       
        $folss = followup::get();

        foreach($leadsdata as $leas)
        {
            $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

            //    dd($da);

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
             
                   // dd($leas->nxtfollowupdate);   
            }


        }
        
            $dates = date('Y-m-d');
            return view('affiliatesmarketing.leads.todaysfollowup',compact('leadsdata','folss','dates','userdata'));

    }



     public function filtersdatas(Request $request)
    {

        $dfors = $request->DateFor;

         //  dd($dfors);

        if($cdatas = $request->coursedatas)
        {

            $dsearch = $request->datesearch;
            $uSerId = Auth::user()->id;
                $mdatas ="";
                $mobilefinds="";
                $sourcesFind="";
                $CourseModeFInd="";
                $FollowupsFind="";
                $AssinedSearch="";
                $datewiseSearc="";
            //$coursefinds="";
            
                $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                $userdata = User::get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();

                $coursefinds = leads::where('leads.user_id',$uSerId)->whereRaw('FIND_IN_SET("'.$cdatas.'",leads.course)')->get();
                                    foreach($coursefinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

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

             /* return view('superadmin.leads.filtersleads',compact('userdata','Cdates','cour','folss','coursefinds','cdatas','mdatas','mobilefinds','sourcesFind','CourseModeFInd','FollowupsFind'));*/

              return view('affiliatesmarketing.leads.filtersleads',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata'));

        }

        else if($mdatas = $request->mobilefilters)
        {
            $uSerId = Auth::user()->id;
            $cdatas ="";
            $coursefinds="";
            $sourcesFind="";
            $CourseModeFInd="";
            $FollowupsFind="";
            $datewiseSearc="";
            $AssinedSearch= "";

            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                $userdata = User::get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();

                $mobilefinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$uSerId)->where('leads.phone',$mdatas)->orWhere('leads.whatsappno',$mdatas)->get();

                //dd($mobilefinds);
                                    foreach($mobilefinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

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

              //return view('superadmin.leads.filtersleads',compact('userdata','Cdates','cour','folss','mdatas','cdatas','mobilefinds','coursefinds'));
            return view('affiliatesmarketing.leads.filtersleads',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata'));
        }


        else if($sourcessearch = $request->sourceSearch)
        {
                $uSerId = Auth::user()->id;
                $cdatas ="";
                $mdatas ="";
                $mobilefinds="";
                $coursefinds="";
                $sourcesFind="";
                $CourseModeFInd="";
                $FollowupsFind="";
                $AssinedSearch="";
                $datewiseSearc="";

            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                $userdata = User::get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();

                $sourcesFind = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sourcessearch)->where('leads.user_id',$uSerId)->get();
                                    foreach($sourcesFind as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

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

             return view('affiliatesmarketing.leads.filtersleads',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata'));

        }

        else if($cmodessearch = $request->CourseModeSearch)
        {
                $uSerId = Auth::user()->id;
                $cdatas ="";
                $mdatas ="";
                $mobilefinds="";
                $coursefinds="";
                $sourcesFind="";
                $CourseModeFInd="";
                $FollowupsFind="";
                $AssinedSearch="";
                $datewiseSearc="";
            
            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                $userdata = User::get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();

                $CourseModeFInd = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$uSerId)->where('leads.coursesmode',$cmodessearch)->get();
                                    foreach($CourseModeFInd as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

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

            
              return view('affiliatesmarketing.leads.filtersleads',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata'));

        }

        else if($Fsearch = $request->FollowupsSearch)
        {

            $uSerId = Auth::user()->id;
            $cdatas ="";
                $mdatas ="";
                $mobilefinds="";
                $coursefinds="";
                $sourcesFind="";
                $CourseModeFInd="";
                $FollowupsFind="";
                $AssinedSearch="";
                $datewiseSearc="";
            
            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                $userdata = User::get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();

                $FollowupsFind = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus',$Fsearch)->where('leads.user_id',$uSerId)->get();
                //dd($FollowupsFind);

                
                                    foreach($FollowupsFind as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('followupstatus',$Fsearch)->orderBy('id','DESC')->first();

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

             /* return view('superadmin.leads.filtersleads',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','Fsearch'));*/
             return view('affiliatesmarketing.leads.filtersleads',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata'));
        }


        else if($Asearch = $request->AssignedToSearch)
        {
            $uSerId = Auth::user()->id;

            $cdatas ="";
            $mdatas ="";
            $mobilefinds="";
            $coursefinds="";
            $sourcesFind="";
            $CourseModeFInd="";
            $FollowupsFind="";
            $datewiseSearc="";
            
            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                $userdata = User::get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();

                $AssinedSearch = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$uSerId)->where('leads.user_id',$Asearch)->get();
                //dd($FollowupsFind);

                
                                    foreach($AssinedSearch as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->orderBy('id','DESC')->first();

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

            //  return view('superadmin.leads.filtersleads',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch'));

                return view('affiliatesmarketing.leads.filtersleads',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata'));
        }

        else if($dfors == "Lead Date")
        {
            $uSerId = Auth::user()->id;
            $dsearch = $request->datesearch;
            $cdatas ="";
                $mdatas ="";
                $mobilefinds="";
                $coursefinds="";
                $sourcesFind="";
                $CourseModeFInd="";
                $FollowupsFind="";
                $AssinedSearch="";
            
            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                $userdata = User::get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();

                $datewiseSearc = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$uSerId)->where('leads.leaddate',$dsearch)->get();
                //dd($FollowupsFind);

                
                                    foreach($datewiseSearc as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

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

              return view('affiliatesmarketing.leads.filtersleads',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata'));



        }



        else if($dfors == "Next Followup Date")
        {
            $uSerId = Auth::user()->id;
            $dsearch = $request->datesearch;
            $cdatas ="";
                $mdatas ="";
                $mobilefinds="";
                $coursefinds="";
                $sourcesFind="";
                $CourseModeFInd="";
                $FollowupsFind="";
                $AssinedSearch="";
            
            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                $userdata = User::get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();

                //$datewiseSearc = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.leaddate',$dsearch)->get();
                 $datewiseSearc = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$uSerId)->whereDate('leadsfollowups.nxtfollowupdate', '=',$dsearch)->where('nxtfollowupdate',$dsearch)->get();
                //dd($FollowupsFind);

                
                                    foreach($datewiseSearc as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

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

              return view('affiliatesmarketing.leads.filtersleads',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata'));

        }

        else if($dfors == "Followup Date")
        {
            $uSerId = Auth::user()->id;

            $dsearch = $request->datesearch;
            $cdatas ="";
                $mdatas ="";
                $mobilefinds="";
                $coursefinds="";
                $sourcesFind="";
                $CourseModeFInd="";
                $FollowupsFind="";
                $AssinedSearch="";
            
            $userBranch = Auth::user()->branchs;
                $Cdates = date('Y-m-d');
                $userdata = User::get();
                $cour = course::all();
                $sourcedata = Source::all();
                $folss = followup::get();

                 $datewiseSearc = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$uSerId)->whereDate('leadsfollowups.flfollwpdate', '=',$dsearch)->get();
                //dd($FollowupsFind);

                
                                    foreach($datewiseSearc as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('flfollwpdate',$dsearch)->orderBy('id','DESC')->first();

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

              return view('affiliatesmarketing.leads.filtersleads',compact('userdata','Cdates','cour','folss','sourcesFind','mdatas','cdatas','sourcesFind','mobilefinds','coursefinds','CourseModeFInd','CourseModeFInd','FollowupsFind','AssinedSearch','datewiseSearc','sourcedata'));

        }

                    
                   

    }
}
