<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
  .select2-container .select2-selection.select2-selection--single {
    height: 48px !important;
    border-radius: 10px !important;
  }

  .select2-selection__placeholder {
    font-size: 17px !important;
  }

  .card-transparent {
    border-radius: 10px !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow b {
    top: 35%;
  }

  .div.dataTables_wrapper div.dataTables_length select {
    width: 57px !important;
  }

  .input-sm {
    border-radius: 10px !important;
    height: 48px !important;
  }

  .btn-outline-primary.hover:not(.active),
  .btn-outline-primary:hover:not(.active),
  .btn-outline-primary .show .dropdown-toggle.btn-outline-primary {
    background: #2b303b !important;
    border: none !important;
    color: white !important;
  }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<?php
unset($_SESSION['current_session']);
unset($_SESSION['filterByVerticalType']); //kp
unset($_SESSION['filterByUser']); //kp
unset($_SESSION['filterByVerticalType']); //kp

?>
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
                if (count($breadcrumbs) == $i):
                  $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . ucwords($crumb[0]) . '</li>';
                endif;
              }
              ?>
              <div>
                <?php if (isset($_SESSION['active_rr_session_id'])) { ?>
                  <a class="btn btn-outline-primary btn-sm custom_add_button p-2 text-white"
                    onclick="resetRRSession()">Change
                    Session <i class="uil uil-exchange-alt ml-2"></i></a>
                  <a href="/app/re-registrations/export" target="_blank" class="text-white p-2 custom_add_button">Download
                    <i class="uil uil-down-arrow ml-2"></i></a>
                <?php } ?>
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
        <?php if (!isset($_SESSION['active_rr_session_id'])) { ?>
          <div class="row">
            <?php
            $examSessions = $conn->query("SELECT ID, Name FROM Exam_Sessions WHERE University_ID = " . $_SESSION['university_id'] . " AND (RR_Status = 1 OR RR_Last_Date IS NOT NULL) AND Admission_Session LIKE '%Re-Registrations%'");
            while ($examSession = $examSessions->fetch_assoc()) { ?>
              <div class="col-md-2">
                <div class="card cursor-pointer re_reg_radius"
                  onclick="setRRSession(<?= $examSession['ID'] ?>, '<?= htmlspecialchars($examSession['Name'], ENT_QUOTES) ?>')">
                  <div class="card-body" style="border: none;">
                    <p class="hint-text overline font-weight-bold text-black custom_re_reg_txt">EXAM SESSION</p>
                    <h3 class="fownt-weight-bold text-black custom_re_reg_txt">
                      <?= $examSession['Name'] ?>
                    </h3>
                  </div>
                </div>
              </div>

            <?php }
            ?>
          </div>
        <?php } else { ?>
          <div class="card card-body">
            <div class="row clearfix ">
              <div class="col-md-12 d-flex justify-content-between">
                <h5 class="font-weight-bold text-black">
                  <?= $_SESSION['active_rr_session_name'] ?>
                </h5>
                <div>
                 <button class="btn btn-primary btn-sm payment-btn d-none" onclick="payNow('wallet')">Pay by
                    Wallet</button>
                </div>
              </div>
              <div class="col-md-3 m-b-10">
                <div class="form-group">
                  <select class="full-width" data-init-plugin="select2" id="users"
                    onchange="addFilter(this.value, 'users')" data-placeholder="Choose User">
                  </select>
                </div>
              </div>
              <div class="col-md-3 m-b-10">
                <div class="form-group">
                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_center"
                    onchange="addSubCenterFilter(this.value, 'users')" data-placeholder="Choose Sub Center">
                  </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="card card-transparent">
                  <!-- Nav tabs -->
                  <ul class="nav nav-tabs nav-tabs-linetriangle" data-init-reponsive-tabs="dropdownfx">
                    <li class="nav-item">
                      <a class="active" data-toggle="tab" data-target="#applicableList" href="#"><span>Applicable Students
                          -
                          <span id="applicableCount">0</span></span></a>
                    </li>
                    <li class="nav-item">
                      <a data-toggle="tab" data-target="#pending" href="#"><span>Pending - <span
                            id="pendingCount">0</span></span></a>
                    </li>
                    <li class="nav-item">
                      <a data-toggle="tab" data-target="#applied" href="#"><span>Applied - <span
                            id="appliedCount">0</span></span></a>
                    </li>
                  </ul>
                  <!-- Tab panes -->
                  <div class="tab-content">
                    <div class="tab-pane active" id="applicableList">
                      <div class="row m-t-20">
                        <div class="col-12">
                          <table class="table table-striped nowrap " id="applicableTable">
                            <thead>
                              <tr>
                                <th data-orderable="false"></th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>RR Sem</th>
                                <th>Enrollemnt No</th>
                                <th>Adm Session</th>
                                <th>Course</th>
                                <th>Owner</th>
                              </tr>
                            </thead>
                          </table>
                        </div>

                      </div>
                    </div>
                    <div class="tab-pane" id="pending">
                      <div class="row">
                        <div class="col-lg-12">
                          <div class="">
                            <table class="table table-striped nowrap" id="pendingTable">
                              <thead>
                                <tr>
                                  <th>Student ID</th>
                                  <th>Student Name</th>
                                  <th>RR Sem</th>
                                  <th>Enrollemnt No</th>
                                  <th>Adm Session</th>
                                  <th>Course</th>
                                  <th>Owner</th>
                                  <th>Payment ID</th>
                                </tr>
                              </thead>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane" id="applied">
                      <div class="row">
                        <div class="col-lg-12">
                          <div class="">
                            <table class="table table-striped nowrap" id="appliedTable">
                              <thead>
                                <tr>
                                  <th>Student ID</th>
                                  <th>Student Name</th>
                                  <th>RR Sem</th>
                                  <th>Enrollemnt No</th>
                                  <th>Adm Session</th>
                                  <th>Course</th>
                                  <th>Owner</th>
                                  <th>Payment From</th>
                                  <th>Payment ID</th>
                                </tr>
                              </thead>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        <?php } ?>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script>

      $(document).ready(function () {

        $("#users").select2({
          placeholder: 'Choose Center'
        })
        getCenterList('users');
      })
      function setRRSession(id, name) {
        $.ajax({
          url: '/app/re-registrations/set-session',
          type: 'POST',
          data: {
            id,
            name
          },
          dataType: 'json',
          success: function (data) {
            if (data.status) {
              window.location.reload();
            } else {
              notification('danger', data.message);
            }
          }
        })
      }

      function resetRRSession() {
        $.ajax({
          url: '/app/re-registrations/reset-session',
          type: 'POST',
          dataType: 'json',
          success: function (data) {
            if (data.status) {
              window.location.reload();
            } else {
              notification('danger', data.message);
            }
          }
        })
      }
    </script>

    <script>
      var applicableTable = $('#applicableTable');
      var appliedTable = $('#appliedTable');
      var pendingTable = $("#pendingTable");
      var actionVisibility = <?= $_SESSION['show_action_in_active_rr'] == 0 ? 'false' : 'true' ?>;
      var applicableSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/re-registrations/applicable-server',
          'type': 'POST',
          complete: function (xhr, responseText) {
            $('#applicableCount').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [{
          data: "ID",
          render: function (data, type, row) {
            return '<div class="form-check complete" style="margin-bottom: 0px;">\
                <input type="checkbox" class="student-checkbox" onchange="updatePaymentMethod()" id="student-' + data +
              '" name="student_id" value="' + data + '">\
                <label for="student-' + data + '" class="font-weight-bold"></label>\
              </div>';
          }
        },
        {
          data: "Unique_ID"
        },
        {
          data: "First_Name"
        },
        {
          data: "Duration"
        },
        {
          data: "Enrollment_No"
        },
        {
          data: "Admission_Session_ID"
        },
        {
          data: "Course_ID"
        },
        {
          data: "Added_For"
        }
        ],
        "sDom": "<'row  w-100 p-0 m-0'<'col-sm-6 pr-0 pl-0 custon_text_start'l><'col-sm-6 pr-0 pl-0'f>><t><'row'<p i>>",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
          "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        drawCallback: function (settings, json) {
          $('[data-toggle="tooltip"]').tooltip();
        },
        "aaSorting": []
      };

      var appliedSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/re-registrations/applied-server',
          'type': 'POST',
          complete: function (xhr, responseText) {
            $('#appliedCount').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [{
          data: "Unique_ID"
        },
        {
          data: "First_Name"
        },
        {
          data: "Duration"
        },
        {
          data: "Enrollment_No"
        },
        {
          data: "Admission_Session_ID"
        },
        {
          data: "Course_ID"
        },
        {
          data: "Added_For"
        },
        {
          data: "Payment_From"
        },
        {
          data: "Gateway_ID"
        }
        ],
        "sDom": "<'row mt-3 w-100 p-0 m-0'<'col-sm-6 pr-0 pl-0 custon_text_start'l><'col-sm-6 pr-0 pl-0'f>><t><'row'<p i>>",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
          "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        drawCallback: function (settings, json) {
          $('[data-toggle="tooltip"]').tooltip();
        },
        "aaSorting": []
      };

      var pendingSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/re-registrations/pending-server',
          'type': 'POST',
          complete: function (xhr, responseText) {
            $('#pendingCount').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [{
          data: "Unique_ID"
        },
        {
          data: "First_Name"
        },
        {
          data: "Duration"
        },
        {
          data: "Enrollment_No"
        },
        {
          data: "Admission_Session_ID"
        },
        {
          data: "Course_ID"
        },
        {
          data: "Added_For"
        },
        {
          data: "Gateway_ID"
        }
        ],
        "sDom": "<'row mt-3 w-100 p-0 m-0'<'col-sm-6 pr-0 pl-0 custon_text_start'l><'col-sm-6 pr-0 pl-0'f>><t><'row'<p i>>",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
          "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        drawCallback: function (settings, json) {
          $('[data-toggle="tooltip"]').tooltip();
        },
        "aaSorting": []
      };

      applicableTable.dataTable(applicableSettings);
      pendingTable.dataTable(pendingSettings);
      appliedTable.dataTable(appliedSettings);
    </script>

    <?php  //if ($_SESSION['show_action_in_active_rr'] == 1) { ?>
    <script>
      function applyRR(id) {
        $.ajax({
          url: '/app/re-registrations/apply-single',
          type: 'GET',
          data: {
            id
          },
          success: function (data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          }
        })
      }

      function updatePaymentMethod() {
        var ids = [];
        $('.student-checkbox:checked').each(function () {
          ids.push($(this).val());
        });
        if (ids.length > 0) {
          $('.payment-btn').each(function () {
            $(this).removeClass('d-none');
          });
        } else {
          $('.payment-btn').each(function () {
            $(this).addClass('d-none');
          });
        }
      }

      function payNow(type) {
        var ids = [];
        $('.student-checkbox:checked').each(function () {
          ids.push($(this).val());
        });
        if (ids.length > 0) {
          $.ajax({
            url: '/app/re-registrations/payment-methods/' + type,
            type: 'POST',
            data: {
              ids: ids
            },
            success: function (data) {
              $("#lg-modal-content").html(data);
              $("#lgmodal").modal('show');
            }
          })
        }
      }

      function updateTable() {
        applicableTable.DataTable().ajax.reload(null, false);
        pendingTable.DataTable().ajax.reload(null, false);
        appliedTable.DataTable().ajax.reload(null, false);
      }
    </script>
    <script>


      function addFilter(id, by) {
        $.ajax({
          url: '/app/applications/filter',
          type: 'POST',
          data: {
            id,
            by,
          },
          dataType: 'json',
          success: function (data) {
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
              $("#sub_center").html(data.subCenterName);

              if ('<?= $_SESSION['Role'] ?>' == 'Administrator') {
                $(".sub_center").html(data.subCenterName);
                // if(by=='users'){
                //   getCenterListVerticalType(id, by, type);
                // }
              }
            }
          }
        })
      }



      function getCenterListVerticalType(id, by, vertical_type) {
        $.ajax({
          url: '/app/students/center-list?vertical_type=vertical_type&type=' + id + '&id=' + by,
          type: 'GET',
          success: function (data) {
            // alert(by);
            $("#" + by).html(data);
          }
        })
      }

      function addSubCenterFilter(id, by) {
        $.ajax({
          url: '/app/re-registrations/filter',
          type: 'POST',
          data: {
            id,
            by
          },
          dataType: 'json',
          success: function (data) {

            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
              // $  ("#sub_center").html(data.subCenterName);
            }

          }
        })
      }
    </script>
    <?php //} ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>