<style>
  /* .checkbox_p{
    position: absolute !important;
    right: 0px !important;
  } */
</style>
<?php

ini_set('display_errors', 1);

if (isset($_GET['ids']) && isset($_GET['user_id']) && isset($_GET['university_id'])) {
  require '../../includes/db-config.php';

  $user_id = intval($_GET['user_id']);
  $university_id = intval($_GET['university_id']);
  $type_ids = mysqli_real_escape_string($conn, $_GET['ids']);
  if (empty($type_ids)) {
    exit;
  }
  $fees = [];
  $statuses = [];
  
    $alloted_fees = $conn->query("SELECT Fee, Sub_Course_ID,Sub_Courses_Status FROM Center_Sub_Courses WHERE `User_ID` = $user_id AND `University_ID` = $university_id");


    while ($alloted_fee = $alloted_fees->fetch_assoc()) {
      $fees[$alloted_fee['Sub_Course_ID']] = $alloted_fee['Fee'];
      $statuses[$alloted_fee['Sub_Course_ID']] = $alloted_fee['Sub_Courses_Status'];
    }


    $sub_courses = $conn->query("SELECT Sub_Courses.ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') AS Sub_Course FROM Sub_Courses LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID WHERE Courses.Course_Type_ID IN ($type_ids) ORDER BY  Sub_Courses.Name ASC");
    while ($sub_course = $sub_courses->fetch_assoc()) {
     
      ?>
      <div class="row pb-2">
        <div class="col-md-7">
          <dt class="pt-1"><?= $sub_course['Sub_Course']; ?></dt>
        </div>
        <div class="col-md-3">
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
