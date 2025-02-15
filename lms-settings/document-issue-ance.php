<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
  .select2-container .select2-selection {
    border-radius: 10px;
    height: 48px !important;
    font-size: 17px;
    font-family: system-ui;
  }

  .select2-container .select2-selection .select2-selection__arrow {
    top: auto;
    bottom: 11px;
  }

  .select2-container--open .select2-selection {
    box-shadow: none;
    border: 1px solid #2b303b !important;
  }
  .select2-results .select2-results__option--highlighted {
    background-color: #55638d !important;
    border-radius: 3px;
    color: #ffffff !important;
}
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/helpers.php');
unset($_SESSION['current_session']);
unset($_SESSION['filterBySubCourses']);
unset($_SESSION['filterByExamStatus']); //kp


?>
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
        <div class=" container-fluid   sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php
                     $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                    for ($i = 1; $i <= count($breadcrumbs); $i++) {
                      if (count($breadcrumbs) == $i):
                        $active = "active";
                        $crumb = explode("?", $breadcrumbs[$i]);
                        echo '<li class="breadcrumb-item ' . $active . '">' . ucwords($crumb[0]) . '</li>';

                      endif;
                    }
                    ?>
              <!-- <li class="breadcrumb-item active">Document Issuance </li> -->
              <div>
                <button class="custom_add_button" aria-label="" title="" data-toggle="tooltip"
                  data-original-title="Download Excel" onclick="exportData()"> <i
                    class="uil uil-down-arrow"></i></button>
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
              <div class="col-md-3 m-b-10">
                <div class="form-group">
                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_courses"
                    onchange="addFilter(this.value, 'sub_courses')" data-placeholder="Choose Program">
                    <option value="">Choose Program</option>
                    <?php $programs = $conn->query("SELECT ID, Name FROM Sub_Courses WHERE Status =1 AND University_ID = " . $_SESSION['university_id'] . " order by Name ASC");
                    while ($program = $programs->fetch_assoc()) {
                      echo '<option value="' . $program['ID'] . '">' . $program['Name'] . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-md-3 m-b-10">
                <div class="form-group">
                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="exam_exit_status"
                    onchange="addFilter(this.value, 'exam_exit_status')" data-placeholder="Choose Exit Status">
                    <option value="">Choose Exit Status</option>

                    <?php foreach ($exam_exit_arr as $key => $value) {
                      echo '<option value="' . $key . '">' . $value . '</option>';
                    } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-3 m-b-10"></div>

              <div class="col-md-3">
                <input type="text" id="users-search-table" class="form-control pull-right custom_search_section w-50" placeholder="Search">
              </div>
            </div>
            <div class="clearfix"></div>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="users-table">
                <thead>
                  <tr>
                    <th>Student Name</th>
                    <th>Unique ID</th>
                    <th>Enrollment No.</th>
                    <th>Exam Exit Status</th>
                    <th>Sub-Course Name</th>
                    <th>Duration</th>
                    <th>Father Name</th>
                    <th>Mother Name</th>
                    <th>Centre Name</th>
                    <th>Document Issueance Request Date</th>
                    <th>Issue Document</th>
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
    <script type="text/javascript">
      $(function() {

        var table = $('#users-table');
        var role = '<?= $_SESSION['Role'] ?>';
        var is_operations = role == 'Operations' ? true : false;
        var visibile_status = true;
        if (jQuery.inArray(role, ['Accountant', 'Academic Head', 'Operations', 'Counsellor', 'University Head', 'Sub-Counsellor']) >= 0) {
          var visibile_status = false;
        }

        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/exit-exam-system/server'
          },
          'columns': [{
              data: "full_name",
            },

            {
              data: "Unique_ID",
            },
            {
              data: "Enrollment_No",
            },
            {
              data: "exit_status",
            },
            {
              data: "sub_course_name",
            },

            {
              data: "Duration",
            },
            {
              data: "Father_Name",
            },
            {
              data: "Mother_Name",
            },
            {
              data: "user_name",
            },
            {
              data: "exam_exit_request_date",
            },
            {
              data: "ID",
              "render": function(data, type, row) {
                var disabled = (row.result_uploaded_status == 0) ? "disabled" : '';
                if (row.exam_exit_status == 6) {
                  allotButton = "<b>Student is dropped out</b>";
                } else if (row.exam_exit_status == 1 || row.exam_exit_status == 2 || row.exam_exit_status == 3 || row.exam_exit_status == 4) {
                  allotButton = "<b class='btn btn-danger cursor-pointer'>Document Issue In-Progress</b>";
                } else {
                  var allotButton = '<button class="btn btn-success cursor-pointer" ' + disabled + ' title="Issue Document Button" onclick="issueDocFunc(\'' + row.Enrollment_No + '\',\'' + row.Duration + '\')" ><b>Issue Document</b></button>';
                }
                return '<div class="button-list text-end">\
              ' + allotButton + '\
            </div>';
              },
              visible: visibile_status
            },

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
          "drawCallback": function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
          },
        };

        table.dataTable(settings);

        // search box for table
        $('#users-search-table').keyup(function() {
          table.fnFilter($(this).val());
        });

      })

      function issueDocFunc(enroll, duration) {
        $.ajax({
          url: '/app/exit-exam-system/create',
          type: 'POST',
          data: {
            enroll: enroll,
            current_duration: duration
          },
          success: function(data) {
            data = JSON.parse(data);
            if (data.status == '400') {
              notification('danger', data.message);
            } else if (data.status == '200') {
              notification('success', data.message);
            } else {
              $('#md-modal-content').html(data);
              $('#mdmodal').modal('show');
            }
          }
        })
      }

      function addFilter(id, by) {
        $.ajax({
          url: '/app/applications/filter',
          type: 'POST',
          data: {
            id,
            by
          },
          dataType: 'json',
          success: function(data) {
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }

      function exportData() {
        window.open('/app/exit-exam-system/export');
      }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>