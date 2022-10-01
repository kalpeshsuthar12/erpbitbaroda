<?php

namespace App\Http\Controllers;

use App\leadsfollowups;
use App\leads;
use App\User;
use App\followup;
use Auth;
use DB;
use Illuminate\Http\Request;

class LeadsfollowupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $foll = leadsfollowups::all();

       return view('superadmin.followupsleads.manage',compact('foll'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id,Request $request)
    {
        $usersid =  $userId = Auth::user()->id;
        $leadsfollowupsmodel = new leadsfollowups();
        $leadsfollowups = $leadsfollowupsmodel->create([
            'leadsfrom'=> $id,
            'flstudentname'=> $request->sname,
            'flemail'=> $request->semail,
            'flphoneno'=> $request->sphoneno,
            'flwhatsappno'=> $request->swhatsappno,
            'flfollwpdate'=> $request->fdate,
            'flremarsk'=> $request->remarks,
           
            
        ]);



        return redirect('/followups-leads')->with('success','Followups Created Successfully!!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\leadsfollowups  $leadsfollowups
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\leadsfollowups  $leadsfollowups
     * @return \Illuminate\Http\Response
     */
    public function edit($id,leadsfollowups $leadsfollowups)
    {
        $edi = leadsfollowups::find($id);

       return view('superadmin.followupsleads.edit',compact('edi'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\leadsfollowups  $leadsfollowups
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, leadsfollowups $leadsfollowups)
    {
        
        
        $up = leadsfollowups::find($id);
        $up->flstudentname = $request->sname;
        $up->flemail = $request->semail;
        $up->flphoneno = $request->sphoneno;
        $up->flwhatsappno = $request->swhatsappno;
        $up->flfollwpdate = $request->fdate;
        $up->flremarsk = $request->remarks;
        $up->save();

          return redirect('/followups-leads')->with('success','Followups Updated Successfully!!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\leadsfollowups  $leadsfollowups
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,leadsfollowups $leadsfollowups)
    {
        $dels = leadsfollowups::find($id);
        $dels->delete();

        return redirect('/followups-leads')->with('success','Followups Deleted Successfully!!');
    }
    public function ajax(Request $request)
    {
       
       //dd($request->all());
            //dd()
       // $data = $request->all();
        //$result = leadsfollowups::insert($data);

        $datas1  = $request->followupstatus; 
        $datas2  = $request->takenby; 
        $datas3  = $request->flfollwpdate; 
        $datas4  = $request->flremarsk; 
        $datas5  = $request->nxtfollowupdate; 
        $datas6  = $request->followupsby; 
        $datas7  = $request->leadsfrom; 

        $getid = leadsfollowups::where('leadsfrom',$datas7)->update(array('fstatus' => 1));
        
         /*if($getid = leadsfollowups::where('leadsfrom',$datas7)->where('followupstatus',"Walked-In")->first())
         {
             $getid->fstatus = 0;
             $getid->save();
            
             
         }*/
        
        
       if($datas1 != " ")
         {
                    $data = $request->all();
                $result = leadsfollowups::insert($data);
                
                 return response()->json(
                    [
                        'success' => true,
                        'message' => 'Followups Done successfully'
                    ]
                );   
         }        


    }

    public function ajaxData(Request $request)
    {
       
       //dd($request->all());
        
        $datasafull = $request->all();
        $result = leadsfollowups::insert($datasafull);

        if($result->followupstatus == 'Cold Follow-ups')
        {
            $leadid = $request->leadsfrom;

           $updates =  leads::find($leadid);
           $updates->followupstatus =  'Cold Follow-ups';
           $updates->save();
        }
        
    }

    public function getfull(Request $request)
    {
        $le = $request->leadid;
        $data= array();
        $result = leadsfollowups::where('leadsfrom','=',$le)->orderBy('id','DESC')->get();
        //dd($result);
        foreach($result as $res)
        {
            $row = array();
            $row[] = $res->followupstatus;
            $row[] = $res->takenby;
            $row[] = date('d-m-Y',strtotime($res->flfollwpdate));
            $row[] = $res->flremarsk;
            if($res->nxtfollowupdate != NULL)
            {
              $row[] = date('d-m-Y',strtotime($res->nxtfollowupdate));
            }
            else
            {
                $row[] = "";
            }
           
            $row[] = $res->followupsby;
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);



    }

    public function getcurrentdate(Request $request)
    {
        $userId = Auth::user()->id;
        $getcurrentdate = $request->currentdatesss;
        $data= array();
        
        $result = leads::where('user_id',$userId)->get();

               

        foreach($result as $res)
        {

            $da = leadsfollowups::where('leadsfrom','=',$res->id)->where('nxtfollowupdate',$getcurrentdate)->orderBy('id','DESC')->first();
            

                $res->followupstatus ='';
                $res->takenby ='';
                $res->flfollwpdate ='';
                $res->flremarsk = '';
                $res->nxtfollowupdate = '';
            $row = array();
        

                
            $row[] = $res->studentname;
            $row[] = $res->email;
            $row[] = $res->phone;
            $row[] = $res->whatsappno;
            $row[] = '<center><button type="button" class="btn btn-primary waves-effect waves-light" onclick="followupfunction('.$res->id.')"><i class="fa fa-tty"></i></button></center>';

            if($da)
            {
                $row[] = '<div class="badge bg-soft-success font-size-12">Followups Done</div>';

                 if($getcurrentdate == $da->nxtfollowupdate)
                {
                    $row[] = '<span class="text-red blink-hard">'.date('d-m-Y',strtotime($da->nxtfollowupdate)).'</span>';
                    $row[] = date('d-m-Y',strtotime($da->flfollwpdate));
                    $row[] = $res->flremarsk = $da->flremarsk;
                }

                else
                {
                    $row[] = $res->nxtfollowupdate = date('d-m-Y',strtotime($da->nxtfollowupdate));
                    $row[] = $res->flfollwpdate =  date('d-m-Y',strtotime($da->flfollwpdate));
                    $row[] = $res->flremarsk = $da->flremarsk;
                }

                
              
             
            }
            else
            {
                $row[] = '<span class="text-red blink-hard">Pending</span>';
                $row[] = $res->nxtfollowupdate = '';
                $row[] = $res->flfollwpdate = '';
                $row[] = $res->flremarsk = '';
            }

      
          

         
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);

              // dd($result);
    }


    public function selecteddateleads(Request $request)
    {
       $Cdates = date('Y-m-d');
        $userId = Auth::user()->id;
        $sdates = $request->selecteddates;
        $data= array();
        
        //$result = leads::where("created_at","LIKE","%{$sdates}%")->where('user_id',$userId)->get();
       $result = leads::select("leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leads.user_id',$userId)->whereDate('leadsfollowups.nxtfollowupdate', '=',$sdates)->get();

               

        foreach($result as $res)
        {

            $da = leadsfollowups::where('leadsfrom','=',$res->id)->orderBy('id','DESC')->first();

                $res->flfollwpdate ='';
                $res->flremarsk = '';
                $res->nxtfollowupdate = '';
            $row = array();

                
            $row[] = $res->studentname;
            $row[] = $res->course;
            $row[] = $res->phone;
            $row[] = $res->whatsappno;
            $row[] = $res->email;
            
            if($da)
            {

                if($res->walkedinstatus == '1')
                {
                    $row[] = ' <div class="badge bg-soft-success font-size-12">Walked-In</div>';
                }

                else
                {
                    $row[] = '';
                }
                 if($da->nxtfollowupdate < $Cdates)
                {
                    $row[] = '<div class="badge bg-soft-danger font-size-12">Pending Followup</div>';
                    
                }
                
                else
                {
                    $row[] = '<div class="badge bg-soft-success font-size-12">Followups Done</div>';
                }


                 $row[] = '<center><button type="button" class="btn btn-primary waves-effect waves-light" onclick="followupfunction('.$res->id.')"><i class="fa fa-tty"></i></button></center>';

                 if($sdates == $da->nxtfollowupdate)
                {
                    $row[] = date('d-m-Y',strtotime($da->flfollwpdate));
                    $row[] = $res->flremarsk = $da->flremarsk;
                    $row[] = '<span class="text-red blink-hard">'.date('d-m-Y',strtotime($da->nxtfollowupdate)).'</span>';
                    $row[] = $res->branch;
                    $row[] = $res->tobranchs;
                    $row[] = $res->source;
                    $row[] = $res->coursesmode;
                    $row[] = $res->lvalue;
                    if($da->leadstatus == '0')
                    {
                       $row[] = '<div class="badge bg-soft-danger font-size-12">Deactivate</div>';
                    }

                    else
                    {
                        $row[] = '<div class="badge bg-soft-danger font-size-12">Activate</div>';
                    }
                    $row[] = $res->leadduration;

                    if($da->conversationstatus == '1')
                    {
                         $row[] = '<div class="badge bg-soft-success font-size-12">Converted</div>';

                         $row[] = '<div class="badge bg-soft-success font-size-12">Completed</div>';
                    }
                    else
                    {
                        $row[] = '<a href="/create-admission-process/'.$res->id.'" class="btn btn-success">Admission</a>';
                        
                        $row[] = '<div class="badge bg-soft-warning font-size-12">Pending</div>';

                        
                    }

                    $row[] = $res->created_at;
                }

                else
                {
                    $row[] = $res->flfollwpdate =  date('d-m-Y',strtotime($da->flfollwpdate));
                    $row[] = $res->flremarsk = $da->flremarsk;
                    $row[] = $res->nxtfollowupdate = date('d-m-Y',strtotime($da->nxtfollowupdate));
                    $row[] = $res->branch;
                    $row[] = $res->tobranchs;
                    $row[] = $res->source;
                    $row[] = $res->coursesmode;
                    $row[] = $res->lvalue;

                    if($da->leadstatus == '0')
                    {
                       $row[] = '<div class="badge bg-soft-danger font-size-12">Deactivate</div>';
                    }

                    else
                    {
                        $row[] = '<div class="badge bg-soft-success font-size-12">Activate</div>';
                    }

                    $row[] = $res->leadduration;

                   if($da->conversationstatus == '1')
                    {
                         $row[] = '<div class="badge bg-soft-success font-size-12">Converted</div>';

                         $row[] = '<div class="badge bg-soft-success font-size-12">Completed</div>';
                    }
                    else
                    {
                        $row[] = '<a href="/direct-admission/'.$res->id.'" class="btn btn-success">Admission</a>';
                        
                        $row[] = '<div class="badge bg-soft-warning font-size-12">Pending</div>';

                        
                    }
                    $row[] = $res->created_at;
                }


                
              
             
            }
            else
            {

                    if($res->walkedinstatus == '1')
                {
                    $row[] = ' <div class="badge bg-soft-success font-size-12">Walked-In</div>';
                }

                else
                {
                    $row[] = '';
                }
                    $row[] = '<span class="text-red blink-hard">Pending</span>';
                     $row[] = '<center><button type="button" class="btn btn-primary waves-effect waves-light" onclick="followupfunction('.$res->id.')"><i class="fa fa-tty"></i></button></center>';
                    $row[] = $res->flfollwpdate = '';
                    $row[] = $res->flremarsk = '';
                    $row[] = $res->nxtfollowupdate = '';
                    $row[] = $res->branch;
                    $row[] = $res->tobranchs;
                    $row[] = $res->source;
                    $row[] = $res->coursesmode;
                    $row[] = $res->lvalue;
                    if($da->leadstatus == '0')
                    {
                       $row[] = '<div class="badge bg-soft-danger font-size-12">Deactivate</div>';
                    }

                    else
                    {
                        $row[] = '<div class="badge bg-soft-success font-size-12">Activate</div>';
                    }

                    $row[] = $res->leadduration;

                    if($da->conversationstatus == '1')
                    {
                         $row[] = '<div class="badge bg-soft-success font-size-12">Converted</div>';

                         $row[] = '<div class="badge bg-soft-success font-size-12">Completed</div>';
                    }
                    else
                    {
                        $row[] = '<a href="/direct-admission/'.$res->id.'" class="btn btn-success">Admission</a>';
                        
                        $row[] = '<div class="badge bg-soft-warning font-size-12">Pending</div>';

                        
                    }

                    $row[] = $res->created_at;
            }



           
          

         
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);

              // dd($result);
    }


    public function centremanagerselecteddateleads(Request $request)
    {
       $Cdates = date('Y-m-d');
        $userId = Auth::user()->id;
        $sdates = $request->selecteddates;
        $data= array();
        
        //$result = leads::where("created_at","LIKE","%{$sdates}%")->where('user_id',$userId)->get();
       $result = leads::select("leads.*","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->where('leads.user_id',$userId)->whereDate('leadsfollowups.nxtfollowupdate', '=',$sdates)->get();

               

        foreach($result as $res)
        {

            $da = leadsfollowups::where('leadsfrom','=',$res->id)->orderBy('id','DESC')->first();

                $res->flfollwpdate ='';
                $res->flremarsk = '';
                $res->nxtfollowupdate = '';
            $row = array();

                
            $row[] = $res->studentname;
            $row[] = $res->course;
            $row[] = $res->phone;
            $row[] = $res->whatsappno;
            $row[] = $res->email;
            
            if($da)
            {

                if($res->walkedinstatus == '1')
                {
                    $row[] = ' <div class="badge bg-soft-success font-size-12">Walked-In</div>';
                }

                else
                {
                    $row[] = '';
                }
                 if($da->nxtfollowupdate < $Cdates)
                {
                    $row[] = '<div class="badge bg-soft-danger font-size-12">Pending Followup</div>';
                    
                }
                
                else
                {
                    $row[] = '<div class="badge bg-soft-success font-size-12">Followups Done</div>';
                }


                 $row[] = '<center><button type="button" class="btn btn-primary waves-effect waves-light" onclick="followupfunction('.$res->id.')"><i class="fa fa-tty"></i></button></center>';

                 if($sdates == $da->nxtfollowupdate)
                {
                    $row[] = date('d-m-Y',strtotime($da->flfollwpdate));
                    $row[] = $res->flremarsk = $da->flremarsk;
                    $row[] = '<span class="text-red blink-hard">'.date('d-m-Y',strtotime($da->nxtfollowupdate)).'</span>';
                    $row[] = $res->branch;
                    $row[] = $res->tobranchs;
                    $row[] = $res->source;
                    $row[] = $res->coursesmode;
                    $row[] = $res->lvalue;
                    if($da->leadstatus == '0')
                    {
                       $row[] = '<div class="badge bg-soft-danger font-size-12">Deactivate</div>';
                    }

                    else
                    {
                        $row[] = '<div class="badge bg-soft-danger font-size-12">Activate</div>';
                    }
                    $row[] = $res->leadduration;

                    if($da->conversationstatus == '1')
                    {
                         $row[] = '<div class="badge bg-soft-success font-size-12">Converted</div>';

                         $row[] = '<div class="badge bg-soft-success font-size-12">Completed</div>';
                    }
                    else
                    {
                        $row[] = '<a href="/create-manager-students-admission-process/'.$res->id.'" class="btn btn-success">Admission</a>';
                        
                        $row[] = '<div class="badge bg-soft-warning font-size-12">Pending</div>';

                        
                    }

                    $row[] = $res->created_at;
                }

                else
                {
                    $row[] = $res->flfollwpdate =  date('d-m-Y',strtotime($da->flfollwpdate));
                    $row[] = $res->flremarsk = $da->flremarsk;
                    $row[] = $res->nxtfollowupdate = date('d-m-Y',strtotime($da->nxtfollowupdate));
                    $row[] = $res->branch;
                    $row[] = $res->tobranchs;
                    $row[] = $res->source;
                    $row[] = $res->coursesmode;
                    $row[] = $res->lvalue;

                    if($da->leadstatus == '0')
                    {
                       $row[] = '<div class="badge bg-soft-danger font-size-12">Deactivate</div>';
                    }

                    else
                    {
                        $row[] = '<div class="badge bg-soft-success font-size-12">Activate</div>';
                    }

                    $row[] = $res->leadduration;

                   if($da->conversationstatus == '1')
                    {
                         $row[] = '<div class="badge bg-soft-success font-size-12">Converted</div>';

                         $row[] = '<div class="badge bg-soft-success font-size-12">Completed</div>';
                    }
                    else
                    {
                        $row[] = '<a href="/direct-admission/'.$res->id.'" class="btn btn-success">Admission</a>';
                        
                        $row[] = '<div class="badge bg-soft-warning font-size-12">Pending</div>';

                        
                    }
                    $row[] = $res->created_at;
                }


                
              
             
            }
            else
            {

                    if($res->walkedinstatus == '1')
                {
                    $row[] = ' <div class="badge bg-soft-success font-size-12">Walked-In</div>';
                }

                else
                {
                    $row[] = '';
                }
                    $row[] = '<span class="text-red blink-hard">Pending</span>';
                     $row[] = '<center><button type="button" class="btn btn-primary waves-effect waves-light" onclick="followupfunction('.$res->id.')"><i class="fa fa-tty"></i></button></center>';
                    $row[] = $res->flfollwpdate = '';
                    $row[] = $res->flremarsk = '';
                    $row[] = $res->nxtfollowupdate = '';
                    $row[] = $res->branch;
                    $row[] = $res->tobranchs;
                    $row[] = $res->source;
                    $row[] = $res->coursesmode;
                    $row[] = $res->lvalue;
                    if($da->leadstatus == '0')
                    {
                       $row[] = '<div class="badge bg-soft-danger font-size-12">Deactivate</div>';
                    }

                    else
                    {
                        $row[] = '<div class="badge bg-soft-success font-size-12">Activate</div>';
                    }

                    $row[] = $res->leadduration;

                    if($da->conversationstatus == '1')
                    {
                         $row[] = '<div class="badge bg-soft-success font-size-12">Converted</div>';

                         $row[] = '<div class="badge bg-soft-success font-size-12">Completed</div>';
                    }
                    else
                    {
                        $row[] = '<a href="/direct-admission/'.$res->id.'" class="btn btn-success">Admission</a>';
                        
                        $row[] = '<div class="badge bg-soft-warning font-size-12">Pending</div>';

                        
                    }

                    $row[] = $res->created_at;
            }



           
          

         
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);

              // dd($result);
    }


    public function superadminselecteddateleads(Request $request)
    {
       $Cdates = date('Y-m-d');
       
        $userId = Auth::user()->id;
        $userdata = User::get();
    
        $folss = followup::get();
        $sdates = $request->PstartDate;
        $ddates = $request->Penddate;
      //  $data= array();
        
        //$result = leads::where("created_at","LIKE","%{$sdates}%")->where('user_id',$userId)->get();
       $leadsdata = leads::select("leads.*","users.name","leadsfollowups.followupstatus","leadsfollowups.takenby","leadsfollowups.flfollwpdate","leadsfollowups.flremarsk","leadsfollowups.nxtfollowupdate")->join("leadsfollowups","leadsfollowups.leadsfrom","=","leads.id")->join("users","users.id","=","leads.user_id")->whereBetween('leadsfollowups.nxtfollowupdate',[$sdates,$ddates])->where('leadsfollowups.fstatus',0)->get();

               

                                foreach($leadsdata as $leas)
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
         $dates = date('Y-m-d');

             return view('superadmin.leads.pendingleadsfilteration',compact('leadsdata','folss','dates','userdata','Cdates','sdates','ddates'));
             
    }
}
