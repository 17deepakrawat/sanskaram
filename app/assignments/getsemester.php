<?php
ini_set('display_errors', 1);
require '../../includes/db-config.php';
session_start();
$subCourseId = intval($_POST['subCourseId']);
$subjectId  =  isset($_POST['ID']) ? intval($_POST['ID']) :'';
$university_id = isset($_POST['University_id']) ? intval($_POST['University_id']) :'';
$sql = "SELECT Name, Min_Duration FROM Sub_Courses WHERE ID = $subCourseId AND Status = 1";
$result = $conn->query($sql);

$html = '';

if ($university_id == 47 || $_SESSION['university_id'] == 47) {
    $html = '<option value="">Select Duration</option>';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // $duration = intval($row['Min_Duration']);
             if(is_string($row['Min_Duration'])==1){
                $duration = json_decode($row['Min_Duration'],true)[0];
            }else{
                $duration = intval($row['Min_Duration']);
            }
            // $result1 = '';
            // if (isset($subjectId) && $subjectId != '') {
            //     $sql1 = "SELECT Semester FROM Syllabi WHERE ID = $subjectId";
            //     $result1 = $conn->query($sql1)->fetch_assoc();
            // }
            // if ($duration > 0) {
            //     for ($i = 1; $i <= $duration; $i++) {
            //         $selected = ($i == $result1['Semester']) ? "selected" : "";
            //         $html .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
            //     }
            // }
            for ($i = 1; $i <= $duration; $i++) {
                    
                        $html .= '<option value="' . $i . '" >' . $i . '</option>';
                    }
          
        }
    }
} elseif ($university_id == 48 || $_SESSION['university_id'] == 48) {
    $html = '<option value="">Select Duration</option>';
    $optionsArr = [
        "11/advanced" => "11/Advanced(Advance Diploma)",
        "6/advanced" => "6/Advanced",
        "11/certified" => "11/Certified",
        "6/certified" => "6/Certified",
        "6/certification" => "6/Certification",
        "3/certification" => "3/Certification",
    ];
    foreach ($optionsArr as $value => $label) {
        // $selected = ($value == $result1['Semester']) ? "selected" : "";
        $html .= '<option value="' . $value . '" >' . $label . '</option>';
    }
} else {
    $html = '<option value="">Select a valid university</option>';
}
echo $html;