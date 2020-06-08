<?php
require_once "../includes/DbOperation.php";

$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['device_id']) and isset($_POST['date']) and isset($_POST['time']) and isset($_POST['measurement'])){
        // operate data
        $db = new DbOperator();
        $db->__contruct();
        $result = $db->insertInputDeviceMeasurement($_POST['device_id'], $_POST['date'], $_POST['time'], $_POST['measurement']);
        if($result){
            $response['error'] = false;
            $response['message'] = 'Successfully change measurement';
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