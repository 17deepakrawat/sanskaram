<?php
if (isset($_POST['amount']) && isset($_POST['transaction_id'])) {
  require '../../includes/db-config.php';
  require '../../includes/helpers.php';
  session_start();

  $allowed_file_extensions = array("jpeg", "jpg", "png", "gif", "JPG", "PNG", "JPEG", "pdf", "PDF");
  $file_folder = '../../uploads/offline-payments/';

  $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name']);
  $payment_type = mysqli_real_escape_string($conn, $_POST['payment_type']);
  $transaction_id = strtoupper(strtolower(uniqid()));
  $gateway_id = mysqli_real_escape_string($conn, $_POST['transaction_id']);
  $amount = mysqli_real_escape_string($conn, $_POST['amount']);
  $transaction_date = mysqli_real_escape_string($conn, $_POST['transaction_date']);
  $transaction_date = date("Y-m-d", strtotime($transaction_date));
  $student_id = isset($_POST['student_id']) ? mysqli_real_escape_string($conn, $_POST['student_id']) : '';

  $check = $conn->query("SELECT ID FROM Payments WHERE Transaction_ID = '$gateway_id' AND Type = 1 AND Payment_Mode != 'Cash'");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 400, 'message' => 'Transaction ID already exists!']);
    exit();
  }

  $file = NULL;
  if ($payment_type != 'Cash') {
    if (isset($_FILES["file"]['tmp_name']) && $_FILES["file"]['tmp_name'] != '') {
      $file = mysqli_real_escape_string($conn, $_FILES["file"]['name']);
      $tmp_name = $_FILES["file"]["tmp_name"];
      $file_extension = pathinfo($file, PATHINFO_EXTENSION);
      $file = uniqid() . "." . $file_extension;
      if (in_array($file_extension, $allowed_file_extensions)) {
        if (!move_uploaded_file($tmp_name, $file_folder . $file)) {
          echo json_encode(['status' => 503, 'message' => 'Unable to upload file!']);
          exit();
        } else {
          $file = str_replace('../..', '', $file_folder) . $file;
        }
      } else {
        echo json_encode(['status' => 302, 'message' => 'File should be Image or PDF!']);
        exit();
      }
    } else {
      echo json_encode(['status' => 400, 'message' => 'File is required!']);
      exit();
    }
  }

  $added_for_column = "";
  $added_for_value = "";
  $added_by = $_SESSION['ID'];
  if (!empty($student_id)) {
    $added_for_column = ", `Added_For`";
    $student_id = base64_decode($student_id);
    $student_id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $student_id));
    $added_for_value = "," . $student_id;
    $added_by = $conn->query("SELECT `Added_For` FROM `Students` WHERE ID = $student_id");
    $added_by = $added_by->fetch_assoc();
    $added_by = $added_by['Added_For'];
  }




  $add = $conn->query("INSERT INTO Wallets (Type, Transaction_Date, Transaction_ID, Gateway_ID, Bank, Amount, Payment_Mode, Added_By, File, University_ID $added_for_column) VALUES (1, '$transaction_date', '$transaction_id', '$gateway_id', '$bank_name', '$amount', '$payment_type', " . $added_by . ", '$file', " . $_SESSION['university_id'] . " $added_for_value)");


  if ($add) {
    $userdata = $conn->query("SELECT * FROM Users WHERE ID = $added_by");
    $userdata = $userdata->fetch_assoc();
    $center_name = $userdata['Name'] . '(' . $userdata['Code'] . ')';
    $vartical_type = $userdata['Vertical_type'];
     date_default_timezone_set('Asia/Kolkata');
    $currentTime = date("d-m-Y h:i:sa");
    $subject = "Amount addition request " . $amount . " rupees from {$center_name} for wallet through Offline Mode";

    $message = "
        <p>Dear Reporting Manager,</p>
        <p>I hope this email finds you well.</p>
        <p>A request has been made to add <strong>₹{$amount}</strong> to the wallet of <b>{$center_name}</b>. Kindly review and approve this request at your earliest convenience on {$currentTime}.</p>
        <p><em><strong>This is a system-generated email. Please do not reply.</strong></em></p>
        <p>Thanks & Regards,<br>Edtech Innovate Pvt. Ltd.</p>
    ";

    if ($vartical_type == 0) { // 1- edtech  and 0-iits
      $accountent_email = "groupaccounts@iitseducation.org";
      if ($_SESSION['university_id'] == 48) {
        $operation_email = "syam@iitseducation.org";
      } else {
        $operation_email = "akhil@iitseducation.org";
      }
    } else {
      $accountent_email = "Finance@edtechinnovate.com";
      $operation_email = "arya@edtechinnovate.com";
    }

    // $accountent_email = "karuna@edtechinnovate.com";
    // $operation_email = "karuna@edtechinnovate.com";

    $to = $accountent_email . "," . $operation_email;
    sendMail($to, $subject, $message);


    echo json_encode(['status' => 200, 'message' => 'Wallets amount added successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
