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
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php');
unset($_SESSION['filterByUniversity']);
unset($_SESSION['filterByVerticalType'])
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
                  echo '<li class="breadcrumb-item ' . $active . '">' . ucwords($crumb[0]) . '</li>';
                endif;
              }
              ?>
              <?php if (in_array($_SESSION['Role'], ['Administrator', 'University Head', 'Counsellor', 'Operations'])) { ?>
                <div class="text-end">
                  <button class="btn btn-link custom_add_button" aria-label="" title="" data-toggle="tooltip" data-original-title="Download"
                    onclick="exportData()"> <i class="uil uil-down-arrow"></i></button>
                  <button class="btn btn-link custom_add_button" aria-label="" title="" data-toggle="tooltip" data-original-title="Add"
                    onclick="add('center-master','lg')"> <i class="uil uil-plus-circle"></i></button>
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

            <div class="row">
              <div class="col-md-3 m-b-10">
                <div class="form-group custom_field">
                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="university"
                    onchange="addFilter(this.value, 'university')" data-placeholder="Choose University">
                    <option value="">Choose University </option>
                    <?php
                    $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name FROM Universities WHERE Status=1 AND  ID IS NOT NULL " . $_SESSION['UniversityQuery']);
                    while ($university = $universities->fetch_assoc()) { ?>
                      <option value="<?= $university['ID'] ?>"><?= $university['Name'] ?></option>
                    <?php } ?>
                    <option value="1">University Not Alloted</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6 m-b-10">
              </div>
              <div class="col-md-3">
                <input type="text" id="users-search-table" class="form-control pull-right custom_search_section" placeholder="Search">
              </div>
            </div>
            <div class="clearfix"></div>
          </div>

          <div class="card-body">
            <div class="">
              <table class="table table-hover nowrap table-responsive" id="users-table">
                <thead>
                  <tr>
                    <th data-orderable="false"></th>
                    <th>Name</th>
                    <th>Code</th>
                    <th data-orderable="false">Password</th>
                    <th>Email</th>
                    <th>Joining Date</th>
                    <th>No Of Sub-Center</th>
                    <th>No Of Addmission</th>
                    <th>Current Balance</th>
                    <th data-orderable="false">Can Create Sub-Center?</th>
                    <th data-orderable="false"></th>
                    <th data-orderable="false" class="text-center">Action</th>
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
        var role = '<?= $_SESSION['Role'] ?>'
        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/center-master/server'
          },
          'columns': [{
              data: "Photo",
              "render": function(data, type, row) {
                return '<span class="thumbnail-wrapper d48 circular inline">\
                <img src="' + data + '" alt="" data-src="' + data + '"\
                  data-src-retina="' + data + '" width="32" height="32">\
              </span>';
              }
            },
            {
              data: "Name",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "Code",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },

            {
              data: "Password",
              "render": function(data, type, row) {
                return '<div class="row" style="width:250px !important;">\
                <div class="col-md-10">\
                  <input type="password" class="form-control" disabled="" style="border: 0ch;" value="' + data + '" id="myInput' + row.ID + '">\
                </div>\
                <div class="col-md-2">\
                  <i class="uil uil-eye pt-2 cursor-pointer" onclick="showPassword(' + row.ID + ')"></i>\
                </div>\
              </div>';
              }
            },
            {
              data: "Email"
            },

            {
              data: "Created_At"
            },
            {
              data: "sub_center_count"
            },
            {
              data: "Admission"
            },
            {
              data: "totalamount"
            },
            {
              data: "CanCreateSubCenter",
              "render": function(data, type, row) {
                var active = data == 1 ?
                  '<span class="badge badge-success">Yes</span>' :
                  '<span class="badge badge-danger">No</span>';

                var checked = data == 1 ? 'checked' : '';

                return '<div class="form-check form-check-inline switch switch-lg success">\
                          <input onclick="changeSubCenterStatus(&#39;' + row.ID + '&#39;)" type="checkbox" ' + checked + ' id="sub-center-switch-' + row.ID + '">\
                          <label for="sub-center-switch-' + row.ID + '">' + active + '</label>\
                        </div>';
              }
            },
            {
              data: "Status",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                var checked = data == 1 ? 'checked' : '';
                return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(&#39;Users&#39;, &#39;' + row.ID + '&#39;)" type="checkbox" ' + checked + ' id="status-switch-' + row.ID + '">\
                <label for="status-switch-' + row.ID + '">' + active + '</label>\
              </div>';
              },
              visible: false
            },
            {
              data: "ID",
              "render": function(data, type, row) {
                var allotButton = ['Administrator', 'University Head'].includes(role) ? '<i class="uil uil-plus-circle icon-xs cursor-pointer custom_edit_button" title="Allot University" onclick="allot(&#39;' + data + '&#39, &#39;lg&#39;)"></i>' : '';
                var deleteBtn = ['Administrator', 'University Head'].includes(role) ? '<i class="uil uil-trash icon-xs cursor-pointer custom_edit_button" title="Delete" onclick="destroy(&#39;center-master&#39;, &#39;' + data + '&#39)"></i>' : '';
                return '<div class="button-list text-end">\
                ' + allotButton + '\
                <i class="uil uil-edit icon-xs cursor-pointer custom_edit_button" title="Edit" onclick="edit(&#39;center-master&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
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
          url: '/app/center-master/filter',
          type: 'POST',
          data: {
            id,
            by
          },
          dataType: 'json',
          success: function(data) {
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
              if ('<?= $_SESSION['Role'] ?>' == 'Administrator') {
                $(".sub_center").html(data.subCenterName);

              }
            }
          }
        })
      }

      function allot(id, modal) {
        $.ajax({
          url: '/app/center-master/allot-universities?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#' + modal + '-modal-content').html(data);
            $('#' + modal + 'modal').modal('show');
          }
        });
      }

      function changeSubCenterStatus(id) {
        $.ajax({
          url: '/app/center-master/sub-center-access',
          type: 'POST',
          data: {
            id: id
          },
          dataType: 'json',
          success: function(data) {
            if (data.status == 200) {
              notification('success', data.message);
              $('#users-table').DataTable().ajax.reload(null, false);
            } else {
              notification('danger', data.message);
            }
          }
        });
      }
    </script>

    <script>
      function showPassword(id) {
        var x = document.getElementById("myInput".concat(id));
        if (x.type === "password") {
          x.type = "text";
        } else {
          x.type = "password";
        }
      }
    </script>

    <script type="text/javascript">
      function exportData() {
        var search = $('#users-search-table').val();
        var url = search.length > 0 ? "?search=" + search : "";
        window.open('/app/center-master/export' + url);
      }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>