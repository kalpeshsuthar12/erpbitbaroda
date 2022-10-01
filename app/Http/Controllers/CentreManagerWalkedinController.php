<?php

namespace App\Http\Controllers;

use App\leads;
use App\leadsfollowups;
use App\Source;
use App\User;
use App\Branch;
use App\course;
use App\followup;
use App\coursecategory;
use App\PastLeadsDatas;
use Carbon\Carbon;
use App\Notifications\CentreManagerLeadsTranserNotification;
use DB;
use Auth;
use Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeadImport;
use Illuminate\Http\Request;

class CentreManagerWalkedinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
        public function transfertoleads()
    {
        
           $userBranch = Auth::user()->branchs;
           $currentMonth = date('m');

        $leadsdata = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferbranch',$userBranch)->whereMonth('leads.transferdate',$currentMonth)->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();

           
       // dd($leadsdata);


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
        $cour = course::all();
       $sourcedata = Source::get();

        $folss = followup::get();

        $branchdata = Branch::where('branchname',$userBranch)->get();
        $userdata = User::where('branchs',$userBranch)->get();
        $ccatall = coursecategory::get();
       
        return view('centremanager.leads.transfertoleads',compact('leadsdata','folss','dates','userdata','cour','sourcedata','branchdata','ccatall'));


    }

    public function filtertransferleads(Request $request)
        {

            $userBranch = Auth::user()->branchs;
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

                             $Cdates = date('Y-m-d');
                            if($namedatas = $request->getstudentsnames)
                            {
                                
                                $folss = followup::get();
                                $userdata = User::where('branchs',$userBranch)->get();
                                   $cour = course::all();
                                   $sourcedata = Source::all();
                                   $branchdata = Branch::where('branchname',$userBranch)->get();
                                   $ccatall = coursecategory::get();
                                    /*$leadsdata = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leadsfollowups.id','DESC')->get();*/

                        
                                

                                $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->Where('leads.studentname', 'like', '%' .$namedatas. '%')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


                                
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

                                 return view('centremanager.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates'));
                            }

                            elseif($mobdatas = $request->getMobilesno)
                            {
                                $folss = followup::get();
                                $userdata = User::where('branchs',$userBranch)->get();
                                   $cour = course::all();
                                   $sourcedata = Source::all();
                                   $branchdata = Branch::where('branchname',$userBranch)->get();
                                   $ccatall = coursecategory::get();

                                /*$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.phone',$mobdatas)->where('leads.whatsappno',$mobdatas)->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();*/

                                $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.phone',$mobdatas)->where('leads.whatsappno',$mobdatas)->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();



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

                                 return view('centremanager.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates'));
                            }


                            elseif($datesfor = $request->DateFor)
                            {   
                                $startdates = $request->datestat;
                                $enddats = $request->dateend;

                                if($datesfor == "Transfer Date")
                                {


                                    $folss = followup::get();
                                    $userdata = User::where('branchs',$userBranch)->get();
                                       $cour = course::all();
                                        $sourcedata = Source::all();
                                        $branchdata = Branch::where('branchname',$userBranch)->get();
                                        $ccatall = coursecategory::get();

                                        //$namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leads.leaddate',[$startdates,$enddats])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

                                        $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->whereBetween('leads.transferdate',[$startdates,$enddats])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


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

                                         return view('centremanager.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','startdates','enddats'));
                                    }

                                elseif($datesfor == "Followup Date")
                                {


                                    $folss = followup::get();
                                    $userdata = User::where('branchs',$userBranch)->get();
                                       $cour = course::all();
                                       $sourcedata = Source::all();
                                       $branchdata = Branch::where('branchname',$userBranch)->get();
                                       $ccatall = coursecategory::get();

                                        

                                        $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();

                                        
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

                                         return view('centremanager.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','enddats','startdates'));
                                    }

                                

                                elseif($datesfor == "Next Followup Date")
                                {


                                    $folss = followup::get();  
                                     $cour = course::all();
                                    $userdata = User::where('branchs',$userBranch)->get();
                                    $sourcedata = Source::all();
                                    $branchdata = Branch::where('branchname',$userBranch)->get();
                                    $ccatall = coursecategory::get();

                                        

                                        //$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orWhere('leadsfollowups.followupstatus','Garbage')->orWhere('leadsfollowups.followupstatus','Walked In - Garbage')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

                                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


                                        
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

                                         return view('centremanager.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','enddats','startdates'));
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
                                   $cstartsdates = $request->cdatestat;
                                   $cendsdates = $request->cdateend;

                                

                                $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.transferdate',[$cstartsdates,$cendsdates])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


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

                                 return view('centremanager.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','cstartsdates','cendsdates'));
                            }

                            elseif($cmodes = $request->CourseModeSearch)
                            {
                                $folss = followup::get();
                                $userdata = User::where('branchs',$userBranch)->get();
                                   $cour = course::all();
                                   $sourcedata = Source::all();
                                   $branchdata = Branch::where('branchname',$userBranch)->get();
                                   $ccatall = coursecategory::get();

                                


                                $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.coursesmode',$cmodes)->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
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

                                 return view('centremanager.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates'));
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
        
        
                            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.source',$sources)->whereBetween('leads.transferdate',[$starsdates,$enssdates])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


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

                                 return view('centremanager.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','starsdates','enssdates'));
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

                                $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.transferdate',[$fdates,$fenddates])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
                                        
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

                                         return view('centremanager.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','fdates','fenddates'));
                            }



                            elseif($asearch = $request->AssignedToSearch)
                            {
                                //dd("test");
                                $asdates = $request->AstartDate;
                                $aenddates = $request->AEndDate;

                                $folss = followup::get();
                                $userdata = User::where('branchs',$userBranch)->get();
                                   $cour = course::all();
                                   $sourcedata = Source::all();
                                   $branchdata = Branch::where('branchname',$userBranch)->get();
                                   $ccatall = coursecategory::get();

                                    

                                    		
                                    		
                                    		 $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferfrom")->where('leads.transferto',$asearch)->whereBetween('leads.transferdate',[$asdates,$aenddates])->orderBy('leads.transferdate','DESC')->groupBy("leadsfollowups.leadsfrom")->get();
                                        
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

                                         return view('centremanager.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','asdates','aenddates'));
                            }


                            elseif($bransdata = $request->branchSearchDatas)
                            {
                                $bstartdate = $request->BStartDate;
                                $benddate = $request->BEnddate;
                                
                                //dd($bstartdate,$benddate);

                                $folss = followup::get();
                                $userdata = User::where('branchs',$userBranch)->get();
                                   $cour = course::all();
                                   $sourcedata = Source::all();
                                   $branchdata = Branch::where('branchname',$userBranch)->get();
                                   $ccatall = coursecategory::get();

                                //$namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();


                                //$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',array($bstartdate,$benddate))->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

                                $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferbranch',$bransdata)->whereBetween('leads.transferdate',array($bstartdate,$benddate))->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
                                        
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

                                         return view('centremanager.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','bstartdate','benddate'));
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

                            

                                //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.id','DESC')->get();

                                //$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

                                $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->whereIn('leads.course',$findcourse)->whereBetween('leads.transferdate',[$cstartdate,$cenddate])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
                                        
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

                                         return view('centremanager.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','cstartdate','cenddate'));
                            }

        }

    public function transferleads(Request $request)
    {   
        $leadsid = $request->getadmissions;
        //dd($leadsid);
        if(is_array($leadsid))
        {
            $leadid = implode(',',$leadsid);
        }
        else
        {
            $leadid = $leadsid;
        }
        $getusers = $request->getUser;
        
       // dd($getusers);
        $allusers = User::all();
        /*dd($leadsid);*/

        return view('centremanager.leads.transferleads',compact('leadid','getusers','allusers'));



    }

    public function storetransferleads(Request $request)
    {
      $ufrom = User::where('name',$request->transferfrom)->first();
      $dates = date('Y-m-d');

        $leadsid = $request->allleads;
        $tfrom = $request->transferfrom;
        $tto = $request->transferto;
        $tbranchs = $request->transferbranch;

         $lid = explode(',',$request->allleads);
         
         $oldleads = leads::whereIn('id',$lid)->get();
         
            	$trnsleads = leads::whereIn('id',$lid)->get();
         	foreach($trnsleads as $ntrnsafleads)
	         			{
	         			       // dd($trnsleads->id);
	         			    
	         			    $getid = leadsfollowups::where('leadsfrom',$ntrnsafleads->id)->update(array('fstatus' => 1));
	         			}
	         			
	         			
	         			foreach($trnsleads as $newtrnsafleads)
	         			{   
	         			    $todays = date('Y-m-d');
	         			        //leadsfollowups::where('leadsfrom',$newtrnsafleads->id)->int(array('followupstatus' => 'Transfer Lead', 'flfollwpdate' => $todays ));
	         			        
	         			        leadsfollowups::create([
                                    'leadsfrom' => $newtrnsafleads->id,
                                    'followupstatus' => "Transfer Lead",
                                    'flfollwpdate' => $todays,
                                    
                                    ]);

	         			}

	         			foreach($oldleads as $oleads)
						{

	         			
		         			$PastLeadsDatasmodel = new PastLeadsDatas();
								$PastLeadsDatas = $PastLeadsDatasmodel->create([
									'ptsource'=> $oleads->source,
									'oldid'=> $oleads->id,
									'ptleadsdates'=> $dates,
									'ptoldleadsdates'=> $oleads->leaddate,
									'ptuser_id'=> $oleads->user_id,
									'ptbranch'=> $oleads->branch,
									'pttobranchs'=> $oleads->tobranchs,
									'ptinstitutions'=> $oleads->institutions,
									'ptstudentname'=> $oleads->studentname,
									'ptemail'=> $oleads->email,
									'ptphone'=> $oleads->phone,
									'ptwhatsappno'=> $oleads->whatsappno,
									'ptcourse'=> $oleads->course,
									'ptcoursesmode'=> $oleads->coursesmode,
									'ptlvalue'=> $oleads->lvalue,
									'ptreffrom'=> $oleads->reffrom,
									'ptrefname'=> $oleads->refname,
									'ptrefassignto'=> $oleads->refassignto,
									'ptaddress'=> $oleads->address,
									'ptcity'=> $oleads->city,
									'ptstate'=> $oleads->state,
									'ptzipcode'=> $oleads->zipcode,
									'ptdescription'=> $oleads->description,
									'ptleadstatus'=> $oleads->leadstatus,
									'ptleadduration'=> $oleads->leadduration,
									'ptconversationstatus'=> $oleads->conversationstatus,
											
								]);

							}
      
         $update = leads::whereIn('id',$lid)->update(['user_id' => $tto,'transferfrom'=> $ufrom->id,'transferbranch' =>$tbranchs, 'transferto' => $tto, 'transferdate' => $dates]);
             

              $user = User::where('id',$tto)->first();
                                     //$af = User::where('name',$request->assigneto)->first();

                $user->notify(new CentreManagerLeadsTranserNotification(leads::findOrFail($leadsid)));

        return redirect('/centre-manger-transfer-leads-to-user')->with('success','Leads Transfer To The Another User!!');
        


    }
    public function index()
    {
       
        //$cour = course::all();
       //$sourcedata = Source::get();
           $userBranch = Auth::user()->branchs;
           $currentMonths = date('m');

       $leadsdata = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->where('leads.tobranchs',$userBranch)->whereMonth('leadsfollowups.flfollwpdate',$currentMonths)->orderBy('leadsfollowups.flfollwpdate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
         $userdata = User::where('branchs',$userBranch)->get();
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
        $branchdata = Branch::where('branchname',$userBranch)->get();

        $folss = followup::get();
        $userdata = User::where('branchs',$userBranch)->get();
        $sourcedata = Source::get();
        $ccatall = coursecategory::get();
        
       
        return view('centremanager.leads.walkedinleads',compact('leadsdata','folss','dates','userdata','cour','sourcedata','ccatall','branchdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        

        return view('centremanager.leads.newwalkedinleads');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $getleadid = $request->mobilenos;

        $getid = leads::where('phone',$getleadid)->first();

        $updatelead = leads::find($getid->id);
        //dd($updatelead);

        $updatelead->walkedinstatus = 1;
        $updatelead->description = $request->ldescript;
        $updatelead->save();
        //dd($updatelead);
       

        return redirect('/center-manager-walkedin-leads')->with('success','Walked Leads Created Successfully!!');

        //dd($getid);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($leadsmobilenos)
    {
        $dataleads = leads::where('phone',$leadsmobilenos)->get();

        return response()->json($dataleads);
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

    public function findallleads(Request $request)
    {
        $uysermobile = $request->allfoundmobilenoslead;
         $data= array();

       /*  $result = DB::table('leads')
                 ->join('users', 'users.id', '=', 'leads.user_id')
                 ->where('leads.branch', '=', 'BITSJ')
                 ->where('leads.phone', '=', $uysermobile)
                 ->select('leads.*','users.name')
                 ->get();*/

          $result =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.phone',$uysermobile)->orWhere('leads.whatsappno ',$uysermobile)->get();

            foreach($result as $resf)
            {

                $da = leadsfollowups::where('leadsfrom','=',$resf->id)->orderBy('id','DESC')->first();

                $resf->followupstatus ='';
                $resf->takenby ='';
                $resf->flfollwpdate ='';
                $resf->flremarsk = '';
                $resf->nxtfollowupdate = '';

                $row = array();
                $row[] = date('d-m-Y',strtotime($resf->leaddate));
                $row[] = $resf->studentname.'<br> <a href="javascript: void(0);" onclick="followupfunction('.$resf->id.')">View</a> &nbsp;&nbsp; <a href="/edit-center-manager-new-leads/'.$resf->id.'" style="color:green;">Edit</a>';
                $row[] = $resf->course;
                $row[] = $resf->coursesmode;
                $row[] = $resf->phone;
                $row[] = $resf->name;
                $row[] = '<center><button type="button" class="btn btn-primary waves-effect waves-light" onclick="followupfunction('.$resf->id.')"><i class="fa fa-tty"></i></button></center>';

                if($da)
                {
                    $row[] = $da->followupstatus;
                    $row[] = $da->takenby;
                    $row[] = date('d-m-Y',strtotime($da->flfollwpdate));
                    $row[] = $da->flremarsk;
                    $row[] = date('d-m-Y',strtotime($da->nxtfollowupdate));
                   
                   
                    
                }
                else
                {
                   $row[] = $resf->followupstatus ='';
                    $row[] = $resf->takenby ='';
                     $row[] = $resf->flfollwpdate ='';
                     $row[] = $resf->flremarsk = '';
                      $row[] = $resf->nxtfollowupdate = '';
                }
                $row[] = $resf->whatsappno;
                $row[] = $resf->email;
                $row[] = $resf->branch;
                $row[] = $resf->tobranchs;
                $row[] = $resf->source;
                $row[] = $resf->lvalue;
                
                if($resf->leadstatus == '1')
                {
                   $row[] ='<div class="badge bg-soft-success font-size-12"> Activate </div>';
                }

                else
                {   

                    $row[] = '<div class="badge bg-soft-warning font-size-12">De-Activate </div>';
                }

                $row[] = $resf->leadduration;

                if($resf->conversationstatus == '1')
                {
                     $row[] ='<div class="badge bg-soft-success font-size-12"> Converted </div>';
                }
                else
                {
                    $row[] = '<a href="/create-manager-students-admission-process/'.$resf->id.'" class="btn btn-success"> Admission</a>';
                }
                if($resf->conversationstatus == '1')
                {
                     $row[] ='<div class="badge bg-soft-success font-size-12"> Completed </div>';
                }
                else
                {
                    $row[] = '<div class="badge bg-soft-warning font-size-12">Pending</div>';
                }

                 $row[] = $resf->created_at;
               

            
                $data[] = $row;
        }   

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);



    }
    
    public function pastleads()
    {
            $userBranch = Auth::user()->branchs;

        $leadsdatas = PastLeadsDatas::join('users', 'users.id', '=', 'past_leads_datas.ptuser_id')->select('past_leads_datas.*','users.name','past_leads_datas.id as lid')->where('past_leads_datas.pttobranchs',$userBranch)->orderBy('past_leads_datas.ptleadsdates','DESC')->get();

             $cour = course::all();
         

        $folss = followup::get();
        $userdata = User::where('branchs',$userBranch)->get();
        $branchdata = Branch::where('branchname',$userBranch)->get();
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

        return view('centremanager.leads.pastleadsdatas',compact('leadsdatas','folss','cour','userdata','sourcedata','branchdata','ccatall'));
    }   


    public function filterpastleads(Request $request)
    {   

         $userBranch = Auth::user()->branchs;

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

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.pttobranchs',$userBranch)->Where('ptstudentname', 'like', '%' .$namedatas. '%')->get();
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


             return view('centremanager.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('ptphone',$mobdatas)->orwhere('ptwhatsappno',$mobdatas)->where('past_leads_datas.pttobranchs',$userBranch)->get();
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

             return view('centremanager.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','mobdatas','namedatas','datesfor','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

                   


                    $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->whereBetween('past_leads_datas.ptoldleadsdates',[$startdates,$enddats])->where('past_leads_datas.pttobranchs',$userBranch)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
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

                     return view('centremanager.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
                }

            elseif($datesfor == "Followup Date")
            {


                $folss = followup::get();
               $userdata = User::where('branchs',$userBranch)->get();
                   $cour = course::all();
                   $sourcedata = Source::all();
                   $branchdata = Branch::where('branchname',$userBranch)->get();
                   $ccatall = coursecategory::get();

                    $namesfinds = PastLeadsDatas::select("users.name","past_leads_datas.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","past_leads_datas.ptuser_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","past_leads_datas.oldid")->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->where('past_leads_datas.pttobranchs',$userBranch)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
                    
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

                            

                     return view('centremanager.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
                }

            

            elseif($datesfor == "Next Followup Date")
            {


                $folss = followup::get();  
                 $cour = course::all();
                $userdata = User::where('branchs',$userBranch)->get();
                $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
                $ccatall = coursecategory::get();

                    $namesfinds = PastLeadsDatas::select("users.name","past_leads_datas.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","past_leads_datas.ptuser_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","past_leads_datas.oldid")->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->where('past_leads_datas.pttobranchs',$userBranch)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
                    
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

                        
                     return view('centremanager.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",ptuser_id.ptcourse)')->whereBetween('past_leads_datas.ptoldleadsdates',[$cstarstdates,$cendatea])->where('past_leads_datas.pttobranchs',$userBranch)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
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

                        
             return view('centremanager.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','coursedatas','cstarstdates','cendatea','namedatas','mobdatas','coursedatas','datesfor','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
           $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptcoursesmode',$cmodes)->where('past_leads_datas.pttobranchs',$userBranch)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
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

             return view('centremanager.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','cmodes','mobdatas','datesfor','coursedatas','namedatas','sources','fsearch','asearch','bransdata','categorydata'));
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

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptsource',$sources)->where('past_leads_datas.pttobranchs',$userBranch)->whereBetween('past_leads_datas.ptoldleadsdates',[$starsdates,$enssdates])->orderBy('past_leads_datas.ptleadsdates','DESC')->get();
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

                

             return view('centremanager.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','sources','datesfor','namedatas','mobdatas','coursedatas','cmodes','starsdates','enssdates','fsearch','asearch','bransdata','categorydata'));
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

     $namesfinds = PastLeadsDatas::select("users.name","past_leads_datas.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","past_leads_datas.ptuser_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","past_leads_datas.oldid")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('past_leads_datas.ptoldleadsdates',[$fdates,$fenddates])->where('past_leads_datas.pttobranchs',$userBranch)->orderBy('past_leads_datas.ptleadsdates','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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


                     return view('centremanager.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','fdates','fenddates','asearch','bransdata','categorydata'));
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

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptuser_id',$asearch)->whereBetween('past_leads_datas.ptoldleadsdates',array($asdates,$aenddates))->where('past_leads_datas.pttobranchs',$userBranch)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
                     
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

                                
                        

                     return view('centremanager.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','asdates','aenddates','bransdata','categorydata'));
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

            $namesfinds =  PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptbranch',$bransdata)->whereBetween('past_leads_datas.ptleadsdates',[$bstartdate,$benddate])->where('past_leads_datas.pttobranchs',$userBranch)->orderBy('past_leads_datas.ptleadsdates','DESC')->get();
                    
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
                        
                        

                     return view('centremanager.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','bstartdate','benddate','categorydata'));
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

        

            $namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->whereIn('past_leads_datas.ptcourse',$findcourse)->whereBetween('past_leads_datas.ptleadsdates',[$cstartdate,$cenddate])->where('past_leads_datas.pttobranchs',$userBranch)->orderBy('past_leads_datas.ptleadsdates','DESC')->get();
                    
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
                        

                     return view('centremanager.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
        }

    }


    public function pendingleads()
     {

        $Cdates = date('Y-m-d');
         $userBranch = Auth::user()->branchs;
         $UserId = Auth::user()->id;

         $leadsdata = leads::select("users.name","leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->where('leads.user_id',$UserId)->orderBy('leadsfollowups.id','DESC')->get();
         
         /*  $leadsdata = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();*/


         $userdata = User::where('branchs',$userBranch)->get();
    
       
         $cour = course::all();
       $sourcedata = Source::get();

       $folss = followup::get();
         $branchdata   = Branch::get();
         $ccatall = coursecategory::get();

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
        
       
          return view('centremanager.leads.pendingleads',compact('leadsdata','folss','dates','userdata','cour','sourcedata','Cdates','dates','branchdata','ccatall'));
    }

    public function coldleass()
    {
        $userBranch = Auth::user()->branchs;
            $today = date('Y-m-d');
         //$userdata = User::where('branchs',$userBranch)->get();
            $currentMonth = date('m');
       

        
         $leadsdata = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.tobranchs',$userBranch)->where('leadsfollowups.fstatus',0)->where(function($query) {
			$query->where('leadsfollowups.followupstatus','Cold Follow-ups')
			->orWhere('leadsfollowups.followupstatus','Garbage')
			->orWhere('leadsfollowups.followupstatus','Walked In - Garbage');
})->whereMonth('leads.leaddate',$currentMonths)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
       

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

        $cour = course::all();
       $sourcedata = Source::get();
        $userdata = User::where('branchs',$userBranch)->get();
       $folss = followup::get();
         $branchdata   = Branch::get();
         $ccatall = coursecategory::get();
        
       
        return view('centremanager.leads.coldleads',compact('leadsdata','folss','dates','userdata','cour','sourcedata','branchdata','ccatall'));
    }
    
    
    public function filtercoldleads(Request $request)
    {
        $userBranch = Auth::user()->branchs;

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

         $Cdates = date('Y-m-d');
        if($namedatas = $request->getstudentsnames)
        {
            //dd('called');

            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();
                /*$leadsdata = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leadsfollowups.id','DESC')->get();*/

    
            $namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->Where('leads.studentname', 'like', '%' .$namedatas. '%')->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

             return view('centremanager.leads.filtercoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->where('leads.phone',$mobdatas)->where('leads.whatsappno',$mobdatas)->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

             return view('centremanager.leads.filtercoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

                    $namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

                     return view('centremanager.leads.filtercoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
                }

            elseif($datesfor == "Followup Date")
            {


                $folss = followup::get();
                $userdata = User::where('branchs',$userBranch)->get();
                   $cour = course::all();
                   $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                   $ccatall = coursecategory::get();

                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id as lid")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orWhere('leadsfollowups.followupstatus','Garbage')->orWhere('leadsfollowups.followupstatus','Walked In - Garbage')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

                    
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

                     return view('centremanager.leads.filtercoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','enddats','startdates'));
                }

            

            elseif($datesfor == "Next Followup Date")
            {


                $folss = followup::get();  
                 $cour = course::all();
                $userdata = User::where('branchs',$userBranch)->get();
                $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
                $ccatall = coursecategory::get();

                    

                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id as lid")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orWhere('leadsfollowups.followupstatus','Garbage')->orWhere('leadsfollowups.followupstatus','Walked In - Garbage')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                     return view('centremanager.leads.filtercoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','enddats','startdates'));
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
               $cstartsdates = $request->cdatestat;
               $cendsdates = $request->cdateend;

            

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leadsfollowups.flfollwpdate',[$cstartsdates,$cendsdates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

             return view('centremanager.leads.filtercoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            
            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Cold Follow-ups')->where('leads.coursesmode',$cmodes)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

             return view('centremanager.leads.filtercoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

        /*  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->orderBy('leads.id','DESC')->get();*/

        $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leadsfollowups.flfollwpdate',[$starsdates,$enssdates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

             return view('centremanager.leads.filtercoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
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

    
            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leadsfollowups.flfollwpdate',[$fdates,$fenddates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                     return view('centremanager.leads.filtercoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','fdates','fenddates'));
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

                $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$asearch)->where('leadsfollowups.fstatus',0)->whereBetween('leadsfollowups.flfollwpdate',[$asdates,$aenddates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                     return view('centremanager.leads.filtercoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
        }


        elseif($bransdata = $request->branchSearchDatas)
        {
            $bstartdate = $request->BStartDate;
            $benddate = $request->BEnddate;
            
            //dd($bstartdate,$benddate);

            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            //$namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.id','DESC')->get();


          
            
            /*	$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.tobranchs',$bransdata)->whereBetween('leads.leaddate',array($bstartdate,$benddate))->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();*/
            	
            		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.tobranchs',$bransdata)->where('leadsfollowups.fstatus',0)->where('leadsfollowups.fstatus',0)->where('leadsfollowups.followupstatus','Cold Follow-ups')->orWhere('leadsfollowups.followupstatus','Garbage')->orWhere('leadsfollowups.followupstatus','Walked In - Garbage')->whereBetween('leadsfollowups.flfollwpdate',[$bstartdate,$benddate])->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

                     return view('centremanager.leads.filtercoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
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

        

            

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->whereIn('leads.course',$findcourse)->whereBetween('leadsfollowups.flfollwpdate',[$cstartdate,$cenddate])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                     return view('centremanager.leads.filtercoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
        }

    }

    public function todaysfollowup()
    {
       
         $userBranch = Auth::user()->branchs;

        $userId = Auth::user()->id;
        $username = Auth::user()->name;

        $leadsdata = leads::select("leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate",'leads.id as lid')->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leads.user_id',$userId)->orWhere('leadsfollowups.followupsby',$username)->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->where('leadsfollowups.fstatus',0)->groupBy('leadsfollowups.leadsfrom')->get();

     

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
               $userdata = User::where('branchs',$userBranch)->get();
                 $cour = course::all();
                 $sourcedata = Source::get();
                $branchdata = Branch::where('branchname',$userBranch)->get();
               
                $folss = followup::get();
                    $ccatall = coursecategory::get();
            return view('centremanager.leads.todaysfollowup',compact('leadsdata','folss','dates','userdata','cour','sourcedata','dates','branchdata','ccatall'));

    }
    
    public function filtersdatas(Request $request)
    {   

        $userBranch = Auth::user()->branchs; 
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

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->Where('studentname', 'like', '%' .$namedatas. '%')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
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


             return view('centremanager.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->where('leads.phone',$mobdatas)->orWhere('leads.whatsappno',$mobdatas)->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
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

             return view('centremanager.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','mobdatas','namedatas','datesfor','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($datesfor = $request->DateFor)
        {   
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if($datesfor == "Walked-In Date")
            {


                $folss = followup::get();
                $userdata = User::where('branchs',$userBranch)->get();
                   $cour = course::all();
                    $sourcedata = Source::all();
                   $branchdata = Branch::where('branchname',$userBranch)->get();
                    $ccatall = coursecategory::get();

                   


                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*",'leads.id as lids')->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->where('leads.tobranchs',$userBranch)->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->groupBy('leadsfollowups.leadsfrom')->orderBy('leadsfollowups.flfollwpdate','DESC')->get();
                    
                   
                    

                    foreach($namesfinds as $leas)
                                            {
                                                $da = leadsfollowups::where('leadsfrom','=',$leas->lids)->where('followupstatus','Walked-In')->orderBy('id','DESC')->first();

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

                     return view('centremanager.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
            }



            
        }

        elseif($coursedatas = $request->coursedatas)
        {
            
            //dd($coursedatas);
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

               $cstarstdates = $request->cdatestat;
               $cendatea = $request->cdateend;

            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.leaddate',[$cstarstdates,$cendatea])->orderBy('leads.leaddate','DESC')->get();
            
           // $selectedcourse = explode(',',$coursedatas);
            //dd($selectedcourse);
            
            
                $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.leaddate',[$cstarstdates,$cendatea])->where('leadsfollowups.followupstatus','Walked-In')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();

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

                        
             return view('centremanager.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','coursedatas','cstarstdates','cendatea','namedatas','mobdatas','coursedatas','datesfor','cmodes','sources','fsearch','asearch','bransdata','categorydata')); 
            

           
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

              

            $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$uSerId)->where('leadsfollowups.followupstatus','Walked-In')->where('leads.coursesmode',$cmodes)->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
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

             return view('centremanager.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','cmodes','mobdatas','datesfor','coursedatas','namedatas','sources','fsearch','asearch','bransdata','categorydata'));
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

            $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->where('leadsfollowups.followupstatus','Walked-In')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
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

                

             return view('centremanager.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','sources','datesfor','namedatas','mobdatas','coursedatas','cmodes','starsdates','enssdates','fsearch','asearch','bransdata','categorydata'));
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

            $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leadsfollowups.flfollwpdate',[$starsdates,$enssdates])->where('leadsfollowups.followupstatus','Walked-In')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
                    
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


                     return view('centremanager.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','fdates','fenddates','asearch','bransdata','categorydata'));
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

                                
                        

                     return view('centremanager.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','asdates','aenddates','bransdata','categorydata'));
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

            //$namesfinds =  leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.leaddate','DESC')->get();

             $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.tobranchs',$bransdata)->whereBetween('leadsfollowups.flfollwpdate',[$bstartdate,$benddate])->where('leadsfollowups.followupstatus','Walked-In')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
                    
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
                        
                        

                     return view('centremanager.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','bstartdate','benddate','categorydata'));
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
              
              //$implodedcourses = implode(",",$findcourse);
              //dd($implodedcourses);

              /* foreach($findcourse as $courses)
               {
                    $getourses = $courses->coursename;

               }*/

        

                //  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.leaddate','DESC')->get();

              $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leadsfollowups.flfollwpdate',[$cstartdate,$cenddate])->where('leadsfollowups.followupstatus','Walked-In')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
                    
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
                        

                     return view('centremanager.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
        }

    }
    
    public function filterPendingDatas(Request $request)
     {
        $userBranch = Auth::user()->branchs;
         $Cdates = date('Y-m-d');

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
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();
                /*$leadsdata = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leadsfollowups.id','DESC')->get();*/

    
            $namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->Where('leads.studentname', 'like', '%' .$namedatas. '%')->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

             return view('centremanager.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.phone',$mobdatas)->orWhere('leads.whatsappno',$mobdatas)->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

              return view('centremanager.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

                    $namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leads.leaddate',[$startdates,$enddats])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

                      return view('centremanager.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
                }

            elseif($datesfor == "Followup Date")
            {


                $folss = followup::get();
                $userdata = User::where('branchs',$userBranch)->get();
                   $cour = course::all();
                   $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                   $ccatall = coursecategory::get();

                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate')->groupBy('leadsfollowups.leadsfrom')->get();

                    
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

                      return view('centremanager.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','enddats','startdates'));
                }

            

            elseif($datesfor == "Next Followup Date")
            {


                $folss = followup::get();  
                 $cour = course::all();
                $userdata = User::where('branchs',$userBranch)->get();
                $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
                $ccatall = coursecategory::get();

                    

                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->where('leadsfollowups.fstatus',0)->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('centremanager.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
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
               $cstartsdates = $request->cdatestat;
               $cendsdates = $request->cdateend;

            

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.leaddate',[$cstartsdates,$cendsdates])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

              return view('centremanager.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            
            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.coursesmode',$cmodes)->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

              return view('centremanager.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

        /*  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->orderBy('leads.id','DESC')->get();*/

        $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

              return view('centremanager.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
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

    
            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.leaddate',[$fdates,$fenddates])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('centremanager.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','fdates','fenddates'));
        }



        elseif($asearch = $request->AssignedToSearch)
        {
            //dd('test');
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

               /* $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$asearch)->whereBetween('leads.leaddate',[$asdates,$aenddates])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();*/
                
                	$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$asearch)->whereBetween('leadsfollowups.flfollwpdate',[$asdates,$aenddates])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('centremanager.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
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


            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.branch',$bransdata)->whereBetween('leadsfollowups.flfollwpdate',[$bstartdate,$benddate])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('centremanager.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
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
             
              $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$findcourse.'",leads.course)')->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('centremanager.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
        }

    }
    
     public function filterdatastodayfollowup(Request $request)
    {
        $userBranch = Auth::user()->branchs;

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
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();
                /*$leadsdata = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leadsfollowups.id','DESC')->get();*/

    
            $namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->Where('leads.studentname', 'like', '%' .$namedatas. '%')->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

              return view('centremanager.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.phone',$mobdatas)->orWhere('leads.whatsappno',$mobdatas)->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.id','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

              return view('centremanager.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

                    $namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leads.leaddate',[$startdates,$enddats])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

                      return view('centremanager.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
                }

            elseif($datesfor == "Followup Date")
            {


                $folss = followup::get();
                $userdata = User::where('branchs',$userBranch)->get();
                   $cour = course::all();
                   $sourcedata = Source::all();
                  $branchdata = Branch::where('branchname',$userBranch)->get();
                   $ccatall = coursecategory::get();

                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

                    
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

                      return view('centremanager.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
                }

            

            elseif($datesfor == "Next Followup Date")
            {


                $folss = followup::get();  
                 $cour = course::all();
                $userdata = User::where('branchs',$userBranch)->get();
                $sourcedata = Source::all();
               $branchdata = Branch::where('branchname',$userBranch)->get();
                $ccatall = coursecategory::get();

                    

                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('centremanager.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
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
               $cstartsdates = $request->cdatestat;
               $cendsdates = $request->cdateend;

            

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leadsfollowups.nxtfollowupdate',[$cstartsdates,$cendsdates])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

              return view('centremanager.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
            $userdata = User::where('branchs',$userBranch)->get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::where('branchname',$userBranch)->get();
               $ccatall = coursecategory::get();

            
            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.coursesmode',$cmodes)->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

              return view('centremanager.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

        /*  $namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->orderBy('leads.id','DESC')->get();*/

        $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leadsfollowups.nxtfollowupdate',[$starsdates,$enssdates])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.id','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

              return view('centremanager.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
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

    
            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.leaddate',[$fdates,$fenddates])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('centremanager.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','fdates','fenddates'));
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

                      return view('centremanager.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
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

                      return view('centremanager.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
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

        

            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.id','DESC')->get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leadsfollowups.nxtfollowupdate',[$cstartdate,$cenddate])->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
                    
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

                      return view('centremanager.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
        }

    }
}
