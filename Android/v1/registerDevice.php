<?php
require_once "../includes/DbOperation.php";

$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['device_id']) and isset($_POST['user_id']) and isset($_POST['device_name']) 
    and isset($_POST['linked_device_id']) and isset($_POST['linked_device_name']) and isset($_POST['threshold'])){
        // operate data
        $db = new DbOperator();
        $db->__contruct();
        $result = $db->addDevice($_POST['user_id'], $_POST['device_id'], $_POST['device_name'],
         $_POST['linked_device_id'],  $_POST['linked_device_name'], $_POST['threshold']);
        
        if($result == 1){
            $response['error'] = false;
            $response['message'] = 'Successfully register new device';
        }
        elseif($result == 2){
            $response['error'] = true;
            $response['message'] = 'Some error occur';
        }
        elseif($result == 0){
            $response['error'] = true;
            $response['message'] = 'Device already exist on system';
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