<?php

namespace App\Http\Controllers;
use App\leads;
use App\leadsfollowups;
use App\CompanyQuotation;
use App\quotation_courses_details;
use App\course;
use Mail;
use PDF;
use Illuminate\Http\Request;

class CompanyQuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $quodetails = CompanyQuotation::all();
        return view('superadmin.quotation.manage',compact('quodetails'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
         $year = date("Y");
         $month = date("m");

        $getid = $request->getquotation;
        $quotationdetails = leads::find($getid);
        $cours = course::get();

        $latests = CompanyQuotation::latest()->get()->pluck('quotenos');
            //dd($latests);
            $mj = isset($latests[0]) ? $latests[0] : false;
            $counts = $mj + 1;
            $code_nos = str_pad($counts, 4, "0", STR_PAD_LEFT);
            $value = 'BIT-Quo/'.$year.'/'.$month.'/'.$code_nos;

        return view('superadmin.quotation.create',compact('quotationdetails','cours','value'));

    }

    public function view(Request $request)
    {
        $getids = $request->quotationid;
         $data= array();

         $getmobiles = leads::where('id',$getids)->pluck('phone');

          $result = CompanyQuotation::where('cphoneno',$getmobiles)->get();

            foreach($result as $res)
        {
            $row = array();
            $row[] =  date('d-m-Y',strtotime($res->quotatdate));
            $row[] =  date('d-m-Y',strtotime($res->quotatduedate));;
            $row[] = $res->cname;
            $row[] = $res->ccontactperson;
            $row[] = $res->cphoneno;
            $row[] = $res->cwhatsappno;
            $row[] = $res->cemails;
            $row[] = '<a href="javascript: void(0);" onclick="QuotationFunction('.$res->id.')" class="btn btn-primary">View</a>';
            $row[] = $res->quotationno;
            $row[] = $res->cfinaltotals;
            $row[] = '<div class="badge bg-soft-success font-size-12">Quotation Send</div>';
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);
    }


    public function getcourses(Request $request)
    {
        $getqousid = $request->quotationsis;
        $data= array();
        $result = quotation_courses_details::select('courses.coursename','quotation_courses_details.compcoursemode')->Join('courses','courses.id','=','quotation_courses_details.compcourse')->where('quotation_courses_details.companyquotationid',$getqousid)->get();

            foreach($result as $res)
        {
            $row = array();
            $row[] = $res->coursename;
            $row[] = $res->compcoursemode;
            $data[] = $row;
        }

         $response = array(
            "recordsTotal"    => count($data),  
            "recordsFiltered" => count($data), 
            "data"            => $data   
         );

         echo json_encode($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            $quono = $request->qnos;
            $quotationno = explode("/",$quono);
            $qnosse = $quotationno[3];

             $dtype = $request->ccddiscotypes;
            

             if($dtype == "2")
            {
                 $discoun = $request->pdiscounts;
            }

            elseif($dtype == "1")
            {
                $discoun = $request->fdiscounts;
            }

           $CompanyQuotationmodel = new CompanyQuotation();
            $CompanyQuotation = $CompanyQuotationmodel->create([
            'quotatdate'=> $request->qdate,
            'quotatduedate'=> $request->ddates,
            'cname'=> $request->ccname,
            'ccontactperson'=> $request->cperson,
            'cphoneno'=> $request->cmobileno,
            'cwhatsappno'=> $request->cwhatsappno,
            'cemails'=> $request->cemaiels,
            'quotationno'=> $request->qnos,
            'quotenos'=> $qnosse,
            'caddress'=> $request->address,
            'cdiscountypes'=>$dtype,
            'cdiscounts'=> $discoun,
            'csubtotal'=> $request->subtotals,
            'ctotal'=> $request->total,
            'cgsttax'=> $request->gstaxs,
            'ctaxamounts'=> $request->gstamounts,
            'cfinaltotals'=> $request->finaltotals,
                ]);

            $CompanyQuotationid = $CompanyQuotation->id;
                    $coursesdata = $request->invcourse;
                    $coursesubcourse = $request->invsubcourses;
                    $csmode = $request->coursdataemode;
                    $csfeess = $request->coursesFees;
                    $nstudents = $request->nofstudents;
                     for($i=0; $i < (count($coursesdata)); $i++)
                    {
                                $quotation_courses_details = new quotation_courses_details([
                                
                                'companyquotationid' => $CompanyQuotationid,
                                'compcourse'   => $coursesdata[$i],
                                'compspecializations'   => $coursesubcourse[$i],
                                'compcoursemode'   => $csmode[$i],
                                'compcoursefees'   => $csfeess[$i],
                                'compnofstudents'   => $nstudents[$i],
                                
                            ]);
                            $quotation_courses_details->save();
                    }


                    $to_name = "Shah Meet";
                    $to_email = "mshah0140@gmail.com";

              
                $getUserQuotationDetails = CompanyQuotation::find($CompanyQuotationid);
                
                $getCourseDetasils = quotation_courses_details::select('courses.coursename','quotation_courses_details.compcoursemode')->Join('courses','courses.id','=','quotation_courses_details.compcourse')->where('quotation_courses_details.companyquotationid',$CompanyQuotationid)->get();
                    
                         $Comapanyname = $getUserQuotationDetails->cname;
                         $contactperson = $getUserQuotationDetails->ccontactperson;
                         $inquirydate = date('d-m-Y',strtotime($getUserQuotationDetails->quotatdate));

                     $data = array('CompanyName' => $Comapanyname, 'ContactPerson' => $contactperson, 'QuoteDate' => $inquirydate); 
                     $data["email"] = $getUserQuotationDetails->cemails;
                     $data["title"] = "Quotations For" .$Comapanyname;
                     
                     $pdf = PDF::loadView('superadmin.quotation.viewquotations',$data);
                     
                     Mail::send('superadmin.quotation.quotationmail', $data, function ($message) use ($data, $pdf) {
            $message->to($data["email"], $data["email"])
                ->from('support@bitbaroda.com','BIT Baroda Institute Of Technology')
                ->cc('admission@bitbaroda.com','Admission BIT')
                ->subject($data["title"])
                ->attachData($pdf->output(), "Quotations.pdf");
        });

        dd('Email has been sent successfully');


    }

    public function viewquotations($id)
    {
        $getdetails = CompanyQuotation::find($id);

        return view('superadmin.quotation.viewquotations');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CompanyQuotation  $companyQuotation
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $findmobileno = $request->CompanyQuotataion;

        $leadsMob = leads::select("leads.*","users.name")->join("users","users.id","=","leads.user_id")->where('leads.phone',$findmobileno)->orWhere('leads.whatsappno',$findmobileno)->get();
        foreach($leadsMob as $leas)
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

        return view('superadmin.quotation.filterquotations',compact('leadsMob'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CompanyQuotation  $companyQuotation
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyQuotation $companyQuotation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CompanyQuotation  $companyQuotation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyQuotation $companyQuotation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CompanyQuotation  $companyQuotation
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyQuotation $companyQuotation)
    {
        //
    }
}
