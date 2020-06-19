<?php
require_once "../includes/DbOperation.php";

$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['device_id'])){
        $db = new DbOperator();
        $db->__contruct();
        $status_list = $db->getOutputStatus($_POST["device_id"]);
        $response['error'] = false;
        $response['status_list'] = $status_list;
    }
    else{
        $response['error'] = true;
        $response['message'] = 'Required field are missing';
    }
}

echo json_encode($response);