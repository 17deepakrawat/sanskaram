<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
  .custom_field .select2-container .select2-selection {
    border-radius: 10px;
    height: 48px !important;
    font-size: 17px;
    font-family: system-ui;
  }

  .custom_field .select2-container .select2-selection .select2-selection__arrow {
    top: auto;
    bottom: 11px;
  }

  thead tr th {
    font-weight: 700 !important;
  }

 .custom_field .select2-container .select2-selection {
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
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php');
unset($_SESSION['filterByUniversity']);
unset($_SESSION['subCourseFilter']);
unset($_SESSION['durationFilter']); ?>

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
                  echo '<li class="breadcrumb-item ' . $active . '">' . ucwords($crumb[0]) . '</li>';
                endif;
              }
              ?>
              <div>
                <button class="custom_add_button" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Subject" onclick="add('subjects','lg')">Add <i class="uil uil-plus-circle ml-1"></i></button>

                <button class="custom_add_button" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload"
                  onclick="upload('subjects', 'lg')"> <i class="uil uil-export"></i></button>

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
                <div class="form-group custom_field">
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
                <div class="form-group custom_field">
                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="duration"
                    onchange="addFilter(this.value, 'duration')" data-placeholder="Choose Duration">
                  </select>
                </div>
              </div>
              <div class="col-md-4"></div>
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
                    <th>Code</th>
                    <th>Name</th>
                    <th>Sub-Course Name</th>
                    <th>Duration</th>
                    <th>Min/Max Marks</th>
                    <th>Paper Type</th>
                    <th>Credit</th>
                    <th>Action</th>
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
        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/subjects/server'
          },
          'columns': [{
              data: "Code",
            },
            {
              data: "subject_name",
            },

            {
              data: "sub_course_name"
            },
            {
              data: "Semester"
            },
            {
              data: "Marks",
            },
            {
              data: "Paper_Type"
            },
            {
              data: "Credit"
            },
            {
              data: "ID",
              className: "text-center",
              "render": function(data, type, row) {
                var uni_id = '<?= $_SESSION['university_id'] ?>';
                let downloadSylBtn = "";
                if (row.files != null) {
                  downloadSylBtn = '<a class="dropdown-item" href="..' + row.files + '"><i class="uil uil-down-arrow"></i> Download</a>';
                }

                var deleteBtn = ['Administrator', 'University Head'].includes(role) ?
                  '<a class="dropdown-item custom_drpdown_btn" href="#" onclick="destroy(\'subjects\', \'' + data + '\')"><i class="uil uil-trash"></i> Delete</a>' : '';

                var uploadSylBtn = ['Administrator', 'University Head'].includes(role) ?
                  '<a class="dropdown-item custom_drpdown_btn" href="#" onclick="upload(\'subjects\', \'' + data + '\', \'' + row.Code + '\', \'' + row.subject_name + '\')"><i class="uil uil-upload"></i> Upload</a>' : '';

                var editBtn = '<a class="dropdown-item custom_drpdown_btn" href="#" onclick="edit(\'subjects\', \'' + data + '\', \'lg\')"><i class="uil uil-edit"></i> Edit</a>';

                return `<div class="dropdown text-center">
                       <button class="border-0 bg-white custom_hover_dot" type="button" id="dropdownMenu-${data}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           <i class="uil uil-ellipsis-v"></i>
                       </button>
                       <div class="dropdown-menu dropdown-menu-right custom_drop_div" aria-labelledby="dropdownMenu-${data}">
                           ${downloadSylBtn}
                           ${uploadSylBtn}
                           ${editBtn}
                           ${deleteBtn}
                       </div>
                   </div>`;
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
    </script>

    <script>
      function addFilter(id, by) {
        $.ajax({
          url: '/app/subjects/filter',
          type: 'POST',
          data: {
            id,
            by
          },
          dataType: 'json',
          success: function(data) {
            if (by == "sub_course") {
              getDuration(id);
            }
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }

      $(document).ready(function() {
        getDuration();
        $("#sub_course").select2({
          placeholder: 'Choose Sub Course',
        })
      })

      function getDuration(id) {
        $.ajax({
          url: '/app/subjects/get-duration',
          data: {
            id: id
          },
          type: 'POST',
          success: function(data) {
            $("#duration").html(data);
            addFilter(id);
          }
        })
      }


      function upload(url, id, code, subject_name) {
        var modal = 'md';
        $.ajax({
          url: '/app/' + url + '/upload',
          type: 'POST',
          data: {
            id: id,
            modal: modal,
            code: code,
            subject_name: subject_name
          },
          success: function(data) {
            $('#' + modal + '-modal-content').html(data);
            $('#' + modal + 'modal').modal('show');
          }
        })
      }
    </script>


    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>