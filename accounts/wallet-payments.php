<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"
  media="screen">
<style>
  .select2-container .select2-selection {
    border-radius: 10px;
    height: 48px !important;
    font-size: 17px;
    font-family: system-ui;
  }

  .btn:hover {
    /* background: #2b303b !important; */
    color: white !important;
    font-size: 14px !important;
  }

  .table-hover tbody tr:hover .custom_hover_dot {
    background-color: #d3eeff !important;
  }

  .btn:hover:not(.active) {
    background: #2b303b !important;
  }
</style>
<?php unset($_SESSION['filterByUser']);
unset($_SESSION['filterByDate']); ?>
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
          <div class="d-flex justify-content-between">
            <div class="inner"> <!-- START BREADCRUMB -->
              <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
                <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                for ($i = 1; $i <= count($breadcrumbs); $i++) {
                  if (count($breadcrumbs) == $i):
                    $active = "active";
                    $crumb = explode("?", $breadcrumbs[$i]);
                    echo '<li class="breadcrumb-item ' . $active . '">' . ucwords($crumb[0]) . '</li>';
                  endif;
                }
                ?>
                <div>

                </div>
              </ol>
              <!-- END BREADCRUMB -->
            </div>
            <div class="breadcrumb_btn_custom">
              <?php if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') { ?>
                <a href="/accounts/center-ledger/student-wise-ledger" target="" class="btn custom_add_button text-white p-2 pt-1" aria-label=""
                  title="" data-toggle="tooltip" data-original-title="Go On Ladger"> <i
                    class="uil uil-arrow-left"></i></a>
                <a class="btn custom_add_button text-white p-2 pt-1" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Amount"
                  onclick="add_wallet();"> <i class="uil uil-plus"></i></a>
              <?php } ?>
              <a class="btn custom_add_button p-2" href="/app/wallet-payments/admission-history"><i class="uil uil-history mr-2"></i> Admission History </a>
              <a class="btn custom_add_button text-white p-2" aria-label="" title="Download Wallet Payment" data-toggle="tooltip"
                data-original-title="Download Wallet Payment" onclick="exportData('Wallet-Payment')"> <i
                  class="uil uil-down-arrow mr-2"></i> Download</a>
            </div>
          </div>
        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <div class="card card-transparent">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-md-3 ">
                <div class="form-group mb-0">
                  <div class="input-daterange input-group" id="datepicker-range">
                    <input type="text" class="input-sm form-control  custom_input_st_en" placeholder="Select Date" id="startDateFilter"
                      name="start" />
                    <div class="input-group-addon custom_input_st_en_to">to</div>
                    <input type="text" class="input-sm form-control custom_input_st_en1" placeholder="Select Date" id="endDateFilter"
                      onchange="addDateFilter()" name="end" />
                  </div>
                </div>
              </div>
              <?php if ($_SESSION['Role'] != 'Sub-Center') { ?>
                <div class="col-md-3 ">
                  <div class="form-group mb-0">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="users"
                      onchange="addFilter(this.value, 'users', 2)" data-placeholder="Choose User">

                    </select>
                  </div>
                </div>
                <div class="col-md-3 ">
                  <div class="form-group mb-0">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_center"
                      onchange="addSubCenterFilter(this.value, 'users')" data-placeholder="Choose Sub Center">
                    </select>
                  </div>
                </div>
              <?php } ?>
              <div class="col-md-3  text-end">
                <input type="text" id="payments-search-table" class="form-control pull-right custom_search_section" placeholder="Search">
              </div>
            </div>
            <div class="pull-right">
              <div class="col-xs-12">

              </div>
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
                    <th>Type</th>
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
            'url': '/app/wallet-payments/server'
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
              data: "Type"
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
                var status = '';
                var label_class = '';
                if (row.Type == "Online") {
                  status = data == 0 ? "Failled" : data == 1 ? "Added" : "Rejected";
                  label_class = data == 0 ? "danger" : data == 1 ? "success" : "danger";
                } else {
                  status = data == 0 ? "Pending" : data == 1 ? "Added" : "Rejected";
                  label_class = data == 0 ? "warning" : data == 1 ? "success" : "danger";
                }
                return '<span class="label label-' + label_class + '">' + status + '</span>';
              }
            },
            {
              data: "ID",
              "render": function(data, type, row) {
                // Show dropdown only if Type is not "Online" and Status is 0 (Pending)
                if (row.Type === "Online" || row.Status != 0) {
                  return ''; // Hide the button and dropdown
                }

                var status_button = (role == 'Accountant' || role == 'Administrator') ?
                  '<a class="dropdown-item custom_drpdown_btn" onclick="updatePaymentStatus(\'' + data + '\', \'1\',\'' + row.user_id + '\')"><i class="uil uil-check-circle"></i> Approve</a>\
         <a class="dropdown-item custom_drpdown_btn" onclick="updatePaymentStatus(\'' + data + '\', \'2\')"><i class="uil uil-times-circle"></i> Reject</a>' :
                  '';

                var action_button = (role == 'Accountant' || role == 'Administrator' || role == 'University Head') ?
                  '<a class="dropdown-item custom_drpdown_btn" onclick="edit(\'offline-payments\', \'' + data + '\', \'lg\')"><i class="uil uil-edit"></i> Edit</a>\
         <a class="dropdown-item custom_drpdown_btn" onclick="destroy(\'offline-payments\', \'' + data + '\')"><i class="uil uil-trash"></i> Delete</a>' :
                  '';

                var dropdownContent = status_button + action_button;

                return '<div class="dropdown text-center">\
              <button class="border-0 bg-white custom_hover_dot" type="button" id="dropdownMenuButton' + data + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\
                <i class="uil uil-ellipsis-v"></i>\
              </button>\
              <div class="dropdown-menu dropdown-menu-right custom_drop_div" aria-labelledby="dropdownMenuButton' + data + '">\
                ' + dropdownContent + '\
              </div>\
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
              url: '/app/wallet-payments/update-payment-status',
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

      function exportData(filename) {
        let searchValue = $('#payments-search-table').val()
        $.ajax({
          url: '/app/wallet-payments/server',
          type: 'POST',
          data: {
            draw: 1,
            start: 0,
            'search[value]': searchValue
          },
          success: function(response) {
            var response = JSON.parse(response);
            // console.log(response);
            // return false;
            if (response && response.aaData && response.aaData.length > 0) {
              let headers = ['Transaction ID', 'Gateway ID', 'Mode', 'Bank Name', 'Amount', 'Type', 'Payment By', 'Date', 'Status'];

              let csvData = convertToCSV(response.aaData, headers);
              downloadCSV(csvData, filename + '.csv');
            } else {
              alert("No data available for export.");
            }
          },
          error: function() {
            alert("Error while fetching data.");
          }
        });
      }

      function convertToCSV(data, headers, type) {
        let csvRows = [headers.join(',')]; // Add headers to CSV
        data.forEach(row => {
          var user_name = row.Center_Name + '(' + row.Center_Code + ')';
          var status = '';
          var label_class = '';
          if (row.Type == "Online") {
            status = row.Status == 0 ? "Failled" : row.Status == 1 ? "Added" : "Rejected";
          } else {
            status = row.Status == 0 ? "Pending" : row.Status == 1 ? "Added" : "Rejected";
          }
          csvRows.push([
            `"${row.Transaction_ID}"`,
            `"${row.Gateway_ID}"`,
            `"${row.Payment_Mode}"`,
            `"${row.Bank}"`,
            `"${row.amounts}"`,
            `"${row.Type}"`,
            `"${user_name}"`,
            `"${row.Transaction_Date}"`,
            `"${status}"`,
          ].join(','));
        });
        return csvRows.join('\n');
      }

      function downloadCSV(csvData, filename) {
        let blob = new Blob([csvData], {
          type: 'text/csv'
        });
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
      }

      function addFilter(id, by, page) {
        var wallet_payments = "wallet-payments";
        $.ajax({
          url: '/app/payments/filter',
          type: 'POST',
          data: {
            id,
            by,
            page,
            wallet_payments
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

      function addSubCenterFilter(id, by) {
        var wallet_payments = "wallet-payments";

        var page = 2;
        $.ajax({
          url: '/app/payments/filter',
          type: 'POST',
          data: {
            id,
            by,
            page,
            wallet_payments
          },
          dataType: 'json',
          success: function(data) {

            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
            }

          }
        })
      }

      function addDateFilter() {
        var startDate = $("#startDateFilter").val();
        var endDate = $("#endDateFilter").val();
        var wallet_payments = "wallet-payments";
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
            page,
            wallet_payments

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
    <script>
      function add_wallet() {
        modal = 'md';
        $.ajax({
          url: '/app/wallet-payments/create',
          type: 'GET',
          success: function(data) {
            $('#' + modal + '-modal-content').html(data);
            $('#' + modal + 'modal').modal('show');
          }
        })
      }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>