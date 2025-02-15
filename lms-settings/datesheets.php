<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php');
unset($_SESSION['filterByUniversity']);
unset($_SESSION['subCourseFilter']);
unset($_SESSION['durationFilter']);
unset($_SESSION['usersFilter']);

?>
<style>
  thead tr th {
    font-weight: 700 !important;
  }
</style>
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
                  echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                endif;
              }
              ?>
              <div>
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload"
                  onclick="upload('datesheets', 'lg')"> <i class="uil uil-export"></i></button>
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
                  <?php $get_course = $conn->query("SELECT ID, Name FROM Sub_Courses WHERE Status = 1 AND University_ID = " . $_SESSION['university_id'] . " ORDER BY Name ASC"); ?>
                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_course"
                    onchange="addFilter(this.value, 'sub_course')" data-placeholder="Choose Sub-Courses">
                    <option value="">Select Sub-Courses</option>
                    <?php while ($row = $get_course->fetch_assoc()) { ?>
                      <option value="<?php echo $row['ID']; ?>"><?php echo ucwords(strtolower($row['Name'])); ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="col-md-3 m-b-10">
                <div class="form-group">
                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="duration"
                    onchange="addFilter(this.value, 'duration')" data-placeholder="Choose Duration">

                  </select>
                </div>
              </div>
              <div class="col-md-3"></div>
              <div class="col-md-3">
                <input type="text" id="users-search-table" class="form-control pull-right" placeholder="Search">
              </div>
            </div>
            <div class="clearfix"></div>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="users-table">
                <thead>
                  <tr>
                    <th><b>Exam Session</b></th>
                    <th><b>Paper Code</b></th>
                    <th><b>Subject Name</b></th>
                    <th><b>Exam Date</b></th>
                    <th><b>Time</b></th>
                    <th><b>Duration</b></th>
                    <th><b>Sub-Course Name</b></th>
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
      $(function () {

        var table = $('#users-table');
        var role = '<?= $_SESSION['Role'] ?>';
        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/datesheets/server'
          },
          'columns': [
            {
              data: "Exam_Session",
            },
            {
              data: "Code",
            },
            {
              data: "subject_name",
            },
            {
              data: "exam_date"
            },
            {
              data: "exam_time"
            },
            
            {
              data: "Semester"
            },
            {
              data: "sub_course_name"
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
          "drawCallback": function (settings) {
            $('[data-toggle="tooltip"]').tooltip();
          },
        };

        table.dataTable(settings);
        // search box for table
        $('#users-search-table').keyup(function () {
          table.fnFilter($(this).val());
        });

      })
    </script>

    <script>
      function addFilter(id, by) {

        $.ajax({
          url: '/app/datesheets/filter',
          type: 'POST',
          data: {
            id,
            by
          },
          dataType: 'json',
          success: function (data) {
            const universityId = <?php echo json_encode($_SESSION['university_id']); ?>;
            if (by == "sub_course" && universityId == 47) {
              getDuration(id);
            }

            // if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);

            // }
          }
        })
      }
      
      $(document).ready(function () {
        getDuration();
        
        $("#sub_course").select2({
          placeholder: 'Choose Sub Course',
        })
      })
      function getDuration(id) {

        $.ajax({
          url: '/app/subjects/get-duration',
          data: { id: id },
          type: 'POST',
          success: function (data) {
            $("#duration").html(data);
            addFilter(id);
          }
        })
      }
    </script>


    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>