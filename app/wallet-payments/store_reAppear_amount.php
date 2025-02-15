<?php

require '../../includes/db-config.php';
include '../../includes/helpers.php';
session_start();
if (isset($_POST['amount']) && isset($_POST['ids']) && isset($_POST['payfor'])) {
  
    $id = mysqli_real_escape_string($conn, $_POST['ids']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $payfor = mysqli_real_escape_string($conn, $_POST['payfor']);
    $transaction_id = strtoupper(strtolower(uniqid()));
    $file = $transaction_id;
    $payment_type = "Wallet";
    $bank_name = "Wallet";
    $gateway_id = $transaction_id;
    $transaction_date = date("Y-m-d");

    $check = $conn->query("SELECT ID FROM Wallet_Payments WHERE Transaction_ID = '$gateway_id' AND Type = 3 AND Payment_Mode != 'Cash'");
    if ($check->num_rows > 0) {
        exit(showResponse(false,'Transaction ID already exists!'));
    }

    $student = $conn->query("SELECT Duration, Admission_Session_ID FROM Students WHERE ID = $id");
    $student = $student->fetch_assoc();
    $duration = $student['Duration'];

    $insert_invoice = $conn->query("INSERT INTO Wallet_Invoices (`User_ID`, `Student_ID`, `Duration`, `University_ID`, `Invoice_No`, `Amount`) VALUES (" . $_SESSION['ID'] . ", $id, '$duration', " . $_SESSION['university_id'] . ", '$transaction_id', $amount)");

    if ($insert_invoice) {
        $payment_type = 'Exam Re-appear fee';
        $insert_wallet_payment = $conn->query("INSERT INTO Wallet_Payments (Type, Status, Transaction_Date, Transaction_ID, Gateway_ID, Bank, Amount, Payment_Mode, Added_By, File, University_ID) VALUES (3, 1, '$transaction_date', '$transaction_id', '$gateway_id', '$bank_name', '$amount', '$payment_type', " . $_SESSION['ID'] . ", '$file', " . $_SESSION['university_id'] . ")");
        if ($insert_wallet_payment) {
            $insert_ladger = $conn->query("INSERT INTO Student_Ledgers (Student_ID, Duration, Date, University_ID, Type, Source, Transaction_ID, Fee, Status) VALUES ('$id', '$duration', '$transaction_date', " . $_SESSION['university_id'] . ", 3, 'Wallet', '$transaction_id', '" . json_encode(['Paid' => $amount]) . "', 1)");

            if ($insert_ladger) {
                $column_name = "attempt" .$payfor. "_payment";
                $update = $conn->query("UPDATE Examination_Confirmation SET $column_name = '$transaction_id' WHERE Student_Id = '$id'");
                showResponse($update);    
            } else {
                showResponse(false);
            }
        } else {
            showResponse(false);    
        }
    } else {
        showResponse(false);
    }
}

function showResponse($response,$message = "Something went wrong!") {
    if ($response) {
        echo json_encode(['status' => 200, 'message' => 'Payment added successfully!']);
    } else {
        echo json_encode(['status' => 400, 'message' => $message]);
    }
}   

?>