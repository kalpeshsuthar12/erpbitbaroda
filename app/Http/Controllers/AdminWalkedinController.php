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
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeadImport;
use Auth;
use DB;

use Illuminate\Http\Request;

class AdminWalkedinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $cour = course::all();
       $sourcedata = Source::get();
           $userBranch = Auth::user()->branchs;

        $leadsdata = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Walked-In')->where('leadsfollowups.fstatus',0)->get();
         $userdata = User::where('branchs',$userBranch)->get();
       // dd($leadsdata);

        $folss = followup::get();

        foreach($leadsdata as $leas)
        {
            $da = leadsfollowups::where('leadsfrom','=',$leas->lid)->orderBy('id','DESC')->first();

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
        
       
        return view('admin.leads.walkedinleads',compact('leadsdata','folss','dates','userdata','cour','sourcedata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        

        return view('admin.leads.newwalkedinleads');
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


    public function pendingleads()
     {

        $Cdates = date('Y-m-d');
         $userBranch = Auth::user()->branchs;
         $UserId = Auth::user()->id;

      $leadsdata = leads::select("leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leads.user_id',$UserId)->whereDate('leadsfollowups.nxtfollowupdate', "<",$Cdates)->orderBy('leadsfollowups.id','DESC')->get();

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
        
       
         return view('admin.leads.pendingleads',compact('leadsdata','folss','dates','userdata','Cdates'));
    }

    public function coldleass()
    {
        $userBranch = Auth::user()->branchs;
        $userId = Auth::user()->id;

        $leadsdata = leads::select("leads.*","users.name","leadsfollowups.*","leads.id as lid")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->where('leadsfollowups.followupstatus','Cold Follow-ups')->where('leads.user_id',$userId)->get();
         $userdata = User::where('branchs',$userBranch)->get();
       // dd($leadsdata);

        $folss = followup::get();

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
        
       
        return view('admin.leads.coldleads',compact('leadsdata','folss','dates','userdata'));
    }

    public function todaysfollowup()
    {
       
         $userBranch = Auth::user()->branchs;

        $userId = Auth::user()->id;

        $leadsdata = leads::select("leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leads.user_id',$userId)->whereDate('leadsfollowups.nxtfollowupdate', '=', date('Y-m-d'))->get();

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
            return view('admin.leads.todaysfollowup',compact('leadsdata','folss','dates','userdata'));

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


             return view('admin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
        }

        elseif($mobdatas = $request->getMobilesno)
        {
            $folss = followup::get();
            $userdata = User::get();
               $cour = course::all();
               $sourcedata = Source::all();
               $branchdata = Branch::get();
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

             return view('admin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','mobdatas','namedatas','datesfor','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

                     return view('admin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

                            

                     return view('admin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

                        
                     return view('admin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','startdates','enddats','mobdatas','namedatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

                        
             return view('admin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','coursedatas','cstarstdates','cendatea','namedatas','mobdatas','coursedatas','datesfor','cmodes','sources','fsearch','asearch','bransdata','categorydata'));
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

             return view('admin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','cmodes','mobdatas','datesfor','coursedatas','namedatas','sources','fsearch','asearch','bransdata','categorydata'));
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

                

             return view('admin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','sources','datesfor','namedatas','mobdatas','coursedatas','cmodes','starsdates','enssdates','fsearch','asearch','bransdata','categorydata'));
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


                     return view('admin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','fdates','fenddates','asearch','bransdata','categorydata'));
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

                                
                        

                     return view('admin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','asdates','aenddates','bransdata','categorydata'));
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
                        
                        

                     return view('admin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','bstartdate','benddate','categorydata'));
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
                        

                     return view('admin.leads.filterswalkedinleads',compact('namesfinds','folss','userdata','cour','sourcedata','branchdata','ccatall','datesfor','namedatas','mobdatas','coursedatas','cmodes','sources','fsearch','asearch','bransdata','categorydata','cstartdate','cenddate'));
        }

    }
    
}

