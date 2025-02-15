<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'includes/db-config.php';
require 'includes/helpers.php';

$students = $conn->query("SELECT ID FROM `Students` WHERE Unique_ID IN ('GBVOC59845')");
while($student = $students->fetch_assoc()){
echo generateStudentLedger($conn, $student['ID']);
}

