<?php

require '../../../includes/db-config.php';
session_start();

if (isset($_REQUEST['dispatch_by']) && isset($_REQUEST['dispatch_date']) && isset($_REQUEST['dispatch_mode']) && isset($_REQUEST['docket_id'])) {
    $dispatch_by = mysqli_real_escape_string($conn,$_REQUEST['dispatch_by']);
    $dispatch_mode = mysqli_real_escape_string($conn,$_REQUEST['dispatch_mode']);
    $docket_id = mysqli_real_escape_string($conn,$_REQUEST['docket_id']);
    $dispatch_date = date_format(date_create(mysqli_real_escape_string($conn,$_REQUEST['dispatch_date'])),'Y-m-d');
    
    $consignment_no = '';
    if(isset($_REQUEST['consignment_no'])) {
        $consignment_no = mysqli_real_escape_string($conn,$_REQUEST['consignment_no']);
    }

    $filepath = '';
    if(isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
        $filepath = checkAndUploadfile($docket_id);
        if(!$filepath) {
            echo json_encode(['status'=>400,'message'=>'Upload file must be pdf or image']);
            die;
        } 
    }

    if (empty($filepath)) {
        $check_details = $conn->query("SELECT scan_copy FROM `dispatch_marksheet` WHERE dockect_id = '$docket_id'");
        $check_details = mysqli_fetch_column($check_details);
        if(!is_null($check_details)) {
            $filepath = $check_details;
        }
    }

    $updateDispatch = $conn->query("UPDATE dispatch_marksheet SET consignment_no = '$consignment_no' , dispatch_by = '$dispatch_by'  , dispatch_date = '$dispatch_date' , scan_copy = '$filepath' , mode = '$dispatch_mode' WHERE dockect_id = '$docket_id'");

    if ($updateDispatch) {
        $updateDispatchStatus = $conn->query("UPDATE MarkSheet_Entry SET Dispatch_status = '2' WHERE Docket_Id = '$docket_id'");
        if($updateDispatchStatus) {
            showResponse($updateDispatchStatus,"updated");
        }
    } else {
        showResponse(false);
    }
}

function checkAndUploadfile($docket_id) : bool|string {
    
    $mimes = [
        'image/jpeg',
        'image/jpg',
        "image/jpg","image/png","image/gif",
        'application/pdf',
        'application/doc',
        'application/docx',
    ]; 
    
    if (in_array($_FILES["file"]["type"], $mimes)) {
        $extension = substr($_FILES["file"]["name"],strlen($_FILES["file"]["name"])-4,strlen($_FILES["file"]["name"]));
        move_uploaded_file($_FILES['file']['tmp_name'], '../../../uploads/marksheet_dispatch/marksheet_dispatch_bill/' . $docket_id . $extension);
        return '../../../uploads/marksheet_dispatch/marksheet_dispatch_bill/' . $docket_id . $extension;
    } else {
        return false;
    }
}

function showResponse($response, $message = 'Something went wrong!') {
    if ($response) {
        echo json_encode(['status' => 200, 'message' => "Dispatch details $message successfully!"]);
    } else {
        echo json_encode(['status' => 400, 'message' => $message]);
    }
}
?>