<?php
require_once "../includes/DbOperation.php";

$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // operate data
    if(isset($_POST['device_id']) and isset($_POST['type'])and isset($_POST['day']) and isset($_POST['month']) and isset($_POST['year'])){
        // operate data
        $db = new DbOperator();
        $db->__contruct();
        $result = $db->getValueInCustomDate($_POST['device_id'], $_POST['type'],$_POST['day'],$_POST['month'],$_POST['year']);
        $response['date'] = $result;

    }
    else{
        $response['error'] = true;
        $response['duy'] = false;
        $response['message'] = 'Required field are missing';
    }
}
else {

    $response['error'] = true;
    $response['message'] = 'Invalid Request';
}

echo json_encode($response);