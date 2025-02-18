<?php
//error_reporting(0);
require '../../includes/db-config.php';
require '../../includes/helpers.php';
session_start();

$url = WEB_URL;
$passFail = "PASS";

use setasign\Fpdi\PdfReader;
use setasign\Fpdi\Fpdi;

ob_end_clean();
require_once('../../extras/TCPDF/tcpdf.php');
require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');
require '../../extras/vendor/autoload.php';
require('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
require('../../extras/vendor/nuovo/spreadsheet-reader/SpreadsheetReader.php');

// echo "<pre>"; print_r($_REQUEST); exit;

$sqlQuery = '';
if (isset($_POST['course_type_id']) && !empty($_POST['course_type_id'])) {
    $course_id = $_POST['course_type_id'];
    $sqlQuery .= "AND Students.Course_ID = '$course_id'";
}

if (isset($_POST['course_id']) && !empty($_POST['course_id'])) {
    $sub_course_id = $_POST['course_id'];
    $sqlQuery .= " AND Students.Sub_Course_ID = '$sub_course_id'";
}

if (isset($_POST['student_id']) && !empty($_POST['student_id'])) {
    $student_id_array = explode(",", $_POST['student_id']);
    foreach ($student_id_array as &$en_no) {
        $en_no = "'" . trim($en_no) . "'";
    }
    unset($en_no);
    $student_id = implode(",", $student_id_array);
    $sqlQuery .= " AND Students.Enrollment_No IN ($student_id)";
}

if (isset($_POST['category']) && !empty($_POST['category'])) {
    $sub_course_id = $_POST['category'];
    $sqlQuery .= " AND Students.Duration = '$sub_course_id'";
}


$pdf_dir = '../../uploads/marksheet/';
$export_data = [];
$header = array('Enrollment_No', 'Course', 'Sub-Course', 'Semester', 'Remark');

$export_data[] = $header;
$student = $conn->query("SELECT Students.*, Sub_Courses.Min_Duration as total_duration,Sub_Courses.Name as course, Courses.Name as program_Type FROM Students LEFT JOIN Sub_Courses ON Sub_Courses.ID = Students.Sub_Course_ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID  WHERE Students.Enrollment_No IS NOT NULL $sqlQuery");
if ($student->num_rows > 0) {
    while ($row = $student->fetch_assoc()) {
        // echo "<pre>"; print_r($row);
        $students_result = $conn->query("SELECT Students.*, Sub_Courses.Min_Duration as total_duration, Modes.Name as mode, Sub_Courses.Name as course, Courses.Name as program_Type, Admission_Sessions.Name as Admission_Session,Admission_Sessions.Exam_Session, Admission_Types.Name as Admission_Type FROM Students LEFT JOIN Sub_Courses ON Sub_Courses.ID = Students.Sub_Course_ID LEFT JOIN Modes ON Students.University_ID = Modes.University_ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID  LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.Enrollment_No = '" . trim($row['Enrollment_No']) . "'");
        $data = [];
        $data['remarks'] = "Pass";
        $data = $students_result->fetch_assoc();
        // echo "<pre>"; print_r($data);
        $typoArr = ["th", "st", "nd", "rd", "th", "th", "th", "th", "th"];
        $total_obt = 0;
        $total_max = 0;
        $durations_query = "";
        $min_val = 0;
        $temp_subjects = "";
        $sem_sql = '';
        $scheme_id = NULL;
        $semester = NULL;
        list($month, $year) = explode('-', $data['Admission_Session']);
        $year = (strlen($year) > 2) ? date('y', strtotime("$year-01-01")) : $year;
        $adm_session = '20' . $year . '-' . $year + 1;

        $sem_sql = " AND Semester = " . $data['Duration'];
        $durations_query = " AND Syllabi.Semester = " . $data['Duration'];

        if (isset($_POST['semester']) && !empty($_POST['semester'])) {
            list($scheme_id, $semester) = explode('|', $_POST['semester']);
            $data['Duration'] = $semester;
            $sem_sql = " AND Syllabi.Semester = '$semester' AND Syllabi.Scheme_ID = $scheme_id";
            $durations_query = " AND Syllabi.Semester = " . $semester;
        }


        // $exam_date = $conn->query("SELECT m.exam_month,m.exam_year FROM marksheets AS m LEFT JOIN Syllabi  ON m.subject_id = Syllabi.ID WHERE m.enrollment_no = '" . $data['Enrollment_No'] . "' AND Syllabi.Course_ID = " . $data['Course_ID'] . "  AND  Syllabi.Sub_Course_ID = " . $data['Sub_Course_ID'] . " $sem_sql GROUP BY m.enrollment_no");
        // if ($exam_date->num_rows > 0) {
        //     $examArr = $exam_date->fetch_assoc();
        //     if (!empty($examArr['exam_month']) || !empty($examArr['exam_year'])) {
        //         $exam_month = ucwords($examArr['exam_month']) . '-' . $examArr['exam_year'];
        //         list($date_of_issue) = selectExamSessionAndDateOfIssue($data['Admission_Session'],$semester,'date');
        //     } else {

        //         //$exam_month = ucwords($data['Exam_Session']);
        //     }
        // }
        list($date_of_issue, $exam_month) = selectExamSessionAndDateOfIssue($data['Admission_Session'], $semester, 'date&exam');
        $temp_subjects = $conn->query("SELECT Paper_Type,marksheets.exam_month,marksheets.exam_year, marksheets.obt_marks_ext, marksheets.obt_marks_int,marksheets.obt_marks,marksheets.status,marksheets.remarks,marksheets.Created_At,Syllabi.Code,Syllabi.Name as subject_name,Syllabi.Min_Marks, Syllabi.Max_Marks, Syllabi.Credit FROM marksheets LEFT JOIN Syllabi ON marksheets.subject_id = Syllabi.ID WHERE enrollment_no = '" . $data['Enrollment_No'] . "' AND Sub_Course_ID = '" . $row['Sub_Course_ID'] . "'  $sem_sql ORDER BY Syllabi.Code ASC");
        $data['marks'] = array();
        $temp_subject = [];
        $total_obt = 0;
        $total_max = 0;
        $resultPublishDay = "";
        // $o_int_marks = [];
        // IF here the data is came that means this sem result is came if not then this sem result not present
        if ($temp_subjects->num_rows > 0) {
            makeStatus($data, "Result Found");
            while ($temp_subject = $temp_subjects->fetch_assoc()) {

                if ($temp_subject != null) {
                    $resultPublishDay = date("d/m/Y", strtotime($temp_subject['Created_At']));

                    $obt_marks_ext = isset($temp_subject['obt_marks_ext']) ? $temp_subject['obt_marks_ext'] : 0;
                    $obt_marks_int = isset($temp_subject['obt_marks_int']) ? $temp_subject['obt_marks_int'] : 0;

                    $obt_marks_ext = ($temp_subject['obt_marks_ext'] == 'AB') ? 'AB' : $temp_subject['obt_marks_ext'];
                    $obt_marks_int = ($temp_subject['obt_marks_int'] == 'AB') ? 'AB' : $temp_subject['obt_marks_int'];

                    if ($obt_marks_ext != 'AB' && $obt_marks_int != 'AB') {
                        $total_obt = $total_obt + intval($obt_marks_ext) + intval($obt_marks_int);
                    } else {
                        $total_obt = (int) $total_obt + (int) $obt_marks_ext + (int) $obt_marks_int;
                    }
                    $temp_subject['remarks_status'] = "Pass";


                    if ($total_obt <= $min_val || $temp_subject['obt_marks_ext'] == 0 || $temp_subject['obt_marks_ext'] == 'AB' || $temp_subject['obt_marks_int'] == 0 || $temp_subject['obt_marks_int'] == 'AB') {
                        $temp_subject['remarks_status'] = "FAIL";
                    }
                    $total_max = $total_max + $temp_subject['Min_Marks'] + $temp_subject['Max_Marks'];

                    if ($temp_subject['obt_marks_ext'] == 'AB' && $temp_subject['obt_marks_ext'] == 'AB') {
                        $temp_subject['obt_marks'] = 'AB';

                    } else if ($temp_subject['obt_marks_ext'] == 'AB' && $temp_subject['obt_marks_int'] != 'AB') {
                        $obtintmarks = isset($temp_subject['obt_marks_int']) ? intval($temp_subject['obt_marks_int']) : 0;
                        $temp_subject['obt_marks'] = $obtintmarks + 0;

                    } else if ($temp_subject['obt_marks_int'] == 'AB' && $temp_subject['obt_marks_ext'] != 'AB') {
                        $obtextmarks = isset($temp_subject['obt_marks_ext']) ? intval($temp_subject['obt_marks_ext']) : 0;
                        $temp_subject['obt_marks'] = $obtextmarks + 0;
                    } else if ($temp_subject['obt_marks_ext'] != 'AB' && $temp_subject['obt_marks_ext'] != 'AB') {
                        $temp_subject['obt_marks'] = intval($temp_subject['obt_marks_ext']) + intval($temp_subject['obt_marks_int']);
                    }

                    // end remarks_status

                    if ($temp_subject['Min_Marks'] == 40 && $temp_subject['Max_Marks'] == 60) {
                        $min_val = ($temp_subject['Min_Marks'] + $temp_subject['Max_Marks']) * 40 / 100;
                    } else if ($temp_subject['Max_Marks'] == 100 && $temp_subject['Min_Marks'] == 40) {
                        $min_val = $temp_subject['Min_Marks'];
                    } else if ($temp_subject['Max_Marks'] == 100 && $temp_subject['Min_Marks'] == 100) {
                        $min_val = ($temp_subject['Max_Marks']) * 40 / 100;
                    }
                    if ($temp_subject['Paper_Type'] == 'Practical' && $temp_subject['obt_marks_int'] == 0 && $temp_subject['Min_Marks'] == 0) {
                        $temp_subject['Min_Marks'] = '-';
                        $temp_subject['obt_marks_int'] = "-";
                        $temp_subject['remarks_status'] = "Pass";
                    }

                    $temp_subject['remarks'] = $temp_subject['remarks_status'];
                    $data['marks'][] = $temp_subject;
                    $data['allremarks'][] = $temp_subject['remarks_status'];
                }
            }

            $percentage = '';

            if (in_array('FAIL', $data['allremarks'])) {
                $data['remarks'] = 'Fail';
                $percentage = '';
            } else {
                $data['remarks'] = 'Pass';
                if ($total_max !== 0) {
                    $percentage = ($total_obt / $total_max) * 100;
                    $percentage = number_format($percentage, 2) . "%";
                }
            }

            $data['total_max'] = $total_max;
            $data['total_obt'] = $total_obt;

            $marksWords = ucwords(strtolower(numberToWordFunc($total_obt)));
            $count = $temp_subjects->num_rows;
            $hours = '';
            $total_duration = '';


            $data['university_name'] = "Sanskaram School of Vocational Studies";
            $data['mode_type'] = "Semester";
            $durations = "B. VOC";


            $data['duration_val'] = $durations;

            $durMonthYear = "";
            if ($data['mode'] == "Monthly") {
                $durMonthYear = " Months";
            } elseif ($data['mode'] == "Sem") {
                $durMonthYear = " Semester";
            } else {
                $durMonthYear = " Years";
            }


            $data['durMonthYear'] = $data['Duration'] . $typoArr[$data['Duration']];


            $student_doc_query = "SELECT Location FROM Student_Documents WHERE Student_ID = '" . $data['ID'] . "' AND Type = 'Photo'";
            $student_doc = $conn->query($student_doc_query);
            $student_doc = $student_doc->fetch_assoc();
            $photo = $student_doc['Location'];
            $data['Photo'] = $url . $photo;

            $pdf = new Fpdi();
            $pdf->addPage();

            // Set the Grade and grade value
            $total_obt_grade_value = 0;
            $total_credit = 0;
            foreach ($data['marks'] as $key => $value) {
                if ($value['remarks_status'] == 'FAIL') {
                    $data['marks'][$key]['grade'] = ($value['obt_marks'] == 'AB') ? 'S' : 'F';
                    $data['marks'][$key]['grade_value'] = '0';
                } else {
                    $grandTotal = (int) $value['Min_Marks'] + (int) $value['Max_Marks'];
                    $student_obt_mark = $value['obt_marks'];
                    $student_obt_per = round(($student_obt_mark / $grandTotal) * 100, 2);
                    if ($student_obt_per > 90) {
                        $data['marks'][$key]['grade'] = 'O';
                        $data['marks'][$key]['grade_value'] = '10';
                    } elseif ($student_obt_per > 80 && $student_obt_per <= 90) {
                        $data['marks'][$key]['grade'] = 'A+';
                        $data['marks'][$key]['grade_value'] = '9';
                    } elseif ($student_obt_per > 70 && $student_obt_per <= 80) {
                        $data['marks'][$key]['grade'] = 'A';
                        $data['marks'][$key]['grade_value'] = '8';
                    } elseif ($student_obt_per > 60 && $student_obt_per <= 70) {
                        $data['marks'][$key]['grade'] = 'B+';
                        $data['marks'][$key]['grade_value'] = '7';
                    } elseif ($student_obt_per > 55 && $student_obt_per <= 60) {
                        $data['marks'][$key]['grade'] = 'B';
                        $data['marks'][$key]['grade_value'] = '6';
                    } elseif ($student_obt_per > 50 && $student_obt_per <= 55) {
                        $data['marks'][$key]['grade'] = 'C';
                        $data['marks'][$key]['grade_value'] = '5';
                    } elseif ($student_obt_per >= 40 && $student_obt_per <= 50) {
                        $data['marks'][$key]['grade'] = 'P';
                        $data['marks'][$key]['grade_value'] = '4';
                    } else {
                        $data['marks'][$key]['grade'] = 'F';
                        $data['marks'][$key]['grade_value'] = '0';
                    }
                }
                $total_obt_grade_value += intval($data['marks'][$key]['grade_value'] * $data['marks'][$key]['Credit']);
                $total_credit += intval($data['marks'][$key]['Credit']);
            }
            $data['SGPA'] = number_format($total_obt_grade_value / $total_credit, 2);
            $total_course_dur = (json_decode($data['total_duration'], true))[0];
            $checkLastSem = ($total_course_dur == $data['Duration']) ? true : false;
            $sgpa_record = [];
            if ($checkLastSem) {
                $sgpa_record = calculateCGPA($data['Enrollment_No'], $row['Sub_Course_ID'], $data['Duration']);
                $sgpa_record[] = array(
                    'semester' => $data['Duration'],
                    'sgpa' => $data['SGPA'],
                    'total_credit' => $total_credit
                );
            }
            //echo "<pre>"; print_r($sgpa_record); exit;
            // echo "<pre>"; print_r($data); exit;
            if (isset($_REQUEST['marksheet_in_grade'])) {
                setHeaderGrade($data, $exam_month);
                $cellHeight = 10;
                $pdf->SetXY(16, 99);
                $pdf->SetFont('times', 'B', 12);
                $pdf->MultiCell(30, $cellHeight, 'Course Code', 'TLB', 'C');
                $pdf->SetXY(46, 99);
                $pdf->MultiCell(107, $cellHeight, 'Course Title ', 'TLB', 'C');
                $pdf->SetXY(153, 99);
                $pdf->MultiCell(20, $cellHeight, 'Credit', 'TLB', 'C');
                $pdf->SetXY(173, 99);
                $pdf->MultiCell(20, $cellHeight, 'Grade', 'TLRB', 'C');
                $pdf->Ln();
                $pdf->SetFont('times', '', 12);
                $x_cor = 16;
                $y_cor = 109;
                $pdf->SetX($x_cor);
                $pdf->SetY($y_cor);
                makeGradeMarksheet($data['marks'], $data['SGPA'], $x_cor, $sgpa_record, $data['remarks']);
            } else {
                setHeaderPercentage($data, $exam_month);
                // Header Part
                $pdf->SetFont('times', 'B', 10);
                $cellWidth = 25;
                $cellHeight = 10;
                $pdf->SetXY(16.1, 115);
                $pdf->MultiCell(25, 10, 'Subject Code', 'TL', 'C');
                $pdf->SetXY(41, 115);
                $pdf->MultiCell(76, 10, 'Subject Name ', 'TL', 'C');
                $pdf->SetXY(117, 115);
                $pdf->MultiCell(25, 10, 'Internal', 'TL', 'C');
                $pdf->SetXY(142, 115);
                $pdf->MultiCell(25, 10, 'External', 'TL', 'C');
                $pdf->SetXY(167, 115);
                $pdf->MultiCell(26, 10, 'Total', 'TLR', 'C');
                $pdf->SetXY(16.1, 125);
                $pdf->MultiCell(25, 10, '', 'LB', 'C');
                $pdf->SetXY(41, 125);
                $pdf->MultiCell(76, 10, ' ', 'LB', 'C');
                $pdf->SetXY(117, 125);
                $pdf->MultiCell(12.6, 10, 'Obt', 'TBL', 'C');
                $pdf->SetXY(129.8, 125);
                $pdf->MultiCell(12, 10, 'Max', 'TBL', 'C');
                $pdf->SetXY(142, 125);
                $pdf->MultiCell(12.5, 10, 'Obt', 'TBL', 'C');
                $pdf->SetXY(154.8, 125);
                $pdf->MultiCell(12, 10, 'Max', 'TBL', 'C');
                $pdf->SetXY(167, 125);
                $pdf->MultiCell(14, 10, 'Obt', 'TBL', 'C');
                $pdf->SetXY(181, 125);
                $pdf->MultiCell(12, 10, 'Max', 'TLBR', 'C');
                $pdf->SetXY(10, 125);
                $pdf->Ln();
                $pdf->SetFont('times', '', 10);
                $x_cor = 16;
                $pdf->SetX($x_cor);
                $remark_statuss = [];

                makePercentageMarksheet($data['marks'], $x_cor);

                // Footer Part
                $pdf->SetXY(16, 230.4);
                $pdf->SetFont('times', 'B', 10);
                $pdf->Cell(0, 0, 'Aggregate Marks', 0, 0, 'C', 0);

                $pdf->SetXY(16, 233.4);
                $pdf->Cell(65, 8, 'Marks', 'TL', 1, 'C', 0);
                $pdf->SetXY(81, 233.4);
                $pdf->Cell(35, 8, 'Grand Total', 'TL', 1, 'C', 0);
                $pdf->SetXY(116, 233.4);
                $pdf->Cell(35, 8, 'Result', 'LT', 1, 'C', 0);
                $pdf->SetXY(151, 233.4);
                $pdf->Cell(42, 8, 'Percentage', 'LTR', 1, 'C', 0);
                $pdf->SetFont('times', '', 10);
                $pdf->SetXY(16, 240.4);
                $pdf->Cell(65, 8, ' Obtained Mark', 'TL', 1, 'C', 0);
                $pdf->SetXY(81, 240.4);
                $pdf->Cell(35, 8, $data['total_obt'], 'TL', 1, 'C', 0);

                $pdf->SetXY(116, 240.4);
                $pdf->Cell(35, 8, $data['remarks'], 'TLR', 1, 'C', 0);
                $pdf->SetXY(151, 240.4);
                $pdf->Cell(42, 8, $percentage, 'TR', 1, 'C', 0);


                $pdf->SetXY(16, 247.3);
                $pdf->Cell(65, 8, 'Maximum Mark', 'TLB', 1, 'C', 0);
                $pdf->SetXY(81, 247.3);
                $pdf->Cell(35, 8, $data['total_max'], 'LBT', 1, 'C', 0);
                $pdf->SetXY(116, 247.3);
                $pdf->Cell(35, 8, '', 'LRB', 'LB', 'C', 0);
                $pdf->SetXY(151, 247.3);
                $pdf->Cell(42, 8, '', 'RB', 'RB', 'C', 0);
            }

            $pdf->SetXY(39, 260);
            //$pdf->Cell(0, 9.1, date('d-m-Y'), 0, 1, 'L', 0);
            $pdf->Cell(0, 9.1, $date_of_issue, 0, 1, 'L', 0);
            $filename = $data['Enrollment_No'] . "_" . time() . ".pdf";
            $pdf->Output($pdf_dir . $filename, "F");
        } else {
            makeStatus($data, "Result Not Found");
        }
    }

    $filename = 'marksheet_download_status' . date('h m s');
    SimpleXLSXGen::fromArray($export_data)->saveAs($pdf_dir . $filename . '.xlsx');

    $zip = new ZipArchive();
    $zip_file = $pdf_dir . 'Marksheets_' . time() . '.zip';
    if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $files = glob($pdf_dir . '*.{pdf,xlsx}', GLOB_BRACE);
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . basename($zip_file));
        header('Content-Length: ' . filesize($zip_file));
        readfile($zip_file);

        foreach ($files as $file) {
            unlink($file);
        }
        unlink($zip_file);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create ZIP file.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No record found!']);
}

