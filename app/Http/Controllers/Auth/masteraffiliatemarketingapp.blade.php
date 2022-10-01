<!doctype html>
<html lang="en">
<head>

        <meta charset="utf-8" />
        <title>CRM | BIT INFOTECH </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesbrand" name="author" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

        <!-- plugin css -->
        <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
        
        <!-- Bootstrap Css -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

         <!-- SweetAlert Css-->
        <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />


        <!--Datatables-->

        <!-- DataTables -->
        <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

         <link href="{{ asset('https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css') }}" rel="stylesheet">
        
        <link rel="stylesheet" href="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css') }}" type="text/css">

         <!-- Responsive Table css -->
        <link href="{{ asset('assets/libs/admin-resources/rwd-table/rwd-table.min.css') }}" rel="stylesheet" type="text/css" />
        <style>
ul.breadcrumb {
  padding: 10px 16px;
  list-style: none;
  background-color: #eee;
}
ul.breadcrumb li {
  display: inline;
 
}
ul.breadcrumb li+li:before {
  padding: 8px;
  color: black;
  content: "/\00a0";
}
ul.breadcrumb li a {
  color: #0275d8;
  text-decoration: none;
}
ul.breadcrumb li a:hover {
  color: #01447e;
  text-decoration: underline;
}
</style>
    </head>

    <body data-sidebar="dark">

        <!-- Begin page -->
        <div id="layout-wrapper">

            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="20">
                                </span>
                            </a>

                            <a href="" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="20">
                                </span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>

                        <!-- App Search-->
                        <form class="app-search d-none d-lg-block">
                            <div class="position-relative">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="uil-search"></span>
                            </div>
                        </form>
                    </div>

                    <div class="d-flex">

                        <div class="dropdown d-inline-block d-lg-none ms-2">
                            <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="uil-search"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-search-dropdown">
                    
                                <form class="p-3">
                                    <div class="m-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="dropdown d-inline-block language-switch">
                            <button type="button" class="btn header-item waves-effect"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{{ asset('assets/images/flags/us.jpg') }}" alt="Header Language" height="16">
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                    
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <img src="{{ asset('assets/images/flags/spain.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">Spanish</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <img src="{{ asset('assets/images/flags/germany.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">German</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <img src="{{ asset('assets/images/flags/italy.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">Italian</span>
                                </a>

                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item notify-item">
                                    <img src="{{ asset('assets/images/flags/russia.jpg') }}" alt="user-image" class="me-1" height="12"> <span class="align-middle">Russian</span>
                                </a>
                            </div>
                        </div>

                        <div class="dropdown d-none d-lg-inline-block ms-1">
                            <button type="button" class="btn header-item noti-icon waves-effect"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="uil-apps"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                                <div class="px-lg-2">
                                    <div class="row g-0">
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="#">
                                                <img src="{{ asset('assets/images/brands/github.png') }}" alt="Github">
                                                <span>GitHub</span>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="#">
                                                <img src="{{ asset('assets/images/brands/bitbucket.png') }}" alt="bitbucket">
                                                <span>Bitbucket</span>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="#">
                                                <img src="{{ asset('assets/images/brands/dribbble.png') }}" alt="dribbble">
                                                <span>Dribbble</span>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="row g-0">
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="#">
                                                <img src="{{ asset('assets/images/brands/dropbox.png') }}" alt="dropbox">
                                                <span>Dropbox</span>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="#">
                                                <img src="{{ asset('assets/images/brands/mail_chimp.png') }}" alt="mail_chimp">
                                                <span>Mail Chimp</span>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a class="dropdown-icon-item" href="#">
                                                <img src="{{ asset('assets/images/brands/slack.png') }}" alt="slack">
                                                <span>Slack</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown d-none d-lg-inline-block ms-1">
                            <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                                <i class="uil-minus-path"></i>
                            </button>
                        </div>

                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="uil-bell"></i>
                                <span class="badge bg-danger rounded-pill">3</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-notifications-dropdown">
                                <div class="p-3">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="m-0 font-size-16"> Notifications </h5>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#!" class="small"> Mark all as read</a>
                                        </div>
                                    </div>
                                </div>
                                <div data-simplebar style="max-height: 230px;">
                                    <a href="#" class="text-reset notification-item">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-xs">
                                                    <span class="avatar-title bg-primary rounded-circle font-size-16">
                                                        <i class="uil-shopping-basket"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">Your order is placed</h6>
                                                <div class="font-size-12 text-muted">
                                                    <p class="mb-1">If several languages coalesce the grammar</p>
                                                    <p class="mb-0"><i class="mdi mdi-clock-outline"></i> 3 min ago</p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="text-reset notification-item">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                <img src="assets/images/users/avatar-3.jpg" class="rounded-circle avatar-xs" alt="user-pic">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">James Lemire</h6>
                                                <div class="font-size-12 text-muted">
                                                    <p class="mb-1">It will seem like simplified English.</p>
                                                    <p class="mb-0"><i class="mdi mdi-clock-outline"></i> 1 hours ago</p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="text-reset notification-item">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-xs">
                                                    <span class="avatar-title bg-success rounded-circle font-size-16">
                                                        <i class="uil-truck"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">Your item is shipped</h6>
                                                <div class="font-size-12 text-muted">
                                                    <p class="mb-1">If several languages coalesce the grammar</p>
                                                    <p class="mb-0"><i class="mdi mdi-clock-outline"></i> 3 min ago</p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>

                                    <a href="#" class="text-reset notification-item">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                <img src="assets/images/users/avatar-4.jpg" class="rounded-circle avatar-xs" alt="user-pic">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">Salena Layfield</h6>
                                                <div class="font-size-12 text-muted">
                                                    <p class="mb-1">As a skeptical Cambridge friend of mine occidental.</p>
                                                    <p class="mb-0"><i class="mdi mdi-clock-outline"></i> 1 hours ago</p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2 border-top">
                                    <div class="d-grid">
                                        <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                                            <i class="uil-arrow-circle-right me-1"></i> View More..
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="rounded-circle header-profile-user" src="{{ asset('assets/images/users/avatar-4.jpg') }}"
                                    alt="Header Avatar">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium font-size-15">{{ Auth::user()->name }}</span>
                                <i class="uil-angle-down d-none d-xl-inline-block font-size-15"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a class="dropdown-item" href="#"><i class="uil uil-user-circle font-size-18 align-middle text-muted me-1"></i> <span class="align-middle">View Profile</span></a>
                                <!-- <a class="dropdown-item" href="#"><i class="uil uil-wallet font-size-18 align-middle me-1 text-muted"></i> <span class="align-middle">My Wallet</span></a>
                                <a class="dropdown-item d-block" href="#"><i class="uil uil-cog font-size-18 align-middle me-1 text-muted"></i> <span class="align-middle">Settings</span> <span class="badge bg-soft-success rounded-pill mt-1 ms-2">03</span></a>
                                <a class="dropdown-item" href="#"><i class="uil uil-lock-alt font-size-18 align-middle me-1 text-muted"></i> <span class="align-middle">Lock screen</span></a> -->
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="uil uil-sign-out-alt font-size-18 align-middle me-1 text-muted"></i> <span class="align-middle">Sign out</span></a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                            </div>
                        </div>

                        <!-- div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                                <i class="uil-cog"></i>
                            </button>
                        </div> -->
            
                    </div>
                </div>
            </header>

            <!-- ========== Left Sidebar Start ========== -->
            <div class="vertical-menu">

                <!-- LOGO -->
                <div class="navbar-brand-box">
                    <a href="/home" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="20">
                        </span>
                    </a>

                    <a href="/home" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="20" width="180">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

                <div data-simplebar class="sidebar-menu-scroll">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->
                        <ul class="metismenu list-unstyled" id="side-menu">
                            <li class="menu-title">Menu</li>

                            <li>
                                <a href="/affiliate-user-home">
                                    <i class="uil-home-alt"></i><!-- <span class="badge rounded-pill bg-primary float-end">01</span> -->
                                    <span>Dashboard</span>
                                </a>
                            </li>

                           <!--  <li>
                                <a href="javascript: void(0);" class="has-arrow waves-effect">
                                    <i class="uil-user"></i>
                                    <span>Students</span>
                                </a>
                                <ul class="sub-menu" aria-expanded="true">
                                   
                                            <li><a href="/create-student">Add Students</a></li>
                                            <li><a href="/student">Manage Students</a></li>
                                </ul>
                            </li>
 -->
                            <li>
                                <a href="/leads">Leads</a>
                                    <i class="fa fa-tty"></i>
                                    <span>Leads</span>
                                </a>

                               
                                
                            </li>
                             

                            
                        

                        </ul>
                    </div>
                    <!-- Sidebar -->
                </div>
            </div>
            <!-- Left Sidebar End -->

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">



                   @yield('content')
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->


        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- JAVASCRIPT -->
        <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
        <script src="{{ asset('assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
         <script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>
         <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>

        <!-- apexcharts -->
        <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

        <script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>

        <!-- App js -->
        <script src="{{ asset('assets/js/app.js') }}"></script>

        <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>

        <!-- Sweet Alert Js -->
        <script src="{{ asset('assets/js/sweetalert2.js') }}"></script>
        <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>

        <!--Data table-->
        <!-- Required datatable js -->
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <!-- Buttons examples -->
        <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
        <script src="{{ asset('assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
        <script src="{{ asset('assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

          <!-- Datatable init js -->
        <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
        <!-- Responsive Table js -->
        <script src="{{ asset('assets/libs/admin-resources/rwd-table/rwd-table.min.js') }}"></script>

        <!-- Repeater  -->
        <script src="{{ asset('assets/libs/jquery.repeater/jquery.repeater.min.js')}}"></script>

        <script src="{{ asset('assets/js/pages/form-repeater.int.js')}}"></script>

        <!-- Init js -->
        <script src="{{ asset('assets/js/pages/table-responsive.init.js') }}"></script>

        <script type="text/javascript">

    $(document).ready(function() {
        $('body').removeClass('swal2-height-auto');
    } );

    /** loading overlay start */
    function showLoading() {
        $('.loading').css('display', 'block');
    }

    function hideLoading() {
        $('.loading').css('display', 'none');
    }

    /** loading overlay end */


            
    @if(Session::has('success'))
    swal({
        title: "Success",
        text: "{{ Session::get('success') }}",
        type: 'success'
    });
    @endif

    {{--@if(Session::has('socket'))
            // alert('Socket in session');
        socket.emit('notification', {
            userId: "{{ Session::get('socket') }}"

        });
    @endif--}}

    @if(Session::has('error'))

    swal({
        title: "Oops...!",
        text: "{{ Session::get('error') }}",
        type: 'error'
    });
    @endif

  


          $(document).ready(function() {
        $('select[name="ccategory"]').on('change', function() {
            var categoryID = $(this).val();
            if(categoryID) {
                $.ajax({
                    url: '/coursecategory/'+categoryID,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {

                        
                        $('select[name="csubcategory"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="csubcategory"]').append('<option value="'+ value.id +'">'+ value.subcategory_name +'</option>');
                        });


                    }
                });
            }
        });
    });


  $(document).ready(function(){
                
                // Comment or remove the below change event code if you want send AJAX request from external script file
                $('#validationCustom04').change(function(){
                    var branchdata = $(this).val();

                    //alert(branchdata);
                    $.ajax({
                        type: 'GET',
                        cache: false,
                        dataType: 'json',
                        url: '/enrollmentdata/'+branchdata,
                        success: function(data){
                           console.log(data);

                           $('#validationCustom05').val(data);
                           
                        }
                    });


                });
            });

  $(document).ready(function(){

        $('#usercategory').change(function(){

            var userCategory = $(this).val();

            //alert(userCategory);
            $.ajax({

                type: 'GET',
                cache: false,
                dataType: 'json',
                url: '/assign-user-categorywise/'+userCategory,
                success: function(usercategorydata)
                {
                    //console.log(usercategorydata);

                      $('select[name="assignedtargetto"]').empty();
                        $.each(usercategorydata, function(key, value) {
                           /* $('select[name="assignedtargetto"]').append('<input type="checkbox" name="nse" value="'+value.name+'"><label class="form-label">"'+value.name+'"</label>');*/
                            /*$('select[name="assignedtargetto"]').append('<option value="All">All</option>');*/
                            $('select[name="assignedtargetto[]"]').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                        });
                }
            })


        })

  })

 $(document).ready(function(){
                
                // Comment or remove the below change event code if you want send AJAX request from external script file
                $('#studentslead').change(function(){
                    var studentdata = $(this).val();

                       
                    //alert(studentdata);
                    $.ajax({
                        type: 'GET',
                        cache: false,
                        dataType: 'json',
                        url: '/student-data/'+studentdata,
                        success: function(data){
                           //console.log(data);
                          // console.log(data[0].email);

                        //console.log(data);

                         
                          $('#emails').val(data[0].email);
                          $('#phoneno').val(data[0].phone);
                          $('#aaddress').val(data[0].address);
                          $('#city').val(data[0].city);
                          $('#state').val(data[0].state);
                          $('#zipcode').val(data[0].zipcode);
                          $('#validationCustom04').val(data[0].branch);
                        }
                    });


                });
            });







