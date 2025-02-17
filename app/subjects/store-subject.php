<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require '../../includes/db-config.php';
    // Get and escape user inputs
    $course = $conn->real_escape_string($_POST['course']);
    $sub_course = $conn->real_escape_string($_POST['sub_course']);
    $semester = $conn->real_escape_string($_POST['semester']);
    $name = $conn->real_escape_string($_POST['name']);
    $code = $conn->real_escape_string($_POST['code']);
    $paper_type = $conn->real_escape_string($_POST['paper_type']);
    $credit = $conn->real_escape_string($_POST['credit']);
    $min_marks = $conn->real_escape_string($_POST['min_marks']);
    $max_marks = $conn->real_escape_string($_POST['max_marks']);
    $university_id = UNIVERSITY_ID;

    $getSchemeID = $conn->query("SELECT Scheme_ID FROM Sub_Courses WHERE ID = $sub_course");
    $scheme_id = $getSchemeID->fetch_assoc()['Scheme_ID'];


    // Handle file upload

    $add = $conn->query("INSERT INTO Syllabi (Name,Code,Course_ID, Sub_Course_ID, University_ID,Paper_Type,Credit,Min_Marks,Max_Marks,Semester,Scheme_ID) VALUES ('$name', '$code',$course,$sub_course,$university_id,'$paper_type','$credit',$min_marks,$max_marks,'$semester', $scheme_id)");

    if ($add) {
        echo json_encode(['status' => 200, 'message' => 'Subject Added successlly!']);
    } else {
        echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
} else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);

}
