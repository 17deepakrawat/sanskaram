<?php include ($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
    .select2-container .select2-selection {
    border-radius: 10px;
    height: 48px !important;
    font-size: 17px;
    font-family: system-ui;
  }

  .select2-container .select2-selection .select2-selection__arrow {
    top: auto;
    bottom: 11px;
  }

  .select2-container--open .select2-selection {
    box-shadow: none;
    border: 1px solid #2b303b !important;
  }

  .select2-results .select2-results__option--highlighted {
    background-color: #55638d !important;
    border-radius: 3px;
    color: #ffffff !important;
  }
.custom_filter{
  border-radius: 10px;
}
</style>
<?php include ($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include ($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include ($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
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
                if (count($breadcrumbs) == $i):
                  $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . ucwords($crumb[0]) . '</li>';
                endif;
              }
              ?>
              <div>
                <button class="custom_add_button" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload"
                  onclick="add('subjects', 'lg')"> <i class="uil uil-export"></i></button>
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
        <div class="row">
          <?php if ($_SESSION['university_id'] == '47') { ?>
            <div class="col-md-4">
              <div class="form-group form-group-default required custom_filter">
                <label>Semester</label>
                <?php
                $sem_count = $conn->query("SELECT MAX(Sem) AS duration FROM Admit_Card  WHERE Enrollment_No = '" . $_SESSION['Enrollment_No'] . "'");
                $sem_count = $sem_count->fetch_assoc();
                $sem_count = $sem_count["duration"];
                ?>
                <select class="full-width" style="border: transparent;" id="semester"
                  onchange="getAdmitCards(this.value)">
                  <option value="">Choose</option>
                  <?php
                  for ($i = 1; $i <= $sem_count; $i++) { ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                  <?php }
                  ?>
                </select>
              </div>
            </div>

          <?php } ?>
        </div>
        <div class="row">
          <div class="card-body" id="accordion">
          </div>
        </div>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include ($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script type="text/javascript">

      function getAdmitCards(sem_id) {  
        //var url = "/app/admit-cards/<?= $_SESSION['university_id'] ?>/students-admit-cards.php/?student_id=<?= base64_encode($_SESSION['ID'] . 'W1Ebt1IhGN3ZOLplom9I') ?>&duration=" + sem_id;
        //$('#accordion').html('<iframe src="' + url + '" frameborder="0" scrolling="no" id="myFrame" type="application/pdf" width="100%" height="560px" ></iframe>');
        $('#accordion').html('<p><b>Admit Card Not Available !</b></p>');
        // window.location.href = "/app/admit-cards/<?= $_SESSION['university_id'] ?>/students-admit-cards.php/?student_id=<?= base64_encode($_SESSION['ID'] . 'W1Ebt1IhGN3ZOLplom9I') ?>&duration="+sem_id;
      }
    </script>
    <?php if($_SESSION['university_id']){ ?>
      <script>
        getAdmitCards('<?= $_SESSION['Duration']?>');
      </script>
      <?php }?>


    <?php include ($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>