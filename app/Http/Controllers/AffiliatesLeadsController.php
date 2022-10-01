<?php

namespace App\Http\Controllers;

use App\affiliatesLeads;
use App\affiliatesleadsfollowups;
use App\AffiliatesCategory; 
use App\affiliatestrainingcategory; 
use App\followup;
use App\Source;
use App\User;
use App\Branch;
use Auth;
use Illuminate\Http\Request;

class AffiliatesLeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $afileadsdata = affiliatesLeads::select('affiliates_leads.*','users.name','users.mobileno')->Join('users','users.id','=','affiliates_leads.auserids')->orderBy('affiliates_leads.id','DESC')->get();

       // dd($ad);

        foreach($afileadsdata as $leas)
        {
            $da = affiliatesleadsfollowups::where('afleadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

            $leas->affollowupstatus ='';
            $leas->affollowupdates ='';
                $leas->affollowupremarks = '';
                $leas->afnextsfollowupdates = '';
            
             if($da){
                $leas->affollowupstatus = $da->affollowupstatus;
                $leas->affollowupdates = $da->affollowupdates;
                $leas->affollowupremarks = $da->affollowupremarks;
                $leas->afnextsfollowupdates = $da->afnextsfollowupdates;
                
            }
        }


        $folss = followup::get();
        $userdata = User::get();
        $sourcedata = Source::get(); 
        $categorydata = AffiliatesCategory::get();
      //  dd($afileadsdata);
        return view('superadmin.affiliatesleads.manage',compact('afileadsdata','folss','userdata','categorydata','sourcedata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        $acategorys = AffiliatesCategory::all();
        $atrcateg = affiliatestrainingcategory::all();
        $soru = Source::all();
        $uses = User::all();
        $brnas = Branch::all();

        return view('superadmin.affiliatesleads.create',compact('acategorys','atrcateg','soru','uses','brnas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $userId = Auth::user()->id;

         $affiliatesLeadsmodel = new affiliatesLeads();
            $affiliatesLeads = $affiliatesLeadsmodel->create([
                    'asourcenames'=> $request->afsources,
                    'afleadsdates'=> $request->aldates,
                    'afassignto'=> $request->aassignedto,
                    'afrombranch'=> $request->afrombranchs,
                    'atobranch'=> $request->atobranchs,
                    'affiliatesnames'=> $request->afnames,
                    'acompanyname'=> $request->acompanyname,
                    'aemails'=> $request->afemails,
                    'aphone'=> $request->afphone,
                    'awhatsappno'=> $request->afwhatsapp,
                    'atrainingcategory'=> $request->aftraining,
                    'affiliatescategorys'=> $request->afcategories,
                    'aaddress'=> $request->afaddress,
                    'acity'=> $request->afcity,
                    'astate'=> $request->afstate,
                    'adescriptions'=> $request->afdescriptions,
                    'auserids'=> $userId,

                ]);

           return redirect('/affiliates-leads')->with('success','Leads Created Successfully!!');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\affiliatesLeads  $affiliatesLeads
     * @return \Illuminate\Http\Response
     */
    public function show(affiliatesLeads $affiliatesLeads)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\affiliatesLeads  $affiliatesLeads
     * @return \Illuminate\Http\Response
     */
    public function edit($id,affiliatesLeads $affiliatesLeads)
    {
        $edits = affiliatesLeads::find($id);
        $acategorys = AffiliatesCategory::all();
        $atrcateg = affiliatestrainingcategory::all();
        $soru = Source::all();
        $uses = User::all();
        $brnas = Branch::all();

        return view('superadmin.affiliatesleads.edit',compact('edits','acategorys','atrcateg','soru','uses','brnas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\affiliatesLeads  $affiliatesLeads
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, affiliatesLeads $affiliatesLeads)
    {
        $userId = Auth::user()->id;
        $updates = affiliatesLeads::find($id);
        $updates->asourcenames = $request->afsources;
        $updates->afleadsdates = $request->aldates;
        $updates->afassignto = $request->aassignedto;
        $updates->afrombranch = $request->afrombranchs;
        $updates->atobranch = $request->atobranchs;
        $updates->affiliatesnames = $request->afnames;
        $updates->acompanyname = $request->acompanyname;
        $updates->aemails = $request->afemails;
        $updates->aphone = $request->afphone;
        $updates->awhatsappno = $request->afwhatsapp;
        $updates->atrainingcategory = $request->aftraining;
        $updates->affiliatescategorys =  $request->afcategories;
        $updates->aaddress = $request->afaddress;
        $updates->acity = $request->afcity;
        $updates->astate = $request->afstate;
        $updates->adescriptions = $request->afdescriptions;
        $updates->auserids = $userId;
        $updates->save();

         return redirect('/affiliates-leads')->with('success','Leads Updated Successfully!!');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\affiliatesLeads  $affiliatesLeads
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,affiliatesLeads $affiliatesLeads)
    {
            $deles = affiliatesLeads::find($id);
            $deles->delete();

            $del = affiliatesleadsfollowups::where('afleadsfrom',$id)->get();
            $del->each()->delete();

            return redirect('/affiliates-leads')->with('success','Leads Deleted Successfully!!');
    }


    public function ajaxprofile($g)
    {
        $viewdata = affiliatesLeads::find($g);
       // dd($viewdata);

        return response()->json($viewdata);
    }

    public function viewregisteraffiliatesdata($f)
    {
        $uservie = User::find($f);
       // dd($viewdata);

        return response()->json($uservie);
    }


    public function registeredaffiliates()
    {
       $leadsdatas = affiliatesLeads::select('affiliates_leads.*','users.name','users.id as uid','users.mobileno','users.usercategory')->Join('users','users.affileadsid','=','affiliates_leads.id')->get();

        //dd($leadsdatas->uid);

        foreach($leadsdatas as $leas)
        {
            $da = affiliatesleadsfollowups::where('afleadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

            $leas->affollowupstatus ='';
            $leas->affollowupdates ='';
                $leas->affollowupremarks = '';
                $leas->afnextsfollowupdates = '';
            
             if($da){
                $leas->affollowupstatus = $da->affollowupstatus;
                $leas->affollowupdates = $da->affollowupdates;
                $leas->affollowupremarks = $da->affollowupremarks;
                $leas->afnextsfollowupdates = $da->afnextsfollowupdates;
                
            }
        }
         $folss = followup::get();
        return view('superadmin.affiliatesleads.registeraffiliates',compact('leadsdatas','folss'));

        //dd($leadsdatas);

    }


    public function ajaxstoresdata(Request $request)
    {

        $datas1  = $request->affollowupstatus; 
        $datas3  = $request->affollowupdates; 
        $datas4  = $request->affollowupremarks; 
        $datas5  = $request->afnextsfollowupdates; 
        $datas6  = $request->affollupsby; 
        $datas7  = $request->afleadsfrom; 

        $getid = affiliatesleadsfollowups::where('afleadsfrom',$datas7)->update(array('afstatus' => 1));
        

        $data = $request->all();
        $result = affiliatesleadsfollowups::insert($data);
        
         return response()->json(
            [
                'success' => true,
                'message' => 'Followups Done successfully'
            ]
        );

    }

    public function getfullaffiliatesdetails(Request $request)
    {
            $le = $request->affiliatesid;
            $data= array();
            $result = affiliatesleadsfollowups::where('afleadsfrom','=',$le)->orderBy('id','DESC')->get();
            //dd($result);
            foreach($result as $res)
            {
                $row = array();
                $row[] = $res->affollowupstatus;
                $row[] = date('d-m-Y',strtotime($res->affollowupdates));
                $row[] = $res->affollowupremarks;
                if($res->afnextsfollowupdates != NULL)
                {

                    $row[] = date('d-m-Y',strtotime($res->afnextsfollowupdates));
                }
                else
                {
                    $row[] = "";
                }
                $row[] = $res->affollupsby;
                $data[] = $row;
            }

             $response = array(
                "recordsTotal"    => count($data),  
                "recordsFiltered" => count($data), 
                "data"            => $data   
             );

             echo json_encode($response);

    }


    public function affiliatespendingsleads()
    {
        $Cdates = date('Y-m-d');
        $userBranch = Auth::user()->branchs;
         $UserId = Auth::user()->id;
         $branchdata   = Branch::get();
          $sourcedata = Source::get();

          $leadsdata = affiliatesLeads::select("affiliates_leads.*","users.name","affiliatesleadsfollowups.*","affiliates_leads.id as afiid")->join("affiliatesleadsfollowups","affiliatesleadsfollowups.afleadsfrom","=","affiliates_leads.id")->join("users","users.id","=","affiliates_leads.auserids")->whereDate('affiliatesleadsfollowups.afnextsfollowupdates', "<",$Cdates)->where('affiliatesleadsfollowups.afstatus',0)->orderBy('affiliatesleadsfollowups.id','DESC')->get();

          foreach($leadsdata as $leas)
        {
            $da = affiliatesleadsfollowups::where('afleadsfrom','=',$leas->afiid)->orderBy('id','DESC')->first();

            $leas->affollowupstatus ='';
            $leas->affollowupdates ='';
                $leas->affollowupremarks = '';
                $leas->afnextsfollowupdates = '';
            
             if($da){
                $leas->affollowupstatus = $da->affollowupstatus;
                $leas->affollowupdates = $da->affollowupdates;
                $leas->affollowupremarks = $da->affollowupremarks;
                $leas->afnextsfollowupdates = $da->afnextsfollowupdates;
                
            }
        }


        $folss = followup::get();


            return view('superadmin.affiliatesleads.pendingleads',compact('leadsdata','folss'));
          //dd($leadsdata);

    }

    public function affiliatescoldsleads()
    {
        $userBranch = Auth::user()->branchs;
        $userId = Auth::user()->id;

        $leadsdata = affiliatesLeads::select("affiliates_leads.*","users.name","affiliatesleadsfollowups.*","affiliates_leads.id as afiid")->join("affiliatesleadsfollowups","affiliatesleadsfollowups.afleadsfrom","=","affiliates_leads.id")->join("users","users.id","=","affiliates_leads.auserids")->where('affiliatesleadsfollowups.affollowupstatus','Cold Follow-ups')->orderBy('affiliatesleadsfollowups.id','DESC')->get();
         $userdata = User::get();
       // dd($leadsdata);

        $folss = followup::get();

        foreach($leadsdata as $leas)
        {
            $da = affiliatesleadsfollowups::where('afleadsfrom','=',$leas->afiid)->orderBy('id','DESC')->first();

            $leas->affollowupstatus ='';
            $leas->affollowupdates ='';
                $leas->affollowupremarks = '';
                $leas->afnextsfollowupdates = '';
            
                if($da){
                $leas->affollowupstatus = $da->affollowupstatus;
                $leas->affollowupdates = $da->affollowupdates;
                $leas->affollowupremarks = $da->affollowupremarks;
                $leas->afnextsfollowupdates = $da->afnextsfollowupdates;
                
                }

        }
                 $dates = date('Y-m-d');
       
        return view('superadmin.affiliatesleads.coldleads',compact('leadsdata','folss','dates','userdata'));
    }

    public function affiliatestodaysfollowups()
    {
        $userBranch = Auth::user()->branchs;

        $userId = Auth::user()->id;

        $leadsdata = affiliatesLeads::select("affiliates_leads.*","users.name","affiliatesleadsfollowups.*","affiliates_leads.id as afiid")->join("affiliatesleadsfollowups","affiliatesleadsfollowups.afleadsfrom","=","affiliates_leads.id")->join("users","users.id","=","affiliates_leads.auserids")->where('affiliatesleadsfollowups.afstatus',0)->whereDate('affiliatesleadsfollowups.afnextsfollowupdates', '=', date('Y-m-d'))->orderBy('affiliatesleadsfollowups.id','DESC')->get();

         $userdata = User::get();
         $sourcedata = Source::get();
         $branchdata = Branch::get();
         $folss = followup::get();
 
         foreach($leadsdata as $leas)
        {
            $da = affiliatesleadsfollowups::where('afleadsfrom','=',$leas->afiid)->orderBy('id','DESC')->first();

            $leas->affollowupstatus ='';
            $leas->affollowupdates ='';
                $leas->affollowupremarks = '';
                $leas->afnextsfollowupdates = '';
            
                if($da){
                $leas->affollowupstatus = $da->affollowupstatus;
                $leas->affollowupdates = $da->affollowupdates;
                $leas->affollowupremarks = $da->affollowupremarks;
                $leas->afnextsfollowupdates = $da->afnextsfollowupdates;
                
                }

        }
                 $dates = date('Y-m-d');
       
            return view('superadmin.affiliatesleads.todaysfollowups',compact('leadsdata','folss','dates','userdata'));
         
    }

    public function filtersaffiliatesleads(Request $request)
    {
        $filtersbys = $request->filterbys;
        $dfilter = $request->datefilters;
        $mfilters = $request->mobilefilters;
        $sfilter = $request->sourcefilters;
        $afilter = $request->assignedtofilters;
        $ffilter = $request->followupstatusfilters;
        $cfilter = $request->categoryfilters;

            if($filtersbys == "Date")
            {
                $afileadsdata = affiliatesLeads::select('affiliates_leads.*','users.name')->Join('users','users.id','=','affiliates_leads.auserids')->whereDate('affiliates_leads.afleadsdates',$dfilter)->orderBy('affiliates_leads.id','DESC')->get();

                        foreach($afileadsdata as $leas)
                        {
                            $da = affiliatesleadsfollowups::where('afleadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

                            $leas->affollowupstatus ='';
                            $leas->affollowupdates ='';
                                $leas->affollowupremarks = '';
                                $leas->afnextsfollowupdates = '';
                            
                             if($da){
                                $leas->affollowupstatus = $da->affollowupstatus;
                                $leas->affollowupdates = $da->affollowupdates;
                                $leas->affollowupremarks = $da->affollowupremarks;
                                $leas->afnextsfollowupdates = $da->afnextsfollowupdates;
                                
                            }
                        }


                        $folss = followup::get();
                        $userdata = User::get();
                        $sourcedata = Source::get(); 
                        $categorydata = AffiliatesCategory::get();
                      //  dd($afileadsdata);
                        return view('superadmin.affiliatesleads.filteraffiliatesleads',compact('afileadsdata','folss','userdata','categorydata','sourcedata','dfilter','sfilter','afilter','ffilter','cfilter','mfilters'));
            }

           

             else if($filtersbys == "Mobileno")
            {
                $afileadsdata = affiliatesLeads::select('affiliates_leads.*','users.name')->Join('users','users.id','=','affiliates_leads.auserids')->where('affiliates_leads.aphone',$mfilters)->orWhere('affiliates_leads.awhatsappno',$mfilters)->orderBy('affiliates_leads.id','DESC')->get();

                        foreach($afileadsdata as $leas)
                        {
                            $da = affiliatesleadsfollowups::where('afleadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

                            $leas->affollowupstatus ='';
                            $leas->affollowupdates ='';
                                $leas->affollowupremarks = '';
                                $leas->afnextsfollowupdates = '';
                            
                             if($da){
                                $leas->affollowupstatus = $da->affollowupstatus;
                                $leas->affollowupdates = $da->affollowupdates;
                                $leas->affollowupremarks = $da->affollowupremarks;
                                $leas->afnextsfollowupdates = $da->afnextsfollowupdates;
                                
                            }
                        }


                        $folss = followup::get();
                        $userdata = User::get();
                        $sourcedata = Source::get(); 
                        $categorydata = AffiliatesCategory::get();
                      //  dd($afileadsdata);
                        return view('superadmin.affiliatesleads.filteraffiliatesleads',compact('afileadsdata','folss','userdata','categorydata','sourcedata','dfilter','sfilter','afilter','ffilter','cfilter','mfilters'));
            }

             else if($filtersbys == "Source")
            {
                $afileadsdata = affiliatesLeads::select('affiliates_leads.*','users.name')->Join('users','users.id','=','affiliates_leads.auserids')->where('affiliates_leads.asourcenames',$sfilter)->orderBy('affiliates_leads.id','DESC')->get();

                        foreach($afileadsdata as $leas)
                        {
                            $da = affiliatesleadsfollowups::where('afleadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

                            $leas->affollowupstatus ='';
                            $leas->affollowupdates ='';
                                $leas->affollowupremarks = '';
                                $leas->afnextsfollowupdates = '';
                            
                             if($da){
                                $leas->affollowupstatus = $da->affollowupstatus;
                                $leas->affollowupdates = $da->affollowupdates;
                                $leas->affollowupremarks = $da->affollowupremarks;
                                $leas->afnextsfollowupdates = $da->afnextsfollowupdates;
                                
                            }
                        }


                        $folss = followup::get();
                        $userdata = User::get();
                        $sourcedata = Source::get(); 
                        $categorydata = AffiliatesCategory::get();
                      //  dd($afileadsdata);
                        return view('superadmin.affiliatesleads.filteraffiliatesleads',compact('afileadsdata','folss','userdata','categorydata','sourcedata','dfilter','sfilter','afilter','ffilter','cfilter','mfilters'));
            }

            else if($filtersbys == "Assignedto")
            {
                $afileadsdata = affiliatesLeads::select('affiliates_leads.*','users.name')->Join('users','users.id','=','affiliates_leads.auserids')->where('affiliates_leads.afassignto',$afilter)->orderBy('affiliates_leads.id','DESC')->get();

                        foreach($afileadsdata as $leas)
                        {
                            $da = affiliatesleadsfollowups::where('afleadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

                            $leas->affollowupstatus ='';
                            $leas->affollowupdates ='';
                                $leas->affollowupremarks = '';
                                $leas->afnextsfollowupdates = '';
                            
                             if($da){
                                $leas->affollowupstatus = $da->affollowupstatus;
                                $leas->affollowupdates = $da->affollowupdates;
                                $leas->affollowupremarks = $da->affollowupremarks;
                                $leas->afnextsfollowupdates = $da->afnextsfollowupdates;
                                
                            }
                        }


                        $folss = followup::get();
                        $userdata = User::get();
                        $sourcedata = Source::get(); 
                        $categorydata = AffiliatesCategory::get();
                      //  dd($afileadsdata);
                        return view('superadmin.affiliatesleads.filteraffiliatesleads',compact('afileadsdata','folss','userdata','categorydata','sourcedata','dfilter','sfilter','afilter','ffilter','cfilter','mfilters'));
            }

            else if($filtersbys == "Followupstatus")
            {
                $afileadsdata = affiliatesLeads::select('affiliates_leads.*','users.name','affiliatesleadsfollowups.*','affiliates_leads.id as afiid')->Join('users','users.id','=','affiliates_leads.auserids')->Join('affiliatesleadsfollowups','affiliatesleadsfollowups.afleadsfrom','=','affiliates_leads.id')->where('affiliatesleadsfollowups.affollowupstatus',$ffilter)->where('affiliatesleadsfollowups.afstatus',0)->groupBy('affiliatesleadsfollowups.afleadsfrom')->orderBy('affiliates_leads.id','DESC')->get();

                        foreach($afileadsdata as $leas)
                        {
                            $da = affiliatesleadsfollowups::where('afleadsfrom','=',$leas->afiid)->orderBy('id','DESC')->first();

                            $leas->affollowupstatus ='';
                            $leas->affollowupdates ='';
                                $leas->affollowupremarks = '';
                                $leas->afnextsfollowupdates = '';
                            
                             if($da){
                                $leas->affollowupstatus = $da->affollowupstatus;
                                $leas->affollowupdates = $da->affollowupdates;
                                $leas->affollowupremarks = $da->affollowupremarks;
                                $leas->afnextsfollowupdates = $da->afnextsfollowupdates;
                                
                            }
                        }


                        $folss = followup::get();
                        $userdata = User::get();
                        $sourcedata = Source::get(); 
                        $categorydata = AffiliatesCategory::get();
                      //  dd($afileadsdata);
                        return view('superadmin.affiliatesleads.filteraffiliatesleads',compact('afileadsdata','folss','userdata','categorydata','sourcedata','dfilter','sfilter','afilter','ffilter','cfilter','mfilters'));
            }

            else if($filtersbys == "Category")
            {
                $afileadsdata = affiliatesLeads::select('affiliates_leads.*','users.name')->Join('users','users.id','=','affiliates_leads.auserids')->where('affiliates_leads.affiliatescategorys',$cfilter)->orderBy('affiliates_leads.id','DESC')->get();

                        foreach($afileadsdata as $leas)
                        {
                            $da = affiliatesleadsfollowups::where('afleadsfrom','=',$leas->id)->orderBy('id','DESC')->first();

                            $leas->affollowupstatus ='';
                            $leas->affollowupdates ='';
                                $leas->affollowupremarks = '';
                                $leas->afnextsfollowupdates = '';
                            
                             if($da){
                                $leas->affollowupstatus = $da->affollowupstatus;
                                $leas->affollowupdates = $da->affollowupdates;
                                $leas->affollowupremarks = $da->affollowupremarks;
                                $leas->afnextsfollowupdates = $da->afnextsfollowupdates;
                                
                            }
                        }


                        $folss = followup::get();
                        $userdata = User::get();
                        $sourcedata = Source::get(); 
                        $categorydata = AffiliatesCategory::get();
                      //  dd($afileadsdata);
                        return view('superadmin.affiliatesleads.filteraffiliatesleads',compact('afileadsdata','folss','userdata','categorydata','sourcedata','dfilter','sfilter','afilter','ffilter','cfilter','mfilters'));
            }

    }

}
