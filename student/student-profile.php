<?php
require '../includes/db-config.php';
$data = [];
if (isset($_GET['user_id']) && $_GET['user_id']) {

    $user_id = $_GET['user_id'];
    $password = $_GET['password'];

    $searchQuery = '';
  
    $student = $conn->query("SELECT Student_Documents.Location as photo, Students.Unique_ID,Students.Exam, Students.ID,Students.Enrollment_No,Students.University_ID,Students.Duration,Sub_Courses.Min_Duration,Students.Course_Category,Students.Course_ID ,Students.Sub_Course_ID , CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name))) AS stu_name,Students.Father_Name,Students.Mother_Name,Students.University_ID,Courses.Short_Name as course_short_name, Sub_Courses.Name as course,Modes.Name as mode, Course_Types.Name as Course_Type, Admission_Sessions.Name as Admission_Session, Admission_Sessions.Exam_Session, Admission_Types.Name as Admission_Type, CONCAT(Courses.Short_Name, ' (',Sub_Courses.Name,')') as Course_Sub_Course, TIMESTAMPDIFF(YEAR, DOB, CURDATE()) AS Age FROM Students LEFT JOIN Modes on Students.University_ID=Modes.University_ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.Type= 'Photo' WHERE 1=1 AND Students.Step = 4 AND Students.Status = 1 AND Students.University_ID =".UNIVERSITY_ID." AND UPPER(DATE_FORMAT(Students.DOB, '%d%b%Y')) = '$password' AND Students.Enrollment_No = '$user_id' LIMIT 1");

    if ($student->num_rows > 0) {
        $row = $student->fetch_assoc();
        $data = $row;
        $data['status'] = 200;
        $data['message'] = "Welcome !" . $row['stu_name'];
        echo json_encode($data);
    } else {
        $data = [
            'message' => "Invalid credentials!",
            'status' => "400"
        ];
        echo json_encode($data);
    }
} else {
    $data = [
        'message' => "Invalid credentials!",
        'status' => "400"
    ];
    echo json_encode($data);
}