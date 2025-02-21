<?php
if (isset($_POST['id'])) {
    require '../../../includes/db-config.php';
    $id = intval($_POST['id']);
    $getStatus = $conn->query("SELECT Show_Popup FROM Late_Fees WHERE id = $id");
    if ($getStatus->num_rows == 0) {
        echo json_encode(['status' => false, 'message' => 'No record found for the provided ID.']);
        exit;
    }
    $status = $getStatus->fetch_assoc()['Show_Popup'];
    $status = ($status == 1) ? 0 : 1;
    $updateStatus = $conn->query("UPDATE Late_Fees SET Show_Popup = $status WHERE id = $id");
    if ($updateStatus) {
        echo json_encode(['status' => true, 'message' => 'Late fee popup status has been successfully updated.']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to update the late fee popup status. Please try again.']);
    }
}else{
    echo json_encode(['status' => false, 'message' => 'No ID provided.']);
}

