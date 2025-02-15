<?php
// ini_set('error_reporting', E_ALL );
// ini_set('display_errors', 1 );
session_start();
require '../../includes/db-config.php';
require('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
include '../../includes/helpers.php';

$searchValue = "";
if (isset($_GET['search'])) {
  $searchValue = mysqli_real_escape_string($conn, $_GET['search']); // Search value
}

if (isset($columnSortOrder)) {
  $orderby = "ORDER BY $columnName $columnSortOrder";
} else {
  $orderby = "ORDER BY Users.ID ASC";
}

$university_query = '';
if ($_SESSION['Role'] == 'University Head') {
  $university_query = " AND University_User.University_ID = " . $_SESSION['university_id'];
} elseif ($_SESSION['Role'] == 'Center') {
  $university_query = " AND Center_SubCenter.Center = " . $_SESSION['ID'];
}

$filterByVerticalType = "";
if (isset($_SESSION['filterByVerticalType'])) {
  $filterByVerticalType = $_SESSION['filterByVerticalType'];
}

## Search 
$searchQuery = " " . $filterByVerticalType;
if ($searchValue != '') {
  $searchQuery = " AND (Users.Name like '%" . $searchValue . "%' OR Users.Code like '%" . $searchValue . "%' OR Users.Email like '%" . $searchValue . "%' OR Users.Mobile like '%" . $searchValue . "%')";
}

## Fetch records
$result_record = "SELECT Users.`ID`,Users.Vertical_type, Users.`Name`, Users.`Email`, Users.`Mobile`, Users.`Code`, Users.`Status`, Users.`Photo`, CONCAT(U1.Name, ' (', U1.Code, ')') AS `Reporting`, CAST(AES_DECRYPT(Users.Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Sub_Center LEFT JOIN Users as U1 ON Center_SubCenter.Center = U1.ID WHERE Users.Role = 'Sub-Center' $university_query $searchQuery $orderby";
$empRecords = mysqli_query($conn, $result_record);
$data[] = array('Code', 'Name', 'Reporting User', 'Admissions', 'Password', 'Wallet Amount', 'Vertical Type', 'Status');

while ($row = mysqli_fetch_assoc($empRecords)) {

  $admissions = $conn->query("SELECT COUNT(ID) as Applications FROM Students WHERE Added_For = " . $row['ID'] . "");
  $admissions = mysqli_fetch_assoc($admissions);


  $sub_center = $conn->query("SELECT GROUP_CONCAT(Sub_Center) FROM `Center_SubCenter` WHERE Center= '" . $row['ID'] . "'");
  $sub_centers = mysqli_fetch_assoc($sub_center);
  $sub_center = isset($sub_centers['GROUP_CONCAT(Sub_Center)']) ? $sub_centers['GROUP_CONCAT(Sub_Center)'] : "";
  $sub_center = !empty($sub_center) ? $sub_center . ',' . $row['ID'] : $row['ID'];

  // credit amount
  $credit_query = $conn->query("SELECT SUM(Amount) AS totalamount FROM Wallets WHERE Added_By = '" . $row['ID'] . "' AND Status=1");
  $creditamount = mysqli_fetch_assoc($credit_query);
  $total_credit_amount = isset($creditamount['totalamount']) ? $creditamount['totalamount'] : 0;
  // debit amount
  $debit_query = $conn->query("SELECT SUM(Amount) AS totalamount FROM Wallet_Payments WHERE Added_By = '" . $row['ID'] . "' AND Status=1");
  $debit_amount = mysqli_fetch_assoc($debit_query);
  $total_debit_amount = isset($debit_amount['totalamount']) ? $debit_amount['totalamount'] : 0;
  $current_amount = $total_credit_amount - $total_debit_amount;

  $vertical_type = ($row['Vertical_type'] == 1) ? "Edtech" : (($row['Vertical_type'] == 0) ? "IITS LLP Paramedical" : "Not Assign");


  $data[] = array(
    $row['Code'],
    $row['Name'],
    $row['Reporting'],
    $admissions['Applications'],
    $row['password'],
    $current_amount,
    $vertical_type,
    $row["Status"] == 1 ? 'Active' : 'Inactive',
  );
}

$xlsx = SimpleXLSXGen::fromArray($data)->downloadAs('Sub-Centers.xlsx');
