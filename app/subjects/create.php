<link href="../../assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Add <span class="semi-bold">Subject</span></h5>
</div>

<?php
require '../../includes/db-config.php';
require '../../includes/ClassHelper.php';

session_start();


$getClassFunc = new ClassHelper();
$course = $getClassFunc->getProgram($conn);

?>
<form role="form" id="form-add-subjects" action="/app/subjects/store-subject" method="POST"
  enctype="multipart/form-data">
  <div class="modal-body">
    <!-- University & Course -->
    <div class="row">

      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Course</label>
          <select class="full-width" style="border: transparent;" id="course" name="course"
            onchange="getSpecialization(this.value)">
            <option value="">Choose Course</option>
            <?= $course ?>
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Sub-Course</label>
          <select class="full-width" style="border: transparent;" id="sub_course_ids" name="sub_course"
            onchange="getSemesters(this.value)">
            <option value="">Choose Sub-Courses</option>
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Semester</label>
          <select class="full-width" style="border: transparent;" name="semester" id="semester">
            <option value="">Choose Semester</option>
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Subject Name</label>
          <input type="text" name="name" id="name" class="form-control" placeholder="ex: Hindi" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Subject Code</label>
          <input type="text" name="code" id="code" class="form-control" placeholder="ex: BVOC-101" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Paper Type</label>
          <select class="full-width" style="border: transparent;" name="paper_type" id="paper_type">
            <option value="">Choose Paper Type</option>
            <option value="Theory">Theory</option>
            <option value="Project">Project</option>
            <option value="Practical">Practical</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Credit</label>
          <input type="text" name="credit" id="credit" class="form-control" placeholder="ex: 4" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Min Marks</label>
          <input type="text" name="min_marks" id="min_marks" class="form-control" placeholder="ex: 40" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Max Marks</label>
          <input type="text" name="max_marks" id="max_marks" class="form-control" placeholder="ex: 60" required>
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
  $(function () {
    $("#eligibilities").select2();
    $("#course").select2({
      placeholder: 'Choose Course',
      allowClear: true,
      dropdownParent: $('#lg-modal-content')
    });
    $("#sub_course_ids").select2({
      placeholder: 'Choose Sub-Courses',
      allowClear: true,
      dropdownParent: $('#lg-modal-content')
    });
    $("#semester").select2({
      placeholder: 'Choose Semester',
      allowClear: true,
      dropdownParent: $('#lg-modal-content')
    });

    $("#paper_type").select2({
      placeholder: 'Choose Paper Type',
      allowClear: true,
      dropdownParent: $('#lg-modal-content')
    });
  })

  $(function () {
    $('#form-add-subjects').validate({
      rules: {
        course: {
          required: true
        },
        sub_course: {
          required: true
        },
        course: {
          required: true
        },
      },
      highlight: function (element) {
        $(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function (element) {
        $(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });
  })

  function getSpecialization(course_id) {
    $.ajax({
      url: '/app/sub-courses/get-sub-course?course_id=' + course_id,
      type: 'GET',
      success: function (data) {
        $('#sub_course_ids').html(data);
        getSemesters(course_id);
      }
    });
  }

  function getSemesters(sub_course_id) {
    $.ajax({
      url: '/app/subjects/get-duration',
      data: { id: sub_course_id },
      type: 'POST',
      success: function (data) {
        $("#semester").html(data);
      }
    })
  }


  $("#form-add-subjects").on("submit", function (e) {
    if ($('#form-add-subjects').valid()) {
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
        success: function (data) {
          if (data.status == 200) {
            $('.modal').modal('hide');
            notification('success', data.message);
            $('#form-add-subjects-table').DataTable().ajax.reload(null, false);
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