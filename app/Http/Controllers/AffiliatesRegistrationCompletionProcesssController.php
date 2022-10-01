<?php

namespace App\Http\Controllers;
use App\affiliatesLeads;
use App\AffiliatesCategory; 
use App\affiliatestrainingcategory; 
use App\User;
use App\Source;
use App\Branch;
use App\termsandconditions;
use App\Notifications\AggrementUploadedNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Mail;
use Auth;
use Notification;

class AffiliatesRegistrationCompletionProcesssController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::user()->id;

       $leadsdatas = User::where('id',$userId)->where('btnstatus',1)->get();

        return view('affiliatesmarketing.aggrementsdetails.manage',compact('leadsdatas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $usercatgorys = Auth::user()->uafficategory;
        //dd($usercatgorys);

        $acategorys = AffiliatesCategory::all();
        $atrcateg = affiliatestrainingcategory::all();
        $soru = Source::all();
        $uses = User::all();
        $brnas = Branch::all();

           if($usercatgorys == 'Franchise') 
           {
                $termsconditionsdetails = AffiliatesCategory::where('acategoriesname','Franchise')->first();
                 $userdetails = Auth::user()->name;
           }

           else
           {

                $termsconditionsdetails = termsandconditions::where('rulecategories','Affiliate Agreement')->first();
                $userdetails = Auth::user()->name;
                //dd($termsconditionsdetails);
           }


        return view('affiliatesmarketing.aggrementsdetails.create',compact('termsconditionsdetails','userdetails'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  
    public function store($id,Request $request)
    {
                        $user = User::where('id',1)->first();


                        if ($request->hasFile('aggrementfile') || $request->hasFile('pancards') || $request->hasFile('acards')  ||$request->hasFile('ppic') || $request->hasFile('gstcertificates') || $request->hasFile('creg') || $request->hasFile('resumes')) 
                        {

                        


                            $aggrementsfiles = $request->file('aggrementfile');
                            $aggrementsfilesName = $aggrementsfiles->getClientOriginalName();
                            $name = time().'.'.$aggrementsfiles->getClientOriginalExtension();
                            $aggrementsfilesNamedestinationPath = public_path('/agreementdetails');
                            $aggrementsfiles->move($aggrementsfilesNamedestinationPath, $aggrementsfilesName);

                          

                            $pancard = $request->file('pancards');
                            $pancardName = $pancard->getClientOriginalName();
                            $name = time().'.'.$pancard->getClientOriginalExtension();
                            $pancardNamedestinationPath = public_path('/pancarddetails');
                            $pancard->move($pancardNamedestinationPath, $pancardName);


                            $acard = $request->file('acards');
                            $acardName = $acard->getClientOriginalName();
                            $name = time().'.'.$acard->getClientOriginalExtension();
                            $acardNamedestinationPath = public_path('/adhardetails');
                            $acard->move($acardNamedestinationPath, $acardName);


                            $ppic = $request->file('ppic');
                            $ppicName = $ppic->getClientOriginalName();
                            $name = time().'.'.$ppic->getClientOriginalExtension();
                            $ppicNameNamedestinationPath = public_path('/profilepicsdetails');
                            $ppic->move($ppicNameNamedestinationPath, $ppicName);


                            $gstcertificatess = $request->file('gstcertificates');
                            $gstcertificatessName = $gstcertificatess->getClientOriginalName();
                            $name = time().'.'.$gstcertificatess->getClientOriginalExtension();
                            $gstcertificatessNamedestinationPath = public_path('/gstcertificatesdetails');
                            $gstcertificatess->move($gstcertificatessNamedestinationPath, $gstcertificatessName);

                            $companreg = $request->file('creg');
                            $companregsName = $gstcertificatess->getClientOriginalName();
                            $name = time().'.'.$companreg->getClientOriginalExtension();
                            $companregNamedestinationPath = public_path('/companyregistratindetails');
                            $companreg->move($companregNamedestinationPath, $companregsName);


                             $res = $request->file('resumes');
                            $reumeName = $res->getClientOriginalName();
                            $name = time().'.'.$res->getClientOriginalExtension();
                            $resumeNamedestinationPath = public_path('/resumes');
                            $res->move($resumeNamedestinationPath, $reumeName);

                            
                            $upoda = User::find($id); 
                            $upoda->uaffinames = $request->afnames; 
                            $upoda->ucompanyname = $request->aficompanynames; 
                            $upoda->email = $request->usernames; 
                            $upoda->mobileno = $request->phone; 
                            $upoda->uwhatsappnos = $request->whatsapp; 
                            $upoda->baddress = $request->address; 
                            $upoda->ucity = $request->city; 
                            $upoda->ustate = $request->state; 
                            $upoda->utobranchs = $request->tbranhs; 
                            $upoda->ufrombranchs = $request->fbranchs; 
                            $upoda->uaffitraining = $request->afftraining; 
                            $upoda->uafficategory = $request->affcategory; 
                            $upoda->uaffiliatescreateddates = $request->afcreatedat; 
                            $upoda->usource = $request->sources; 
                            $upoda->tstatus = 1; 
                            $upoda->agreementfile = $aggrementsfilesName; 
                            $upoda->pancardfile = $pancardName; 
                            $upoda->acardsfiles = $acardName; 
                            $upoda->profilepic = $ppicName; 
                            $upoda->gstcertificatefile = $gstcertificatessName;  
                            $upoda->compabyregisterafile = $companregsName;  
                            $upoda->resumefile = $reumeName;  
                            $upoda->btnstatus = 1;  
                            $upoda->save();
                                                
                                $mobille = $request->phone;

                                $af = affiliatesLeads::where('aphone',$mobille)->first();

                                $af->aconverteds = 1;
                                if ($af->save()) {
                                        $user->notify(new AggrementUploadedNotification(User::findOrFail($id)));

                                    }

                            return redirect('/affiliates-aggrements-approvals')->with('success','Your Account Will Be Activate within 24 Hours Working Days!!!');



                        
                    }

       
    }
    
     public function acceptstermsconditions(Request $request)
    {
        $UserId = Auth::user()->id;

        $ud = User::find($UserId);
        $ud->rstatus = 1;
        $ud->tstatus = 1;
        $ud->save();

        return redirect('/affiliate-user-home')->with('success','You Have Accepted Terms and Conditions Successfully !!');
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
}