function deleteInvoicetable(r)
{

    var i = r.parentNode.parentNode.rowIndex;
    //alert(i);
    document.getElementById("invoicetable").deleteRow(i);
}

function deleteEmiTable(r)
{
     var i = r.parentNode.parentNode.rowIndex;
    //alert(i);
    document.getElementById("installmenttable").deleteRow(i);
}

    /*  $(document).ready(function(){
                
                // Comment or remove the below change event code if you want send AJAX request from external script file


                $('#courses').change(function(){
                    var dataId = $(this).parent().parent();
                    var courseId = dataId.find('#courses').val();

                    //alert(courseId);

                    //alert(branchdata);
                    $.ajax({
                        type: 'GET',
                        cache: false,
                        dataType: 'json',
                        url: '/course-price/'+courseId,
                        success: function(response){
                           console.log(response);

                          $('#price').val(response[0].courseprice);
                         // $('#totalprice').val(response[0].courseprice);
                         $('#totalprice').val(response[0].courseprice);
                           
                        }
                    });

            


                });
            });*/
$(document).ready(function(){
    $('#coursemods').change(function(){
         var dataId = $(this).parent().parent();
                    var courseId = dataId.find('#coursemods').val();
         var cours =  $('#courses').val();
                //alert(courseId);
                $.ajax({

                        type: 'GET',
                        cache: false,
                        dataType: 'json',
                        url: '/course-online-offline-price/'+courseId+'/'+cours,

                        success: function(courseprices)
                    {
                        //console.log(courseprices);
                         $('#price').val(courseprices);
                        
                    }

                })



    })


})


    function update_price(e) 
    {
                  
 
          var price = 0;                          
          $('option:selected', $(e)).each(function() {
                              // ^ add this
            console.log($(this).data('price'));
            // sum price for selected items
            price += $(this).data('price');

          });
          $('.lvalue').val(price);


          var duration = 0;
           $('option:selected', $(e)).each(function() {
                              // ^ add this
            console.log($(this).data('duration'));
            // sum price for selected items
             var check =  $(this).data('duration')
            
            duration = Math.max(check);
            

          });

        $('.lduration').val(duration);

       /* var pdf = "";

         $('option:selected', $(e)).each(function() {
                              // ^ add this
            console.log($(this).data('pdf'));
            // sum price for selected items
             var pdfsdata =  $(this).data('pdf')

             var firstpart = "/brocheure/";
             var secondpart = pdfsdata;

             //console.log(pdfsdata);
             //window.location.href= firstpart + secondpart;

             window.open(firstpart + secondpart,'_blank');
           

          });*/

         var website = "";
          $('option:selected', $(e)).each(function() {
                              // ^ add this
            console.log($(this).data('link'));
            // sum price for selected items
             var website =  $(this).data('link')

             //var newwebsitedata = website;

             
                document.getElementById('webdata').style.display="";
                document.getElementById('websit').innerHTML=website;
                
           

          });
    }

