<?php

namespace App\Http\Controllers;

use App\EmailConfiguration;
use App\User;
use App\messagetemplate;
use Auth;
use App\Mail\DynamicEmail;
use Illuminate\Http\Request;
use Mail;
class EmailConfigurationController extends Controller
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
    public function store(Request $request)
    {
        //
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
    
    public function emailconfigurations()
    {
        return view('superadmin.emailconfigurations.create');
    }

        public function createConfiguration(Request $request,EmailConfiguration $EmailConfiguration) {

        $configuration  =   EmailConfiguration::create([
            "user_id"       =>      Auth::user()->id,
            "driver"        =>      $request->driver,
            "host"          =>      $request->host,
            "port"          =>      $request->port,
            "encryption"    =>      $request->encryption,
            "user_name"     =>      $request->usenname,
            "password"      =>      $request->password,
            "sender_name"   =>      $request->sendername,
            "sender_email"  =>      $request->senderemail
        ]);

        if(!is_null($configuration)) {
           return back()->with("success", "Email configuration created.");
        }

        else {
            return back()->with("failed", "Email configuration not created.");
        }
    }

    public function composeEmail(messagetemplate $messagetemplate) {
        $mtemplate = messagetemplate::get();
        return view('superadmin.emailconfigurations.composeemail',compact('mtemplate'));
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

    public function sendEmail(Request $request)
    {

        //dd($request->all());
        $toEmail    =   $request->emailAddress;
        $emails = explode(',',$toEmail);
        //$newemails = implode(',',$emails);
        //dd($emails);
        $data       =   array(
            "message"    =>   $request->message
        );


        //$newsemails = [$emails];

        //dd($emails);
        // pass dynamic message to mail class
        Mail::to($emails)->send(new DynamicEmail($data));

        if(Mail::failures() != 0) {
            return back()->with("success", "E-mail sent successfully!");
        }

        else {
            return back()->with("failed", "E-mail not sent!");
        }
    }

    public function ajaxtemplates($templatesid,messagetemplate $messagetemplate)
    {
        $datas = messagetemplate::where('id',$templatesid)->pluck('messagedetails');

            return response()->json($datas);

        
    }
}