function makeGradeMarksheet($marks_arr, $sgpa, $x_cor, $sgpa_record, $remark)
{

    global $pdf;
    $cellHeight = 10;
    foreach ($marks_arr as $mark) {
        $pdf->SetX($x_cor);
        $mark['subject_name'] = str_replace("\xC2\xA0", ' ', $mark['subject_name']);
        $mark['subject_name'] = preg_replace('/\s+/', ' ', $mark['subject_name']);
        if (strlen($mark['subject_name']) > 50) {
            $pdf->Cell(30, $cellHeight, $mark['Code'], 'LB', 0, 'C');
            $nameParts = explode("\n", wordwrap($mark['subject_name'], 50));
            $pdf->MultiCell(107, 5, " " . $nameParts[0] . chr(10) . " " . $nameParts[1], 'BL', 0, 0, 'L');
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->SetXY(153, $y - 10);
            $pdf->Cell(20, $cellHeight, $mark['Credit'], 'LB', 0, 'C');
            $pdf->Cell(20, $cellHeight, $mark['grade'], 'LBR', 0, 'C');
        } else {
            $pdf->Cell(30, $cellHeight, $mark['Code'], 'LB', 0, 'C');
            $pdf->Cell(107, $cellHeight, " " . $mark['subject_name'], 'LB', 0, 'L');
            $pdf->Cell(20, $cellHeight, $mark['Credit'], 'LB', 0, 'C');
            $pdf->Cell(20, $cellHeight, $mark['grade'], 'LBR', 0, 'C');
        }
        $pdf->Ln();
    }
    $pdf->SetFont('times', 'B', 12);
    $y_cor = $pdf->GetY();
    $pdf->SetXY(153, $y_cor);
    $pdf->Cell(20, $cellHeight, 'SGPA:', 'LB', 0, 'C');
    $pdf->Cell(20, $cellHeight, $sgpa, 'LBR', 0, 'C');

    if (!empty($sgpa_record)) {
        $pdf->SetFont('times', '', 12);
        $y_cor = $pdf->GetY();
        $y_cor += 20;
        $pdf->SetXY(16, $y_cor);
        $a = 1;
        $total_sgpa_credit = 0;
        $grand_total_credit = 0;
        $total_sem = count($sgpa_record);

        foreach ($sgpa_record as $sgpa_details) {
            $total_sgpa_credit += $sgpa_details['sgpa'] * $sgpa_details['total_credit'];
            $grand_total_credit += intval($sgpa_details['total_credit']);
            $sgpaTotal = number_format((float) $sgpa_details['sgpa'], 2, '.', '');
            $content = " SGPA of Semester " . intToRoman($sgpa_details['semester']) . ":";
            $width = $pdf->GetStringWidth($content);
            $pdf->Cell($width, $cellHeight, $content, 'LBT', 0, 'L');
            $pdf->SetFont('times', 'B', 12);
            $newWidth = 59 - $width;
            $pdf->Cell($newWidth, $cellHeight, " $sgpaTotal", 'BTR', 0, 'L');
            $pdf->SetFont('times', '', 12);
            if ($a == 3) {
                $pdf->Ln();
                $pdf->SetX(16);
                $a = 0;
            }
            $a++;
        }
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetX(17);
        $pdf->Cell(30, $cellHeight, "RESULT: " . strtoupper($remark), '', 0, 'C');
        $cgpa = round(($total_sgpa_credit / $grand_total_credit), 2);
        $pdf->SetX(134);
        $pdf->Cell(39, $cellHeight, "CGPA: ", 'LBRT', 0, 'R');
        $pdf->Cell(20, $cellHeight, $cgpa, 'LBRT', 0, 'C');
    }
}

