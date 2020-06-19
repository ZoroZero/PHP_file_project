<?php
require_once "../includes/DbOperation.php";

$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['plant_name']) and isset($_POST['user_id']) and isset($_POST['buy_date']) 
            and isset($_POST['buy_location']) and isset($_POST['amount']) and isset($_POST['linked_sensor_id'])){
        // operate data
        $db = new DbOperator();
        $db->__contruct();
        $result = $db->addPlant($_POST['user_id'],$_POST['plant_name'],$_POST['buy_date'],
        $_POST['buy_location'],$_POST['amount'], $_POST['linked_sensor_id']);
        
        if($result == 1){
            $response['error'] = false;
            $response['message'] = 'Successfully register new plant';
        }
        elseif($result == 2){
            $response['error'] = true;
            $response['message'] = 'Some error occur';
        }
        elseif($result == 0){
            $response['error'] = true;
            $response['message'] = 'You already registered this same kind of plant';
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