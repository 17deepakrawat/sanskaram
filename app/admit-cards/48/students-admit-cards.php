<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

if (isset($_GET['student_id']) || isset($_GET['student_ids'])) {

    require '../../../includes/db-config.php';
    session_start();
    date_default_timezone_set('Asia/Kolkata');
    if (isset($_GET['student_ids'])) {
        $id = mysqli_real_escape_string($conn, $_GET['student_ids']);
    } else {


        $id = mysqli_real_escape_string($conn, $_GET['student_id']);
        $id = base64_decode($id);
        $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
        //$id='100';
    }

    $student = $conn->query("SELECT Students.*, Courses.Name as course_name, Admission_Sessions.Exam_Session as Session, Sub_Courses.Name as sub_course_name, Date_Sheets.Exam_date as exam_date, Date_Sheets.Start_time as start_time, Date_Sheets.End_time as end_time, Student_Documents.Location as photos 
                        FROM Students 
                        LEFT JOIN Student_Documents ON Students.ID = Student_Documents.Student_ID AND Student_Documents.Type = 'Photo'
                        LEFT JOIN Courses ON Students.Course_ID = Courses.ID 
                        LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID 
                        LEFT JOIN Syllabi ON Students.Sub_Course_ID = Syllabi.Sub_Course_ID 
                        LEFT JOIN Date_Sheets ON Syllabi.ID = Date_Sheets.Syllabus_ID 
                        LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID 
                        WHERE Students.Id = $id");


    if ($student->num_rows == 0) {
        header('Location: /dashboard');
    }

    $student = $student->fetch_assoc();
    //   echo "<pre>"; print_r($student);
    if(strtolower($student['Course_Category'])=='certified' && ($student['Duration']==6  || $student['Duration']==11)){
        $duration = $student['Duration'].'/'.$student['Course_Category'];
    }else if(strtolower($student['Course_Category'])=='certification'){
        $duration = $student['Duration'].'/certification';
    }else if(strtolower($student['Course_Category'])=='advance-diploma' && strtolower($student['Duration'])=='11/advance-diploma'){
        $duration = '11/advanced';
    }else{
        $duration = $student['Duration'];
    }

    $file_extensions = array('.png', '.jpg', '.jpeg');

    $photo = "";
    // $document = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = " . $student['ID'] . " AND `Type` = 'Photo'");
    // if ($document->num_rows > 0) {
    //   $photo = $document->fetch_assoc();

    $photo = rtrim("../../..", '/') . '/' . ltrim($student['photos'], '/');

    if (file_exists($photo) && filetype($photo) === 'file') {
        // Get the student photo as a base64 encoded string
        $student_photo = base64_encode(file_get_contents($photo));
    
        // Decode the base64-encoded photo once
        $data1 = base64_decode($student_photo);
    
        // Assuming $file_extensions is an array with valid extensions
        $i = 0;
        $end = 3; // Adjust as needed
        
        while ($i < $end) {
            // Create filenames using the student's ID and file extension
            $filename1 = $student['ID'] . "_Photo" . $file_extensions[$i];
    
            // Write the decoded image content to the file
            file_put_contents($filename1, $data1);
            $i++;
        }
    }
    
    require_once('../../../extras/qrcode/qrlib.php');
    require_once('../../../extras/vendor/setasign/fpdf/fpdf.php');
    require_once('../../../extras/vendor/setasign/fpdi/src/autoload.php');

    $pdf = new Fpdi();

    $pdf->SetTitle('Admit Card');

    $pageCount = $pdf->setSourceFile('Sanskaram Admit Card.pdf');

    $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
    $pdf->addPage();
    $pdf->useImportedPage($pageId, 0, 0, 210);

    $pdf->SetMargins(0, 0, 0);
    $pdf->SetAutoPageBreak(true, 1);

    $pdf->AddFont('Hondo', '', 'hondo.php');
    $pdf->SetFont('Hondo', '', 12);

    $pdf->SetXY(165, 25);
    $pdf->Write(1, $student['Enrollment_No']);

    // $student_id = empty($student['Unique_ID']) ? $student['ID'] : $student['Unique_ID'];
    // $pdf->SetXY(159, 45.5);
    // $pdf->Write(1, $student_id);

    $student_name = array($student['First_Name'] . " " . $student['Middle_Name'] . " " . $student['Last_Name']);
    $student_name = array_filter($student_name);
    $pdf->SetXY(29, 32);
    $pdf->Write(1, ucwords(strtolower(implode(" ", $student_name))));

    $pdf->SetXY(41, 39.5);
    $pdf->Write(1, ucwords(strtolower($student["Father_Name"])));

    $pdf->SetXY(42, 46.5);
    $pdf->Write(1, ucwords(strtolower($student["Mother_Name"])));

    $pdf->SetXY(28, 52.5);
    $pdf->Write(1, $student['course_name']);

    if (file_exists($photo) && filetype($photo) === 'file') {
    // if (filetype($photo) === 'file' && file_exists($photo)) {
        try {
            $filename = $student['ID'] . "_Photo" . $file_extensions[0];
            $image = $filename;
            $pdf->Image($image, 171, 29, 30, 32);
            $photo = $image;
        } catch (Exception $e) {
            try {
                $filename = $student['ID'] . "_Photo" . $file_extensions[1];
                $image = $filename;
                $pdf->Image($image, 171, 29, 30, 32);
                $photo = $image;
            } catch (Exception $e) {
                try {
                    $filename = $student['ID'] . "_Photo" . $file_extensions[2];
                    $image = $filename;
                    $pdf->Image($image, 171, 29, 30, 32);
                    $photo = $image;
                } catch (Exception $e) {
                    echo 'Message: ' . $e->getMessage();
                }
            }
        }
    }

    $pdf->SetXY(115, 52.7);
    $pdf->Write(1, ucwords(strtolower($student['sub_course_name'])));

    $pdf->SetXY(127, 19.7);
    $pdf->Write(1, $student['Duration']);

    $pdf->SetXY(82, 19.5);
    $pdf->Write(1, $student['Session']);

    // Syllabus
    $pdf->SetFont('Hondo', '', 10.5);
    $y = 68.5;
    $counter = 1;

// echo "SELECT Syllabi.*, Date_Sheets.Exam_Date as Exam_Date, Date_Sheets.Start_Time as Start_Time FROM Syllabi LEFT JOIN Date_Sheets ON Syllabi.ID = Date_Sheets.Syllabus_ID WHERE Date_Sheets.Sub_Course_ID = " . $student['Sub_Course_ID'] . " AND Semester= '" . $duration . "'  AND Date_Sheets.Syllabus_ID IS NOT NULL ORDER BY Code ASC";die;
    $syllabi = $conn->query("SELECT Syllabi.*, Date_Sheets.Exam_Date as Exam_Date, Date_Sheets.Start_Time as Start_Time FROM Syllabi LEFT JOIN Date_Sheets ON Syllabi.ID = Date_Sheets.Syllabus_ID WHERE Date_Sheets.Sub_Course_ID = " . $student['Sub_Course_ID'] . " AND Semester= '" . $duration . "'  AND Date_Sheets.Syllabus_ID IS NOT NULL ORDER BY Code ASC");


// Static data for "Mock Test"

$syllabus1 = array(
    'Code' => '-',
    'Name' => 'Mock Test',    
    'Exam_Date' => '22-10-2024',
    'Start_Time' => '10:30 AM'
);

$syllabusList = [];

while ($syllabus = $syllabi->fetch_assoc()) {
    $syllabusList[] = $syllabus;  
}

array_push($syllabusList, $syllabus1);  

$counter = 1;  

foreach ($syllabusList as $syllabus) {
    $pdf->SetXY(16, $y);
    $pdf->Write(1, $counter++);

    $pdf->SetXY(26, $y);
    $pdf->Write(1, $syllabus['Code']);


    $pdf->SetXY(50, $y);
    $pdf->Write(1, substr($syllabus['Name'], 0, 32));


    $pdf->SetXY(152, $y);
    $pdf->Write(1, date("d-m-Y", strtotime($syllabus['Exam_Date'])));


    $pdf->SetXY(178, $y);
    $startTime = strtotime($syllabus['Start_Time']);
    $formattedTime = date("h:i A", $startTime);  // AM/PM formatting
    $pdf->Write(1, $formattedTime);
    $y += 5;
}


    $i = 0;
    $end = 3;
    while ($i < $end) {
        // Delete Photos
        if (!empty($student_photo)) {
            $filename = $student['ID'] . "_Photo" . $file_extensions[$i]; //$file_extensions loops through the file extensions
            unlink($filename);
        }
        $i++;
    }

    $pdf->Output('I', ucwords(strtolower(implode(" ", $student_name))) . ' Admit Card.pdf');
}
