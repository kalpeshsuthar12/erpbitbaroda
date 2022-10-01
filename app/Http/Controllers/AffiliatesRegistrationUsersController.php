<?php

namespace App\Http\Controllers;
use App\affiliatesLeads;
use App\AffiliatesCategory; 
use App\affiliatestrainingcategory; 
use App\User;
use App\Source;
use App\Branch;
use App\Notifications\ActivateUserNotification;
use App\Notifications\DeactivateUserNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Mail;
use Notification;

class AffiliatesRegistrationUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $ids = $request->getids;
        //dd($ids);
        if($ids == null)
        {
            return redirect()->back()->with('error','Please Select Checkbox');
        }

        else
        {
            $leadsda = affiliatesLeads::find($ids);
            $acategorys = AffiliatesCategory::all();
            $atrcateg = affiliatestrainingcategory::all();
            $soru = Source::all();
            $uses = User::all();
            $brnas = Branch::all();

             return view('superadmin.affiliatesregistrations.create',compact('acategorys','atrcateg','soru','uses','brnas','leadsda'));   
        }

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
             $usermodel = new User();
        $User = $usermodel->create([
            'name'=> $request->afnames,
            'personalemail'=> $request->apfemails,
            'email'=> $request->afemails,
            'mobileno'=> $request->afphone,
            'uwhatsappnos'=> $request->afwhatsapp,
            'usercategory'=> 'Affiliate Marketing',
            'password'=>  Hash::make($request->afpasswords),
            'sepass'=>  $request->afpasswords,
            'baddress'=>  $request->afaddress,
            'usource'=>  $request->afsources,
            'uaffiliatescreateddates'=>  $request->aldates,
            'ufrombranchs'=>  $request->afrombranchs,
            'utobranchs'=>  $request->atobranchs,
            'uaffitraining'=>  $request->aftraining,
            'uafficategory'=>  $request->afcategories,
            'uaffinames'=>  $request->afnames,
            'ucompanyname'=>  $request->acompanyname,
            'ucity'=>  $request->afcity,
            'ustate'=>  $request->afstate,
            'affileadsid'=>  $request->affiusersid,
        ]);

         $data["UserNames"] = $request->afemails;
        $data["Userpasswords"] = $request->afpasswords;
        $data["Useremail"] = $request->afemails;

        
            Mail::send('superadmin.affiliatesregistrations.affiliatesmails', $data, function ($message) use ($data) {
            $data;
            $message->to($data["Useremail"],$data["Useremail"])
                ->from('bitdeveloper21@gmail.com','BIT Baroda Institute Of Technology')
                ->cc('support@bitbaroda.com','Admission BIT')
                ->subject("Welcome letter to New Affiliate.");
                
        });

         
        if (Mail::failures()) {
                    dd('mailerror');
                } else {

                    return redirect('/affiliates-leads')->with('success','Affiliate Created and Mail Sent Successfully!!!');

                }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $leadsdatas = User::where('btnstatus',1)->where('uafficategory','!=','Franchise')->get();
      // dd($leadsdatas);


        return view('superadmin.affiliatesregistrations.affiliatesreagistered',compact('leadsdatas'));
    }


    public function franchise()
    {
         $leadsdatas = User::where('btnstatus',1)->where('uafficategory','Franchise')->get();
      // dd($leadsdatas);


        return view('superadmin.affiliatesregistrations.franchiseuserlist',compact('leadsdatas'));
    }

    public function activate($id)
    {

        $user = User::where('id',$id)->first();
        $status = User::find($id);
        $status->utstatus = 1;
        
         if ($status->save()) {
                $user->notify(new ActivateUserNotification(User::findOrFail($status->id)));

            }
        return redirect()->back()->with('success',$status->name." is Activate Now!!");
    }

    public function deactivate($id)
    {
        $user = User::where('id',$id)->first();
        $status = User::find($id);
        $status->utstatus = 0;
       /* $status->save();*/

        if ($status->save()) {
                $user->notify(new DeactivateUserNotification(User::findOrFail($status->id)));

            }
        
          //  return redirect('/posts');

        return redirect()->back()->with('success',$status->name." is Deactivate Now!!");
    }
    
    public function markRead(Request $request)
    {
        
            auth()->user()->unreadNotifications->markAsRead();
            return redirect()->back();
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
            $edits = User::find($id);
            $acategorys = AffiliatesCategory::all();
            $atrcateg = affiliatestrainingcategory::all();
            $soru = Source::all();
            $uses = User::all();
            $brnas = Branch::all();

            return view('superadmin.affiliatesregistrations.edit',compact('acategorys','atrcateg','soru','uses','brnas','edits'));  
    }

    public function editregisteredaffiliates($id)
    {
        //dd($id);
        
        $ufins = User::find($id);
        $usercatgorys = $ufins->uafficategory;
        $acategorys = AffiliatesCategory::all();
        $atrcateg = affiliatestrainingcategory::all();
        $soru = Source::all();
        $uses = User::all();
        $brnas = Branch::all();
        
        $Aggrementsdetails = AffiliatesCategory::where('acategoriesname',$usercatgorys)->first();

        return view('superadmin.affiliatesregistrations.editregisteredaffiliates',compact('Aggrementsdetails','ufins'));
        
    }


    public function updateregisteredaffiliates($id, Request $request)
    {
         if ($request->hasFile('aggrements') || $request->hasFile('pancards') || $request->hasFile('acards')  ||$request->hasFile('ppic') || $request->hasFile('gstcertificates') || $request->hasFile('creg') || $request->hasFile('resumes')) 
                        {

                        

                            // dd("test");
                            $aggrements = $request->file('aggrements');
                            $aggrementsName = $aggrements->getClientOriginalName();
                            $name = time().'.'.$aggrements->getClientOriginalExtension();
                            $aggrementsNamedestinationPath = public_path('/agreementdetails');
                            $aggrements->move($aggrementsNamedestinationPath, $aggrementsName);


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
                            $upoda->agreementfile = $aggrementsName; 
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
                                $af->save();

                            return redirect('/affiliates-aggrements-approvals')->with('success','Affiliate Account is been Updated!!');



                        
                    }
                    else
                    {
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
                            $upoda->save();


                            return redirect('/registered-affiliates')->with('success','Affiliate Account is been Updated!!');
                    }


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
        $updates = User::find($id);

        if($request->afpasswords == $updates->sepass)
        {
                $updates->usource = $request->afsources;
                $updates->uaffiliatescreateddates = $request->aldates;
                $updates->ufrombranchs = $request->afrombranchs;
                $updates->utobranchs = $request->atobranchs;
                $updates->uaffitraining = $request->aftraining;
                $updates->uafficategory = $request->afcategories;
                $updates->uaffinames = $request->afnames;
                $updates->name = $request->afnames;
                $updates->ucompanyname = $request->acompanyname;
                $updates->personalemail = $request->apfemails;
                $updates->mobileno = $request->afphone;
                $updates->uwhatsappnos = $request->afwhatsapp;
                $updates->baddress = $request->afaddress;
                $updates->ucity = $request->afcity;
                $updates->ustate = $request->afstate;
                $updates->email = $request->afemails;
                $updates->password = Hash::make($request->afpasswords);
                $updates->sepass = $request->afpasswords;
                $updates->save();

                return redirect('/register-affiliates')->with('success',$request->afnames." Details Updated!");

        }

        else
        {
            $updates->usource = $request->afsources;
                $updates->uaffiliatescreateddates = $request->aldates;
                $updates->ufrombranchs = $request->afrombranchs;
                $updates->utobranchs = $request->atobranchs;
                $updates->uaffitraining = $request->aftraining;
                $updates->uafficategory = $request->afcategories;
                $updates->uaffinames = $request->afnames;
                $updates->name = $request->afnames;
                $updates->ucompanyname = $request->acompanyname;
                $updates->personalemail = $request->apfemails;
                $updates->mobileno = $request->afphone;
                $updates->uwhatsappnos = $request->afwhatsapp;
                $updates->baddress = $request->afaddress;
                $updates->ucity = $request->afcity;
                $updates->ustate = $request->afstate;
                $updates->email = $request->afemails;
                $updates->password = Hash::make($request->afpasswords);
                $updates->sepass = $request->afpasswords;
                $updates->save();

                 $data["UserNames"] = $request->afemails;
        $data["Userpasswords"] = $request->afpasswords;
        $data["Useremail"] = $request->afemails;

        
            Mail::send('superadmin.affiliatesregistrations.affiliatesupdated', $data, function ($message) use ($data) {
            $data;
            $message->to($data["Useremail"],$data["Useremail"])
                ->from('bitdeveloper21@gmail.com','BIT Baroda Institute Of Technology')
                ->cc('support@bitbaroda.com','Admission BIT')
                ->subject("Welcome letter to New Affiliate.");
                
        });

         
        if (Mail::failures()) {
                    dd('mailerror');
                } else {

                    return redirect('/affiliates-leads')->with('success',$request->afnames." Details Updated and Mail Sent Successfully !!");

                }

                
        }
       

       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
          $deles = User::find($id);   
          $deles->delete();

          return redirect('/affiliates-leads')->with('success',"Affiliates User has been deleted !"); 
    }
}
