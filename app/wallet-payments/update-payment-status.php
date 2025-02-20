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
  $update = $conn->query("UPDATE Wallets SET Status = $value, Approved_By = " . $_SESSION['ID'] . ", Approved_On = now() WHERE ID = $id");
  if ($update && $value == 1) {
    echo json_encode(['status' => 200, 'message' => 'Amount added to user wallet successfully!']);
  } else if ($update) {
    echo json_encode(['status' => 200, 'message' => 'Payment status updated successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
