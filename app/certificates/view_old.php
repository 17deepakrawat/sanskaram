<?php
error_reporting(1);

require '../../includes/db-config.php';

session_start();

$id = $_GET['student_id'];

//University_ID = 48 AND
$students_temps_result = $conn->query("SELECT students.*,sub_courses.Min_Duration as total_duration,modes.Name as mode,sub_courses.Name as course,courses.Name as program_Type FROM students left join sub_courses on sub_courses.ID=students.Sub_Course_ID left join modes on students.University_ID=modes.University_ID left join courses on students.Course_ID=courses.ID  WHERE students.ID = '" . $id . "' ");    //AND Duration = $sem 
if ($students_temps_result->num_rows > 0) {
    $students_temps = $students_temps_result->fetch_assoc();
} else {
    $students_temps['duration'] = '';
}

$durMonthYear = "";
if ($students_temps['mode'] == "Monthly") {
    $durMonthYear = "Months";
} elseif ($students_temps['mode'] == "Sem") {
    $durMonthYear = "Semesters";
} else {
    $durMonthYear = "Years";
}
$total_duration = 0;

$total_duration = 0;
if (str_contains($students_temps['total_duration'], '"')) {
    $a = str_replace('"', '', $students_temps['total_duration']);
    $total_duration = (int) $a;
} else {
    $total_duration = (int) $students_temps['total_duration'];
}


$courseCategory = "";
if (str_contains($students_temps['Course_Category'], '_')) {
    $a = str_replace('_', ' ', $students_temps['Course_Category']);
    $courseCategory = ucfirst($a);
} else {
    $courseCategory = ucfirst($students_temps['Course_Category']);
}

$hours = 0;

if ($total_duration == 3 && $durMonthYear == "Months") {
    $hours = 160;
} elseif ($total_duration == 6 && $durMonthYear == "Months") {
    $hours = 320;
} elseif ($total_duration == 11 && $durMonthYear == "Months") {
    $hours = 960;
} elseif ($total_duration == 6 && $durMonthYear == "Semester") {
    $hours = "NA";
}

$a = implode(' ', array_slice(explode(' ', $students_temps['course']), 0, 6));
$b = implode(' ', array_slice(explode(' ', $students_temps['course']), 6));

$name = $students_temps['First_Name'] . " " . $students_temps['Middle_Name'] . " " . $students_temps['Last_Name'];

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

ob_end_clean();
require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

$pdf = new \setasign\Fpdi\Fpdi();
$pdf->AddPage('L', 'A4');
$pdf->setSourceFile('../../assets/img/University_Certification.pdf');

$pageId = $pdf->importPage(1, \setasign\Fpdi\PdfReader\PageBoundaries::MEDIA_BOX);
$pdf->useImportedPage($pageId, 0, 0, 297, 210);

$pdf->SetY(105);
$pdf->AddFont('GreatVibes-Regular', '', 'GreatVibes-Regular.php');
$pdf->SetFont('GreatVibes-Regular', '', 24);

$pdf->MultiCell(0, 18, $name, 0, 'C', 0);

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetX(156);
$pdf->MultiCell(0, 10, $courseCategory, 0, 0, 0);

$pdf->SetFont('Arial', 'B', 16);
$pdf->MultiCell(0, 6, strtoupper($students_temps['course']), 0, 'C', 0);

$pdf->SetFont('Arial', '', 13);
$pdf->MultiCell(0, 10, "", 0, 0, 0);
$pdf->SetFont('Arial', 'B', 15);
$pdf->SetX(96);
$pdf->MultiCell(0, 5, "AY 2023-24", 0, 0, 0);
$pdf->SetX(172);
$pdf->MultiCell(0, -6, $hours . " hours/" . $total_duration . " " . $durMonthYear, 0, 0, 0);

$pdf->Output();








