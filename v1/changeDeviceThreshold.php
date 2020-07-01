<?php
require_once "../includes/DbOperation.php";

$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['device_id']) and isset($_POST['new_threshold'])){
        // operate data
        $db = new DbOperator();
        $db->__contruct();
        $result = $db->changeThreshold($_POST['device_id'], $_POST['new_threshold']);
        if($result){
            $response['error'] = false;
            $response['message'] = 'Successfully change threshold';
        }
        else{
            $response['error'] = false;
            $response['message'] = 'Unable to change threshold';
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