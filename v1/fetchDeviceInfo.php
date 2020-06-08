<?php
require_once "../includes/DbOperation.php";

$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['user_id'])){
        $db = new DbOperator();
        $db->__contruct();
        $result = $db->checkOwnDevices($_POST['user_id']);
        if($result){
            $list_device = $db->getDevices($_POST['user_id']);
            $response['error'] = false;
            $response['list'] = $list_device;
        }
        else{
            $response['error'] = true;
            $response['message'] = 'No device found';
        }

    }
    else{
        $response['error'] = true;
        $response['message'] = 'Required field are missing';
    }
}

echo json_encode($response);