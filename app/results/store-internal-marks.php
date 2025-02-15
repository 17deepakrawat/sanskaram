<?php
if (isset($_POST['enroll'])) {
  require '../../includes/db-config.php';
  session_start();

  $enroll = mysqli_real_escape_string($conn, $_POST['enroll']);
  $semester = isset($_POST['duration']) ? $_POST['duration'] : "";
  
  if ($_SESSION['university_id'] == 48) {
    $marksArr = is_array($_POST['obt_ext_marks']) ? array_filter($_POST['obt_ext_marks']) : [];
  } else {
    $marksArr = is_array($_POST['obt_marks_int']) ? array_filter($_POST['obt_marks_int']) : [];
  }
  
    $max_marks_arr = is_array($_POST['max_marks']) ? array_filter($_POST['max_marks']) : [];


  $add = '';
  $update = '';

  if (!empty($marksArr)) {
    if ($_SESSION['university_id'] == 48) {
      foreach ($marksArr as $sub_id => $obt_ext_marks) {
        $obt_ext_marks = mysqli_real_escape_string($conn, $obt_ext_marks);
        
         $max_marks =$max_marks_arr[$sub_id];
          if (($max_marks < $obt_ext_marks || $obt_ext_marks < 0) && strtolower($obt_ext_marks)!='ab') {
            echo json_encode(['status' => 400,'message' => 'Invalid marks!']);
            exit;
          }
          
        $check = $conn->query("SELECT * FROM marksheets WHERE enrollment_no='$enroll' AND subject_id = $sub_id ");
        if ($check->num_rows == 0) {
          $add = $conn->query("INSERT INTO marksheets (enrollment_no, subject_id, obt_marks_ext) VALUES ('$enroll', '$sub_id', '$obt_ext_marks')");
          if (!$add) {
            break;
          }
        } else {
          $update = $conn->query("UPDATE marksheets SET obt_marks_ext = '$obt_ext_marks' WHERE enrollment_no='$enroll' AND subject_id = $sub_id");
          if (!$update) {
            break;
          }
        }
      }
    } else {
      foreach ($marksArr as $sub_id => $obt_marks_int) {
        $obt_marks_int = mysqli_real_escape_string($conn, $obt_marks_int);
        
          $max_marks =$max_marks_arr[$sub_id];
          if (($max_marks < $obt_marks_int || $obt_marks_int < 0) && strtolower($obt_marks_int)!='ab') {
            echo json_encode(['status' => 400,'message' => 'Invalid marks!']);
            exit;
          }
        
        $check = $conn->query("SELECT * FROM marksheets WHERE enrollment_no='$enroll' AND subject_id = $sub_id ");
        if ($check->num_rows == 0) {
          $add = $conn->query("INSERT INTO marksheets (enrollment_no, subject_id, obt_marks_int) VALUES ('$enroll', '$sub_id', '$obt_marks_int')");
          if (!$add) {
            break;
          }
        } else {
          $update = $conn->query("UPDATE marksheets SET obt_marks_int = '$obt_marks_int' WHERE enrollment_no='$enroll' AND subject_id = $sub_id");
          if (!$update) {
            break;
          }
        }
      }
    }
  }


  if ($add || $update) {
    echo json_encode(['status' => 200, 'message' => 'Marks added successlly!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
?>