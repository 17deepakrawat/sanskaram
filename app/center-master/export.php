<?php
// ini_set('error_reporting', E_ALL );
// ini_set('display_errors', 1 );
session_start();
require '../../includes/db-config.php';
require('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

if (isset($columnSortOrder)) {
  $orderby = "ORDER BY $columnName $columnSortOrder";
} else {
  $orderby = "ORDER BY Users.ID ASC";
}

$center_query = "";
if ($_SESSION['Role'] != "Administrator") {
  $check_has_unique_center_code = $conn->query("SELECT Center_Suffix FROM Universities WHERE ID = " . $_SESSION['university_id'] . " AND Has_Unique_Center = 1");
  if ($check_has_unique_center_code->num_rows > 0) {
    $center_suffix = mysqli_fetch_assoc($check_has_unique_center_code);
    $center_suffix = $center_suffix['Center_Suffix'];
    $center_query = " AND Code LIKE '$center_suffix%' AND Is_Unique = 1";
  } else {
    $center_query = " AND Is_Unique = 0";
  }
}


$filterByUniversity = "";
if (isset($_SESSION['filterByUniversity'])) {
  $filterByUniversity = $_SESSION['filterByUniversity'];
}


## Search 
$searchQuery = " ".$filterByVerticalType. $filterByUniversity ;
if (isset($_GET['search'])) {
  $searchValue = mysqli_real_escape_string($conn, $_GET['search']); // Search value
  if (!empty($searchValue)) {
    $searchQuery = " AND (Users.Name like '%" . $searchValue . "%' OR Users.Code like '%" . $searchValue . "%' OR Users.Email like '%" . $searchValue . "%' OR Users.Mobile like '%" . $searchValue . "%')";
  }
}

## Fetch records
$result_record = "SELECT ID,Vertical_type, `Name`, Short_Name, Contact_Name, `CanCreateSubCenter`, `Email`, `Mobile`, `Alternate_Mobile`, `Code`, `Address`, `City`, `District`, `State`, `Pincode`, `Status`, CAST(AES_DECRYPT(Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password, Created_At FROM Users WHERE Role = 'Center' $center_query $searchQuery $orderby";
$empRecords = mysqli_query($conn, $result_record);
$data[] = array('Code', 'Name', 'Short Name', 'Contact Name', 'Email', 'Mobile', 'Vertical Type', 'Alternate Mobile', 'Address', 'City', 'District', 'State', 'Pincode', 'Password', 'Sub Center Access', 'Status', 'Universities', 'Total No. of Admission', 'Wallet Amount ', 'Created Date');

while ($row = mysqli_fetch_assoc($empRecords)) {
  $alloted_universities = $conn->query("SELECT GROUP_CONCAT(CONCAT(Universities.Short_Name, '(', Universities.Vertical, ')')) as Alloted_Universities FROM Alloted_Center_To_Counsellor LEFT JOIN Universities ON Alloted_Center_To_Counsellor.University_ID = Universities.ID WHERE Alloted_Center_To_Counsellor.Code = " . $row['ID'] . "");
  $alloted_universities = mysqli_fetch_assoc($alloted_universities);

  $sub_center = $conn->query("SELECT GROUP_CONCAT(Sub_Center) FROM `Center_SubCenter` WHERE Center= '" . $row['ID'] . "'");
  $sub_centers = mysqli_fetch_assoc($sub_center);
  $sub_center = isset($sub_centers['GROUP_CONCAT(Sub_Center)']) ? $sub_centers['GROUP_CONCAT(Sub_Center)'] : "";
  $sub_center = !empty($sub_center) ? $sub_center . ',' . $row['ID'] : $row['ID'];

  $admissions_query = $conn->query("SELECT COUNT(ID) AS Applications FROM Students WHERE Added_For IN ($sub_center) AND Step=4 AND Process_By_Center IS NOT NULL AND Payment_Received IS NOT NULL AND  Deleted_At IS NULL");
  $admissions = mysqli_fetch_assoc($admissions_query);
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
    $row['Short_Name'],
    $row['Contact_Name'],
    $row['Email'],
    $row['Mobile'],
    $vertical_type,
    $row['Alternate_Mobile'],
    $row['Address'],
    $row['City'],
    $row['District'],
    $row['State'],
    $row['Pincode'],
    $row['password'],
    $row['CanCreateSubCenter'] == 1 ? 'Yes' : 'No',
    $row["Status"] == 1 ? 'Active' : 'Inactive',
    $alloted_universities['Alloted_Universities'],
    $no_of_admission = $admissions['Applications'],
    $current_amount,
    $row["Created_At"]
  );
}


$xlsx = SimpleXLSXGen::fromArray($data)->downloadAs('Center Master.xlsx');
