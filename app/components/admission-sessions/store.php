<?php

require '../../../includes/db-config.php';
session_start();
if(isset($_POST['name']) && isset($_POST['university_id']) && isset($_POST['scheme'])){
  
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $exam_session = mysqli_real_escape_string($conn, $_POST['exam_session']);
  $scheme = intval($_POST['scheme']);
  $university_id = intval($_POST['university_id']);
  
  if(empty($name) || empty($university_id)) {
    exit(json_encode(['status'=>403, 'message'=>'All fields are mandatory!']));
  }
  insertAdmission($name,$university_id,$exam_session,$scheme);

} elseif (isset($_POST['month']) && isset($_POST['year']) && isset($_POST['scheme'])) {

  $month = mysqli_real_escape_string($conn,$_POST['month']);
  $year = mysqli_real_escape_string($conn,$_POST['year']);
  $scheme = intval($_POST['scheme']);
  $university_id = intval($_POST['university_id']);

  if(empty($month) || empty($year) || empty($university_id)) {
    exit(json_encode(['status'=>403, 'message'=>'All fields are mandatory!']));
  }

  $name = date('M',mktime(0, 0, 0, $month, 1)) .'-'. $year;
  insertAdmission($name,$university_id,null,$scheme);
}

function insertAdmission($name,$university_id,$exam_session,$scheme) {
  global $conn;
  $check = $conn->query("SELECT ID FROM Admission_Sessions WHERE Name LIKE '$name' AND University_ID = $university_id");
  if($check->num_rows>0){
    exit(showResponse(false,$name.' already exists!'));
  }
  
  $add = $conn->query("INSERT INTO `Admission_Sessions` (`Name`, `Exam_Session`, `Scheme_ID`,  `University_ID`) VALUES ('$name', '$exam_session', $scheme, $university_id)");
  showResponse($add,$name);
}

function showResponse($response,$message = 'Something went wrong!') {
  if($response){
    echo json_encode(['status'=>200, 'message'=>$message.' added successlly!']);
  } else {
    echo json_encode(['status'=>400, 'message'=>$message]);
  }
}

?>
