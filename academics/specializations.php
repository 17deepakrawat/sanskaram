<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
   .input-sm{
    border-radius: 10px !important;
    height: 48px !important;
  }
</style>
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
        <div class=" container-fluid   sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . ucwords($crumb[0]) . '</li>';
                endif;
              }
              ?>
              <div>
                <?php if (in_array($_SESSION['Role'], ['Administrator', 'University Head'])) { ?>
                  <button class="custom_add_button" aria-label="" title="" data-toggle="tooltip" data-original-title="Download" onclick="exportData()"> Download<i class="uil uil-down-arrow ml-2"></i></button>
                  <button class="custom_add_button" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Specialization" onclick="add('sub-courses','lg')">Add <i class="uil uil-plus-circle ml-2"></i></button>
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
        <div class="card card-transparent">
          <div class="card-header">
            <div class="pull-right">
              <!-- <div class="col-xs-12">
                <input type="text" id="sub-courses-search-table" class="form-control pull-right custom_search_section" placeholder="Search">
              </div> -->
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="">
              <table class="table table-hover nowrap" id="sub-courses-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Program</th>
                    <th>Type</th>
                    <th>Scheme</th>
                    <th>Mode</th>
                    <th data-orderable="false">University</th>
                    <th data-orderable="false">Status</th>
                    <th data-orderable="false" class="text-end">Action</th>
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
        var role = '<?= $_SESSION['Role'] ?>';
        var show = role == 'Administrator' ? true : false;
        var table = $('#sub-courses-table');

        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/sub-courses/server'
          },
          'columns': [{
              data: "Name"
            },
            {
              data: "Course"
            },
            {
              data: "CourseType"
            },
            {
              data: "Scheme"
            },
            {
              data: "Mode"
            },
            {
              data: "University",
              visible: show
            },
            {
              data: "Status",
              "render": function(data, type, row) {
                var statusText = data == 1 ? 'Active' : 'Inactive';
                var badgeClass = data == 1 ? 'badge bg-success' : 'badge bg-danger';
                var checked = data == 1 ? 'checked' : '';

                return '<div class="form-check form-check-inline switch switch-lg success">\
              <input onclick="changeStatus(&#39;Sub-Courses&#39;, &#39;' + row.ID + '&#39;)" type="checkbox" ' + checked + ' id="status-switch-' + row.ID + '">\
              <label for="status-switch-' + row.ID + '">\
                <span class="' + badgeClass + '">' + statusText + '</span>\
              </label>\
            </div>';
              }
            },
            {
              data: "ID",
              "render": function(data, type, row) {
                return '<div class="button-list text-end">\
                <i class="uil uil-edit icon-xs cursor-pointer custom_edit_button" onclick="edit(&#39;sub-courses&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                <i class="uil uil-trash icon-xs cursor-pointer custom_delete_button" onclick="destroy(&#39;sub-courses&#39;, &#39;' + data + '&#39)"></i>\
              </div>'
              },
              visible: ['Administrator', 'University Head'].includes(role) ? true : false
            },
          ],
          "sDom": "<'row mt-3 w-100 p-0 m-0'<'col-sm-6 pr-0 pl-0 custon_text_start'l><'col-sm-6 pr-0 pl-0'f>><t><'row'<p i>>",
          "destroy": true,
          "scrollCollapse": true,
          "oLanguage": {
            "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
          },
          drawCallback: function(settings, json) {
            $('[data-toggle="tooltip"]').tooltip();
          },
          "aaSorting": []
        };

        table.dataTable(settings);

        // search box for table
        $('#sub-courses-search-table').keyup(function() {
          table.fnFilter($(this).val());
        });

      })
    </script>

    <script type="text/javascript">
      function changeColumnStatus(id, column) {
        $.ajax({
          url: '/app/sub-courses/status',
          type: 'post',
          data: {
            id: id,
            column: column
          },
          dataType: 'json',
          success: function(data) {
            if (data.status == 200) {
              notification('success', data.message);
              $('#sub-courses-table').DataTable().ajax.reload(null, false);
            } else {
              notification('danger', data.message);
              $('#sub-courses-table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }
    </script>

    <script type="text/javascript">
      function exportData() {
        var search = $('#sub-courses-search-table').val();
        var url = search.length > 0 ? "?search=" + search : "";
        window.open('/app/sub-courses/export' + url);
      }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>