/*$(document).ready(function(){

        $('#snames').change(function(){

            var studentId = $(this).val();

            $.ajax({

                    type : 'GET',
                    cache:false,
                    dataType: 'json',
                    url: '/invoice-student/'+studentId,
                    success: function(ajaxdata)
                    {
                        console.log(ajaxdata);
                        document.querySelector('#streetdata').innerHTML = ajaxdata.street;
                        document.querySelector('#citydata').innerHTML =   ajaxdata.city;
                        document.querySelector('#statedata').innerHTML = ajaxdata.state;
                        document.querySelector('#zipcodedata').innerHTML = ajaxdata.zipcode;
                      

                       
                    }
             })

        })
    })*/

    $(document).ready(function(){

        $('#snames').change(function(){

            var studentsId = $(this).val();

            //alert(studentsId);

            $.ajax({
                type: 'GET',
                cache: false,
                dataType: 'json',
                url: '/invoice-student/'+studentsId,
                success: function(ajaxdata)
                    {
                        //console.log(ajaxdata);
                        document.querySelector('#streetdata').innerHTML = ajaxdata[0].street;
                        document.querySelector('#citydata').innerHTML =   ajaxdata[0].city;
                        document.querySelector('#statedata').innerHTML = ajaxdata[0].state;
                        document.querySelector('#zipcodedata').innerHTML = ajaxdata[0].zipcode;
                      

                       
                    }
            })
        })
    })

