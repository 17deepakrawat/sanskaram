<?php
if (isset($_GET['id'])) {
  require '../../../includes/db-config.php';
  require '../../../includes/helpers.php';

  session_start();
  $id = mysqli_real_escape_string($conn, $_GET['id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
  $student = $conn->query("SELECT exam_exit_status,Payment_Received,Enrollment_No FROM Students WHERE ID = $id");
  $student = mysqli_fetch_assoc($student);
  $exam_status = $student['exam_exit_status'];
//   $exit_status = '';
    if (!empty($student['Payment_Received']) && (empty($student['Enrollment_No']) || $student['Enrollment_No'] === NULL)) {
      $exit_status = 'Active';
    } elseif (!empty($student['Enrollment_No']) && (empty($student['exam_exit_status']) || ($student['exam_exit_status']==5))) {
        $exam_status = 5;
    }

?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left mb-4">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Exit Exam Status</h5>
  </div>
  <form role="form" id="form-exam-status" action="/app/applications/exit-status/store" method="POST">
    <div class="modal-body">
      <div class="row clearfix">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Exit Exam Status</label>
            <select class="full-width " style="border: transparent;" name="exam_status" id="exam_status" >
            <?php foreach($exam_exit_status as $key => $value){ ?>
                <option value="<?= $key ?>" <?php if($exam_status==$key){echo "selected";}else{echo "";} ?>><?= $value ?></option>
              <?php }?>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-footer flex justify-content-between">
      <div class="m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <span>Update</span>
          <span class="hidden-block">
            <i class="pg-icon">tick</i>
          </span>
        </button>
      </div>
    </div>
  </form>


  <script type="text/javascript">
    $(function() {
      $('#form-exam-status').validate({
        rules: {
          enrollment_no: {
            required: true
          },
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

    $("#form-exam-status").on("submit", function(e) {
      e.preventDefault();
      if ($('#form-exam-status').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?= $id ?>');
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
              $('.table').DataTable().ajax.reload(null, false);
            } else {
              $(':input[type="submit"]').prop('disabled', false);
              notification('danger', data.message);
            }
          }
        });
      }
    });

  </script>

<?php } ?>
