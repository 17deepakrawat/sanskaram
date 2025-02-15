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
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload" onclick="add('subjects', 'lg')"> <i class="uil uil-export"></i></button>
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
<?php $col_clsss =  ($_SESSION['university_id'] == '48') ? '2' :'3';  ?>
              <div class="col-md-<?= $col_clsss ?> m-b-10">
                <div class="form-group">
                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="duration"
                    onchange="addFilter(this.value, 'duration')" data-placeholder="Choose Duration">

                  </select>
                </div>
              </div>
              <?php if ($_SESSION['university_id'] == '48') { ?>
              <div class="col-md-2">
                  <div class="form-group ">
                    <?php $center = $conn->query("SELECT Users.ID, Users.Name, Users.Code FROM Alloted_Center_To_Counsellor as ac LEFT JOIN Users on ac.Code = Users.ID  WHERE Users.Role='Center' AND University_ID = '".$_SESSION['university_id']."' "); ?>
                        <select class="full-width" style="width:40px" id="center" onchange="addFilter(this.value,'users'); removeTable()">
                        <option value="">Choose Center</option> 
                        <?php while($row = $center->fetch_assoc()){?>
                            <option value="<?=$row['ID'] ?>"><?= ucwords(strtolower($row['Name'])).'('.$row['Code'].')' ?></option>
                          <?php } ?>

                        </select>
                  </div>
              </div>
            <?php } ?>

              <?php if ($_SESSION['university_id'] == '47') { ?>
              <div class="col-md-3"></div>
              <?php } ?>

              <?php if ($_SESSION['university_id'] == '48' && $_SESSION['Role'] =="Administrator") { ?>
                <div class="col-md-2">
                            <div class="form-group">
                                <select class="form-control" name="exam_type" id="exam_type" onchange="addFilter(this.value,'subjects');">
                                    <option value="">Select Exam Type</option>
                                    <option value="1" >Center</option>
                                    <option value="0" >Online</option>
                                </select>
                            </div>
                        </div>
              <?php } ?>

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
                    <th>Code</th>
                    <th>Name</th>
                    <th>Sub-Course Name</th>
                    <th>Duration</th>
                    <th>Min/Max Marks</th>
                    <th>Paper Type</th>
                    <th>Credit</th>
                    <th>Exam Type</th>
                    <th>Center Count</th>
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
      $(function () {

        var table = $('#users-table');
        var role = '<?= $_SESSION['Role'] ?>';
        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/subjects/server'
          },
          'columns': [
            {
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
                data: "Exam_Type",
                render: function (data, type, row) {
                    var exam_type = (row.Exam_Type == 0) ? 'Online' : (row.Exam_Type == 1) ? 'Center' : 'Not Selected';
                    return exam_type;
                },
                visible: '<?= $_SESSION['university_id'] ?>' == 47 ? false : true
            },

            {
              data: "alloted_center",
              visible: '<?= $_SESSION['university_id'] ?>' == 47 ? false : true
            },
            {
              data: "ID",
              "render": function (data, type, row) {
                var uni_id = '<?= $_SESSION['university_id'] ?>';
                let downloasdSylBtn = "";
                if (row.files != null) {
                  downloasdSylBtn = '<a href="..' + row.files + '"><i class="uil uil-down-arrow icon-xs cursor-pointer" title="Download" ></i></a>';
                }
                var allotButton = [48].includes(parseInt(uni_id)) ? '<i class="uil uil-plus-circle icon-xs cursor-pointer" title="Allot Center" onclick="allotCenter(\'' + data + '\', \'lg\')"></i>' : '';
                var deleteBtn = ['Administrator', 'University Head'].includes(role) ? '<i class="uil uil-trash icon-xs cursor-pointer" title="Delete" onclick="destroy(&#39;subjects&#39;, &#39;' + data + '&#39)"></i>' : '';
                var uploadSylBtn = ['Administrator', 'University Head'].includes(role) ? '<i class="uil uil-upload icon-xs cursor-pointer" title="Upload" onclick="upload(&#39;subjects&#39;, &#39;' + data + '&#39, &#39;' + row.Code + '&#39, &#39;' + row.subject_name + '&#39)"></i>' : '';

                return '<div class="button-list text-end">\
                '+ downloasdSylBtn + '\
                '+ uploadSylBtn + '\
                ' + allotButton + '\
                <i class="uil uil-edit icon-xs cursor-pointer" title="Edit" onclick="edit(&#39;subjects&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                ' + deleteBtn + '\
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
          url: '/app/subjects/filter',
          type: 'POST',
          data: {
            id,
            by
          },
          dataType: 'json',
          success: function (data) {
            const universityId = <?php echo json_encode($_SESSION['university_id']); ?>;
             if(by =="sub_course" && universityId==47){
                getDuration(id);
              }
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }
      function allotCenter(id, modal) {
        $.ajax({
          url: '/app/subjects/allot-center?id=' + id,
          type: 'GET',
          success: function (data) {
            $('#' + modal + '-modal-content').html(data);
            $('#' + modal + 'modal').modal('show');
          }
        });
      }
      $(document).ready(function () {
        getDuration();
        $("#center").select2({
          placeholder: 'Choose Center',
        })
        $("#sub_course").select2({
          placeholder: 'Choose Sub Course',
        })
        $("#exam_type").select2({
          placeholder: 'Choose Exam Type',
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
      
      
      function upload(url, id, code, subject_name) {
        var modal = 'md';
        $.ajax({
          url: '/app/' + url + '/upload',
          type: 'POST',
          data: { id: id, modal: modal, code: code, subject_name: subject_name },
          success: function (data) {
            $('#' + modal + '-modal-content').html(data);
            $('#' + modal + 'modal').modal('show');
          }
        })
      }
    </script>


    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>