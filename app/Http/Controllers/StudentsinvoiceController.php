<?php

namespace App\Http\Controllers;
use App\invoices;
use App\students;
use App\Branch;
use App\course;
use App\invoicescourses;
use App\invoicesinstallmentfees;
use App\payment;
use App\leads;
use App\Tax;
use Illuminate\Http\Request;

class StudentsinvoiceController extends Controller
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
    public function create($studentsid,students $students,Branch $branch,course $course,Tax $tax)
    {
        $studentdetails = students::get();
        $getstudents = students::find($studentsid);
        $branchdetails = Branch::get();
        $course = course::get();
        $taxesna = Tax::get();

        return view('marketing.invoice.create',compact('studentdetails','branchdetails','course','taxesna','getstudents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,invoices $invoices,invoicescourses $invoicescourses,invoicesinstallmentfees $invoicesinstallmentfees)
    {
        $sjinvno = "0";
        $mjinvno = "0";
        $waginvno = "0";
        $discoun = "NULL";

        $studentname = $request->sname;
        $idate = $request->invoicedate;
        $ddate = $request->duedate;
        $branchdata = $request->brnach;
        $invno = $request->invno;
        $pmode = $request->paymentmode;
        $dtype = $request->discounttype;
        $subto = $request->subtotal;
        $tot = $request->total;
        $raxe = $request->taxs;
       
        $discoun2 = $request->discount2;

        $inoviceno = explode("/",$invno);
       
        if($inoviceno[0] == 'Inv-BITSJ')
        {
            $sjinvno = $inoviceno[3];

           
        }
        else if($inoviceno[0] == 'Inv-BITMJ')
        {
            $mjinvno = $inoviceno[3];
         
        }
        elseif($inoviceno[0] == 'Inv-BITWG')
        {
            $waginvno = $inoviceno[3];
        }


        if($dtype == "2")
        {
             $discoun = $request->discount1;
        }

        elseif($dtype == "1")
        {
            $discoun = $request->discount2;
        }

        if($pmode == "EMI") 

        {
            $invoicesmodel = new invoices();
                    $invoices = $invoicesmodel->create([
                        'studentid' => $studentname,
                        'branchId' => $branchdata,
                        'branchInvno' => $invno,
                        'sjIno' => $sjinvno,
                        'mjIno' => $mjinvno,
                        'wgIno' => $waginvno,
                        'discounttype' => $dtype,
                        'paymentmode' => $pmode,
                        'invdate' => $idate,
                        'duedate' => $ddate,
                        'subtotal' => $subto,
                        'invtotal' => $tot,
                        'discount' => $discoun,
                        'taxes' => $raxe,

                    ]);

                    $invoicesid = $invoices->id;
                    $coursesdata = $request->invcourse;
                    $courseprice = $request->invprice;
                    $csmode = $request->coursdataemode;
                    $cd = $request->duration;
                    $ct = $request->tax;
                    $installdate = $request->installmentdate;
                    $installprice = $request->installmentprice;
                    $pamount = $request->pendingamount;

                    for($i=0; $i < (count($coursesdata)); $i++)
                    {
                                $invoicescourses = new invoicescourses([
                                
                                'invid' => $invoicesid,
                                'courseid'   => $coursesdata[$i],
                                'coursemode'   => $csmode[$i],
                                'courseprice'   => $courseprice[$i],
                                
                            ]);
                            $invoicescourses->save();
                    }

                    for($k=0; $k <(count($installdate)); $k++)
                    {
                        $invoicesinstallmentfees = new invoicesinstallmentfees([
                            
                            'invoid' => $invoicesid,
                            'invoicedate'   => $installdate[$k],
                            'installmentamount'   => $installprice[$k],
                            'pendinamount'   => $pamount[$k],

                        ]);

                         $invoicesinstallmentfees->save();  
                    }


            return redirect('/view-invoice/'.$invoicesid);

        }

        else
        {


                    $invoicesmodel = new invoices();
                    $invoices = $invoicesmodel->create([
                        'studentid' => $studentname,
                        'branchId' => $branchdata,
                        'branchInvno' => $invno,
                        'sjIno' => $sjinvno,
                        'mjIno' => $mjinvno,
                        'wgIno' => $waginvno,
                        'discounttype' => $dtype,
                        'paymentmode' => $pmode,
                        'invdate' => $idate,
                        'duedate' => $ddate,
                        'subtotal' => $subto,
                        'invtotal' => $tot,
                        'discount' => $discoun,
                        'taxes' => $raxe,

                    ]);

                    $invoicesid = $invoices->id;
                    $coursesdata = $request->invcourse;
                    $courseprice = $request->invprice;
                    $cd = $request->duration;
                    $ct = $request->tax;

                    for($i=0; $i < (count($coursesdata)); $i++)
                    {
                                $invoicescourses = new invoicescourses([
                                
                                'invid' => $invoicesid,
                                'courseid'   => $coursesdata[$i],
                                'coursemode'   => $csmode[$i],
                                'courseprice'   => $courseprice[$i],
                                
                            ]);
                            $invoicescourses->save(); 
                    }

                 
                  
                return redirect('/view-invoice/'.$invoicesid)->with('success','Invoice Created Successfully!!');


        }
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
