<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php');
  unset($_SESSION['current_session']);
  unset($_SESSION['assign_sub_course_ID']);
  ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <div class="page-content-wrapper">
    <div class="content">
      <div class="jumbotron" data-pages="parallax">
        <div class="container-fluid sm-p-l-0 sm-p-r-0"></div>
      </div>
      <div class="container-fluid">
        <div class="card card-transparent">
          <div class="card-header">
          <div class="d-flex justify-content-start">
            <div class="col-md-2">
              <div class="form-group">
                <select class="form-control" data-init-plugin="select2" id="sessions"
                  onchange="changeSession(this.value)">
                  <option value="All">All</option>
                  <?php
                  $role_query = "";
                  if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') {
                    $role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
                    $role_query = str_replace('{{ column }}', 'Added_For', $role_query);
                  }
                  $sessions = $conn->query("SELECT Admission_Sessions.ID,Admission_Sessions.Name,Admission_Sessions.Current_Status FROM Admission_Sessions LEFT JOIN Students ON Admission_Sessions.ID = Students.Admission_Session_ID WHERE Admission_Sessions.University_ID = '" . $_SESSION['university_id'] . "' $role_query GROUP BY Name ORDER BY Admission_Sessions.ID ASC");
                  while ($session = mysqli_fetch_assoc($sessions)) { ?>
                    <option value="<?= $session['Name'] ?>" <?php print $session['Current_Status'] == 1 ? 'selected' : '' ?>>
                      <?= $session['Name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-md-3 ">
              <div class="form-group">
                <select class="form-control" data-init-plugin="select2" id="sub_courses"
                  onchange="addFilter(this.value, 'sub_courses')" data-placeholder="Choose Sub-Course">
                  <option value="">Choose Sub-Course</option>
                  <?php $programs = $conn->query("SELECT Sub_Courses.ID, CONCAT(Sub_Courses.Name) as Name FROM Sub_Courses WHERE University_ID =  " . $_SESSION['university_id'] . " AND Status = 1");
                  while ($program = $programs->fetch_assoc()) {
                    echo '<option value="' . $program['ID'] . '">' . $program['Name'] . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>
            <div class="pull-right">
              <div class="row">
                <div class="col-xs-7" style="margin-right: 10px;">
                  <input type="text" id="e-book-search-table" class="form-control pull-right p-2 fw-bold" placeholder="Search">
                </div>
                <div class="d-flex">
                  <button class="btn btn-sm btn-primary" aria-label="Add Assignments" data-toggle="tooltip" data-placement="top" title="Add Assignments" onclick="add('assignments','lg')">Create Assignments</button>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="admin_table">
                <thead>
                  <tr>
                
                    <th>Sub Course Name</th>
                    <th>Subject Name</th>
                    <th>Admission Session</th>
                    <th>Semester</th>
                    <th>Assignment Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Created By</th>
                    <th>Marks</th>
                    <th>Updated Date</th>
                    <th>Created Date</th>
                    <th>Download Assignment</th>
                    <th>Action</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  


<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

<script type="text/javascript">
  $(function() {
    var role = '<?= $_SESSION['Role'] ?>';
    var show = role == 'Administrator' ? true : false;
    var table = $('#admin_table');

    var settings = {
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      'ajax': {
        'url': '/app/assignments/admin'
      },
      'columns': [
        {
          data: "sub_course_name"
        },
        {
          data: "subject_name"
        },
        {
          data: "adm_session"
        },
        {
          data: "semester"
        },
        {
          data: "assignment_name"
        },
        {
          data: "start_date"
        },
        {
          data: "end_date"
        },
        {
          data: "created_by"
        },
        {
          data: "marks"
        },
        {
          data: "updated_date"
        },
        {
          data: "created_date"
        },
        {
          data: "file_path",
          render: function(data, type, row) {
            var path = '../../uploads/assignments/';
            var file;
            if (row.File_Type && row.File_Type.toLowerCase() === 'pdf') {
              file = '<a href="' + path + data + '" class="btn btn-success btn-sm" download>Download Assignment</a>';
            } else {
              file = '<a href="' + path + data + '"  class="btn btn-success btn-sm" download>Download Assignment</a>';
            }
            return file;
          }
        },
        {
          data: "Assignment_id",
          "render": function(data, type, row) {
            return '<div class="button-list text-end">\
                <i class="uil uil-edit icon-xs cursor-pointer" title="Edit" onclick="edit(&#39;assignments&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                <i class="uil uil-trash icon-xs cursor-pointer" title="Delete" onclick="destroy(&#39;assignments&#39;, &#39;' + data + '&#39)"></i>\
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
    };
    table.dataTable(settings);
    $('#e-book-search-table').keyup(function() {
      table.fnFilter($(this).val());
    });
  });
</script>
<script type="text/javascript">
    function changeSession(value) {
      $('input[type=search]').val('');
      updateSession();
    }

    function updateSession() {
      var session_id = $('#sessions').val();
      $.ajax({
        url: '/app/applications/change-session',
        data: {
          session_id: session_id
        },
        type: 'POST',
        success: function (data) {
          $('.table').DataTable().ajax.reload(null, false);
        }
      })
    }

    
    function addFilter(id, by, role = null) {
      console.log(id, 'id');
      console.log(by, 'by');
      console.log(role, 'role');
      $.ajax({
        url: '/app/assignments/filter',
        type: 'POST',
        data: {
          id,
          by,
          role
        },
        dataType: 'json',
        success: function (data) {
          console.log(data);
          if (data.status) {
            $('.table').DataTable().ajax.reload(null, false);
            if ('<?= $_SESSION['Role'] ?>' === 'Administrator') {
              $(".sub_center").html(data.subCenterName);
            }
            //$(".sub_center").html(data.subCenterName);
          }
        }
      })
    }
  </script>