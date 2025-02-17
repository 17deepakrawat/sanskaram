<?php
require '../../includes/db-config.php';
require '../../includes/helpers.php';

session_start();
$enroll = isset($_POST['enroll']) ? $_POST['enroll'] : "";
$duration = isset($_POST['duration']) ? $_POST['duration'] : "";
$centerQuery = "";
$uni_id = $_SESSION['university_id'];
$marks_type = "Internal";

if (!empty($enroll) && !empty($duration) && $uni_id == UNIVERSITY_ID) {

    $getSubject = $conn->query("select sy.Min_Marks,sy.Max_Marks, sy.Name as subject_name,sy.User_ID,sy.Paper_Type, sy.Code, sy.ID from Syllabi as sy left join Students on sy.Sub_Course_ID= Students.Sub_Course_ID AND sy.Course_ID= Students.Course_ID where Enrollment_No = '$enroll' AND sy.Semester = '$duration'  $centerQuery ");
    if ($getSubject->num_rows == 0) {
        $data = ['status' => 400, 'message' => 'Subject Not Uploaded of this Duration & Sub-Course.'];
    } else {
        $data = ['status' => 200];
    }
} else {
    $data = ['status' => 400, 'message' => 'Something went wrong. Please try again!'];
}

?>

<?php if ($data['status'] == 200) { ?>
    <div class="row m-t-10">
        <div class="col-md-5"><span style="font-weight:500; font-size:14px">Subject Name</span></div>
        <div class="col-md-3"><span style="font-weight:500; font-size:14px">Min/Max Marks</span></div>
        <div class="col-md-4"><span style="font-weight:500; font-size:14px">Obtain <?= $marks_type ?> Marks</span></div>
    </div>
    <input type="hidden" name="enroll" value="<?= $enroll ?>">
    <div class="row mt-4">
        <?php
        $readonly = "";
        while ($row = $getSubject->fetch_assoc()) {
            $getmarks = $conn->query("SELECT obt_marks_int,obt_marks_ext FROM marksheets WHERE enrollment_no= '$enroll' AND subject_id = '" . $row['ID'] . "'");
            $marks = $getmarks->fetch_assoc();

            if ($getmarks->num_rows > 0 && ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center')) {
                $readonly = "readonly";
            } else if ($_SESSION['Role'] == "Operations") {
                $readonly = "readonly";
            }

            $obt_marks_int = isset($marks['obt_marks_int']) ? $marks['obt_marks_int'] : "";
            $obt_marks_ext = isset($marks['obt_marks_ext']) ? $marks['obt_marks_ext'] : "";


            $max_obt_ext = $row['Min_Marks'];
            $min_int_marks = ($row['Min_Marks']) * 40 / 100;
            $min_max_marks = $min_int_marks . '/' . $row['Min_Marks'];

            ?>
            <div class="col-md-5">
                <?= $row['subject_name'] . ' (' . $row['Code'] . ')' ?>
            </div>
            <div class="col-md-3">
                <?= $min_max_marks ?>
            </div>
            <div class="col-md-4">
                <div class="form-group form-group-default required">

                    <input type="text" name="obt_marks_int[<?= $row['ID'] ?>]" value="<?= $obt_marks_int ?>"
                        class="form-control" placeholder="Enter marks less than <?= $max_obt_ext ?>" <?= $readonly ?> required>

                    <input type="hidden" name="max_marks[<?= $row['ID'] ?>]" value="<?= $max_obt_ext ?>">
                </div>
            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <div class="row mt-2">
        <div class="col-md-12">
            <p style="color:red; text-align:center"><?= $data['message'] ?></p>
        </div>
    </div>
<?php } ?>