$(document).ready(function(){

    $('#templatesfor').change(function(){

       // alert('cale');

            var templatesid = $(this).val();

            $.ajax({

                type : 'GET',
                cache: false,
                dataType: 'json',
                url:"/message-data/"+templatesid,
                success: function(templatesdata)
                    {
                        console.log(templatesdata);
                            
                        //document.querySelector('#messatemplates').innerHTML = templatesdata;
                         $('#messagse').val(templatesdata);
                        
                    }

            })

    })
})


$(document).ready(function(){

        $('#invosbrnach').change(function(){

            var branchId = $(this).val();

            $.ajax({

                    type : 'GET',
                    cache:false,
                    dataType: 'json',
                    url: '/branchewise-invoiceno/'+branchId,
                    success: function(branchdata)
                    {
                        console.log(branchdata);

                        $('#invno').val(branchdata)
                       
                       /* $('#streetdata').val(ajaxdata[0].street);
                        $('#citydata').val(ajaxdata[0].city);
                        $('#statedata').val(ajaxdata[0].state);
                        $('#zipcodedata').val(ajaxdata[0].zipcode);*/
                    }
             })

        })
    })


$(document).ready(function(){

    $('#directbranchdata').change(function(){


        var directbranchdata = $(this).val();

        $.ajax({

            type: 'GET',
            cache: false,
            url: '/directbrancherno/'+directbranchdata,

              success: function(directdata)
                    {
                        //console.log(branchdata);

                        $('#enrollementnosnw').val(directdata)
                       
                       /* $('#streetdata').val(ajaxdata[0].street);
                        $('#citydata').val(ajaxdata[0].city);
                        $('#statedata').val(ajaxdata[0].state);
                        $('#zipcodedata').val(ajaxdata[0].zipcode);*/
                    }

        })

        //alert(directbranchdata);
    })


})


