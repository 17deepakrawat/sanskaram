<?php


ini_set('display_errors', 1);
require '../../includes/db-config.php';
require '../../includes/helpers.php';
session_start();
$passFail = "PASS";
$typoArr = ["th", "st", "nd", "rd", "th", "th", "th", "th", "th"];

if (isset($_GET['user_id']) && isset($_GET['password'])) {
    $user_id = $_GET['user_id'];
    $password = $_GET['password'];
    $searchQuery = " AND Students.Unique_ID LIKE '$user_id' AND Students.Unique_ID = '$password' ";
} else if (isset($_GET['enroll_no'])) {
    $searchQuery = " AND Students.Enrollment_No = '" . $_GET['enroll_no'] . "'";
} else if (isset($_GET['studentId'])) {
    $searchQuery = " AND Students.ID = '" . $_GET['studentId'] . "'";
} else {
    $searchQuery = " AND Students.Enrollment_No = '" . $_SESSION['Enrollment_No'] . "'";
}

$student = $conn->query("SELECT  Students.Unique_ID,Students.Exam, Students.ID,Students.Enrollment_No,Students.University_ID,Students.Duration,Sub_Courses.Min_Duration,Students.Course_Category,Students.Course_ID ,Students.Sub_Course_ID , CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name))) AS stu_name,Students.Father_Name,Students.University_ID,Courses.Name as program_Type, Sub_Courses.Name as course,Modes.Name as mode, Course_Types.Name as Course_Type, Admission_Sessions.Name as Admission_Session, Admission_Sessions.Exam_Session, Admission_Types.Name as Admission_Type, CONCAT(Courses.Short_Name, ' (',Sub_Courses.Name,')') as Course_Sub_Course, TIMESTAMPDIFF(YEAR, DOB, CURDATE()) AS Age FROM Students LEFT JOIN Modes on Students.University_ID=Modes.University_ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE 1=1 $searchQuery LIMIT 1");
$Students_temps = [];
if ($student->num_rows > 0) {
    $Students_temps = $student->fetch_assoc();

    $result_test = "";
    $semQuery = '';
    $category_type = '';
    $due_val = '';
    $obt_query = "";


    if (isset($_GET['year_sem'])) {
        $sem = $_GET['year_sem'];
    } else {
        $sem = 1;
    }
    $due_val = $sem;
    $semQuery = ' AND s.Semester=' . $sem;
    $obt_query = "";
    // $obt_query = " AND m.obt_marks_int IS NOT NULL AND m.obt_marks_ext IS NOT NULL ";



    $total_subject = totalUloadedSubjectsFunc($conn, $Students_temps['University_ID'], $Students_temps['ID'], $due_val);
    $total_subject_count = count($total_subject);

    $getDataSQL = $conn->query("SELECT Paper_Type,s.Name as subject_name,m.exam_month,m.exam_year, s.Code,s.Max_Marks, s.Min_Marks, m.obt_marks,m.remarks,m.obt_marks_ext,m.obt_marks_int From marksheets AS m LEFT JOIN Syllabi AS s ON m.subject_id = s.ID WHERE m.enrollment_no = '" . $Students_temps['Enrollment_No'] . "' AND s.Course_ID = " . $Students_temps['Course_ID'] . " AND  s.Sub_Course_ID = " . $Students_temps['Sub_Course_ID'] . "   $semQuery $obt_query ORDER BY s.Code");
    if ($getDataSQL->num_rows == 0) {
        echo json_encode(['status' => false, 'msg' => 'Result Not Published Yet.']);
        die;
    }


    $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = " . $Students_temps['ID'] . " AND Type = 'Photo'");
    if ($photo->num_rows > 0) {
        $photo = $photo->fetch_assoc();
        $Students_temps['Photo'] = $photo['Location'];
    }
    // echo "SELECT m.exam_month,m.exam_year FROM marksheets AS m LEFT JOIN Syllabi AS s ON m.subject_id = s.ID WHERE m.enrollment_no = '" . $Students_temps['Enrollment_No'] . "' AND s.Course_ID = " . $Students_temps['Course_ID'] . "  AND  s.Sub_Course_ID = " . $Students_temps['Sub_Course_ID'] . " $semQuery  GROUP BY m.enrollment_no"; die;
    $exam_date = $conn->query("SELECT m.exam_month,m.exam_year FROM marksheets AS m LEFT JOIN Syllabi AS s ON m.subject_id = s.ID WHERE m.enrollment_no = '" . $Students_temps['Enrollment_No'] . "' AND s.Course_ID = " . $Students_temps['Course_ID'] . "  AND  s.Sub_Course_ID = " . $Students_temps['Sub_Course_ID'] . " $semQuery  GROUP BY m.enrollment_no");
    if ($exam_date->num_rows > 0) {
        $examArr = $exam_date->fetch_assoc();
        if (!empty($examArr['exam_month']) || !empty($examArr['exam_year'])) {
            $Students_temps['stu_exam_session'] = ucwords($examArr['exam_month']) . '-' . $examArr['exam_year'];
        } else {
            $Students_temps['stu_exam_session'] = ucwords($Students_temps['Exam_Session']);
        }
    }

    $total_obt = 0;
    $total_max = 0;
    $result_status = [];
    $total_obt_marks = [];
    $obt_marks_ab_int = 0;
    $obt_marks_ab_ext = 0;
    while ($getDataArr = $getDataSQL->fetch_assoc()) {
        // echo "<pre>"; print_r($getDataArr);
        $getDataArr['remarks_status'] = "Pass";
        $obt_marks_ext = ($getDataArr['obt_marks_ext'] == 'AB') ? 'AB' : (isset($getDataArr['obt_marks_ext']) ? $getDataArr['obt_marks_ext'] : 0);
        $obt_marks_int = ($getDataArr['obt_marks_int'] == 'AB') ? 'AB' : (isset($getDataArr['obt_marks_int']) ? $getDataArr['obt_marks_int'] : 0);
        $total_obt = (int) $obt_marks_ext + (int) $obt_marks_int;

        // start min value
        $min_val = '0';
        if ($getDataArr['Min_Marks'] == 40 && $getDataArr['Max_Marks'] == 60) {
            $min_val = ($getDataArr['Min_Marks'] + $getDataArr['Max_Marks']) * 40 / 100;
        } else if ($getDataArr['Max_Marks'] == 100 && $getDataArr['Min_Marks'] == 40) {
            $min_val = $getDataArr['Min_Marks'];
        } else if ($getDataArr['Max_Marks'] == 100 && $getDataArr['Min_Marks'] == 100) {
            // $min_val = ($getDataArr['Max_Marks'])*40/100;
        }
        // end min value
        // start remarks_status

        if ($total_obt <= $min_val || $getDataArr['obt_marks_ext'] == 0 || $getDataArr['obt_marks_ext'] == 'AB' || $getDataArr['obt_marks_int'] == 0 || $getDataArr['obt_marks_int'] == 'AB') {
            $getDataArr['remarks_status'] = "FAIL";
        }
        $total_max = $total_max + $getDataArr['Min_Marks'] + $getDataArr['Max_Marks'];

        // end remarks_status
        $getDataArr['obt_marks_ext'] = ($getDataArr['obt_marks_ext'] == 'AB') ? 'AB' : trim($getDataArr['obt_marks_ext']);
        $getDataArr['obt_marks_int'] = ($getDataArr['obt_marks_int'] == 'AB') ? 'AB' : trim($getDataArr['obt_marks_int']);

        // start total obt
        $obt_marks_ab_ext = $getDataArr['obt_marks_ext'];
        $obt_marks_ab_int = $getDataArr['obt_marks_int'];

        if ($obt_marks_ab_ext == 'AB' && $obt_marks_ab_int != 'AB') {
            $obt_marks_ab_ext = 0;
        } else if ($obt_marks_ab_int == 'AB' && $obt_marks_ab_ext != 'AB') {
            $obt_marks_ab_int = 0;
        } else if ($obt_marks_ab_int == 'AB' && $obt_marks_ab_ext == 'AB') {
            $obt_marks_ab_int = 0;
            $obt_marks_ab_ext = 0;
        }
        $obt_marks_ab_ext;
        // ECHO $obt_marks_ab_int;

        $getDataArr['total_obtain_ext_int'] = (int) $obt_marks_ab_int + (int) $obt_marks_ab_ext;
        $getDataArr['grand_total_ext_int'] = $getDataArr['Min_Marks'] + $getDataArr['Max_Marks'];// grand total obt

        if ($getDataArr['Paper_Type'] == 'Practical' && $getDataArr['obt_marks_int'] == 0 && $getDataArr['Min_Marks'] == 0) {
            $getDataArr['Min_Marks'] = '-';
            $getDataArr['obt_marks_int'] = "-";
            $getDataArr['remarks_status'] = "Pass";
        }
        // store value in Array
        $result_status[] = $getDataArr['remarks_status'];
        $getDataArr['minimum_marks'] = $min_val;
        $Students_temps['marks'][] = $getDataArr;
        $total_obt_marks[] = $total_obt;
    }

    $Students_temps['total_max'] = $total_max;
    $Students_temps['total_obt'] = array_sum($total_obt_marks);
    $Students_temps['in_word_marks'] = ucwords(strtolower(numberToWordFunc($Students_temps['total_obt'])));
    $Students_temps['Enrollment_No'] = isset($Students_temps['Enrollment_No']) ? $Students_temps['Enrollment_No'] : '';
    $percentage = 0;
    if ($total_max != 0) {
        $percentage = ($Students_temps['total_obt'] / $total_max) * 100;
    }

    if (in_array('FAIL', $result_status)) {
        $Students_temps['result_status'] = 'Fail';
        $Students_temps['percentage'] = '';
    } else {
        $Students_temps['result_status'] = 'Pass';
        $Students_temps['percentage'] = number_format($percentage, 2) . '%';
    }

    $durMonthYear = "";
    if ($Students_temps['mode'] == "Monthly") {
        $durMonthYear = " Months";
    } elseif ($Students_temps['mode'] == "Sem") {
        $durMonthYear = " Semester";
    } else {
        $durMonthYear = " Years";
    }


    $Students_temps['mode_type'] = "Semester";
    $Students_temps['university_name'] = "Vocational Studies";

    $durations = '';

    $durations = "B. VOC";
    $Students_temps['durMonthYear'] = $sem . $typoArr[$sem];

    $Students_temps['duration_val'] = $durations;
    $Students_temps['stu_name'] = strtoupper($Students_temps['stu_name']);
    $Students_temps['Father_Name'] = strtoupper($Students_temps['Father_Name']);


    $Students_temps['Min_Duration'] = json_decode($Students_temps['Min_Duration'], true)[0];

    //echo "<pre>";
    //print_r($Students_temps);die;

    echo json_encode(['status' => true, 'data' => $Students_temps]);

} else {
    echo json_encode(['status' => false, 'msg' => 'Invalid credentials!']);
    // echo '<div class="mt-5 mb-4 text-center" style="margin-top:220px;"><h5>Invalid credentials!</h5></div>';
    die;
}