function intToRoman($num)
{
    $mapping = [10 => 'X', 9 => 'IX', 5 => 'V', 4 => 'IV', 1 => 'I'];
    $result = '';
    foreach ($mapping as $value => $roman) {
        while ($num >= $value) {
            $result .= $roman;
            $num -= $value;
        }
    }
    return $result;
}


function makePercentageMarksheet($marks_arr, $x_cor)
{
    global $pdf;
    foreach ($marks_arr as $mark) {
        $pdf->SetX($x_cor);
        $cellHeight = (strlen($mark['subject_name']) > 30) ? 20 : 10;
        $remark_statuss[] = $mark['remarks'];
        $mark['subject_name'] = str_replace("\xC2\xA0", ' ', $mark['subject_name']);
        $mark['subject_name'] = preg_replace('/\s+/', ' ', $mark['subject_name']);
        $mark['subject_name'] = utf8_encode($mark['subject_name']);
        if (strlen($mark['subject_name']) > 30) {
            $pdf->SetFont('times', '', 11);
            $pdf->Cell(25, $cellHeight - 10, $mark['Code'], 'BL', 0, 'L');
            $nameParts = explode("\n", wordwrap($mark['subject_name'], 30));
            $pdf->MultiCell(76, 5, $nameParts[0] . chr(10) . $nameParts[1], 'BL', 0, 0, 'L');
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->SetXY($x + 107, $y - 10);
            $pdf->Cell(12.8, 10, $mark['obt_marks_int'], 'LB', 0, 'C');
            $pdf->Cell(12.2, 10, $mark['Min_Marks'], 'LB', 0, 'C');
            $pdf->Cell(12.8, 10, $mark['obt_marks_ext'], 'BL', 0, 'C');
            $pdf->Cell(12.2, 10, $mark['Max_Marks'], 'BL', 0, 'C');
            $pdf->Cell(14, 10, $mark['obt_marks'], 'BL', 0, 'C');
            $pdf->Cell(12, 10, $mark['Min_Marks'] + $mark['Max_Marks'], 'BLR', 0, 'C');
        } else {
            $pdf->Cell(25, $cellHeight, $mark['Code'], 'LB', 0, 'L');
            $pdf->Cell(76, $cellHeight, $mark['subject_name'], 'LB', 0, 'L');
            $pdf->Cell(12.8, 10, $mark['obt_marks_int'], 'LB', 0, 'C');
            $pdf->Cell(12.2, 10, $mark['Min_Marks'], 'LB', 0, 'C');
            $pdf->Cell(12.8, 10, $mark['obt_marks_ext'], 'BL', 0, 'C');
            $pdf->Cell(12.2, 10, $mark['Max_Marks'], 'BL', 0, 'C');
            $pdf->Cell(14, 10, $mark['obt_marks'], 'BL', 0, 'C');
            $pdf->Cell(12, 10, $mark['Min_Marks'] + $mark['Max_Marks'], 'BLR', 0, 'C');
        }
        $pdf->Ln();
    }
}

