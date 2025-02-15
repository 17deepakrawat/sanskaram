<?php
if (isset($_GET['university_id'])) {
  require '../../../includes/db-config.php';

  $monthOptionTag = monthOptionTag();
  $yearOptionTag = yearOptionTag();
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h6>Add <span class="semi-bold">Admission Session</span></h6>
  </div>
  <form role="form" id="form-add-admission-sessions" action="/app/components/admission-sessions/store" method="POST">
    <div class="modal-body">
      <?php if($_GET['university_id'] == '47') { ?>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="name" class="form-control" placeholder="ex: Jan-22">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Exam Session</label>
            <input type="text" name="exam_session" class="form-control" placeholder="ex: July-22">
          </div>
        </div>
      </div>
      <?php } else { ?>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Month</label>
            <select class="full-width" style="border: transparent;" name="month">
              <?=$monthOptionTag?>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Year</label>
            <select class="full-width" style="border: transparent;" name="year">
              <?=$yearOptionTag?>
            </select>
          </div>
        </div>
      </div>
      <?php } ?>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Scheme</label>
            <select class="full-width" style="border: transparent;" name="scheme">
              <option value="">Choose</option>
              <?php
                $schemes = $conn->query("SELECT ID, Schemes.Name FROM Schemes WHERE Schemes.Status = 1 AND University_ID = ".$_GET['university_id']."");
                while($scheme = $schemes->fetch_assoc()) { ?>
                  <option value="<?=$scheme['ID']?>"><?=$scheme['Name']?></option>
              <?php } ?>
            </select>
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

  <script>
    $(function(){
      $('#form-add-admission-sessions').validate({
        rules: {
          name: {required:true},
          exam_session: {required:true},
          month: {required:true},
          year: {required:true}, 
          scheme: {required:true},
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

    $("#form-add-admission-sessions").on("submit", function(e){
      if($('#form-add-admission-sessions').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('university_id', '<?=$_GET['university_id']?>');
        $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(data) {
            if(data.status==200){
              $('.modal').modal('hide');
              notification('success', data.message);
              $('#tableAdmissionSessions').DataTable().ajax.reload(null, false);
            }else{
              $(':input[type="submit"]').prop('disabled', false);
              notification('danger', data.message);
            }
          }
        });
        e.preventDefault();
      }
    });
  </script>
<?php } ?>

<?php 

function monthOptionTag() : string {

  $monthOption = '<option value="">Select Month</option>';
  $i = 1;
  while($i <= 12) {
    $month = date('M',mktime(0, 0, 0, $i, 1));
    $monthOption .= '<option value="'.$i.'">'.$month.'</option>';
    $i++;
  }
  
  return $monthOption;
}

function yearOptionTag() : string {
  
  $yearOption = '<option value="">Select Year</option>';
  $lastYear = date('Y',strtotime('-1 year'));
  $yearOption .= '<option value = "'.$lastYear.'">'.$lastYear.'</option>';
  $currentYear = date("Y");
  $yearOption .= '<option value = "'.$currentYear.'">'.$currentYear.'</option>';
  $nextYear = date('Y',strtotime('+1 year'));
  $yearOption .= '<option value = "'.$nextYear.'">'.$nextYear.'</option>';
  return $yearOption;
}
?>
