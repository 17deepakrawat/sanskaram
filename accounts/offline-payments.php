 <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
 <link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
 <link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" media="screen">
 <?php
  unset($_SESSION['filterByUser']);
  unset($_SESSION['filterByDate']);
  ?>
 <style>
   .select2-container .select2-selection {
     border-radius: 10px;
     height: 48px !important;
     font-size: 17px;
     font-family: system-ui;
   }
   .btn:hover{
    background: #2b303b !important;
    color: white !important;
   }
 </style>
 <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
 <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
 <!-- START PAGE-CONTAINER -->
 <div class="page-container ">
   <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
   <!-- START PAGE CONTENT WRAPPER -->
   <div class="page-content-wrapper ">
     <!-- START PAGE CONTENT -->
     <div class="content ">
       <!-- START JUMBOTRON -->
       <div class="jumbotron" data-pages="parallax">
         <div class=" container-fluid sm-p-l-0 sm-p-r-0">
           <div class="inner">
             <!-- START BREADCRUMB -->
             <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
               <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                for ($i = 1; $i <= count($breadcrumbs); $i++) {
                  if (count($breadcrumbs) == $i) : $active = "active";
                    $crumb = explode("?", $breadcrumbs[$i]);
                    echo '<li class="breadcrumb-item ' . $active . '">' . ucwords($crumb[0]) . '</li>';
                  endif;
                }
                ?>
               <div>
                 <a href="/app/payments/export?type=1" target="_blank" class="btn text-white custom_add_button " aria-label="" title="" data-toggle="tooltip" data-original-title="Download"> <i class="uil uil-down-arrow"></i></a>
               </div>
             </ol>
             <!-- END BREADCRUMB -->
           </div>
         </div>
       </div>
       <!-- END JUMBOTRON -->
       <!-- START CONTAINER FLUID -->
       <div class=" container-fluid">
         <!-- BEGIN PlACE PAGE CONTENT HERE -->
         <div class="card card-transparent">
           <div class="card-header">
             <div class="row">
               <div class="col-md-12 d-flex justify-content-start">
                 <div class="col-md-3 m-b-10">
                   <div class="input-daterange input-group" id="datepicker-range">
                     <input type="text" class="input-sm form-control custom_input_st_en" placeholder="Select Date" id="startDateFilter" name="start" />
                     <div class="input-group-addon custom_input_st_en_to">to</div>
                     <input type="text" class="input-sm form-control custom_input_st_en1" placeholder="Select Date" id="endDateFilter" onchange="addDateFilter()" name="end" />
                   </div>
                 </div>
                 <?php if ($_SESSION['Role'] != 'Sub-Center') { ?>
                   <div class="col-md-3 m-b-10">
                     <div class="form-group">
                       <select class="full-width" style="width:40px" data-init-plugin="select2" id="users" onchange="addFilter(this.value, 'users', 2)" data-placeholder="Choose User">

                       </select>
                     </div>
                   </div>
                   <div class="col-md-3 m-b-10">
                     <div class="form-group">
                       <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_center" onchange="addSubCenterFilter(this.value, 'users')" data-placeholder="Choose Sub Center">

                       </select>
                     </div>
                   </div>
                 <?php } ?>
                 <div class="col-md-3 m-b-10">
                   <input type="text" id="payments-search-table" class="form-control pull-right custom_search_section" placeholder="Search">
                 </div>
               </div>
             </div>
             <div class="pull-right">
               <!-- <div class="col-xs-12">
                 <input type="text" id="payments-search-table" class="form-control pull-right" placeholder="Search">
               </div> -->
             </div>
             <div class="clearfix"></div>
           </div>
           <div class="card-body">
             <div class="">
               <table class="table table-hover nowrap " id="payments-table">
                 <thead>
                   <tr>
                     <th>File</th>
                     <th>Transaction ID</th>
                     <th>Gateway ID</th>
                     <th>Mode</th>
                     <th>Bank Name</th>
                     <th>Amount</th>
                     <th>Student</th>
                     <th>Payment By</th>
                     <th>Date</th>
                     <th>Status</th>
                     <th data-orderable="false"></th>
                   </tr>
                 </thead>
               </table>
             </div>
           </div>
         </div>
         <!-- END PLACE PAGE CONTENT HERE -->
       </div>
       <!-- END CONTAINER FLUID -->
     </div>
     <!-- END PAGE CONTENT -->
     <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
     <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
     <script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
     <script type="text/javascript">
       $(function() {
         var role = "<?= $_SESSION['Role'] ?>";
         var showToAdminHeadAccountant = role == 'Administrator' || role == 'University Head' || role == 'Accountant' ? true : false;
         var table = $('#payments-table');

         var settings = {
           'processing': true,
           'serverSide': true,
           'serverMethod': 'post',
           'ajax': {
             'url': '/app/offline-payments/server'
           },
           'columns': [{
               data: "File",
               "render": function(data, type, row) {
                 var file = row.File_Type != 'pdf' ? '<a href="' + data + '" target="_blank"><img src="' + data + '" height="20"></a>' : '<a href="' + data + '" target="_blank">PDF</a>';
                 return file;
               }
             },
             {
               data: "Transaction_ID",
               "render": function(data, type, row) {
                 return '<strong>' + data + '</strong>';
               }
             },
             {
               data: "Gateway_ID"
             },
             {
               data: "Payment_Mode"
             },
             {
               data: "Bank"
             },
             {
               data: "Amount"
             }, {
               data: "Student",
               "render": function(data, type, row) {
                 // console.log(row.Amount);
                 var Std_name = [];
                 var transaction_id = row.Gateway_ID;
                 // console.log(row.Amount);

                 //for(let i=0; i <data.length; i++){
                 //Std_name.push(data[i]);
                 //}
                 // Assuming data is an array of student names
                 if (Array.isArray(data)) {
                   Std_name = data; // If data is already an array, you can assign it directly
                 } else {
                   // If data is not an array, make sure it is handled appropriately
                   // You might need to adjust this part based on the actual structure of your data
                   Std_name.push(data);
                 }
                 return '<strong class="cursor-pointer" onclick="show_students(\'' + transaction_id + '\');">' + data.length + '<span id="sdsds_' + transaction_id + '" data-value="' + Std_name + '"></span></strong>';
               }
             },
             {
               data: "Center_Name",
               "render": function(data, type, row) {
                 return '<strong>' + data + ' (' + row.Center_Code + ')</strong>';
               }
             },
             {
               data: "Transaction_Date"
             },
             {
               data: "Status",
               "render": function(data, type, row) {
                 var label_class = data == 0 ? "warning" : data == 1 ? "success" : "danger";
                 var status = data == 0 ? "Pending" : data == 1 ? "Approved" : "Rejected";
                 return '<span class="label label-' + label_class + '">' + status + '</span>';
               }
             },
             {
               data: "ID",
               "render": function(data, type, row) {
                 var status_button = (role == 'Accountant' || role == 'Administrator') && row.Status == 0 ? '<i class="uil uil-check-circle icon-xs cursor-pointer" data-toggle="tooltip" data-original-title="Approve" title="" onclick="updatePaymentStatus(&#39;' + data + '&#39, &#39;1&#39;)"></i><i class="uil uil-times-circle icon-xs cursor-pointer" data-toggle="tooltip" data-original-title="Reject" title="" onclick="updatePaymentStatus(&#39;' + data + '&#39, &#39;2&#39;)"></i>' : '';
                 var action_button = (role == 'Accountant' || role == 'Administrator' || role == 'University Head') && row.Status != 1 ? '<i class="uil uil-edit icon-xs cursor-pointer" title="Edit" onclick="edit(&#39;offline-payments&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i><i class="uil uil-trash icon-xs cursor-pointer" title="Delete" onclick="destroy(&#39;offline-payments&#39;, &#39;' + data + '&#39)"></i>' : '';
                 return '<div class="button-list text-end">\
              ' + status_button + action_button + '\
            </div>';
               },
               visible: showToAdminHeadAccountant
             }
           ],
           "sDom": "<t><'row'<p i>>",
           "destroy": true,
           "scrollCollapse": true,
           "oLanguage": {
             "sLengthMenu": "_MENU_ ",
             "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
           },
           "aaSorting": [],
           "iDisplayLength": 25,
           "drawCallback": function() {
             $('[data-toggle="tooltip"]').tooltip();
           }
         };

         table.dataTable(settings);

         // search box for table
         $('#payments-search-table').keyup(function() {
           table.fnFilter($(this).val());
         });
       })
     </script>

     <script type="text/javascript">
       function updatePaymentStatus(id, value) {
         Swal.fire({
           title: 'Are you sure?',
           text: "You won't be able to revert this!",
           icon: 'question',
           showCancelButton: true,
           confirmButtonColor: '#3085d6',
           cancelButtonColor: '#d33',
           confirmButtonText: 'Yes'
         }).then((result) => {
           if (result.isConfirmed) {
             $.ajax({
               url: '/app/offline-payments/update-payment-status',
               type: 'POST',
               data: {
                 id,
                 value
               },
               dataType: 'json',
               success: function(data) {
                 if (data.status == 200) {
                   notification('success', data.message);
                   $('#payments-table').DataTable().ajax.reload(null, false);
                 } else {
                   notification('danger', data.message);
                 }
               }
             })
           }
         })
       }
     </script>
     <script>
       $('#datepicker-range').datepicker({
         format: 'dd-mm-yyyy',
         autoclose: true,
         endDate: '0d'
       });
     </script>
     <script>
       if ($("#users").length > 0) {
         $("#users").select2({
           placeholder: 'Choose Center'
         })
         getCenterList('users');
       }

       function addFilter(id, by, page) {
         $.ajax({
           url: '/app/payments/filter',
           type: 'POST',
           data: {
             id,
             by,
             page
           },
           dataType: 'json',
           success: function(data) {
             if (data.status) {
               $('.table').DataTable().ajax.reload(null, false);
               $("#sub_center").html(data.subCenterName);

             }
           }
         })
       }

       function addDateFilter() {
         var startDate = $("#startDateFilter").val();
         var endDate = $("#endDateFilter").val();
         if (startDate.length == 0 || endDate == 0) {
           return
         }
         var id = 0;
         var by = 'date';
         page = 2;
         $.ajax({
           url: '/app/payments/filter',
           type: 'POST',
           data: {
             id,
             by,
             startDate,
             endDate,
             page
           },
           dataType: 'json',
           success: function(data) {
             if (data.status) {
               $('.table').DataTable().ajax.reload(null, false);
             }
           }
         })
       }

       function addSubCenterFilter(id, by) {


         var page = 2;
         $.ajax({
           url: '/app/payments/filter',
           type: 'POST',
           data: {
             id,
             by,
             page
           },
           dataType: 'json',
           success: function(data) {

             if (data.status) {
               $('.table').DataTable().ajax.reload(null, false);
             }

           }
         })
       }
     </script>
     <script>
       function show_students(transaction_id) {
         console.log(transaction_id);
         modal = 'md';
         var sdsds = $('#sdsds_' + transaction_id).attr('data-value');
         $.ajax({
           url: '/app/online-payments/paid-students?ids=' + sdsds,
           type: 'GET',
           success: function(data) {
             $('#' + modal + '-modal-content').html(data);
             $('#' + modal + 'modal').modal('show');
           }
         })

       }
     </script>
     <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>