function makeStatus($student_data, $message)
{

    global $export_data;
    list($scheme_id, $semester) = explode('|', $_POST['semester']);
    $export_data[] = array($student_data['Enrollment_No'], $student_data['program_Type'], $student_data['course'], $semester, $message);

}

function calculateCGPA($enrol, $sub_course_id, $total_sem_dur)
{

    global $conn;
    $sgpa_record = [];
    list($scheme_id, $semester) = explode('|', $_POST['semester']);
    $i = 1;
    while ($i < $total_sem_dur) {
        $subject_record = [];
        $sem_sql = " AND Syllabi.Semester = '$i' AND Syllabi.Scheme_ID = $scheme_id";
        $temp_subjects = $conn->query("SELECT Paper_Type,marksheets.obt_marks_ext, marksheets.obt_marks_int,marksheets.obt_marks,marksheets.status,marksheets.remarks,Syllabi.Name as subject_name,Syllabi.Min_Marks, Syllabi.Max_Marks, Syllabi.Credit FROM marksheets LEFT JOIN Syllabi ON marksheets.subject_id = Syllabi.ID WHERE enrollment_no = '$enrol' AND Sub_Course_ID = '$sub_course_id' $sem_sql ORDER BY Syllabi.Code ASC");
        if ($temp_subjects->num_rows > 0) {
            $j = 0;
            while ($temp_subject = $temp_subjects->fetch_assoc()) {
                $subject_record[$j]['Min_Marks'] = $temp_subject['Min_Marks'];
                $subject_record[$j]['Max_Marks'] = $temp_subject['Max_Marks'];
                $subject_record[$j]['Credit'] = $temp_subject['Credit'];
                if ($temp_subject['obt_marks_ext'] == 'AB' && $temp_subject['obt_marks_ext'] == 'AB') {
                    $subject_record[$j]['obt_marks'] = 'AB';
                    $subject_record[$j]['remarks_status'] = 'FAIL';
                } else if ($temp_subject['obt_marks_ext'] == 'AB' && $temp_subject['obt_marks_int'] != 'AB') {
                    $subject_record[$j]['obt_marks'] = $temp_subject['obt_marks_int'];
                    $subject_record[$j]['remarks_status'] = 'FAIL';
                } else if ($temp_subject['obt_marks_int'] == 'AB' && $temp_subject['obt_marks_ext'] != 'AB') {
                    $subject_record[$j]['obt_marks'] = $temp_subject['obt_marks_int'];
                    $subject_record[$j]['remarks_status'] = 'FAIL';
                } else if ($temp_subject['obt_marks_ext'] != 'AB' && $temp_subject['obt_marks_ext'] != 'AB') {
                    $subject_record[$j]['obt_marks'] = intval($temp_subject['obt_marks_ext']) + intval($temp_subject['obt_marks_int']);
                    $subject_record[$j]['remarks_status'] = 'PASS';
                }
                $j++;
            }
            list($sgpa, $total_credit) = calculateSGPA($subject_record);
            $sgpa_record[] = array(
                'semester' => $i,
                'sgpa' => $sgpa,
                'total_credit' => $total_credit
            );
        }
        $i++;
    }
    return $sgpa_record;
}

