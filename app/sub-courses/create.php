<link href="../../assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Add <span class="semi-bold">Specialization</span></h5>
</div>

<?php 
require '../../includes/db-config.php';
session_start();
?>
  <form role="form" id="form-add-sub-course" action="/app/sub-courses/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <!-- University & Course -->
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>University</label>
          <select class="full-width" style="border: transparent;" id="university_id" name="university_id" onchange="getDetails(this.value);">
            <option value="">Choose</option>
            <?php
            require '../../includes/db-config.php';
            session_start();
            $university_query = $_SESSION['Role'] != 'Administrator' ? " AND ID =" . $_SESSION['university_id'] : '';
            $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name FROM Universities WHERE Status=1 AND ID IS NOT NULL $university_query");
            while ($university = $universities->fetch_assoc()) { ?>
              <option value="<?= $university['ID'] ?>"><?= $university['Name'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Program</label>
          <select class="full-width" style="border: transparent;" id="course" name="course">
            <option value="">Choose</option>
            
          </select>
        </div>
      </div>
    </div>

    <!-- Name -->
    <div class=" row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Mechanical Engineering" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Short Name</label>
          <input type="text" name="short_name" class="form-control" placeholder="ex: ME" required>
        </div>
      </div>
    </div>
    <div class=" row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Name (In-House)</label>
          <input type="text" name="in_house_course_name" class="form-control" placeholder="ex: Mechanical Engineering" required>
        </div>
      </div>
    </div>
    <!-- Scheme & Mode -->
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Scheme</label>
          <select class="full-width" style="border: transparent;" id="scheme" name="scheme">

          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Mode</label>
          <select class="full-width" style="border: transparent;" id="mode" name="mode" onchange="getFeeSructures()">

          </select>
        </div>
      </div>
    </div>

    <!-- Eligibility -->
    <?php $eligibilities = array("High School", "Intermediate", "UG", "PG", "Other"); ?>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default form-group-default-select2 required">
          <label style="z-index:9999">Academic Eligibility</label>
          <select class=" full-width" data-init-plugin="select2" id="eligibilities" name="eligibilities[]" multiple>
            <?php foreach($eligibilities as $eligibility){ ?>
              <option value="<?=$eligibility?>"><?=$eligibility?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>

    <!-- Duration -->
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Min Duration</label>
          <input type="tel" name="min_duration[]" id="min_duration" class="form-control" placeholder="ex: 8" onkeypress="return isNumberKey(event)" onkeyup="getFeeSructures()" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Max Duration</label>
          <input type="tel" name="max_duration" class="form-control" placeholder="ex: 8" onkeypress="return isNumberKey(event)" required>
        </div>
      </div>
    </div>

    <div id="fee">
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="form-group form-group-default required">
          <label>Lateral</label>
          <select class="full-width" style="border: transparent;" id="lateral" name="lateral">
            <option value="0">No</option>
            <option value="1">Yes</option>
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default">
          <label>LE Start</label>
          <input type="text" id="le_start" name="le_start" class="form-control" placeholder="ex: 3,5">
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default">
          <label>LE SOL</label>
          <input type="tel" id="le_sol" name="le_sol" class="form-control" placeholder="ex: 8" onkeypress="return isNumberKey(event)">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="form-group form-group-default required">
          <label>Credit Transfer</label>
          <select class="full-width" style="border: transparent;" id="ct_transfer" name="ct_transfer">
            <option value="0">No</option>
            <option value="1">Yes</option>
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default">
          <label>CT Start</label>
          <input type="tel" id="ct_start" name="ct_start" class="form-control" placeholder="ex: 3" onkeypress="return isNumberKey(event)">
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default">
          <label>CT SOL</label>
          <input type="tel" id="ct_sol" name="ct_sol" class="form-control" placeholder="ex: 8" onkeypress="return isNumberKey(event)">
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Save</span>
        <span class="hidden-block">
          <i class="pg-icon">tick</i>
        </span>
      </button>
    </div>
  </div>
</form>

<script type="text/javascript" src="../../assets/plugins/select2/js/select2.full.min.js"></script>

<script>
  $(function(){
    $("#eligibilities").select2();
    $("#course_category").select2();
  })

  function getDetails(id) {
   
    $.ajax({
      url: '/app/sub-courses/courses?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#course').html(data);
      }
    });

    $.ajax({
      url: '/app/sub-courses/schemes?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#scheme').html(data);
      }
    });

    $.ajax({
      url: '/app/sub-courses/modes?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#mode').html(data);
      }
    });
  }

  function getFeeSructures() {
    const durations = $('#min_duration').val();
    const university_id = $('#university_id').val();
    const mode = $('#mode').val();
    $.ajax({
      url: '/app/sub-courses/fee-structures?durations=' + durations + '&university_id=' + university_id + '&mode=' + mode,
      type: 'GET',
      success: function(data) {
        $('#fee').html(data);
      }
    });
  }

  $(function() {
    $('#form-add-sub-course').validate({
      rules: {
        name: {
          required: true
        },
        short_name: {
          required: true
        },
        university_id: {
          required: true
        },
        course: {
          required: true
        },
        scheme: {
          required: true
        },
        mode: {
          required: true
        },
        lateral: {
          required: true
        },
        ct_transfer: {
          required: true
        },
        'eligibilities[]': {
          required: true
        }
      },
      highlight: function(element) {
        $(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function(element) {
        $(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });
  })

  $("#form-add-sub-course").on("submit", function(e) {
    if ($('#form-add-sub-course').valid()) {
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
      $.ajax({
        url: this.action,
        type: 'post',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(data) {
          if (data.status == 200) {
            $('.modal').modal('hide');
            notification('success', data.message);
            $('#sub-courses-table').DataTable().ajax.reload(null, false);
          } else {
            $(':input[type="submit"]').prop('disabled', false);
            notification('danger', data.message);
          }
        }
      });
      e.preventDefault();
    }
  });
  

</script>
