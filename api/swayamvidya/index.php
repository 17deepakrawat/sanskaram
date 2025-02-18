<?php


header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST['center'])) {
    require '../../includes/db-config.php';
    include '../../includes/helpers.php';
    ini_set('display_errors', 1);
    $admission_session_id = $conn->query("SELECT ID FROM Admission_Sessions WHERE Name = '" . $_POST['admission_session'] . "' ");
    $admission_type_id = $conn->query("SELECT ID FROM Admission_Types WHERE Name = '" . $_POST['admission_type'] . "' ");
    $course_id = $conn->query("SELECT ID,University_ID FROM Courses WHERE Name Like '%" . $_POST['program_type'] . "%' OR Short_Name like '%" . $_POST['program_type'] . "%'");
    $sub_course_id = $conn->query("SELECT ID FROM Sub_Courses WHERE Name Like '%" . $_POST['specialization'] . "%' OR Short_Name like '%" . $_POST['specialization'] . "%'");
    $center_id = $conn->query("SELECT ID FROM Users WHERE Name Like '%" . $_POST['center'] . "%' OR Short_Name like '%" . $_POST['center'] . "%' OR Code Like '%" . $_POST['center'] . "%'");
    $admission_session_id = mysqli_fetch_assoc($admission_session_id);

    if (!isset($admission_session_id)) {
        echo json_encode(array(['status' => 'error', 'message' => 'Admission session not found.']));
        exit;
    }
    $admission_type_id = mysqli_fetch_assoc($admission_type_id);
    if (!isset($admission_type_id)) {
        echo json_encode(array(['status' => 'error', 'message' => 'Admission type not found.']));
        exit;
    }
    $course_id = mysqli_fetch_assoc($course_id);
    if (!isset($course_id)) {
        echo json_encode(array(['status' => 'error', 'message' => 'Program type not found.']));
        exit;
    }
    $sub_course_id = mysqli_fetch_assoc($sub_course_id);
    if (!isset($sub_course_id)) {
        echo json_encode(array(['status' => 'error', 'message' => 'Specialization not found.']));
        exit;
    }
    $center_id = mysqli_fetch_assoc($center_id);
    if (!isset($center_id)) {
        echo json_encode(array(['status' => 'error', 'message' => 'Center not found.']));
        exit;
    }
    if (!isset($_POST['phone']) && !isset($_POST['phone'])) {
        echo json_encode(array(['status' => 'error', 'message' => 'Mobile number not exit.']));
        exit;
    }
    $id = '';
    $lead_id = intval($_POST['lead_id']);
    $center = intval($_POST['center_id']);
    $admission_session = intval($admission_session_id['ID']);
    $admission_type = intval($admission_type_id['ID']);
    $course = intval($course_id['ID']);
    $sub_course = intval($sub_course_id['ID']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    if (isset($_POST['full_name']) && $_POST['full_name'] != '') {
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $full_name = str_replace('  ', ' ', $full_name);
        $full_name = explode(' ', $full_name, 3);
        $count = count($full_name);
        if ($count == 2) {
            $first_name = trim($full_name[0]);
            $first_name = strtoupper(strtolower($first_name));
            $middle_name = NULL;
            $last_name = trim($full_name[1]);
            $last_name = strtoupper(strtolower($last_name));
        } elseif ($count > 2) {
            $first_name = trim($full_name[0]);
            $first_name = strtoupper(strtolower($first_name));
            $middle_name = trim($full_name[1]);
            $middle_name = strtoupper(strtolower($middle_name));
            $last_name = trim($full_name[2]);
            $last_name = strtoupper(strtolower($last_name));
        } else {
            $first_name = trim($full_name[0]);
            $first_name = strtoupper(strtolower($first_name));
            $middle_name = NULL;
            $last_name = NULL;
        }
    } else {
        $first_name = strtoupper($_POST['first_name']);
        $middle_name = strtoupper($_POST['middle_name']);
        $last_name = strtoupper($_POST['last_name']);
    }

    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $father_name = strtoupper(strtolower($father_name));
    $mother_name = mysqli_real_escape_string($conn, $_POST['mother_name']);
    $mother_name = strtoupper(strtolower($mother_name));

    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $dob = date('Y-m-d', strtotime($dob));

    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $employment_status = mysqli_real_escape_string($conn, $_POST['employment_status']);
    $marital_status = mysqli_real_escape_string($conn, $_POST['marital_status']);
    $religion = mysqli_real_escape_string($conn, $_POST['religion']);
    $aadhar = mysqli_real_escape_string($conn, $_POST['aadhar']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);


    $mode = $conn->query("SELECT Mode_ID FROM Sub_Courses WHERE ID = $sub_course");
    $mode = mysqli_fetch_assoc($mode);
    $mode = $mode['Mode_ID'];




    $student_check = $conn->query("SELECT ID FROM Students WHERE First_Name = '$first_name' AND Father_Name = '$father_name' AND Mother_Name = '$mother_name' AND DOB = '$dob' AND University_ID = " . $course_id['University_ID'] . " AND Course_ID = $course AND Added_For = $center");
    if ($student_check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Student with same details already exists!']);
        exit();
    }


    $add_student = $conn->query("INSERT INTO Students (Added_By, Added_For, University_ID, Admission_Type_ID, Admission_Session_ID, Course_ID, Sub_Course_ID, Mode_ID, Duration,Adm_Duration, First_Name, Middle_Name, Last_Name, Father_Name, Mother_Name, DOB, Aadhar_Number, Category, Gender, Nationality, Employement_Status, Marital_Status, Religion, Step) VALUES(" . $center . ", $center, " . $course_id['University_ID'] . ", $admission_type, $admission_session, $course, $sub_course, $mode, '" . $duration . "', '" . $duration . "', '$first_name', '$middle_name', '$last_name', '$father_name', '$mother_name', '$dob', '$aadhar', '$category', '$gender', '$nationality', '$employment_status', '$marital_status', '$religion', 1)");

    if ($add_student) {
        $student_id = $conn->insert_id;


        if (empty($lead_id)) {
            $has_unique_student_id = $conn->query("SELECT ID_Suffix, Max_Character FROM Universities WHERE ID = " . $course_id['University_ID'] . " AND Has_Unique_StudentID = 1");
            if ($has_unique_student_id->num_rows > 0) {
                $has_unique_student_id = $has_unique_student_id->fetch_assoc();
                $suffix = $has_unique_student_id['ID_Suffix'];
                $characters = $has_unique_student_id['Max_Character'];
                $unique_id = generateStudentID($conn, $suffix, $characters, $course_id['University_ID']);
                $conn->query("UPDATE Students SET Unique_ID = '$unique_id' WHERE ID = $student_id");

                // ABC ID update
                if (isset($_POST['abc_id'])) {
                    $abcid = mysqli_real_escape_string($conn, $_POST['abc_id']);
                    $conn->query("UPDATE Students SET ABC_ID = '$abcid' WHERE ID = $student_id");
                }
            }
        } else {
            $unique_id = $conn->query("SELECT Unique_ID FROM Lead_Status WHERE ID = $lead_id");
            $unique_id = $unique_id->fetch_assoc();
            $conn->query("UPDATE Students SET Unique_ID = '" . $unique_id['Unique_ID'] . "' WHERE ID = $student_id");

            $final_stage = $conn->query("SELECT ID FROM Stages WHERE Is_Last = 1");
            if ($final_stage->num_rows > 0) {
                $final_stage = $final_stage->fetch_assoc();
                $final_stage = $final_stage['ID'];
            } else {
                $final_stage = $conn->query("INSERT INTO Stages (`Name`, Is_Last) VALUES ('Admission Done', 1)");
                $final_stage = $conn->insert_id;
            }

            $conn->query("UPDATE Lead_Status SET Admission = 1, Stage_ID = $final_stage, Reason_ID = NULL WHERE ID = $lead_id");
        }
        generateStudentLedger($conn, $student_id);
        /////////STEP 1 FORM ENDS HERE


        ///////STEP 2 FORM START FROM HERE
        $step = $conn->query("SELECT Step FROM Students WHERE ID = $student_id");
        $step = mysqli_fetch_array($step);
        $step = $step['Step'];

        $step_query = "";
        if ($step < 2) {
            $step_query = ", `Step` = 2";
        }


        $email = mysqli_real_escape_string($conn, $_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email']);
            exit();
        }
        $email = strtolower($email);

        $alternate_email = mysqli_real_escape_string($conn, $_POST['alternate_email']);
        if (!empty($alternate_email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid alternate email']);
            exit();
        }
        $alternate_email = strtolower($alternate_email);
        $contact = mysqli_real_escape_string($conn, $_POST['phone'] ? $_POST['phone'] : ($_POST['mobile'] ? $_POST['mobile'] : 0));
        if (strlen($contact) < 10) {
            echo json_encode(['status' => 'error', 'message' => 'Please Enter the 10 Digits Number!']);
        }
        $alternate_contact = mysqli_real_escape_string($conn, $_POST['alternate_contact']);

        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $address = strtoupper(strtolower($address));
        $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
        $city = mysqli_real_escape_string($conn, $_POST['city']);
        $city = strtoupper(strtolower($city));
        $district = mysqli_real_escape_string($conn, $_POST['district']);
        $district = strtoupper(strtolower($district));
        $state = mysqli_real_escape_string($conn, $_POST['state']);
        $state = strtoupper(strtolower($state));

        $address = json_encode(['present_address' => $address, 'present_pincode' => $pincode, 'present_city' => $city, 'present_district' => $district, 'present_state' => $state]);

        $update = $conn->query("UPDATE Students SET Email = '$email', Alternate_Email = '$alternate_email', Contact = '$contact', Alternate_Contact = '$alternate_contact', Address = '$address' $step_query WHERE ID = $student_id");

        ///////STEP 2 ENDS HERE

        //////STEP 3 START FROM HERE
        include 'document_upload.php';
        $uploadStatus = uploadDocuments($student_id, $_POST);
        if (json_decode($uploadStatus, true)['status'] == 'error') {
            echo json_encode(array(['status' => 'error', 'message' => $uploadStatus['message']]));
            exit;
        }
        /////STEP 3 ENDS HERE

        return json_encode(['status' => 'success', 'message' => 'Student data added!', 'student_id' => $unique_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data not added']);
    }
} else {
    echo json_encode(array(['status' => 'error', 'message' => 'center name or code or short name required']));
}
