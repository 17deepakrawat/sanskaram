<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container">
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
                if (count($breadcrumbs) == $i) :
                  $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . ucwords($crumb[0]) . '</li>';
                endif;
              } ?>
              <div>
                <button class="custom_add_button" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload" onclick="add('document-issuence/marksheet', 'lg')"> <i class="uil uil-export"></i></button>
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
              <div class="col-xs-12">
                <input type="text" id="users-search-table" class="form-control pull-right custom_search_section" placeholder="Search">
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="marksheet-entry-table">
                <thead>
                  <tr>
                    <th>Student_ID</th>
                    <th>Enrollment_No</th>
                    <th>Exam Session</th>
                    <th>Duration</th>
                    <th>Marksheet_No</th>
                    <th>Docket_Id</th>
                    <th>Dispatch_status</th>
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
  $(function(){
      var table = $('#marksheet-entry-table');
      var settings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url':'/app/document-issuence/marksheet/marksheet_entry-server'
        },
        'columns': [  { 
            data: "Student_id" , 
            render : function(data,type,row) {
              return "<b>"+data+"</b>";
            }
          },{ 
            data: "Enrollment_No", 
            render : function(data,type,row) {
              return "<b>"+data+"</b>";
            }
          },{ 
            data: "Exam_session"
          },{ 
            data: "Duration"
          },{ 
            data: "Marksheet_No",
          },{ 
            data: "Docket_Id",
            render : function(data,type,row) {
              if(data == null) {
                return "<div class = 'badge badge-danger'>Not Assign</div>";
              } else {
                return "<div class='badge badge-success'>" + data + "</div>";
              }
            }
          },{
            data : "Dispatch_status",
            render : function(data,type,row) {
              if(data == '1') {
                var edit = '<span class="badge badge-danger cursor-pointer">Not Dispatched</span>';
                return edit;
              } else {
                var edit = '<span class="badge badge-success cursor-pointer">Dispatched</span>';
                return edit;
              }
            } 
          }
        ],
        "sDom": "<t><'row'<p i>>",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
            "sLengthMenu": "_MENU_ ",
            "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        "aaSorting": [],
        "iDisplayLength": 10,
        "drawCallback": function( settings ) {
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
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>