function calculateSGPA($temp_subject)
{

    $total_obt_grade_value = 0;
    $total_credit = 0;
    $grade_value = '';
    foreach ($temp_subject as $key => $value) {
        if ($value['remarks_status'] == 'FAIL') {
            $grade_value = '0';
        } else {
            $grandTotal = $value['Min_Marks'] + $value['Max_Marks'];
            $student_obt_mark = $value['obt_marks'];
            $student_obt_per = round(($student_obt_mark / $grandTotal) * 100, 2);
            $grade_value = match (true) {
                $student_obt_per > 90 => '10',
                $student_obt_per > 80 && $student_obt_per <= 90 => '9',
                $student_obt_per > 70 && $student_obt_per <= 80 => '8',
                $student_obt_per > 60 && $student_obt_per <= 70 => '7',
                $student_obt_per > 55 && $student_obt_per <= 60 => '6',
                $student_obt_per > 50 && $student_obt_per <= 55 => '5',
                $student_obt_per >= 40 && $student_obt_per <= 50 => '4',
                default => '0',
            };
        }
        $total_obt_grade_value += intval($grade_value) * intval($value['Credit']);
        $total_credit += intval($value['Credit']);
    }

    return [number_format($total_obt_grade_value / $total_credit, 2), $total_credit];

}


