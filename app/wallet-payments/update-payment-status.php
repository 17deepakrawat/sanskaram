<?php
if (isset($_POST['id']) && isset($_POST['value'])) {
  require '../../includes/db-config.php';
  require '../../includes/helpers.php';

  session_start();

  $id = intval($_POST['id']);
  $value = intval($_POST['value']);


  $payment = $conn->query("SELECT * FROM Wallets WHERE Type = 1 AND ID  = $id");
  $payment = $payment->fetch_assoc();
  $student_id = $payment['Added_For'];
  $amount = $payment['Amount'];


  // send mail
  $userdata = $conn->query("SELECT Name, Code, Added_By as user_id,Vertical_type FROM Users left join Wallets on Added_By = Users.ID WHERE Wallets.ID = $id");
  $userdata = $userdata->fetch_assoc();
  $center_name = $userdata['Name'] . '(' . $userdata['Code'] . ')';
  $vartical_type = $userdata['Vertical_type'];

date_default_timezone_set('Asia/Kolkata');
        $currentTime = date("d-m-Y h:i:sa");
  $subject = "Addition Confirmation of {$amount} Rupees to {$center_name} Wallet through offline mode";

  $message = "
      <p>Dear Reporting Manager,</p>
      <p>I hope this email finds you well.</p>
      <p>An amount of <strong>₹{$amount}</strong> has been added to the wallet of <b>{$center_name} </b> through offline mode on {$currentTime}.</p>
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

//   $accountent_email = "karuna@edtechinnovate.com";
//   $operation_email = "karuna@edtechinnovate.com";

  $to = $accountent_email . "," . $operation_email;


  // end send mail 

  $update = $conn->query("UPDATE Wallets SET Status = $value, Approved_By = " . $_SESSION['ID'] . ", Approved_On = now() WHERE ID = $id");
  if ($update && $value == 1) {
    sendMail($to, $subject, $message);
    echo json_encode(['status' => 200, 'message' => 'Amount added to user wallet successfully!']);
  } else if ($update) {
    echo json_encode(['status' => 200, 'message' => 'Payment status updated successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