/*$(document).ready(function(){

    $('#invcourses').change(function(){

        var invcourseId = $(this).val();

        $.ajax({

                type : 'GET',
                cache: false,
                dataType: 'json',
                url: '/coursedetails/'+invcourseId,
                success: function(coursedetails)
                {
                    //console.log(coursedetails);

                    $('#invprice').val(coursedetails[0].courseprice);
                    $('#check').val(coursedetails[0].courseprice);
                        document.getElementById('totalpruice').innerHTML = coursedetails[0].courseprice;
                        document.getElementById('subtotaldata').innerHTML = coursedetails[0].courseprice;
                    $('#subtotal').val(coursedetails[0].courseprice);
                    $('#total').val(coursedetails[0].courseprice);
                }

        }) 

    })

})*/

$(document).ready(function(){

    $('#coursemode').change(function(){

        var corsemodeve = $(this).val();
         var cours =  $('#invcourses').val();

         //alert(corsemodeve);
         $.ajax({

                type : 'GET',
                cache: false,
                dataType: 'json',
                url: '/coursedetails/'+corsemodeve+'/'+cours,

                success: function(coursedetails)
                {
                    console.log(coursedetails);

                    $('#invprice').val(coursedetails);
                    $('#check').val(coursedetails);
                        document.getElementById('totalpruice').innerHTML = coursedetails;
                        document.getElementById('subtotaldata').innerHTML = coursedetails;
                    $('#subtotal').val(coursedetails);
                    $('#total').val(coursedetails);
                }

         })



    })
})