function setHeaderGrade($data, $exam_month)
{

    global $pdf;
    $pdf->SetFont("times", '', 12);
    $pdf->SetXY(15, 55);
    $stat = (isset($_REQUEST['marksheet_in_grade']) ? "Statement of Grades" : "Statement of Marks");
    $pdf->Cell(0, 0, strtoupper($stat), 0, 0, 'C', 0);
    $pdf->SetFont("times", 'B', 12);
    $pdf->SetXY(15, 62);
    $pdf->Cell(0, 0, $data['duration_val'] . '. ' . 'IN' . '  ' . strtoupper($data['course']), 0, 0, 'C', 0);
    $pdf->SetXY(15, 69);
    $pdf->SetFont("times", '', 12);
    $addmissionSession = explode('-', $data['Admission_Session']);
    $addmissionYearFrom = 2000 + (int) $addmissionSession[1];
    $addmissionYearTo = 3 + (int) $addmissionYearFrom;
    $pdf->Cell(0, 0, strtoupper('Session: ' . $addmissionYearFrom . '-' . $addmissionYearTo . ''), 0, 0, 'C', 0);
    $pdf->SetFont("times", 'B', 12);
    $pdf->SetXY(16.1, 75);
    $pdf->Cell(13, 10, 'Name: ', 'LTB', 0, 'L', 0);
    $full_name = $data['First_Name'] . ' ' . $data['Middle_Name'] . ' ' . $data['Last_Name'];
    checkWarpText(strtoupper($full_name), 34, 94);
    $pdf->SetXY(123, 75);
    $pdf->Cell(70, 10, 'Enrollment No.: ' . $data['Enrollment_No'], 'LTR', 0, 'L', 0);
    // $pdf->SetXY(16.1,83);
    // $pdf->Cell(26, 10, 'Father Name : ', 'LTB', 0, 'L', 0);
    // checkWarpText(strtoupper($data['Father_Name']),33,94);
    // $pdf->SetXY(123,83);
    // $pdf->Cell(70, 10, $data['mode_type'] . ' ' . ':' . ' ' . $data['durMonthYear'], 1, 0, 'L', 0);
    $pdf->SetXY(16.1, 85);
    $pdf->Cell(107, 10, 'School: ' . $data['university_name'], 'LTB', 0, 'L', 0);
    $pdf->SetXY(123, 85);
    $semInRoman = intToRoman((int) $data['durMonthYear']);
    $examSession = explode('-', $exam_month);
    $examMonth = date('F', strtotime($examSession[0]));
    $examYear = 2000 + (int) $examSession[1];
    $pdf->Cell(70, 10, 'Semester: ' . $semInRoman . ' (' . ucwords(strtolower($examMonth . ' ' . $examYear)) . ')', 1, 0, 'L', 0);
    $pdf->SetFont('times', 'B', 10);
}

