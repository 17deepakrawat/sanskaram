<?php 

include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php');
require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
$header[] = array('Student_ID','Enrollment_No', 'Marksheet No','Exam Session','Duration');
$xlsx = SimpleXLSXGen::fromArray( $header )->downloadAs('Marksheet Entry Sheet Sample.xlsx');
?>