function CheckPrice()
{
     var courseInvId = document.getElementById('invoicescourse').value;
      $.ajax({
                        type: 'GET',
                        cache: false,
                        dataType: 'json',
                        url: '/multipleinvcourse/'+courseInvId,
                        success: function(successdata){
                           console.log(successdata);

                            $('#dprice').val(successdata[0].courseprice);
                             document.getElementById('calculatedprice').innerHTML = successdata[0].courseprice;
                             $('#tcheck').val(successdata[0].courseprice);
                          //$('#totalprice').val(datas[0].courseprice);
                           
                        }
                    });


     

      
      //console.log(price);
    
     // price += datassf;

  
    //alert(dataid);


}





function calculateDiscount()
{
    var subtotal = document.getElementById('subtotal').value;
    var discount = document.getElementById('discount').value;

     var totalValue = subtotal * ( (100-discount) / 100 )
     //document.getElementById("discount").value = totalValue.toFixed(2)
     document.getElementById("total").value = totalValue.toFixed(2)

   // alert(subtotal);
}

function changeDiscountype()
{
    //alert('called');
    var i = document.getElementById('discounttype').value;

   // alert(i);

   if (i == 1)
   {
        document.getElementById('flat').style.display = "";
        document.getElementById('Percentagedisc').style.display = "none";
   }
   else
   {
        document.getElementById('flat').style.display = "none";
        document.getElementById('Percentagedisc').style.display = "";
   }


}

function CalculateFlatDiscount()
{
    var subtotal = document.getElementById('subtotal').value;
    var flatdiscount = document.getElementById('flatdiscount').value;
    //alert(subtotal);

    var finaltotalvalue = parseInt(subtotal) - parseInt(flatdiscount);
    $('#total').val(finaltotalvalue);


}


function Ammountceivedfunction()
{
    //alert('called');
    var p = document.getElementById('payment').value;
    var t = document.getElementById('paymentrecived').value;

     var finaltotalvalue = parseInt(p) - parseInt(t);
     $('#ramount').val(finaltotalvalue);

}