function setHeaderPercentage($data, $exam_month)
{

    global $pdf;
    $pdf->SetFont("times", '', 12);
    $pdf->SetXY(15, 55);
    $pdf->SetXY(15, 60);
    $stat = (isset($_REQUEST['marksheet_in_grade']) ? "Statement of Grades" : "Statement of Marks");
    $pdf->Cell(0, 0, strtoupper($stat), 0, 0, 'C', 0);
    $pdf->SetXY(15, 67.4);
    $pdf->Cell(0, 0, $data['duration_val'] . '. ' . 'IN' . ' ' . strtoupper($data['course']), 0, 0, 'C', 0);
    $pdf->SetXY(15, 75);
    $addmissionSession = explode('-', $data['Admission_Session']);
    $addmissionYearFrom = 2000 + (int) $addmissionSession[1];
    $addmissionYearTo = 3 + (int) $addmissionYearFrom;
    $pdf->Cell(0, 0, strtoupper('Admission Session :' . $addmissionYearFrom . '-' . $addmissionYearTo . ''), 0, 0, 'C', 0);
    $pdf->SetFont("times", 'B', 12);
    $pdf->SetXY(16.1, 82);
    $pdf->Cell(14, 10, 'Name : ', 'LTB', 0, 'L', 0);
    $full_name = $data['First_Name'] . ' ' . $data['Middle_Name'] . ' ' . $data['Last_Name'];
    checkWarpText(strtoupper($full_name), 33, 94);
    $pdf->SetXY(123, 82);
    $pdf->Cell(70, 10, 'Enrollment No : ' . $data['Enrollment_No'], 'LTR', 0, 'L', 0);
    $pdf->SetXY(16.1, 92);
    $pdf->Cell(26, 10, 'Father Name : ', 'LTB', 0, 'L', 0);
    checkWarpText(strtoupper($data['Father_Name']), 33, 94);
    $pdf->SetXY(123, 92);
    $pdf->Cell(70, 10, $data['mode_type'] . ' ' . ':' . ' ' . $data['durMonthYear'], 1, 0, 'L', 0);
    $pdf->SetXY(16.1, 102);
    $pdf->Cell(107, 10, 'School : ' . ' ' . $data['university_name'], 'LTB', 0, 'L', 0);
    $pdf->SetXY(123, 102);
    $examSession = explode('-', $exam_month);
    $examMonth = date('F', strtotime($examSession[0]));
    $examYear = 2000 + (int) $examSession[1];
    $pdf->Cell(70, 10, 'Exam Session : ' . ' ' . ucwords(strtolower($examMonth . ' ' . $examYear)), 1, 0, 'L', 0);
    $pdf->SetFont('times', 'B', 10);
}

