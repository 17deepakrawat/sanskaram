<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/helpers.php'); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php');

  ?>
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
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                endif;
              }
              ?>
              <div>
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
          <div class="col-md-12">

            <?php

            if ($_SESSION['Role'] == 'Student') {
              if (strtolower($_SESSION['Course_Category']) == 'certified' && ($_SESSION['Duration'] == 6  || $_SESSION['Duration'] == 11)) {
                $duration = $_SESSION['Duration'] . '/' . $_SESSION['Course_Category'];
              } else if (strtolower($_SESSION['Course_Category']) == 'certification') {
                $duration = $_SESSION['Duration'] . '/certification';
              } else if (strtolower($_SESSION['Course_Category']) == 'advance_diploma' || $_SESSION['Duration'] == '11/advance-diploma') {
                $duration = '11/advanced';
              } else {
                $duration = $_SESSION['Duration'];
              }
            } else {
              $duration = $_SESSION['Duration'];
            }
			if(isset($_GET['duration']) && $_GET['duration']!='')
            {
            	$duration = $_GET['duration'];
            }
            $syllabus_ids = array();
            if ($_SESSION['university_id'] == 48) {
              $center_id = getUserIdFunc($conn, $_SESSION['Added_For']);
              $getCenterID = $conn->query("select Code from Users where ID = '$center_id'");
              if ($getCenterID->num_rows > 0) {
                $codeArr = $getCenterID->fetch_assoc();
                $code = trim($codeArr['Code']);
                $userQuery = " AND JSON_CONTAINS(User_ID, '\"" . mysqli_real_escape_string($conn, trim($code)) . "\"')";
              } else {
                $code = '';
                $userQuery = '';
              }
              $examSession = 7;
              $codes = $conn->query("SELECT ID FROM Syllabi WHERE Course_ID = " . $_SESSION['Course_ID'] . " AND Sub_Course_ID = " . $_SESSION['Sub_Course_ID'] . " AND Semester = '" .  $duration . "' $userQuery ");
            } else {
              $codes = $conn->query("SELECT ID FROM Syllabi WHERE Course_ID = " . $_SESSION['Course_ID'] . " AND Sub_Course_ID = " . $_SESSION['Sub_Course_ID'] . " AND Semester = '" .  $duration . "'");
            	$examSession = 8;
            }

            if ($codes->num_rows > 0) {
              while ($row = $codes->fetch_assoc()) {
                $syllabus_ids[] = $row['ID'];
              }
			
              $date_sheets = $conn->query("SELECT Date_Sheets.*, Exam_Sessions.Name as Exam_Session, Syllabi.Name, Syllabi.Code FROM Date_Sheets LEFT JOIN Syllabi ON Date_Sheets.Syllabus_ID = Syllabi.ID LEFT JOIN Exam_Sessions ON Date_Sheets.Exam_Session_ID = Exam_Sessions.ID WHERE Exam_Sessions.ID=$examSession and Syllabus_ID IN (" . implode(",", $syllabus_ids) . ") ORDER BY Exam_Date ASC");
              if ($date_sheets->num_rows == 0) {
                echo '<center><h1>Date Sheet Not Available</h1></center>';
              } else {
            ?>
                <?php
                if ($_SESSION['university_id'] == 47) {
                  $durationOption = "<option value=''>Choose Semeter</option>";
                  for ($i = $_SESSION['Duration']; $i >= 1; $i--) {
                    	$selected = "";
                    if($duration==$i)
                    {
                    	$selected = "selected='selected'";
                    }
                    $durationOption .= "<option value='$i' $selected  >$i</option>";
                  }
                ?>
                  <div class="col-md-4">
                    <label> Semester </label>
                    <select class="form-control" onchange="changeDatesheetSemester(this.value)">
                      <?= $durationOption ?>
                    </select>
                  </div>
                <?php
                }
                ?>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Exam Session</th>
                        <th>Paper Code</th>
                        <th>Paper Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Exam Url</th>
                        <th>User ID</th>
                        <th>Password</th>
                        <!-- <th>Exam</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      while ($date_sheet = $date_sheets->fetch_assoc()) { ?>
                        <tr>
                          <td><?= $date_sheet['Exam_Session'] ?></td>
                          <td><?= $date_sheet['Code'] ?></td>
                          <td><?= $date_sheet['Name'] ?></td>
                          <td><?= date("l, dS M, Y", strtotime($date_sheet['Exam_Date'])) ?></td>
                          <td><?= date("h:i A", strtotime($date_sheet['Start_Time'])) . " to " . date("h:i A", strtotime($date_sheet['End_Time'])) ?></td>
                          <td><a target="_blank" href="https://glocal.exam-portal.in/auth/student">Click Here</a> </td>
                          <td><?= $_SESSION['Unique_ID'] ?> </td>
                          <td><?= date('d-M-Y', strtotime($_SESSION['DOB'])) ?> </td>
                          <!-- <td><button class="btn btn-primary disabled" data-toggle="tooltip" data-placement="top" title="Button will be enable before 15 mins of exam schedule.">Start</button></td> -->
                        </tr>
                      <?php } ?>
                      <tr>
                        <td>Jan-25</td>
                        <td></td>
                        <td>Mock Test</td>
                        <?php
                        if ($_SESSION['university_id'] == 48) {
                        ?> <td>Wednesday, 5th Feb, 2025</td>
                          <td>10:30 AM to 12:30 PM</td>
                        <?php
                        } else {
                        ?> <td>Saturday, 25th Jan, 2025</td>
                          <td>10:30 AM to 12:30 PM</td>
                        <?php
                        }
                        ?>
                        <td><a target="_blank" href="https://glocal.exam-portal.in/auth/student">Click Here</a> </td>
                        <td><?= $_SESSION['Unique_ID'] ?> </td>
                        <td><?= date('d-M-Y', strtotime($_SESSION['DOB'])) ?> </td>
                        <!-- <td><button class="btn btn-primary disabled" data-toggle="tooltip" data-placement="top" title="Button will be enable before 15 mins of exam schedule.">Start</button></td> -->
                      </tr>
                    </tbody>
                  </table>
                </div>
            <?php }
            } else {
              // No Date Sheet Available
              echo '<center><h1>Date Sheet Not Available</h1></center>';
            }
            ?>
          </div>
        </div>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
   
    
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
     <script>
      function changeDatesheetSemester(duration)
      {
        document.location.href = "/student/examination/date-sheets?duration="+duration;
      }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
    
    