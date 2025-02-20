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

  $alloted_fees = $conn->query("SELECT Fee, Sub_Course_ID FROM Sub_Center_Sub_Courses WHERE `User_ID` = $user_id AND `University_ID` = $university_id");
  while ($alloted_fee = $alloted_fees->fetch_assoc()) {
    $fees[$alloted_fee['Sub_Course_ID']] = $alloted_fee['Fee'];
  }

  $center_subCenters = $conn->query("SELECT Center FROM Center_SubCenter WHERE `Sub_Center` = $user_id");
  while ($center_subCenter = $center_subCenters->fetch_assoc()) {
    $user_id = $center_subCenter['Center'];
  }

  $sub_courses = $conn->query("SELECT Sub_Courses.ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') AS Sub_Course FROM Center_Sub_Courses LEFT JOIN Sub_Courses ON Center_Sub_Courses.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Courses ON Center_Sub_Courses.Course_ID = Courses.ID WHERE Center_Sub_Courses.User_ID =  $user_id AND Center_Sub_Courses.University_ID = $university_id");
  while ($sub_course = $sub_courses->fetch_assoc()) { ?>
    <div class="row pb-2">
      <div class="col-md-3">
        <dt class="pt-1"><?= $sub_course['Sub_Course'] ?></dt>
      </div>
      <div class="col-md-3">
        <input type="number" min="0" step="500" placeholder="Fee" name="fee[<?= $sub_course['ID'] ?>]"
          value="<?php echo array_key_exists($sub_course['ID'], $fees) ? $fees[$sub_course['ID']] : '' ?>"
          class="form-control" />
      </div>
    </div>
  <?php }


}
