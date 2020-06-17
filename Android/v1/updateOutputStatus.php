<?php
require_once "../includes/DbOperation.php";
date_default_timezone_set("Asia/Ho_Chi_Minh");
$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['device_id']) and isset($_POST['status'])){
        // operate data
        $db = new DbOperator();
        $db->__contruct();
        $result = $db->insertOutputDeviceStatus($_POST['device_id'], date("Y-m-d H:i:s"), $_POST['status']);
        if($result){
            $response['error'] = false;
            $response['message'] = 'Successfully change status';
        }
        else{
            $response['error'] = true;
            $response['message'] = 'Something went wrong';
        }
    }
    else{
        $response['error'] = true;
        $response['message'] = 'Required field are missing';
    }
}
else {
    $response['error'] = true;
    $response['message'] = 'Invalid Request';
}

echo json_encode($response);