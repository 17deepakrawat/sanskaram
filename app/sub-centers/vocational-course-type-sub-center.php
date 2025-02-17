<?php
ini_set('display_errors', 1);

if (isset($_GET['id']) && isset($_GET['university_id'])) {
  require '../../includes/db-config.php';

  $university_id = intval($_GET['university_id']);
  $id = intval($_GET['id']);

  $subCourseData = [];
  $subcenterQuery = $conn->query("SELECT Code, ID,Role FROM Users WHERE ID=$id AND Role='Sub-Center'");
  $subcenterArr = $subcenterQuery->fetch_assoc();
  $subcentercode = explode('.', $subcenterArr["Code"]);
  $centerCode = $subcentercode[0];
  $centerQuery = $conn->query("SELECT ID, Code, Role FROM Users WHERE Code='$centerCode' AND Role='Center'");
  $centerArr = $centerQuery->fetch_assoc();

  $course_id = $conn->query("SELECT Course_ID, Sub_Course_ID FROM Center_Sub_Courses WHERE `User_ID` = " . $centerArr['ID'] . " AND University_ID = $university_id");

  while ($courseIdArr = $course_id->fetch_assoc()) {

    $subCourseId = $courseIdArr['Sub_Course_ID'];
    $courseId = $courseIdArr['Course_ID'];
    $subCourseQuery = $conn->query("SELECT ID, Name, Course_ID, University_ID, Min_Duration as durections FROM Sub_Courses WHERE `ID` = $subCourseId AND `Course_ID` = $courseId AND University_ID = $university_id");
    $subCourseArr = $subCourseQuery->fetch_assoc();
    $subCourseData[] = $subCourseArr;
  }

  $courseTypeQuery = $conn->query("SELECT Course_Type_ID FROM Courses WHERE ID=$courseId AND Status =1");
  $courseTypeId = $courseTypeQuery->fetch_assoc();

  $user_id = $centerArr['ID'];

  $type_ids = $courseTypeId['Course_Type_ID'];
  if (empty($type_ids)) {
    exit;
  }
  $fees = [];


  $alloted_fees = $conn->query("SELECT Fee, Sub_Course_ID,Sub_Courses_Status FROM Sub_Center_Sub_Courses WHERE `User_ID` = $id AND `University_ID` = $university_id");


  while ($alloted_fee = $alloted_fees->fetch_assoc()) {
    $fees[$alloted_fee['Sub_Course_ID']] = $alloted_fee['Fee'];
    $statuses[$alloted_fee['Sub_Course_ID']] = $alloted_fee['Sub_Courses_Status'];
  }

  foreach ($subCourseData as $sub_course) {
    ?>
    <div class="row pb-2">
      <div class="col-md-7">
        <dt class="pt-1">
          <?= $sub_course['Name']; ?>
        </dt>
      </div>
      <div class="col-md-3">
        <input type="hidden" id="course_type" name="course_type[]" value="<?= $type_ids ?>">
        <input type="number" min="0" step="500" placeholder="Fee" name="fee[<?= $sub_course['ID'] ?>]"
          value="<?php echo array_key_exists($sub_course['ID'], $fees) ? $fees[$sub_course['ID']] : '' ?>"
          class="form-control" />
      </div>
      <div class="col-md-2 d-flex text-center justify-content-center">
        <input type="checkbox" name="sub_course_status[<?= $sub_course['ID'] ?>]" <?= isset($statuses[$sub_course['ID']]) && ($statuses[$sub_course['ID']] == 1) ? 'checked' : '' ?> class="text-center" value="1">
      </div>
    </div>
  <?php }

}
