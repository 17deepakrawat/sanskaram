

 
<?php
ini_set('display_errors', 1);
require '../../includes/db-config.php';
require '../../includes/helpers.php';

session_start();
$username = $_GET['user_id'];
$password  = $_GET['password'];
$url = WEB_URL;
$passFail = "PASS";


$student = $conn->query("SELECT Students.ID FROM Students LEFT JOIN Modes on Students.University_ID=Modes.University_ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.Unique_ID LIKE '$username' AND Students.Unique_ID = '$password' AND Students.Step = 4 AND Students.Status = 1");

if ($student->num_rows > 0) {
 
  $student = $student->fetch_assoc();
  $data = array('status'=>true, 'url'=>WEB_URL.'/student/examination/result-pdf?id='.$student['ID']);

  echo json_encode($data);
} else {
  echo json_encode(array("message" => "Invalid username or password", "status" => 0));
}


$conn->close();
?>