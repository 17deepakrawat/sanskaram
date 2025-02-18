<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); 
  unset($_SESSION['filterByVerticalType']);
  ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <style>
    .table thead tr th {
      font-weight: 700;
      font-size: 11.5px;
    }
  </style>
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
              <?php if ($_SESSION['Role'] == 'Administrator') { ?>
                <div>

                  <button class="btn btn-primary p-2 " data-toggle="tooltip" data-original-title="Bulk import"
                    onclick="upload('results', 'lg')"> <i class="uil uil-upload"></i></button>
                </div>
              <?php } ?>
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
            <div class=" row ">
            
              <div class="col-md-9"></div>
              <div class="col-md-3 pull-right">
                <input type="text" id="courses-search-table" class="form-control pull-right" placeholder="Search">
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="courses-table">
                <thead>
                  <tr>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Enrollment No</th>
                    <!-- <th>Duration</th> -->
                    <th>Course Name</th>
                    <th>Published At</th>
                    <th>Action</th>
                    <th>Status</th>
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
        var role = '<?= $_SESSION['Role'] ?>';
        var show = role == 'Administrator' ? true : false;
        var table = $('#courses-table');

        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/results/server'
          },
          'columns': [{
            data: "student_name"
          },
          {
            data: "Unique_ID"
          },
          {
            data: "Enrollment_No"
          },
          // {
          //   data: "duration"
          // },
          {
            data: "subcourse_name"
          },
          {
            data: "published_on",
            visible: show
          },
          {
            data: "ID",
            "render": function (data, type, row) {
              return '<div class="button-list">\
                <a href="/student/examination/marksheet?studentId='+ row.stu_id + '" target="_blank"><i class="uil uil-eye icon-xs cursor-pointer" target="_blank"></i></a>\
              </div>'
            },
            visible: ['Administrator', 'University Head', 'Center', 'Sub-Center'].includes(role) ? true : false
          },
          {
            data: "Exam",
            "render": function (data, type, row) {
              var active = data == 1 ? 'Active' : 'Inactive';
              var checked = data == 1 ? 'checked' : '';
              return '<div class="form-check form-check-inline switch switch-lg success">\
                        <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Exam\')" type="checkbox" ' + checked + ' id="status-switch-' + row.ID + '">\
                        <label for="status-switch-' + row.ID + '">' + active + '</label>\
                      </div>';
            },
            visible: ['Administrator', 'University Head'].includes(role) ? true : false

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
          "iDisplayLength": 25
        };

        table.dataTable(settings);

        // search box for table
        $('#courses-search-table').keyup(function () {
          table.fnFilter($(this).val());
        });

      })
    </script>


    <script type="text/javascript">
      function addFilter(id, by) {
        $.ajax({
          url: '/app/applications/filter',
          type: 'POST',
          data: {
            id,
            by
          },
          dataType: 'json',
          success: function (data) {
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }
     
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>