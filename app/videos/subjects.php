<?php
ini_set('display_errors', 1);
session_start();
require '../../includes/db-config.php';

$sub_course_id = intval($_POST['sub_course_id']);
$duration = $_POST['duration'];
$uniQuery = "";

if ($duration) {
  $studentSubjects = "SELECT Syllabi.`ID` as subject_id,Syllabi.Name,Code from Syllabi WHERE Syllabi.Sub_Course_ID = $sub_course_id AND Semester = '$duration' $uniQuery";
} else {
  $studentSubjects = "SELECT Syllabi.`ID` as subject_id,Syllabi.Name,Code from Syllabi WHERE Syllabi.Sub_Course_ID = $sub_course_id $uniQuery";
}

$subjects = mysqli_query($conn, $studentSubjects);

$html = '<option value="">Select Subject</option>';
while ($row = mysqli_fetch_assoc($subjects)) {
  $html = $html . '<option value="' . $row['subject_id'] . '">' . $row['Name'] . '(' . $row['Code'] . ')' . '</option>';
}

echo $html;

?>