function checkWarpText($content, $content_length, $width)
{

    global $pdf;
    if (strlen($content) > $content_length) {
        $nameParts = explode("\n", wordwrap($content, $content_length));
        $pdf->SetFont("times", '', 10);
        $pdf->MultiCell($width, 5, $nameParts[0] . chr(10) . $nameParts[1], 'TB', 0, 0, 'L');
        $pdf->SetFont("times", '', 12);
    } else {
        $pdf->Cell($width, 10, $content, 'TB', 0, 'L', 0);
    }
}

function selectExamSessionAndDateOfIssue($admission_session, $semester, $type)
{
    list($month, $year) = explode('-', $admission_session);
    $month = ucwords(strtolower(substr($month, 0, 3)));
    $year = intval($year);
    $year = (strlen($year) > 2) ? date('y', strtotime("$year-01-01")) : $year;
    $updated_adm_session = $month . '-' . $year;
    $list_details = array(
        [
            'admission_session' => 'Jul-21',
            'exam_session' => 'Dec-21',
            'semester' => '1',
            'date_of_issue' => '10-02-2022'
        ],
        [
            'admission_session' => 'Jul-21',
            'exam_session' => 'Jun-22',
            'semester' => '2',
            'date_of_issue' => '08-08-2022'
        ],
        [
            'admission_session' => 'Jul-21',
            'exam_session' => 'Dec-22',
            'semester' => '3',
            'date_of_issue' => '15-02-2023'
        ],
        [
            'admission_session' => 'Jul-21',
            'exam_session' => 'Jun-23',
            'semester' => '4',
            'date_of_issue' => '22-07-2023'
        ],
        [
            'admission_session' => 'Jul-21',
            'exam_session' => 'Dec-23',
            'semester' => '5',
            'date_of_issue' => '26-02-2024'
        ],
        [
            'admission_session' => 'Jul-21',
            'exam_session' => 'Jun-24',
            'semester' => '6',
            'date_of_issue' => '19-10-2024'
        ],
        [
            'admission_session' => 'Jul-22',
            'exam_session' => 'Dec-22',
            'semester' => '1',
            'date_of_issue' => '15-02-2023'
        ],
        [
            'admission_session' => 'Jul-22',
            'exam_session' => 'Jun-23',
            'semester' => '2',
            'date_of_issue' => '22-07-2023'
        ],
        [
            'admission_session' => 'Jul-22',
            'exam_session' => 'Dec-23',
            'semester' => '3',
            'date_of_issue' => '20-02-2024'
        ],
        [
            'admission_session' => 'Jul-22',
            'exam_session' => 'Jun-24',
            'semester' => '4',
            'date_of_issue' => '19-10-2024'
        ],
        [
            'admission_session' => 'Jul-23',
            'exam_session' => 'Dec-23',
            'semester' => '1',
            'date_of_issue' => '26-02-2024'
        ],
        [
            'admission_session' => 'Jul-23',
            'exam_session' => 'Jun-24',
            'semester' => '2',
            'date_of_issue' => '19-10-2024'
        ]
    );
    $exam_session = '';
    $date_of_issue = '';
    foreach ($list_details as $key => $value) {
        if ($value['admission_session'] == $updated_adm_session && $value['semester'] == $semester) {
            $exam_session = $value['exam_session'];
            $date_of_issue = $value['date_of_issue'];
            break;
        }
    }
    return ($type == 'date&exam') ? [$date_of_issue, $exam_session] : [$date_of_issue];
}