function CalculatePrice()
{
   // alert('called');

   var price1 = document.getElementById('check').value;
   var price2 = document.getElementById('dprice').value;

   var totlav = parseInt(price1) + parseInt(price2);
     document.getElementById('subtotaldata').innerHTML = totlav;
                    $('#subtotal').val(totlav);
                    $('#total').val(totlav);
                    $('#check').val(totlav);


}
function EMiPayament()
{
    //alert('called');
    
    var emioptions = document.getElementById('emeipa').value;
    //alert(emioptions);

    if(emioptions == "EMI")
    {
        document.getElementById('emidata').style.display="";
    }
    else
    {
         document.getElementById('emidata').style.display="none";
    }
    

}

function FollowupFunction()
{
    var fols = document.getElementById('followupstatus').value;
   // alert(fols);

    if(fols == "Followups")
    {
        document.getElementById('followupdate').style.display="";
    }
    else
    {
        document.getElementById('followupdate').style.display="none";
    }
}

function installmentfees()
{
    var totalfees = document.getElementById('total').value;
    var firstinstallment = document.getElementById('installmentprice').value;

    var installmenttotal = parseInt(totalfees) - parseInt(firstinstallment);
    $('#pendingamount').val(installmenttotal);
    $('#pnddaamo').val(installmenttotal);


}


function nxtinstallmentFees()
{
    var nxttotalvalues = document.getElementById('pnddaamo').value;
    var latestinstal  = document.getElementById('ipric').value;
    var nxtainstallmenttotal = parseInt(nxttotalvalues) - parseInt(latestinstal);
    $('#pendamske').val(nxtainstallmenttotal);
    $('#pnddaamo').val(nxtainstallmenttotal);


}

function banksdata()
{
    //alert('called');
    var bankpayment = document.getElementById('paymentsmodesdata').value;

    if(bankpayment == 'Bank')
    {

        document.getElementById('bankpaymentdata').style.display="";
    }

    else
    {
        document.getElementById('bankpaymentdata').style.display="none";
    }

   // alert(bankpayment);

}

function DateChange()
{
    

     var startDate = new Date(document.getElementById('chqdate').value);
  var today = new Date();
  if (startDate.getTime() > today.getTime()) {
    $('#chequetype').val('PDC Cheque');
  }
  else
  {
    $('#chequetype').val('Current Cheque');
  }  
}


function DiscountAmont()
{
   // alert('called');

   var discountypes = document.getElementById('dtype').value;

   if(discountypes == 'Fix Amount')
   {
        document.getElementById('fixamoint').style.display="";
        document.getElementById('per').style.display="none";
   }
   else
   {
         document.getElementById('fixamoint').style.display="none";
         document.getElementById('per').style.display="";
   }
}

function Linkedto()
{
    var courseslink = document.getElementById('lto').value;

   // alert(courseslink);

        if(courseslink == 'Link to Course')
        {
            document.getElementById("couslin").style.display="";
            document.getElementById('categorylink').style.display="none";
        }

        else if(courseslink == 'Link to Category')
        {
            document.getElementById("couslin").style.display="none";
            document.getElementById('categorylink').style.display="";
        }
        else
        {
            document.getElementById("couslin").style.display="none";
            document.getElementById('categorylink').style.display="none";
        }
}

 function newDivHere() {
document.getElementById("studentselect").style.display="none";
document.getElementById("operation1").style.display="";
}

function CalculateTaxedAmount()
{
   // alert('called');



var subtotal = document.getElementById('subtotal').value;
var taxRate = document.getElementById('taxs').value;

if(taxRate!=0)
{

/*alert(taxRate);*/
var tot_price = subtotal  * taxRate;
var finaltotwgst = parseInt(subtotal) +  parseInt(tot_price);
   $('#total').val(finaltotwgst);
}

else
{
    $('#total').val(subtotal);
}


}


        </script>
    </body>
</html>


