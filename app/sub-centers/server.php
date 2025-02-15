<?php
## Database configuration
include '../../includes/db-config.php';
session_start();
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if(isset($_POST['order'])){
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

if(isset($columnSortOrder)){
  $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
  $orderby = "ORDER BY Users.ID ASC";
}
$filterByVerticalType = "";
if (isset($_SESSION['filterByVerticalType'])) {
  $filterByVerticalType = $_SESSION['filterByVerticalType'];
}


$filterByUniversity = "";
if (isset($_SESSION['filterByUniversity'])) {
  $filterByUniversity = $_SESSION['filterByUniversity'];
}

$university_query  = '';
if($_SESSION['Role']=='University Head'){
  $university_query = " AND University_User.University_ID = ".$_SESSION['university_id'];
}elseif($_SESSION['Role']=='Center'){
  $university_query = " AND Center_SubCenter.Center = ".$_SESSION['ID'];
}

## Search 
$searchQuery = " ".$filterByVerticalType.$filterByUniversity;
if($searchValue != ''){
  $searchQuery = " AND (Users.Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Users.Email like '%".$searchValue."%' OR Users.Mobile like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count= $conn->query("SELECT COUNT(Users.ID) as allcount FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Sub_Center WHERE Role = 'Sub-Center' $university_query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Users.ID) as filtered FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Sub_Center WHERE Users.Role = 'Sub-Center' $university_query $searchQuery");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Users.`ID`,Users.`Vertical_type`, Users.`Name`, Users.`Email`, Users.`Mobile`, Users.`Code`, Users.`Status`, Users.`Photo`, CONCAT(Users.Name, ' (', Users.Code, ')') AS `Reporting`, CAST(AES_DECRYPT(Users.Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Sub_Center WHERE Users.Role = 'Sub-Center' $university_query $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  
  $admissions = $conn->query("SELECT COUNT(ID) as Applications FROM Students WHERE Added_For = ".$row['ID']."");
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
    "Photo"=> $row['Photo'],
    "Name" => $row['Name'],
    "Email" => $row['Email'],
    "Mobile" => $row['Mobile'],
    "Code" => $row['Code'],
    "Reporting" => $row['Reporting'],
    "Admission" => $admissions['Applications'],
    "Password" => $row['password'],
    "Status"  => $row["Status"],
    "ID"      => $row["ID"],
    "vertical_type"=> $vertical_type,
    "wallet_amount"=> $current_amount
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
