<?php
require_once "../includes/DbOperation.php";

$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['device_id'])){
        // operate data
        $db = new DbOperator();
        $db->__contruct();
        $check = $db->checkIfHasMeasurement($_POST['device_id']);
        $result = $db->getMeasurement($_POST['device_id']);
        if($check){
            $response['error'] = false;
            $response['message'] = 'Successfully change measurement';
            $response['reading'] = $result;
        }
        else{
            $response['error'] = false;
            $response['message'] = 'No reading found';
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