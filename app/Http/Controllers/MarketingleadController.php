<?php

namespace App\Http\Controllers;
use App\leads;
use App\Source;
use App\User;
use App\Branch;
use App\course;
use App\followup;
use App\leadsfollowups;
use App\coursecategory;
use App\PastLeadsDatas;
use App\userpermission;
use Auth;
use DB;
use Illuminate\Http\Request;

class MarketingleadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
   
     public function transferusersleads()
        {
                $uSerId = Auth::user()->id;
                
               // dd($uSerId);
                $cour = course::all();
               $sourcedata = Source::get();
                   $userBranch = Auth::user()->branchs;
                   $today = date('Y-m-d');

                
                 $leadsdata = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$uSerId)->whereBetween('leads.leaddate',['2022-01-01',$today])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();

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
                $folss = followup::get();
            $userdata = User::where('id',$uSerId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();
               
                return view('marketing.leads.transfertoleads',compact('leadsdata','folss','dates','userdata','cour','sourcedata','branchdata','ccatall'));
        }


    public function filtertransferleads(Request $request)
    {
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
        $dates = date('Y-m-d');
         $uSerId = Auth::user()->id;

         $Cdates = date('Y-m-d');
        if($namedatas = $request->getstudentsnames)
        {
            //dd('called');

            $folss = followup::get();
            $userdata = User::where('id',$uSerId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$uSerId)->Where('leads.studentname', 'like', '%' .$namedatas. '%')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


            //dd($namesfinds);

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

             return view('marketing.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
           $userdata = User::where('id',$uSerId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$uSerId)->where('leads.phone',$mobdatas)->where('leads.whatsappno',$mobdatas)->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();



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

             return view('marketing.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates'));
        }


        elseif($datesfor = $request->DateFor)
        {   
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if($datesfor == "Transfer Date")
            {


                $folss = followup::get();
                $userdata = User::where('id',$uSerId)->get();
                   $cour = course::all();
                    $sourcedata = Source::all();
                    $branchdata = Branch::get();
                    $ccatall = coursecategory::get();

                    

                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$uSerId)->whereBetween('leads.transferdate',[$startdates,$enddats])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                     return view('marketing.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','startdates','enddats'));
                }

            elseif($datesfor == "Followup Date")
            {


                $folss = followup::get();
                $userdata = User::where('id',$uSerId)->get();
                   $cour = course::all();
                   $sourcedata = Source::all();
                   $branchdata = Branch::get();
                   $ccatall = coursecategory::get();

                    
                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$uSerId)->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();

                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                     return view('marketing.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','enddats','startdates'));
                }

            

            elseif($datesfor == "Next Followup Date")
            {


                $folss = followup::get();  
                 $cour = course::all();
                $userdata = User::where('id',$uSerId)->get();
                $sourcedata = Source::all();
                $branchdata = Branch::get();
                $ccatall = coursecategory::get();

                    

                    

                $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$uSerId)->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                     return view('marketing.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','enddats','startdates'));
                }

            
            }

        elseif($coursedatas = $request->coursedatas)
        {
            $folss = followup::get();
            $userdata = User::where('id',$uSerId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();
               $cstartsdates = $request->cdatestat;
               $cendsdates = $request->cdateend;

            

            //$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.leaddate',[$cstartsdates,$cendsdates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->groupBy('leadsfollowups.leadsfrom')->get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$uSerId)->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.transferdate',[$cstartsdates,$cendsdates])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

             return view('marketing.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','cstartsdates','cendsdates'));
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
             $userdata = User::where('id',$uSerId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            
            //$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Cold Follow-ups')->where('leads.coursesmode',$cmodes)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$uSerId)->where('leads.coursesmode',$cmodes)->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

             return view('marketing.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates'));
        }


        elseif($sources = $request->sourceSearch)
        {
            $starsdates = $request->sdatestat;
            $enssdates = $request->sdateend;

            $folss = followup::get();
             $userdata = User::where('id',$uSerId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

        /*  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->orderBy('leads.id','DESC')->get();*/

        //$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

        $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$uSerId)->where('leads.source',$sources)->whereBetween('leads.transferdate',[$starsdates,$enssdates])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

             return view('marketing.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','starsdates','enssdates'));
        }



        elseif($fsearch = $request->FollowupsSearch)
        {
            $fdates = $request->fsdate;
            $fenddates = $request->fedate;

            $folss = followup::get();
             $userdata = User::where('id',$uSerId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

    
            //$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.leaddate',[$fdates,$fenddates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$uSerId)->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.transferdate',[$fdates,$fenddates])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                     return view('marketing.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','fdates','fenddates'));
        }



        elseif($asearch = $request->AssignedToSearch)
        {
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
             $userdata = User::where('id',$uSerId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

                /*$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$asearch)->whereBetween('leads.leaddate',[$asdates,$aenddates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();*/

                $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$uSerId)->where('leads.user_id',$asearch)->whereBetween('leads.transferdate',[$asdates,$aenddates])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                     return view('marketing.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','asdates','aenddates'));
        }


        elseif($bransdata = $request->branchSearchDatas)
        {
            $bstartdate = $request->BStartDate;
            $benddate = $request->BEnddate;
            
            //dd($bstartdate,$benddate);

            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            //$namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();


            //$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',array($bstartdate,$benddate))->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferbranch',$bransdata)->whereBetween('leads.transferdate',array($bstartdate,$benddate))->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                     return view('marketing.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','bstartdate','benddate'));
        }


        elseif($categorydata = $request->categorysDatas)
        {

            //dd($categorydata);
            $cstartdate = $request->CStartDate;
            $cenddate = $request->CEnddate;

            $folss = followup::get();
             $userdata = User::where('id',$uSerId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

               $findcourse = course::where('cat_id',$categorydata)->pluck('coursename');
              //dd($findcourse);

              /* foreach($findcourse as $courses)
               {
                    $getourses = $courses->coursename;

               }*/

        

            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.id','DESC')->get();

            //$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$uSerId)->whereIn('leads.course',$findcourse)->whereBetween('leads.transferdate',[$cstartdate,$cenddate])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                     return view('marketing.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','cstartdate','cenddate'));
        }

    }
    public function index(leads $leads,course $course,followup $followup,userpermission $userpermission)
    {
        $userId = Auth::user()->id;
         
       //$leadsdata =  leads::where('user_id',$userId)->orderBy('id','DESC')->get();
       
        $dates = date('Y-m-d');
       
         $leadsdata =  leads::join('users', 'users.id', '=', 'leads.user_id')->select('leads.*','users.name')->where('leads.user_id',$userId)->whereBetween('leads.leaddate',array('2022-01-01',$dates))->orderBy('leads.leaddate','DESC')->get();

        foreach($leadsdata as $leas)
        {
            $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('fstatus',0)->orderBy('id','DESC')->first();

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
        $userBranch = Auth::user()->branchs;

        $folss = followup::get();
       $userdata = User::where('id',$userId)->get();
       $cour = course::all();
       $sourcedata = Source::get();
       $branchdata = Branch::where('branchname',$userBranch)->get();
       $ccatall = coursecategory::get();
        

        
        return view('marketing.leads.manage',compact('leadsdata','folss','dates','userdata','cour','sourcedata','branchdata','ccatall'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Source $source,User $user,Branch $branch,course $course,followup $followup)
    {

         $sourcedata = Source::get();
         $userId = Auth::user()->id;
        $userdata = User::where('id',$userId)->get();
        $branchdata = Branch::get();
        $coursedata = course::get();
        $fol = followup::get();
        return view('marketing.leads.create',compact('sourcedata','userdata','branchdata','coursedata','fol'));
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
                        'description'=> $request->ldescript,
                        'leaddate'=> $request->leaddates,
                        'user_id'=> $userId,
                    ]);
                }
        return redirect('/marketing-leads')->with('success','Leads Created Successfully!!');
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
    
    public function pastleads()
    {
            $userId = Auth::user()->id;

        $leadsdatas = PastLeadsDatas::join('users', 'users.id', '=', 'past_leads_datas.ptuser_id')->select('past_leads_datas.*','users.name','past_leads_datas.id as lid')->where('past_leads_datas.ptuser_id',$userId)->orderBy('past_leads_datas.ptleadsdates','DESC')->get();

             $cour = course::all();
         $branchdata = Branch::get();

        $folss = followup::get();
        $userdata = User::where('id',$userId)->get();
        $sourcedata = Source::get();
        $ccatall = coursecategory::get();

            foreach($leadsdatas as $leas)
            {
                $da = leadsfollowups::where('leadsfrom','=',$leas->oldid)->orderBy('id','DESC')->first();

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

        return view('marketing.leads.pastleadsdatas',compact('leadsdatas','folss','cour','userdata','sourcedata','branchdata','ccatall'));
    }

     public function filterspastleads (Request $request)
    {   

         $userBranch = Auth::user()->branchs;
         $userId = Auth::user()->id;

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
           $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptuser_id',$userId)->Where('ptstudentname', 'like', '%' .$namedatas. '%')->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->oldid)->where('fstatus',0)->orderBy('id','DESC')->first();

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


             return view('marketing.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

              

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('ptphone',$mobdatas)->orwhere('ptwhatsappno',$mobdatas)->where('past_leads_datas.ptuser_id',$userId)->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->oldid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

             return view('marketing.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','mobdatas','namedatas','datesfor','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($datesfor = $request->DateFor)
        {   
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if($datesfor == "Lead Date")
            {


                $folss = followup::get();
                $userdata = User::where('id',$userId)->get();
                   $cour = course::all();
                    $sourcedata = Source::all();
                    $branchdata = Branch::get();
                    $ccatall = coursecategory::get();

                   


                    $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->whereBetween('past_leads_datas.ptoldleadsdates',[$startdates,$enddats])->where('past_leads_datas.ptuser_id',$userId)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->oldid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                     return view('marketing.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
                }

            elseif($datesfor == "Followup Date")
            {


                $folss = followup::get();
                $userdata = User::get();
                   $cour = course::all();
                   $sourcedata = Source::all();
                   $branchdata = Branch::get();
                   $ccatall = coursecategory::get();

                    $namesfinds = PastLeadsDatas::select("users.name","past_leads_datas.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","past_leads_datas.ptuser_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","past_leads_datas.oldid")->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->where('past_leads_datas.ptuser_id',$userId)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->oldid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                            

                     return view('marketing.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
                }

            

            elseif($datesfor == "Next Followup Date")
            {


                $folss = followup::get();  
                 $cour = course::all();
               $userdata = User::where('id',$userId)->get();
                $sourcedata = Source::all();
                $branchdata = Branch::get();
                $ccatall = coursecategory::get();

                    $namesfinds = PastLeadsDatas::select("users.name","past_leads_datas.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","past_leads_datas.ptuser_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","past_leads_datas.oldid")->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->where('past_leads_datas.ptuser_id',$userId)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->oldid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                        
                     return view('marketing.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
                }

            
            }

        elseif($coursedatas = $request->coursedatas)
        {
            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

               $cstarstdates = $request->cdatestat;
               $cendatea = $request->cdateend;

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",ptuser_id.ptcourse)')->whereBetween('past_leads_datas.ptoldleadsdates',[$cstarstdates,$cendatea])->where('past_leads_datas.ptuser_id',$userId)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->oldid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                        
             return view('marketing.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','coursedatas','cstarstdates','cendatea','namedatas','mobdatas','coursedatas','datesfor','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
           $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

              

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptcoursesmode',$cmodes)->where('past_leads_datas.ptuser_id',$userId)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->oldid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

             return view('marketing.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','cmodes','mobdatas','datesfor','coursedatas','namedatas','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($sources = $request->sourceSearch)
        {
            $starsdates = $request->sdatestat;
            $enssdates = $request->sdateend;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptsource',$sources)->where('past_leads_datas.ptuser_id',$userId)->whereBetween('past_leads_datas.ptoldleadsdates',[$starsdates,$enssdates])->orderBy('past_leads_datas.ptleadsdates','DESC')->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->oldid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                

             return view('marketing.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','sources','datesfor','namedatas','mobdatas','coursedatas','cmodes','starsdates','enssdates','fsearch','asearch','bransdata','categorydata'));
        }



        elseif($fsearch = $request->FollowupsSearch)
        {
            $fdates = $request->fsdate;
            $fenddates = $request->fedate;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

     $namesfinds = PastLeadsDatas::select("users.name","past_leads_datas.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","past_leads_datas.ptuser_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","past_leads_datas.oldid")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('past_leads_datas.ptoldleadsdates',[$fdates,$fenddates])->where('past_leads_datas.ptuser_id',$userId)->orderBy('past_leads_datas.ptleadsdates','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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


                     return view('marketing.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','fdates','fenddates','asearch','bransdata','categorydata'));
        }



        elseif($asearch = $request->AssignedToSearch)
        {
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
           $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptuser_id',$asearch)->whereBetween('past_leads_datas.ptoldleadsdates',array($asdates,$aenddates))->where('past_leads_datas.ptuser_id',$userId)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
                     
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->oldid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                                
                        

                     return view('marketing.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','asdates','aenddates','bransdata','categorydata'));
        }


        elseif($bransdata = $request->branchSearchDatas)
        {
            $bstartdate = $request->BStartDate;
            $benddate = $request->BEnddate;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds =  PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptbranch',$bransdata)->whereBetween('past_leads_datas.ptleadsdates',[$bstartdate,$benddate])->where('past_leads_datas.ptuser_id',$userId)->orderBy('past_leads_datas.ptleadsdates','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->oldid)->where('fstatus',0)->orderBy('id','DESC')->first();

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
                        
                        

                     return view('marketing.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','bstartdate','benddate','categorydata'));
        }


        elseif($categorydata = $request->categorysDatas)
        {

            //dd($categorydata);
            $cstartdate = $request->CStartDate;
            $cenddate = $request->CEnddate;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

               $findcourse = course::where('cat_id',$categorydata)->pluck('coursename');
              //dd($findcourse);

              /* foreach($findcourse as $courses)
               {
                    $getourses = $courses->coursename;

               }*/

        

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->whereIn('past_leads_datas.ptcourse',$findcourse)->whereBetween('past_leads_datas.ptleadsdates',[$cstartdate,$cenddate])->where('past_leads_datas.ptuser_id',$userId)->orderBy('past_leads_datas.ptleadsdates','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->oldid)->where('fstatus',0)->orderBy('id','DESC')->first();

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
                        

                     return view('marketing.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
        }

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
        return view('marketing.leads.edit',compact('sourcedata','userdata','branchdata','coursedata','leadsda','selectedcourse','fol'));
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
         $updated->description = $request->ldescript;
         $updated->save();

        return redirect('/marketing-leads')->with('success','Leads Updated Successfully!!');
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

    public function matachunmatach($emails,leads $leads)
    {   
        
        //dd($ea);

        if($ea = leads::where('email','=',$emails)->first())
        {
                $msg = "User  Already Exists";



                return response()->json($msg);
        }

        else
        {
            $msg = " ";
             return response()->json($msg);
        }
    }

     public function pmatchpunmatch($phones,leads $leads)
    {
        
        //dd($ea);

        if($phon = leads::where('phone','=',$phones)->orWhere('whatsappno',$phones)->first())
        {
                 return response()->json(
            [
                'success' => true,
                'message' => 'User Already Exist!!'
            ]);
        }

        else
        {
            $mesg = " ";
             return response()->json($mesg);
        }
    }

      public function marketingwalkedinleads()
    {
        $currentMonths = date('m');
        $usersId = Auth::user()->id;
        
        $leadsdata = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereMonth('leadsfollowups.flfollwpdate',$currentMonths)->where('leadsfollowups.followupstatus','Walked-In')->where('leads.user_id',$usersId)->orderBy('leadsfollowups.flfollwpdate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
         $userdata = User::where('id',$usersId)->get();
       // dd($leadsdata);

        //$folss = followup::get();

        foreach($leadsdata as $leas)
        {
            $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('followupstatus','Walked-In')->orderBy('id','DESC')->first();

            $leas->flfollwpdate ='';
                $leas->flremarsk = '';
                $leas->nxtfollowupdate = '';

            if($da){
                $leas->flfollwpdate = $da->flfollwpdate;
                $leas->flremarsk = $da->flremarsk;
                $leas->nxtfollowupdate = $da->nxtfollowupdate;
                //$foldate = date('d-m-Y',strtotime($leas->flfollwpdate));

                        //dd($foldate);
            }
        }

        $dates = date('Y-m-d');
        $cour = course::all();
        $branchdata = Branch::get();

        $folss = followup::get();
        $userdata = User::where('id',$usersId)->get();
        $sourcedata = Source::get();
        $ccatall = coursecategory::get();
        
       
        return view('marketing.leads.walkedinleads',compact('leadsdata','folss','dates','userdata','cour','sourcedata','ccatall','branchdata'));
    }


    public function filtersmarketingwalkedinleads(Request $request)
    {   

        $userBranch = Auth::user()->branchs; 
        $userId = Auth::user()->id; 
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
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->Where('studentname', 'like', '%' .$namedatas. '%')->where('leads.user_id',$userId)->orWhere('leads.old_user_id',$userId)->orderBy('leads.leaddate','DESC')->get();

            
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('followupstatus','Walked-In')->orderBy('id','DESC')->first();

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


             return view('marketing.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

              

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->where('leads.phone',$mobdatas)->orWhere('leads.whatsappno',$mobdatas)->where('leads.user_id',$userId)->orderBy('leads.leaddate','DESC')->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('followupstatus','Walked-In')->orderBy('id','DESC')->first();

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

             return view('marketing.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','mobdatas','namedatas','datesfor','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($datesfor = $request->DateFor)
        {   
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if($datesfor == "Walked-In Date")
            {


                $folss = followup::get();
                $userdata = User::where('id',$userId)->get();
                   $cour = course::all();
                    $sourcedata = Source::all();
                   $branchdata = Branch::get();
                    $ccatall = coursecategory::get();

                   


                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*",'leads.id as lids')->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->where('leads.user_id',$userId)->orWhere('leads.old_user_id',$userId)->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->orderBy('leads.leaddate','DESC')->get();

                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('followupstatus','Walked-In')->orderBy('id','DESC')->first();

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

                     return view('marketing.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
            }



            
        }

        elseif($coursedatas = $request->coursedatas)
        {
            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

               $cstarstdates = $request->cdatestat;
               $cendatea = $request->cdateend;

            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.leaddate',[$cstarstdates,$cendatea])->orderBy('leads.leaddate','DESC')->get();

            $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.leaddate',[$cstarstdates,$cendatea])->where('leadsfollowups.followupstatus','Walked-In')->where('leads.user_id',$userId)->orWhere('leads.old_user_id',$userId)->orderBy('leads.leaddate','DESC')->get();

            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('followupstatus','Walked-In')->orderBy('id','DESC')->first();

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

                        
             return view('marketing.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','coursedatas','cstarstdates','cendatea','namedatas','mobdatas','coursedatas','datesfor','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

              

            $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$userId)->orWhere('leads.old_user_id',$userId)->where('leadsfollowups.followupstatus','Walked-In')->where('leads.coursesmode',$cmodes)->orderBy('leads.leaddate','DESC')->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('followupstatus','Walked-In')->orderBy('id','DESC')->first();

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

             return view('marketing.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','cmodes','mobdatas','datesfor','coursedatas','namedatas','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($sources = $request->sourceSearch)
        {
            $starsdates = $request->sdatestat;
            $enssdates = $request->sdateend;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.source',$sourcessearch)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->where('leadsfollowups.followupstatus','Walked-In')->where('leads.user_id',$userId)->orWhere('leads.old_user_id',$userId)->orderBy('leads.leaddate','DESC')->get();
            foreach($namesfinds as $leas)
                                    {
                                        $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('followupstatus','Walked-In')->orderBy('id','DESC')->first();

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

                

             return view('marketing.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','sources','datesfor','namedatas','mobdatas','coursedatas','cmodes','starsdates','enssdates','fsearch','asearch','bransdata','categorydata'));
        }



        elseif($fsearch = $request->FollowupsSearch)
        {
            $fdates = $request->fsdate;
            $fenddates = $request->fedate;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leadsfollowups.flfollwpdate',[$starsdates,$enssdates])->where('leadsfollowups.followupstatus','Walked-In')->where('leads.user_id',$userId)->where('leads.old_user_id',$userId)->orderBy('leads.leaddate','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->id)->where('followupstatus','Walked-In')->orderBy('id','DESC')->first();

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


                     return view('marketing.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','fdates','fenddates','asearch','bransdata','categorydata'));
        }



        elseif($asearch = $request->AssignedToSearch)
        {
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

          /*  $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leadsfollowups.flfollwpdate',[$asdates,$aenddates])->where('leadsfollowups.followupstatus','Walked-In')->where('leads.user_id',$asearch)->where('leads.old_user_id',$asearch)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
            */
            $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->whereBetween('leadsfollowups.flfollwpdate',[$asdates,$aenddates])->where('leads.user_id',$asearch)->orWhere('leads.old_user_id',$asearch)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('followupstatus','Walked-In')->orderBy('id','DESC')->first();

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

                                
                        

                     return view('marketing.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','asdates','aenddates','bransdata','categorydata'));
        }




        elseif($categorydata = $request->categorysDatas)
        {

            //dd($categorydata);
            $cstartdate = $request->CStartDate;
            $cenddate = $request->CEnddate;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

               $findcourse = course::where('cat_id',$categorydata)->pluck('coursename');
              //dd($findcourse);



              /* foreach($findcourse as $courses)
               {
                    $getourses = $courses->coursename;

               }*/

        

                //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.leaddate','DESC')->get();

              $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',[$findcourse])->whereBetween('leadsfollowups.flfollwpdate',[$cstartdate,$cenddate])->where('leadsfollowups.followupstatus','Walked-In')->where('leads.user_id',$userId)->orWhere('leads.old_user_id',$userId)->orderBy('leads.leaddate','DESC')->get();
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('followupstatus','Walked-In')->orderBy('id','DESC')->first();

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
                        

                     return view('marketing.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
        }

    }   
    

     public function pendingleads()
     {

        $Cdates = date('Y-m-d');
         $userBranch = Auth::user()->branchs;
         $UserId = Auth::user()->id;

         $branchdata   = Branch::get();
         $cour = course::all();
         $sourcedata = Source::get();
         $ccatall = coursecategory::get();

      /*$leadsdata = leads::select("leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.id','DESC')->get();*/
      
      $leadsdata = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$UserId)->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


         $userdata = User::where('id',$UserId)->get();
    
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
        
       
        return view('marketing.leads.pendingleads',compact('leadsdata','folss','dates','userdata','Cdates','branchdata','cour','sourcedata','ccatall'));
    }


    public function filterPendingDatas(Request $request)
      {
         $Cdates = date('Y-m-d');
          $UserId = Auth::user()->id;

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
            //dd('called');

            $folss = followup::get();
            $userdata = User::where('id',$UserId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();
                /*$leadsdata = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leadsfollowups.id','DESC')->get();*/



    
            $namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->Where('leads.studentname', 'like', '%' .$namedatas. '%')->where('leads.user_id',$UserId)->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
            //dd($namesfinds);

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

             return view('marketing.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::where('id',$UserId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$UserId)->where('leads.phone',$mobdatas)->orWhere('leads.whatsappno',$mobdatas)->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

              return view('marketing.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($datesfor = $request->DateFor)
        {   
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if($datesfor == "Lead Date")
            {


                $folss = followup::get();
                $userdata = User::where('id',$UserId)->get();
                   $cour = course::all();
                    $sourcedata = Source::all();
                    $branchdata = Branch::get();
                    $ccatall = coursecategory::get();

                    $namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$UserId)->whereBetween('leads.leaddate',[$startdates,$enddats])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

                      return view('marketing.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
                }

            elseif($datesfor == "Followup Date")
            {


                $folss = followup::get();
                $userdata = User::where('id',$UserId)->get();
                   $cour = course::all();
                   $sourcedata = Source::all();
                   $branchdata = Branch::get();
                   $ccatall = coursecategory::get();

                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$UserId)->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate')->groupBy('leadsfollowups.leadsfrom')->get();

                    
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

                      return view('marketing.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','enddats','startdates'));
                }

            

            elseif($datesfor == "Next Followup Date")
            {


                $folss = followup::get();  
                 $cour = course::all();
                $userdata = User::where('id',$UserId)->get();
                $sourcedata = Source::all();
                $branchdata = Branch::get();
                $ccatall = coursecategory::get();

                    

                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$UserId)->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('marketing.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
                }

            
            }

        elseif($coursedatas = $request->coursedatas)
        {
            $folss = followup::get();
            $userdata = User::where('id',$UserId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();
               $cstartsdates = $request->cdatestat;
               $cendsdates = $request->cdateend;

            

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$UserId)->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.leaddate',[$cstartsdates,$cendsdates])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

              return view('marketing.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
            $userdata = User::where('id',$UserId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            
            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.coursesmode',$cmodes)->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->where('leads.user_id',$UserId)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

              return view('marketing.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($sources = $request->sourceSearch)
        {
            $starsdates = $request->sdatestat;
            $enssdates = $request->sdateend;

            $folss = followup::get();
            $userdata = User::where('id',$UserId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

        /*  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->orderBy('leads.id','DESC')->get();*/

        $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leads.leaddate',[$starsdates,$enssdates])->where('leads.user_id',$UserId)->where('leads.source',$sources)->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

              return view('marketing.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
        }



        elseif($fsearch = $request->FollowupsSearch)
        {
            $fdates = $request->fsdate;
            $fenddates = $request->fedate;

            $folss = followup::get();
            $userdata = User::where('id',$UserId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

    
            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$UserId)->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.leaddate',[$fdates,$fenddates])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('marketing.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','fdates','fenddates'));
        }



        elseif($asearch = $request->AssignedToSearch)
        {
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
            $userdata = User::where('id',$UserId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

                $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leadsfollowups.flfollwpdate',[$asdates,$aenddates])->where('leads.user_id',$UserId)->where('leads.user_id',$asearch)->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('marketing.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
        }


        elseif($bransdata = $request->branchSearchDatas)
        {
            $bstartdate = $request->BStartDate;
            $benddate = $request->BEnddate;

            $folss = followup::get();
            $userdata = User::where('id',$UserId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            //$namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();


            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$UserId)->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('marketing.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
        }


        elseif($categorydata = $request->categorysDatas)
        {

            //dd($categorydata);
            $cstartdate = $request->CStartDate;
            $cenddate = $request->CEnddate;

            $folss = followup::get();
            $userdata = User::where('id',$UserId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

               $findcourse = course::where('cat_id',$categorydata)->pluck('coursename');
              //dd($findcourse);

              /* foreach($findcourse as $courses)
               {
                    $getourses = $courses->coursename;

               }*/

        

            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.id','DESC')->get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$UserId)->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('marketing.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
        }

    }




    public function coldsleads(Request $request)
    {
        $userBranch = Auth::user()->branchs;

         $userId = Auth::user()->id;
          $leadsdata = leads::select("leads.*","users.name","leadsfollowups.leadsfrom","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->where('leads.user_id',$userId)->where('leadsfollowups.followupstatus','Cold Follow-ups')->latest()->get();


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

        //dd($dates);

        
        /*dd($da);*/
        
        return view('marketing.leads.coldleads',compact('leadsdata','folss','da','dates','cour','le','userdata'));

                //dd($leadsdata);
    }


   public function todaysfollowup()
    {
       
         $userBranch = Auth::user()->branchs;

        $userId = Auth::user()->id;

        $leadsdata = leads::select("leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate","leads.id as lid")->Join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leads.user_id',$userId)->where('leadsfollowups.fstatus',0)->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.id','DESC')->get();

         $userdata = User::where('id',$userId)->latest()->get();
       
        $folss = followup::get();

        foreach($leadsdata as $leas)
        {
            $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->orderBy('id','DESC')->first();

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

            $dates = date('Y-m-d');
               $userdata = User::where('id',$userId)->get();
                 $cour = course::all();
                 $sourcedata = Source::get();
                $branchdata = Branch::where('branchname',$userBranch)->get();
               
                $folss = followup::get();
                    $ccatall = coursecategory::get();
            return view('marketing.leads.todaysfollowup',compact('leadsdata','folss','dates','userdata','cour','sourcedata','branchdata','ccatall'));

    }

    public function filterdatastodayfollowup(Request $request)
    {
        $userBranch = Auth::user()->branchs;
        $userId = Auth::user()->id;

         $Cdates = date('Y-m-d');
         $dates = date('Y-m-d');

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
            //dd('called');

            $folss = followup::get();
            $userdata = User::where('id',$userId)->latest()->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();
                /*$leadsdata = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leadsfollowups.id','DESC')->get();*/

    
            $namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$userId)->Where('leads.studentname', 'like', '%' .$namedatas. '%')->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
            //dd($namesfinds);

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

              return view('marketing.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
                $userdata = User::where('id',$userId)->latest()->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$userId)->where('leads.phone',$mobdatas)->orWhere('leads.whatsappno',$mobdatas)->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.id','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

              return view('marketing.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($datesfor = $request->DateFor)
        {   
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if($datesfor == "Lead Date")
            {


                $folss = followup::get();
                $userdata = User::where('id',$userId)->latest()->get();
                   $cour = course::all();
                    $sourcedata = Source::all();
                   $branchdata = Branch::where('branchname',$userBranch)->get();
                    $ccatall = coursecategory::get();

                    $namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$userId)->whereBetween('leads.leaddate',[$startdates,$enddats])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

                      return view('marketing.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
                }

            elseif($datesfor == "Followup Date")
            {


                $folss = followup::get();
                $userdata = User::where('id',$userId)->latest()->get();
                   $cour = course::all();
                   $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                   $ccatall = coursecategory::get();

                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$userId)->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

                    
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

                      return view('marketing.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
                }

            

            elseif($datesfor == "Next Followup Date")
            {


                $folss = followup::get();  
                 $cour = course::all();
               $userdata = User::where('id',$userId)->latest()->get();
                $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
                $ccatall = coursecategory::get();

                    

                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$userId)->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('marketing.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
                }

            
            }

        elseif($coursedatas = $request->coursedatas)
        {
            $folss = followup::get();
            $userdata = User::where('id',$userId)->latest()->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();
               $cstartsdates = $request->cdatestat;
               $cendsdates = $request->cdateend;

            

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$userId)->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leadsfollowups.nxtfollowupdate',[$cstartsdates,$cendsdates])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

              return view('marketing.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
           $userdata = User::where('id',$userId)->latest()->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            
            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$userId)->where('leads.coursesmode',$cmodes)->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

              return view('marketing.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($sources = $request->sourceSearch)
        {
            $starsdates = $request->sdatestat;
            $enssdates = $request->sdateend;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->latest()->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

        /*  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->orderBy('leads.id','DESC')->get();*/

        $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$userId)->where('leads.source',$sources)->whereBetween('leadsfollowups.nxtfollowupdate',[$starsdates,$enssdates])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.id','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

              return view('marketing.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
        }



        elseif($fsearch = $request->FollowupsSearch)
        {
            $fdates = $request->fsdate;
            $fenddates = $request->fedate;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->latest()->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

    
            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$userId)->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.leaddate',[$fdates,$fenddates])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('marketing.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','fdates','fenddates'));
        }



        elseif($asearch = $request->AssignedToSearch)
        {
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
           $userdata = User::where('id',$userId)->latest()->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

                $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$asearch)->whereBetween('leadsfollowups.nxtfollowupdate',[$asdates,$aenddates])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('marketing.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
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

            //$namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();


            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leadsfollowups.nxtfollowupdate',[$bstartdate,$benddate])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('marketing.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
        }


        elseif($categorydata = $request->categorysDatas)
        {

            //dd($categorydata);
            $cstartdate = $request->CStartDate;
            $cenddate = $request->CEnddate;

            $folss = followup::get();
          $userdata = User::where('id',$userId)->latest()->get();
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

        

            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.id','DESC')->get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$userId)->whereIn('leads.course',$findcourse)->whereBetween('leadsfollowups.nxtfollowupdate',[$cstartdate,$cenddate])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('marketing.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
        }

    }


    
    public function filtersdatas(Request $request)
    {   
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
         
         $userId = Auth::user()->id;

        if($namedatas = $request->getstudentsnames)
        {
            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('leads.studentname', 'like', '%' .$namedatas. '%')->get();
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


             return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

              

            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.phone',$mobdatas)->where('leads.whatsappno',$mobdatas)->get();
            
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

             return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','mobdatas','namedatas','datesfor','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($datesfor = $request->DateFor)
        {   
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if($datesfor == "Lead Date")
            {


                $folss = followup::get();
                $userdata = User::where('id',$userId)->get();
                   $cour = course::all();
                    $sourcedata = Source::all();
                    $branchdata = Branch::get();
                    $ccatall = coursecategory::get();

                   


                    $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereBetween('leads.leaddate',[$startdates,$enddats])->where('leads.user_id',$userId)->orderBy('leads.leaddate','DESC')->get();
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

                     return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
                }

            elseif($datesfor == "Followup Date")
            {


                $folss = followup::get();
                $userdata = User::where('id',$userId)->get();
                   $cour = course::all();
                   $sourcedata = Source::all();
                   $branchdata = Branch::get();
                   $ccatall = coursecategory::get();

                    $namesfinds = leads::select("users.name","leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","leads.user_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leads.user_id',$userId)->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->orderBy('leads.leaddate','DESC')->get();
                    
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

                            

                     return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
                }

            

            elseif($datesfor == "Next Followup Date")
            {


                $folss = followup::get();  
                 $cour = course::all();
                $userdata = User::where('id',$userId)->get();
                $sourcedata = Source::all();
                $branchdata = Branch::get();
                $ccatall = coursecategory::get();

                    $namesfinds = leads::select("users.name","leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","leads.user_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leads.user_id',$userId)->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->orderBy('leads.leaddate','DESC')->get();
                    
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

                        
                     return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
                }
                
            elseif($datesfor == "Walked-In Date")
            {


                $folss = followup::get();  
                 $cour = course::all();
                $userdata = User::where('id',$userId)->get();
                $sourcedata = Source::all();
                $branchdata = Branch::get();
                $ccatall = coursecategory::get();

                  
                    
                 
                     
                     $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->where('leads.user_id',$userId)->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
                   //  dd($namesfinds);
                     
                     
                    
                    
                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->where('fstatus',0)->orderBy('id','DESC')->first();

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

                        
                     return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
                }

            
            }

        elseif($coursedatas = $request->coursedatas)
        {
            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

               $cstarstdates = $request->cdatestat;
               $cendatea = $request->cdateend;

            $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->where('leads.user_id',$userId)->whereBetween('leads.leaddate',[$cstarstdates,$cendatea])->orderBy('leads.leaddate','DESC')->get();
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

                        
             return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','coursedatas','cstarstdates','cendatea','namedatas','mobdatas','coursedatas','datesfor','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

              

            $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.coursesmode',$cmodes)->where('leads.user_id',$userId)->orderBy('leads.leaddate','DESC')->get();
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

             return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','cmodes','mobdatas','datesfor','coursedatas','namedatas','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($sources = $request->sourceSearch)
        {
            $starsdates = $request->sdatestat;
            $enssdates = $request->sdateend;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->where('leads.user_id',$userId)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->orderBy('leads.leaddate','DESC')->get();
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

                

             return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','sources','datesfor','namedatas','mobdatas','coursedatas','cmodes','starsdates','enssdates','fsearch','asearch','bransdata','categorydata'));
        }



        elseif($fsearch = $request->FollowupsSearch)
        {
            $fdates = $request->fsdate;
            $fenddates = $request->fedate;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();
              $userId = Auth::user()->id;

            /*$namesfinds = leads::select("users.name","leads.*")->join("users","users.id","=","leads.user_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leads.user_id',$userId)->orderBy()->get();*/
            
            $namesfinds = leads::select("users.name","leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","leads.user_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leads.user_id',$userId)->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.leaddate',[$fdates,$fenddates])->where('leads.user_id',$userId)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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


                     return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','fdates','fenddates','asearch','bransdata','categorydata'));
        }



        elseif($asearch = $request->AssignedToSearch)
        {
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
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

                                
                        

                     return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','asdates','aenddates','bransdata','categorydata'));
        }


        elseif($bransdata = $request->branchSearchDatas)
        {
            $bstartdate = $request->BStartDate;
            $benddate = $request->BEnddate;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->where('leads.user_id',$userId)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.leaddate','DESC')->get();
                    
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
                        
                        

                     return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','bstartdate','benddate','categorydata'));
        }


        elseif($categorydata = $request->categorysDatas)
        {

            //dd($categorydata);
            $cstartdate = $request->CStartDate;
            $cenddate = $request->CEnddate;

            $folss = followup::get();
            $userdata = User::where('id',$userId)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
               $ccatall = coursecategory::get();

               $findcourse = course::where('cat_id',$categorydata)->pluck('coursename');
              //dd($findcourse);

              /* foreach($findcourse as $courses)
               {
                    $getourses = $courses->coursename;

               }*/

        

            $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->where('leads.user_id',$userId)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.leaddate','DESC')->get();
                    
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
                        

                     return view('marketing.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
        }

    }
}
