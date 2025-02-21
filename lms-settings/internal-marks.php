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
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/helpers.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/ClassHelper.php');
unset($_SESSION['current_session']);
unset($_SESSION['filterBySubCourses']);
unset($_SESSION['filterByExamStatus']);
unset($_SESSION['durationFilter']);
unset($_SESSION['subCourseFilter']);
unset($_SESSION['usersFilter']);
unset($_SESSION['filterByDuration']);
unset($_SESSION['filterByVerticalType']);

$sub_course_arr = new ClassHelper();
$sub_courses = $sub_course_arr->getUserSubCourse($conn, $_SESSION['ID'], $_SESSION['Role'], $_SESSION['university_id']);
function verticalTypeFunc()
{
  $verticalType = '<option value="">Select Vertical Type</option>';
  $verticalType .= '<option value="1">Edtech Innovate</option>';
  $verticalType .= '<option value="0">IITS LLP Paramedical</option>';
  return $verticalType;
}

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
              <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i):
                  $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  $marks_type =  "Internal Marks List";
                  echo '<li class="breadcrumb-item ' . $active . '">' . $marks_type . '</li>';
                endif;
              }
              ?>
              <button class="custom_add_button text-white" aria-label="" title="" data-toggle="tooltip"
                data-original-title="Export Internal Marks" onclick="exportData()">Export<i class="uil uil-down-arrow ml-2"></i>
              </button>
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

            <div class="row justify-content-between">
              <div class="col-md-8 m-b-10">
                <div class=" d-flex flex-row">
                  <div class="form-group w-25 mr-3">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_courses"
                      onchange="addFilter(this.value, 'sub_courses')" data-placeholder="Choose Program">
                      <option value="">Choose Program</option>
                      <?php echo $sub_courses;
                      ?>
                    </select>
                  </div>
                  <div class="form-group w-25">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="duration"
                      onchange="addFilter(this.value, 'duration')" data-placeholder="Choose Duration">
                      <option value="">Choose Duration</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <input type="text" id="users-search-table" class="form-control pull-right custom_search_section" placeholder="Search">
              </div>
            </div>
            <div class="clearfix"></div>
          </div>

          <div class="card-body">
            <div class="">
              <table class="table table-hover nowrap" id="users-table">
                <thead>
                  <tr>
                    <th>Student Name</th>
                    <th>Unique ID</th>
                    <th>Enrollment No.</th>
                    <th>Sub-Course Name</th>
                    <th>Duration</th>
                    <th>Centre Name</th>
                    <th class="text-center">Action</th>
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
        $("#vartical_type").select2({
          placeholder: "Choose Vertical Type",
        });

        var table = $('#users-table');
        var role = '<?= $_SESSION['Role'] ?>';
        var uni_id = '<?= $_SESSION['university_id'] ?>';
        let marks_type = (uni_id == 47) ? "Internal" : "External";

        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/results/internl-marks-server'
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
              data: "sub_course_name",
            },

            {
              data: "Duration",

            },
            {
              data: "user_name",
            },

            {
              data: "ID",
              "render": function(data, type, row) {
                var intMarkButton = '<button class="badge border-0  p-2 badge-success cursor-pointer" title="Obtain ' + marks_type + ' Marks" onclick="obtExtMarks(\'' + row.Enrollment_No + '\',\'' + row.Duration + '\',\'' + row.user_code + '\')" ><span style="font-size: 13px;">Obtain ' + marks_type + ' Marks <i class="uil uil-file"></i></span></button>';
                return '<div class="button-list text-end">\
              ' + intMarkButton + '\
            </div>'
              }
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

      function obtExtMarks(enroll, duration, user_code) {
        $.ajax({
          url: '/app/results/create-internal-marks',
          type: 'POST',
          data: {
            enroll: enroll,
            current_duration: duration,
            user_code: user_code
          },
          success: function(data) {
            $('#lg-modal-content').html(data);
            $('#lgmodal').modal('show');
            // }
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
            if (by == "sub_courses") {
              getDuration(id);
            }

            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }

      function getDuration(id) {
        $.ajax({
          url: '/app/subjects/get-duration',
          data: {
            id: id
          },
          type: 'POST',
          success: function(data) {
            $("#duration").html(data);
            // addFilter(id, 'duration');
          }
        })
      }

      function exportData() {
        var url = '';
        var sub_courses = $("#sub_courses").val();
        var search = $('#users-search-table').val();
        var url = search.length > 0 ? "?search=" + search : "";
        window.open('/app/results/export-internal-marks' + url);
      }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>