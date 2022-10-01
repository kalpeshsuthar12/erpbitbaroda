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
use App\PastLeadsDatas;
use Carbon\Carbon;
use DB;
use Auth;
use Notification;
use App\Notifications\LeadTransfer;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeadImport;
use Illuminate\Http\Request;

class LeadsController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */

        public function leadsajax($leadscourse,$leadvalues,$cmode)
		{
			
			if($cmode == 'Offline')
				{		
						$cours = explode(",",$leadscourse);
						
						 $leadscoursevalues = DB::table('courses')->whereIn('coursename', $cours)->get(['courseprice']);

						 $offlinespricesss = $leadscoursevalues->sum('courseprice');

						 return  response()->json($offlinespricesss);

						//dd($offlinespricesss);
						
				}


				else if($cmode == 'Online')
				{

						$cours = explode(",",$leadscourse);
						$leadscoursevalues = course::whereIn('coursename', $cours)->get(['courseonlineprice']);
						$courseonlineprices = $leadscoursevalues->sum('courseonlineprice');

						return  response()->json($courseonlineprices);

						//dd($courseofflineprices);

				}

				else
				{
					$cours = explode(",",$leadscourse);
						
						 $leadscoursevalues = DB::table('courses')->whereIn('coursename', $cours)->get(['courseprice']);

						 $offlinespricesss = $leadscoursevalues->sum('courseprice');

						 $leadscoursevalues = course::whereIn('coursename', $cours)->get(['courseonlineprice']);
						$courseofflineprices = $leadscoursevalues->sum('courseonlineprice');

						$totalprices = $offlinespricesss + $courseofflineprices;

						return  response()->json($totalprices);
				}
				


		}
		public function leadcoursemultiplevalue($lcmode,$lcourse)
		{
				if($lcmode == 'Offline')
				{		
						$cours = explode(",",$lcourse);
						
						 $leadscoursevalues = DB::table('courses')->whereIn('coursename', $cours)->get(['courseprice']);

						 $offlinespricesss = $leadscoursevalues->sum('courseprice');

						 return  response()->json($offlinespricesss);

						//dd($offlinespricesss);
						
				}

				elseif($lcmode == 'Online')
				{

						$cours = explode(",",$lcourse);
						$leadscoursevalues = course::whereIn('coursename', $cours)->get(['courseonlineprice']);
						$courseonlineprices = $leadscoursevalues->sum('courseonlineprice');

						return  response()->json($courseonlineprices);

						//dd($courseofflineprices);

				}

				else
				{
					$cours = explode(",",$lcourse);
						
						 $leadscoursevalues = DB::table('courses')->whereIn('coursename', $cours)->get(['courseprice']);

						 $offlinespricesss = $leadscoursevalues->sum('courseprice');

						 $leadscoursevalues = course::whereIn('coursename', $cours)->get(['courseonlineprice']);
						$courseofflineprices = $leadscoursevalues->sum('courseonlineprice');

						$totalprices = $offlinespricesss + $courseofflineprices;

						return  response()->json($totalprices);

				}

		}

	public function transferusersleads()
		{
			//	$cour = course::all();
		    //   $sourcedata = Source::get();
		           $userBranch = Auth::user()->branchs;
		           $currentMonths = date('m');

		        $leadsdata = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->groupBy('leadsfollowups.leadsfrom')->whereMonth('leads.transferdate',$currentMonths)->orderBy('leads.transferdate','DESC')->get();

		         //  $userdata = User::where('branchs',$userBranch)->get();
		       // dd($leadsdata);

		       // $folss = followup::get();

		        //$branchdata = Branch::where('branchname',$userBranch)->get();

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
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();
		        
		       
		        return view('superadmin.leads.transfertoleads',compact('leadsdata','folss','dates','userdata','cour','sourcedata','branchdata','ccatall'));
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

    	 $Cdates = date('Y-m-d');
    	if($namedatas = $request->getstudentsnames)
    	{
    		//dd('called');

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();
				/*$leadsdata = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leadsfollowups.id','DESC')->get();*/

    
    		

    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->Where('leads.studentname', 'like', '%' .$namedatas. '%')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


    		//dd($namesfinds);

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

			 return view('superadmin.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates'));
    	}

    	elseif($mobdatas = $request->getMobilesno)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		/*$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.phone',$mobdatas)->where('leads.whatsappno',$mobdatas)->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();*/

    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.phone',$mobdatas)->where('leads.whatsappno',$mobdatas)->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();



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

			 return view('superadmin.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates'));
    	}


    	elseif($datesfor = $request->DateFor)
    	{	
    		$startdates = $request->datestat;
    		$enddats = $request->dateend;

    		if($datesfor == "Transfer Date")
    		{


    			$folss = followup::get();
    			$userdata = User::get();
    			   $cour = course::all();
    			   	$sourcedata = Source::all();
    			   	$branchdata = Branch::get();
    			   	$ccatall = coursecategory::get();

		    		//$namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leads.leaddate',[$startdates,$enddats])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

		    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->whereBetween('leads.transferdate',[$startdates,$enddats])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


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

					 return view('superadmin.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','startdates','enddats'));
		    	}

		    elseif($datesfor == "Followup Date")
    		{


    			$folss = followup::get();
    			$userdata = User::get();
    			   $cour = course::all();
    			   $sourcedata = Source::all();
    			   $branchdata = Branch::get();
    			   $ccatall = coursecategory::get();

		    		//$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orWhere('leadsfollowups.followupstatus','Garbage')->orWhere('leadsfollowups.followupstatus','Walked In - Garbage')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

		    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();

		    		
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

					 return view('superadmin.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','enddats','startdates'));
		    	}

    		

    	    elseif($datesfor == "Next Followup Date")
    		{


    			$folss = followup::get();  
    			 $cour = course::all();
    			$userdata = User::get();
    			$sourcedata = Source::all();
    			$branchdata = Branch::get();
    			$ccatall = coursecategory::get();

		    		

		    		//$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orWhere('leadsfollowups.followupstatus','Garbage')->orWhere('leadsfollowups.followupstatus','Walked In - Garbage')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

    			$namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


		    		
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

					 return view('superadmin.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','enddats','startdates'));
		    	}

    		
    		}

    	elseif($coursedatas = $request->coursedatas)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();
    		   $cstartsdates = $request->cdatestat;
    		   $cendsdates = $request->cdateend;

    		

    		//$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.leaddate',[$cstartsdates,$cendsdates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->groupBy('leadsfollowups.leadsfrom')->get();

    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.transferdate',[$cstartsdates,$cendsdates])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


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

			 return view('superadmin.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','cstartsdates','cendsdates'));
    	}

    	elseif($cmodes = $request->CourseModeSearch)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		
    		//$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Cold Follow-ups')->where('leads.coursesmode',$cmodes)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

			 return view('superadmin.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates'));
    	}


    	elseif($sources = $request->sourceSearch)
    	{
    		$starsdates = $request->sdatestat;
    		$enssdates = $request->sdateend;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    	/*	$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->orderBy('leads.id','DESC')->get();*/

    	//$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

    	$namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.source',$sources)->whereBetween('leads.transferdate',[$starsdates,$enssdates])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();


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

			 return view('superadmin.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','starsdates','enssdates'));
    	}



    	elseif($fsearch = $request->FollowupsSearch)
    	{
    		$fdates = $request->fsdate;
    		$fenddates = $request->fedate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();
    
    		//$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.leaddate',[$fdates,$fenddates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leads.transferdate',[$fdates,$fenddates])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
		    		
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

					 return view('superadmin.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','fdates','fenddates'));
    	}



    	elseif($asearch = $request->AssignedToSearch)
    	{
    		$asdates = $request->AstartDate;
    		$aenddates = $request->AEndDate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    			/*$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$asearch)->whereBetween('leads.leaddate',[$asdates,$aenddates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();*/

    		//	$namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.user_id',$asearch)->whereBetween('leads.transferdate',[$asdates,$aenddates])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
		    		
		    		/*
		    		 $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferfrom")->where('leads.transferto',$asearch)->whereBetween('leads.transferdate',[$asdates,$aenddates])->orderBy('leads.transferdate','DESC')->groupBy("leadsfollowups.leadsfrom")->get();
		    		 */
		    		 
		    		  $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->where('leads.transferto',$asearch)->whereBetween('leads.transferdate',[$asdates,$aenddates])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
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

					 return view('superadmin.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','asdates','aenddates'));
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

					 return view('superadmin.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','bstartdate','benddate'));
    	}


    	elseif($categorydata = $request->categorysDatas)
    	{

    		//dd($categorydata);
    		$cstartdate = $request->CStartDate;
    		$cenddate = $request->CEnddate;

    		$folss = followup::get();
    		$userdata = User::get();
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

    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.transferto")->whereIn('leads.course',$findcourse)->whereBetween('leads.transferdate',[$cstartdate,$cenddate])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.transferdate','DESC')->get();
		    		
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

					 return view('superadmin.leads.filtertransferleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','dates','cstartdate','cenddate'));
    	}

	}
		public function transferleads(Request $request)
		{	
		$leadsid = $request->getadmissions;
		if(is_array($leadsid))
		{
			$leadid = implode(',',$leadsid);
		}
		else
		{
			$leadid = $leadsid;
		}
		$getusers = $request->getUser;
		$allusers = User::all();
		/*dd($leadsid);*/

		return view('superadmin.leads.transferleads',compact('leadid','getusers','allusers'));
		}

	public function storetransferleads(Request $request)
		{
			$ufrom = User::where('name',$request->transferfrom)->first();
			 $leadsid = $request->allleads;
	        $tfrom = $request->transferfrom;
	        $tto = $request->transferto;
	        $tbranchs = $request->transferbranch;
	        $dates = date('Y-m-d');

	         $lid = explode(',',$request->allleads);
	        // dd($lid);
	       //dd($tto);

	              //      $update = leads::whereIn('id',$lid)->update(['user_id' => $tto,'transferfrom'=> $tfrom,'transferbranch' =>$tbranchs]);

	         			/*$PastLeadsDatas = new*/

	         			$oldleads = leads::whereIn('id',$lid)->get();
	         			
	         			
	         			$trnsleads = leads::whereIn('id',$lid)->get();
	         		//	dd();
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

	           
	                    $lid = explode(',',$request->allleads);

	                    $uid = Auth::user()->id;

	                    $update = leads::whereIn('id',$lid)->update(['user_id' => $tto,'transferfrom'=> $uid,'transferbranch' =>$tbranchs,'transferto' => $tto, 'transferdate' => $dates]);

	                     $user = User::where('id',$tto)->first();
	                                 //$af = User::where('name',$request->assigneto)->first();

	                                        $user->notify(new LeadTransfer(leads::findOrFail($leadsid)));

	            
	            

	        return redirect('/transfer-leads-to-users')->with('success','Leads Transfer To The Another User!!');
	    }
	 
	 
	public function pastleads()
	{
	    $currentMonths = date('m');
	    
		$leadsdatas = PastLeadsDatas::join('users', 'users.id', '=', 'past_leads_datas.ptuser_id')->select('past_leads_datas.*','users.name','past_leads_datas.id as lid')->whereMonth('past_leads_datas.ptleadsdates',$currentMonths)->orderBy('past_leads_datas.ptleadsdates','DESC')
                ->get();

             $cour = course::all();
         $branchdata = Branch::get();

		$folss = followup::get();
		$userdata = User::get();
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

		return view('superadmin.leads.pastleadsdatas',compact('leadsdatas','folss','cour','userdata','sourcedata','branchdata','ccatall'));
	}    
	 
	public function index()
	{
	    
	    $currentmonths = date('m');
	    
	   	 $leadsdata = leads::join('users','users.id','=','leads.user_id')->select('leads.*','users.name')->whereMonth('leads.leaddate',$currentmonths)->orderBy('leads.leaddate','DESC')->get();
        
        $cour = course::all();
        $branchdata = Branch::get();
        $folss = followup::get();
		$userdata = User::get();
		$sourcedata = Source::get();
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

		$le = leads::all();

	
		return view('superadmin.leads.manage',compact('leadsdata','folss','da','dates','cour','le','userdata','sourcedata','branchdata','ccatall'));
		
	}
	
	public function allleadsdatas(leads $leads,course $course,followup $followup)
	{
	    
	    $currentMonth = date('m');
		 $leadsdata =DB::table('leads')
                ->join('users', 'users.id', '=', 'leads.user_id')
                ->whereMonth('leads.leaddate',$currentMonth)
                ->select('leads.*','users.name')
                ->orderBy('leads.leaddate','DESC')
                ->get();
               

         $cour = course::all();
         $branchdata = Branch::get();

		$folss = followup::get();
		$userdata = User::get();
		$sourcedata = Source::get();
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


		/*$da = leadsfollowups::where('leadsfrom','=',$leadsss)->get();*/

		$dates = date('Y-m-d');

		$le = leads::all();

		//dd($dates);

		
		/*dd($da);*/
		
		return view('superadmin.leads.alldatas',compact('leadsdata','folss','dates','cour','le','userdata','sourcedata','branchdata','ccatall'));
		
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
		$branchdata = Branch::get();
		$coursedata = course::get();
		$fol = followup::get();
		return view('superadmin.leads.create',compact('sourcedata','userdata','branchdata','coursedata','fol'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request,leads $leads)
	{
	  // dd($request->all());
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
			'leaddate'=> $request->leaddates,
			'user_id'=> $request->assignedto,
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
			'user_id'=> $userId,
			
		]);

		return redirect('/leads')->with('success','Leads Created Successfully!!');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\leads  $leads
	 * @return \Illuminate\Http\Response
	 */
	public function show($id,leads $leads)
	{	
		$followupleads = leads::find($id);
		return view('superadmin.followupsleads.create',compact('followupleads'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\leads  $leads
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id,leads $leads,Source $source,User $user,Branch $branch,course $course)
	{
		
		$sourcedata = Source::all();
		$userdata = User::all();
		$branchdata = Branch::all();
		$coursedata = course::all();
		$leadsda = leads::find($id);
		$selectedcourse = explode(',', $leadsda->course);
		$fol = followup::get();
	   // dd($selectedcourse);

		return view('superadmin.leads.edit',compact('sourcedata','userdata','branchdata','coursedata','leadsda','selectedcourse','fol'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\leads  $leads
	 * @return \Illuminate\Http\Response
	 */
	public function update($id,Request $request, leads $leads)
	{
		//dd($request->all());

		$data =  $request->leadcourse;

	    $userId = Auth::user()->id;
	
		

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
		 $updated->user_id =  $userId;
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
		 $updated->user_id = $userId;
		 $updated->save();

		return redirect('/all-leads-datas')->with('success','Leads Updated Successfully!!');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\leads  $leads
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id,leads $leads)
	{
		$deleets = leads::find($id);
		$deleets->delete();
		return redirect('/leads')->with('success','leads deleted successfully!!');
	}

	public function import()
	{
		return view('superadmin.leads.import_leads');
	}

	public function importleads(Request $request)
	{
		$execeldat = Excel::import(new LeadImport,request()->file('file'));
		return redirect('/leads')->with('success','leads Imported successfully!!');
	}

	 public function activate($id,leads $leads)
	{
		$testi = leads::find($id);
		$testi->leadstatus = '1';
		$testi->save();

		return redirect('/leads')->with('success','Leads Has Been Activated');
		
	}
	public function deactivate($id,leads $leads)
	{
		$testi = leads::find($id);
		$testi->leadstatus = '0';
		$testi->save();

		return redirect('/leads')->with('success','leads Has Been De-Activated');
		
	}

  /*  public function pdf($leadcourseName,course $course)

	{
		 $latest = DB::select("SELECT brocheurefiles FROM courses WHERE coursename = '".$leadcourseName."'");
		return response()->json($latest);
	}*/

		public function matachunmatach($whatsaappno,leads $leads)
		{   
		
			
			//dd($ema);

			if($em = leads::where('whatsappno','=',$whatsaappno)->first())
			{
					$msg = "User Already Exists";
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

		if($phon = leads::where('phone','=',$phones)->first())
		{
				$mesg = "User  Already Exists";



				return response()->json($mesg);
		}

		else
		{
			$mesg = " ";
			 return response()->json($mesg);
		}
	}

	public function getajaxview($f)
	{
		$viewdata = leads::find($f);

		return response()->json($viewdata);
	}
	
	 public function walkedinleads()
    {
        $currentMonths = date('m');
        
    /*	$leadsdata = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->whereMonth('leadsfollowups.flfollwpdate',$currentMonths)->orderBy('leadsfollowups.flfollwpdate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();*/
    	
    	
    	$leadsdata = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->whereMonth('leadsfollowups.flfollwpdate',$currentMonths)->orderBy('leadsfollowups.flfollwpdate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
    	
         $userdata = User::get();
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
        $userdata = User::get();
        $sourcedata = Source::get();
        $ccatall = coursecategory::get();
        
       
        return view('superadmin.leads.walkedinleads',compact('leadsdata','folss','dates','userdata','cour','sourcedata','ccatall','branchdata'));
    }
  	
  	public function filterwalkedinleads(Request $request)
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
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->Where('studentname', 'like', '%' .$namedatas. '%')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
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


             return view('superadmin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

              

            $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->where('leads.phone',$mobdatas)->orWhere('leads.whatsappno',$mobdatas)->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
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

             return view('superadmin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','mobdatas','namedatas','datesfor','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($datesfor = $request->DateFor)
        {   
            $startdates = $request->datestat;
            $enddats = $request->dateend;

            if($datesfor == "Walked-In Date")
            {


                $folss = followup::get();
                $userdata = User::get();
                   $cour = course::all();
                    $sourcedata = Source::all();
                   $branchdata = Branch::get();
                    $ccatall = coursecategory::get();

                   


                    $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*",'leads.id as lids')->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();

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

                     return view('superadmin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
            }



            
        }

        elseif($coursedatas = $request->coursedatas)
        {
            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

               $cstarstdates = $request->cdatestat;
               $cendatea = $request->cdateend;

            //$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.leaddate',[$cstarstdates,$cendatea])->orderBy('leads.leaddate','DESC')->get();

            $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leads.leaddate',[$cstarstdates,$cendatea])->where('leadsfollowups.followupstatus','Walked-In')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();

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

                        
             return view('superadmin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','coursedatas','cstarstdates','cendatea','namedatas','mobdatas','coursedatas','datesfor','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($cmodes = $request->CourseModeSearch)
        {
            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
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

             return view('superadmin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','cmodes','mobdatas','datesfor','coursedatas','namedatas','sources','fsearch','asearch','bransdata','categorydata'));
        }


        elseif($sources = $request->sourceSearch)
        {
            $starsdates = $request->sdatestat;
            $enssdates = $request->sdateend;

            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.source',$sourcessearch)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->where('leadsfollowups.followupstatus','Walked-In')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
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

                

             return view('superadmin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','sources','datesfor','namedatas','mobdatas','coursedatas','cmodes','starsdates','enssdates','fsearch','asearch','bransdata','categorydata'));
        }



        elseif($fsearch = $request->FollowupsSearch)
        {
            $fdates = $request->fsdate;
            $fenddates = $request->fedate;

            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

            $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leadsfollowups.flfollwpdate',[$fdates,$fenddates])->where('leadsfollowups.followupstatus','Walked-In')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
                    
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


                     return view('superadmin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','fdates','fenddates','asearch','bransdata','categorydata'));
        }



        elseif($asearch = $request->AssignedToSearch)
        {
            $asdates = $request->AstartDate;
            $aenddates = $request->AEndDate;

            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

                $usersname = User::find($asearch);
           
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
                                
                        

                     return view('superadmin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','asdates','aenddates','bransdata','categorydata'));
        }


        elseif($bransdata = $request->branchSearchDatas)
        {
            $bstartdate = $request->BStartDate;
            $benddate = $request->BEnddate;

            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
              $branchdata = Branch::get();
               $ccatall = coursecategory::get();

           

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
                        
                        

                     return view('superadmin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','bstartdate','benddate','categorydata'));
        }


        elseif($categorydata = $request->categorysDatas)
        {

            //dd($categorydata);
            $cstartdate = $request->CStartDate;
            $cenddate = $request->CEnddate;

            $folss = followup::get();
            $userdata = User::get();
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

              $namesfinds = leads::select("leads.*","users.name","leads.id as lid","leadsfollowups.*")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',[$findcourse])->whereBetween('leadsfollowups.flfollwpdate',[$cstartdate,$cenddate])->where('leadsfollowups.followupstatus','Walked-In')->groupBy('leadsfollowups.leadsfrom')->orderBy('leads.leaddate','DESC')->get();
                    
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
                        

                     return view('superadmin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
        }

    }


	public function coldsleads(Request $request)
	{
        $userBranch = Auth::user()->branchs;
        $userId = Auth::user()->id;
        $today = date('Y-m-d'); 
        $currentMonths = date('m'); 
       
        
        $leadsdata = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->where(function($query) {
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
         $branchdata = Branch::get();

		$folss = followup::get();
		$userdata = User::get();
		$sourcedata = Source::get();
		$ccatall = coursecategory::get();

        
       
        return view('superadmin.leads.coldleads',compact('leadsdata','dates','userdata','cour','branchdata','folss','sourcedata','ccatall'));
    }
	

	public function filtercoldleads(Request $request)
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

    	 $Cdates = date('Y-m-d');
    	if($namedatas = $request->getstudentsnames)
    	{
    		//dd('called');

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();
				/*$leadsdata = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leadsfollowups.id','DESC')->get();*/

    
    		$namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate",'leads.id as lid')->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->Where('leads.studentname', 'like', '%' .$namedatas. '%')->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


    		//dd($namesfinds);

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

			 return view('superadmin.leads.filterscoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}

    	elseif($mobdatas = $request->getMobilesno)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate",'leads.id as lid')->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->where('leads.phone',$mobdatas)->where('leads.whatsappno',$mobdatas)->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

			 return view('superadmin.leads.filterscoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}


    	elseif($datesfor = $request->DateFor)
    	{	
    		$startdates = $request->datestat;
    		$enddats = $request->dateend;

    		if($datesfor == "Lead Date")
    		{


    			$folss = followup::get();
    			$userdata = User::get();
    			   $cour = course::all();
    			   	$sourcedata = Source::all();
    			   	$branchdata = Branch::get();
    			   	$ccatall = coursecategory::get();

		    		$namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate",'leads.id as lid')->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

					 return view('superadmin.leads.filterscoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
		    	}

		    elseif($datesfor == "Followup Date")
    		{


    			$folss = followup::get();
    			$userdata = User::get();
    			   $cour = course::all();
    			   $sourcedata = Source::all();
    			   $branchdata = Branch::get();
    			   $ccatall = coursecategory::get();

		    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate",'leads.id as lid')->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orWhere('leadsfollowups.followupstatus','Garbage')->orWhere('leadsfollowups.followupstatus','Walked In - Garbage')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();

		    		
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

					 return view('superadmin.leads.filterscoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','enddats','startdates'));
		    	}

    		

    	    elseif($datesfor == "Next Followup Date")
    		{


    			$folss = followup::get();  
    			 $cour = course::all();
    			$userdata = User::get();
    			$sourcedata = Source::all();
    			$branchdata = Branch::get();
    			$ccatall = coursecategory::get();

		    		

		    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate",'leads.id as lid')->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orWhere('leadsfollowups.followupstatus','Garbage')->orWhere('leadsfollowups.followupstatus','Walked In - Garbage')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
		    		
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

					 return view('superadmin.leads.filterscoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','enddats','startdates'));
		    	}

    		
    		}

    	elseif($coursedatas = $request->coursedatas)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();
    		   $cstartsdates = $request->cdatestat;
    		   $cendsdates = $request->cdateend;

    		

    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id",'leads.id as lid')->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->whereRaw('FIND_IN_SET("'.$coursedatas.'",leads.course)')->whereBetween('leadsfollowups.flfollwpdate',[$cstartsdates,$cendsdates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

			 return view('superadmin.leads.filterscoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
    	}

    	elseif($cmodes = $request->CourseModeSearch)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		
    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id",'leads.id as lid')->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->where('leadsfollowups.followupstatus','Cold Follow-ups')->where('leads.coursesmode',$cmodes)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

			 return view('superadmin.leads.filterscoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}


    	elseif($sources = $request->sourceSearch)
    	{
    		$starsdates = $request->sdatestat;
    		$enssdates = $request->sdateend;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    	/*	$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->orderBy('leads.id','DESC')->get();*/

    	$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate",'leads.id as lid')->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->where('leads.source',$sources)->whereBetween('leadsfollowups.flfollwpdate',[$starsdates,$enssdates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();


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

			 return view('superadmin.leads.filterscoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
    	}



    	elseif($fsearch = $request->FollowupsSearch)
    	{
    		$fdates = $request->fsdate;
    		$fenddates = $request->fedate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    
    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('leadsfollowups.flfollwpdate',[$fdates,$fenddates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
		    		
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

					 return view('superadmin.leads.filterscoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','fdates','fenddates'));
    	}



    	elseif($asearch = $request->AssignedToSearch)
    	{
    		$asdates = $request->AstartDate;
    		$aenddates = $request->AEndDate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    			$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate",'leads.id as lid')->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$asearch)->where('leadsfollowups.fstatus',0)->whereBetween('leadsfollowups.flfollwpdate',[$asdates,$aenddates])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
		    		
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

					 return view('superadmin.leads.filterscoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
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

            	/*$namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->where('leads.tobranchs',$bransdata)->whereBetween('leads.leaddate',array($bstartdate,$benddate))->where('leadsfollowups.followupstatus','Cold Follow-ups')->orWhere('leadsfollowups.followupstatus','Garbage')->orWhere('leadsfollowups.followupstatus','Walked In - Garbage')->orderBy('leadsfollowups.id','DESC')->groupBy('leadsfollowups.leadsfrom')->get();*/
		    		
		    		
		    		/* $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.tobranchs',$bransdata)->where('leadsfollowups.fstatus',0)->where('leadsfollowups.followupstatus','Cold Follow-ups')->orWhere('leadsfollowups.followupstatus','Garbage')->orWhere('leadsfollowups.followupstatus','Walked In - Garbage')->whereBetween('leads.leaddate',[$bstartdate,$benddate])->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();*/
		    		
		    		 
        $namesfinds = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.tobranchs',$bransdata)->where('leadsfollowups.fstatus',0)->where('leadsfollowups.followupstatus','Cold Follow-ups')->orWhere('leadsfollowups.followupstatus','Garbage')->orWhere('leadsfollowups.followupstatus','Walked In - Garbage')->whereBetween('leadsfollowups.flfollwpdate',[$bstartdate,$benddate])->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
		    		foreach($namesfinds as $leas)
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

					 return view('superadmin.leads.filterscoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
    	}


    	elseif($categorydata = $request->categorysDatas)
    	{

    		//dd($categorydata);
    		$cstartdate = $request->CStartDate;
    		$cenddate = $request->CEnddate;

    		$folss = followup::get();
    		$userdata = User::get();
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

    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate",lid)->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.fstatus',0)->whereIn('leads.course',$findcourse)->whereBetween('leadsfollowups.flfollwpdate',[$cstartdate,$cenddate])->where('leadsfollowups.followupstatus','Cold Follow-ups')->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
		    		
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

					 return view('superadmin.leads.filterscoldleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
    	}

	}

    public function todaysfollowup()
    {
       $dates = date('Y-m-d');
         $userBranch = Auth::user()->branchs;

        $userId = Auth::user()->id;

        $leadsdata = leads::select("leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate","users.name")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->orderBy('leadsfollowups.id','DESC')->get();

         $userdata = User::get();
       	 $cour = course::all();
         $sourcedata = Source::get();
         $branchdata = Branch::get();
         $folss = followup::get();
         $ccatall = coursecategory::get();
 
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
            return view('superadmin.leads.todaysfollowup',compact('leadsdata','folss','userdata','cour','sourcedata','branchdata','ccatall','dates'));

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

      
      $leadsdata = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();



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
                $leas->followupsby = '';

            if($da){
                $leas->followupstatus = $da->followupstatus;
                $leas->takenby = $da->takenby;
                $leas->flfollwpdate = $da->flfollwpdate;
                $leas->flremarsk = $da->flremarsk;
                $leas->nxtfollowupdate = $da->nxtfollowupdate;
                $leas->followupsby = $da->followupsby;

         
            }
        }

        $dates = date('Y-m-d');
        
       
        return view('superadmin.leads.pendingleads',compact('leadsdata','folss','dates','userdata','Cdates','branchdata','cour','sourcedata','ccatall'));
    }
    
   
   public function coursewithbrocheure(Request $request)
    {
    	$lid = $request->leadid;

    	$username =  Auth::user()->name;
    	$UserMobile =  Auth::user()->mobileno;
    	$UserBranch =  Auth::user()->baddress;
    	$UserMaplocation =  Auth::user()->bglink;
    	/*dd($lid);*/
    	$getcourse = leads::find($lid);

    	$explodecourse = explode(",",$getcourse->course);

    	$coursedetails = course::whereIn('coursename',$explodecourse)->get();

    	

    	foreach($coursedetails as $res)
        {
            $row = array();
            $row[] = $res->coursename;
            $row[] = "<embed src='http://newerp.erpbitbaroda.com/public/brocheure/".$res->brocheurefiles ."' height='200' width='200'>
            				<br>
            	           <a href='https://wa.me/+91".$getcourse->whatsappno."/?text=Hello,%0a%0a*Greetings From BIT!!!*%0a%0aI%20Am%20".$username."%20From%20BIT(Baroda%20Institute%20of%20Technology)!%20I%20would%20be%20happy%20to%20get%20you%20the%20complete%20details%20about%20It%20*".$res->coursename."*.%0a%0a*Baroda%20Institute%20Of%20Technology*%0a".$UserBranch."%0a%0a*Course*: %20".$res->coursename."%0a%0a*View Brocheure*:%20http://newerp.erpbitbaroda.com/public/brocheure/".$res->brocheurefiles."%0a%0a".$username.":-%20*".$UserMobile."*%0a%0a*Google%20Map%20Location*:-%20".$UserMaplocation."%0a*For%20More%20Details%20you%20Can%20Visit%20Our%20Website*:-%20www.bitonlinelearn.com%0a*Facebook%20Page*:-%20https://www.facebook.com/BITBARODA/' target='_blank'>Send Whatsapp</a>
            				";
            $row[] = "<a href=".$res->website." target='_blank'>View</a>";
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);
    	

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

    	if($namedatas = $request->getstudentsnames)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->Where('studentname', 'like', '%' .$namedatas. '%')->get();
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


			 return view('superadmin.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}

    	elseif($mobdatas = $request->getMobilesno)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		  

    		$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('phone',$mobdatas)->get();
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

			 return view('superadmin.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','mobdatas','namedatas','datesfor','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}


    	elseif($datesfor = $request->DateFor)
    	{	
    		$startdates = $request->datestat;
    		$enddats = $request->dateend;

    		if($datesfor == "Lead Date")
    		{


    			$folss = followup::get();
    			$userdata = User::get();
    			   $cour = course::all();
    			   	$sourcedata = Source::all();
    			   	$branchdata = Branch::get();
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

					 return view('superadmin.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
		    	}

		    elseif($datesfor == "Followup Date")
    		{


    			$folss = followup::get();
    			$userdata = User::get();
    			   $cour = course::all();
    			   $sourcedata = Source::all();
    			   $branchdata = Branch::get();
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

							

					 return view('superadmin.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
		    	}

    		

    	    elseif($datesfor == "Next Followup Date")
    		{


    			$folss = followup::get();  
    			 $cour = course::all();
    			$userdata = User::get();
    			$sourcedata = Source::all();
    			$branchdata = Branch::get();
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

						
					 return view('superadmin.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
		    	}

    		
    		}

    	elseif($coursedatas = $request->coursedatas)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

						
			 return view('superadmin.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','coursedatas','cstarstdates','cendatea','namedatas','mobdatas','coursedatas','datesfor','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}

    	elseif($cmodes = $request->CourseModeSearch)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

			 return view('superadmin.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','cmodes','mobdatas','datesfor','coursedatas','namedatas','sources','fsearch','asearch','bransdata','categorydata'));
    	}


    	elseif($sources = $request->sourceSearch)
    	{
    		$starsdates = $request->sdatestat;
    		$enssdates = $request->sdateend;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

				

			 return view('superadmin.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','sources','datesfor','namedatas','mobdatas','coursedatas','cmodes','starsdates','enssdates','fsearch','asearch','bransdata','categorydata'));
    	}



    	elseif($fsearch = $request->FollowupsSearch)
    	{
    		$fdates = $request->fsdate;
    		$fenddates = $request->fedate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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


					 return view('superadmin.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','fdates','fenddates','asearch','bransdata','categorydata'));
    	}



    	elseif($asearch = $request->AssignedToSearch)
    	{
    		$asdates = $request->AstartDate;
    		$aenddates = $request->AEndDate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.user_id',$asearch)->whereBetween('leads.leaddate',array($asdates,$aenddates))->orderBy('leads.leaddate','DESC')->get();
		    		 
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

								
						

					 return view('superadmin.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','asdates','aenddates','bransdata','categorydata'));
    	}


    	elseif($bransdata = $request->branchSearchDatas)
    	{
    		$bstartdate = $request->BStartDate;
    		$benddate = $request->BEnddate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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
						
						

					 return view('superadmin.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','bstartdate','benddate','categorydata'));
    	}


    	elseif($categorydata = $request->categorysDatas)
    	{

    		//dd($categorydata);
    		$cstartdate = $request->CStartDate;
    		$cenddate = $request->CEnddate;

    		$folss = followup::get();
    		$userdata = User::get();
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

    	

    		$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->orderBy('leads.leaddate','DESC')->get();
		    		
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
						

					 return view('superadmin.leads.filtersleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
    	}

	}
	
	public function filterpastleads(Request $request)
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

    	if($namedatas = $request->getstudentsnames)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		$namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->Where('ptstudentname', 'like', '%' .$namedatas. '%')->get();
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


			 return view('superadmin.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}

    	elseif($mobdatas = $request->getMobilesno)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		  

    		$namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('ptphone',$mobdatas)->orwhere('ptwhatsappno',$mobdatas)->get();
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

			 return view('superadmin.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','mobdatas','namedatas','datesfor','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}


    	elseif($datesfor = $request->DateFor)
    	{	
    		$startdates = $request->datestat;
    		$enddats = $request->dateend;

    		if($datesfor == "Lead Date")
    		{


    			$folss = followup::get();
    			$userdata = User::get();
    			   $cour = course::all();
    			   	$sourcedata = Source::all();
    			   	$branchdata = Branch::get();
    			   	$ccatall = coursecategory::get();

    			   


		    		$namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->whereBetween('past_leads_datas.ptoldleadsdates',[$startdates,$enddats])->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
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

					 return view('superadmin.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
		    	}

		    elseif($datesfor == "Followup Date")
    		{


    			$folss = followup::get();
    			$userdata = User::get();
    			   $cour = course::all();
    			   $sourcedata = Source::all();
    			   $branchdata = Branch::get();
    			   $ccatall = coursecategory::get();

		    		$namesfinds = PastLeadsDatas::select("users.name","past_leads_datas.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","past_leads_datas.ptuser_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","past_leads_datas.oldid")->whereBetween('leadsfollowups.flfollwpdate',[$startdates,$enddats])->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
		    		
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

							

					 return view('superadmin.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
		    	}

    		

    	    elseif($datesfor == "Next Followup Date")
    		{


    			$folss = followup::get();  
    			 $cour = course::all();
    			$userdata = User::get();
    			$sourcedata = Source::all();
    			$branchdata = Branch::get();
    			$ccatall = coursecategory::get();

		    		$namesfinds = PastLeadsDatas::select("users.name","past_leads_datas.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","past_leads_datas.ptuser_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","past_leads_datas.oldid")->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
		    		
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

						
					 return view('superadmin.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
		    	}

    		
    		}

    	elseif($coursedatas = $request->coursedatas)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		   $cstarstdates = $request->cdatestat;
    		   $cendatea = $request->cdateend;

    		$namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->whereRaw('FIND_IN_SET("'.$coursedatas.'",ptuser_id.ptcourse)')->whereBetween('past_leads_datas.ptoldleadsdates',[$cstarstdates,$cendatea])->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
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

						
			 return view('superadmin.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','coursedatas','cstarstdates','cendatea','namedatas','mobdatas','coursedatas','datesfor','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}

    	elseif($cmodes = $request->CourseModeSearch)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		  

    		$namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptcoursesmode',$cmodes)->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
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

			 return view('superadmin.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','cmodes','mobdatas','datesfor','coursedatas','namedatas','sources','fsearch','asearch','bransdata','categorydata'));
    	}


    	elseif($sources = $request->sourceSearch)
    	{
    		$starsdates = $request->sdatestat;
    		$enssdates = $request->sdateend;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		$namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptsource',$sources)->whereBetween('past_leads_datas.ptoldleadsdates',[$starsdates,$enssdates])->orderBy('past_leads_datas.ptleadsdates','DESC')->get();
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

				

			 return view('superadmin.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','sources','datesfor','namedatas','mobdatas','coursedatas','cmodes','starsdates','enssdates','fsearch','asearch','bransdata','categorydata'));
    	}



    	elseif($fsearch = $request->FollowupsSearch)
    	{
    		$fdates = $request->fsdate;
    		$fenddates = $request->fedate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

     $namesfinds = PastLeadsDatas::select("users.name","past_leads_datas.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("users","users.id","=","past_leads_datas.ptuser_id")->join("leadsfollowups","leadsfollowups.leadsfrom","=","past_leads_datas.oldid")->where('leadsfollowups.followupstatus',$fsearch)->whereBetween('past_leads_datas.ptoldleadsdates',[$fdates,$fenddates])->orderBy('past_leads_datas.ptleadsdates','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
		    		
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


					 return view('superadmin.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','fdates','fenddates','asearch','bransdata','categorydata'));
    	}



    	elseif($asearch = $request->AssignedToSearch)
    	{
    		$asdates = $request->AstartDate;
    		$aenddates = $request->AEndDate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		$namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptuser_id',$asearch)->whereBetween('past_leads_datas.ptoldleadsdates',array($asdates,$aenddates))->orderBy('past_leads_datas.ptoldleadsdates','DESC')->get();
		    		 
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

								
						

					 return view('superadmin.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','asdates','aenddates','bransdata','categorydata'));
    	}


    	elseif($bransdata = $request->branchSearchDatas)
    	{
    		$bstartdate = $request->BStartDate;
    		$benddate = $request->BEnddate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		$namesfinds =  PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->where('past_leads_datas.ptbranch',$bransdata)->whereBetween('past_leads_datas.ptleadsdates',[$bstartdate,$benddate])->orderBy('past_leads_datas.ptleadsdates','DESC')->get();
		    		
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
						
						

					 return view('superadmin.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','bstartdate','benddate','categorydata'));
    	}


    	elseif($categorydata = $request->categorysDatas)
    	{

    		//dd($categorydata);
    		$cstartdate = $request->CStartDate;
    		$cenddate = $request->CEnddate;

    		$folss = followup::get();
    		$userdata = User::get();
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

    	

    		$namesfinds = PastLeadsDatas::select("past_leads_datas.*","users.name")->join("users","users.id","=","past_leads_datas.ptuser_id")->whereIn('past_leads_datas.ptcourse',$findcourse)->whereBetween('past_leads_datas.ptleadsdates',[$cstartdate,$cenddate])->orderBy('past_leads_datas.ptleadsdates','DESC')->get();
		    		
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
						

					 return view('superadmin.leads.filterpastleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
    	}

	}




	public function filterPendingDatas(Request $request)
    {
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
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();
				

    
    		$namesfinds =  leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->Where('leads.studentname',$namedatas)->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
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

			 return view('superadmin.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}

    	elseif($mobdatas = $request->getMobilesno)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leads.phone',$mobdatas)->orWhere('leads.whatsappno',$mobdatas)->orderBy('leads.leaddate','DESC')->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->groupBy('leadsfollowups.leadsfrom')->get();
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

			  return view('superadmin.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}


    	elseif($datesfor = $request->DateFor)
    	{	
    		$startdates = $request->datestat;
    		$enddats = $request->dateend;

    		if($datesfor == "Lead Date")
    		{


    			$folss = followup::get();
    			$userdata = User::get();
    			   $cour = course::all();
    			   	$sourcedata = Source::all();
    			   	$branchdata = Branch::get();
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

					  return view('superadmin.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
		    	}

		    elseif($datesfor == "Followup Date")
    		{


    			$folss = followup::get();
    			$userdata = User::get();
    			   $cour = course::all();
    			   $sourcedata = Source::all();
    			   $branchdata = Branch::get();
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

					  return view('superadmin.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','enddats','startdates'));
		    	}

    		

    	    elseif($datesfor == "Next Followup Date")
    		{


    			$folss = followup::get();  
    			 $cour = course::all();
    			$userdata = User::get();
    			$sourcedata = Source::all();
    			$branchdata = Branch::get();
    			$ccatall = coursecategory::get();

		    		

		    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leadsfollowups.nxtfollowupdate',[$startdates,$enddats])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
		    		
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

					  return view('superadmin.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
		    	}

    		
    		}

    	elseif($coursedatas = $request->coursedatas)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

			  return view('superadmin.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
    	}

    	elseif($cmodes = $request->CourseModeSearch)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

			  return view('superadmin.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}


    	elseif($sources = $request->sourceSearch)
    	{
    		$starsdates = $request->sdatestat;
    		$enssdates = $request->sdateend;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    	/*	$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->orderBy('leads.id','DESC')->get();*/

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

			  return view('superadmin.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
    	}



    	elseif($fsearch = $request->FollowupsSearch)
    	{
    		$fdates = $request->fsdate;
    		$fenddates = $request->fedate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

					  return view('superadmin.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','fdates','fenddates'));
    	}



    	elseif($asearch = $request->AssignedToSearch)
    	{
    		$asdates = $request->AstartDate;
    		$aenddates = $request->AEndDate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

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

					  return view('superadmin.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
    	}


    	elseif($bransdata = $request->branchSearchDatas)
    	{
    		$bstartdate = $request->BStartDate;
    		$benddate = $request->BEnddate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

					  return view('superadmin.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
    	}


    	elseif($categorydata = $request->categorysDatas)
    	{

    		//dd($categorydata);
    		$cstartdate = $request->CStartDate;
    		$cenddate = $request->CEnddate;

    		$folss = followup::get();
    		$userdata = User::get();
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

    		$namesfinds = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereIn('leads.course',$findcourse)->whereBetween('leads.leaddate',[$cstartdate,$cenddate])->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->where('leadsfollowups.fstatus',0)->orderBy('leads.leaddate','DESC')->groupBy('leadsfollowups.leadsfrom')->get();
		    		
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

					  return view('superadmin.leads.filterpendingleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
    	}

	}


    public function filterdatastodayfollowup(Request $request)
    {
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
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

			  return view('superadmin.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}

    	elseif($mobdatas = $request->getMobilesno)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

			  return view('superadmin.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}


    	elseif($datesfor = $request->DateFor)
    	{	
    		$startdates = $request->datestat;
    		$enddats = $request->dateend;

    		if($datesfor == "Lead Date")
    		{


    			$folss = followup::get();
    			$userdata = User::get();
    			   $cour = course::all();
    			   	$sourcedata = Source::all();
    			   	$branchdata = Branch::get();
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

					  return view('superadmin.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
		    	}

		    elseif($datesfor == "Followup Date")
    		{


    			$folss = followup::get();
    			$userdata = User::get();
    			   $cour = course::all();
    			   $sourcedata = Source::all();
    			   $branchdata = Branch::get();
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

					  return view('superadmin.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
		    	}

    		

    	    elseif($datesfor == "Next Followup Date")
    		{


    			$folss = followup::get();  
    			 $cour = course::all();
    			$userdata = User::get();
    			$sourcedata = Source::all();
    			$branchdata = Branch::get();
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

					  return view('superadmin.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','startdates','enddats'));
		    	}

    		
    		}

    	elseif($coursedatas = $request->coursedatas)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

			  return view('superadmin.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartsdates','cendsdates'));
    	}

    	elseif($cmodes = $request->CourseModeSearch)
    	{
    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

			  return view('superadmin.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
    	}


    	elseif($sources = $request->sourceSearch)
    	{
    		$starsdates = $request->sdatestat;
    		$enssdates = $request->sdateend;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
    		   $ccatall = coursecategory::get();

    	/*	$namesfinds = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.source',$sources)->whereBetween('leads.leaddate',[$starsdates,$enssdates])->orderBy('leads.id','DESC')->get();*/

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

			  return view('superadmin.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','starsdates','enssdates'));
    	}



    	elseif($fsearch = $request->FollowupsSearch)
    	{
    		$fdates = $request->fsdate;
    		$fenddates = $request->fedate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

					  return view('superadmin.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','fdates','fenddates'));
    	}



    	elseif($asearch = $request->AssignedToSearch)
    	{
    		$asdates = $request->AstartDate;
    		$aenddates = $request->AEndDate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

					  return view('superadmin.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','asdates','aenddates'));
    	}


    	elseif($bransdata = $request->branchSearchDatas)
    	{
    		$bstartdate = $request->BStartDate;
    		$benddate = $request->BEnddate;

    		$folss = followup::get();
    		$userdata = User::get();
    		   $cour = course::all();
    		   $sourcedata = Source::all();
    		   $branchdata = Branch::get();
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

					  return view('superadmin.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','bstartdate','benddate'));
    	}


    	elseif($categorydata = $request->categorysDatas)
    	{

    		//dd($categorydata);
    		$cstartdate = $request->CStartDate;
    		$cenddate = $request->CEnddate;

    		$folss = followup::get();
    		$userdata = User::get();
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

					  return view('superadmin.leads.filtertodaysfollowups',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','Cdates','dates','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
    	}

	}

	
}
