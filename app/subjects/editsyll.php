<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require '../../includes/db-config.php';
    // Get and escape user inputs
    $id = $conn->real_escape_string($_POST['ID']);
    $universities = $conn->real_escape_string($_POST['universities']);
    $coursetype = $conn->real_escape_string($_POST['coursetype']);
    $sub_course_type = $conn->real_escape_string($_POST['subcourse_id']);
    $semester = $conn->real_escape_string($_POST['seme']);
    $name = $conn->real_escape_string($_POST['subjectname']);
    $papertype = $conn->real_escape_string($_POST['paper_type']);
    $credit = $conn->real_escape_string($_POST['subjectcredit']);
    $minMarks = $conn->real_escape_string($_POST['minMarks']);
    $maxMarks = $conn->real_escape_string($_POST['maxMarks']);
    $code = $conn->real_escape_string($_POST['subjectcode']);
    $semester = $conn->real_escape_string($_POST['seme']);
    
    // Handle file upload


    $sql = "UPDATE Syllabi SET 
    University_ID = '$universities',
    Course_ID = '$coursetype',
    Sub_Course_ID = '$sub_course_type',
    Semester = '$semester',
                Name = '$name', 
                Paper_Type = '$papertype', 
                Credit = '$credit', 
                Min_Marks = '$minMarks', 
                Max_Marks = '$maxMarks', 
                Code = '$code',
                Semester = $semester
            WHERE ID = $id";
      
    if ($conn->query($sql) === TRUE) {
            echo json_encode(['status'=>200, 'message'=>'Counsellor updated successlly!']);
          }else{
            echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
          }
} else {
    echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);

}

// Close the database connection
$conn->close();
