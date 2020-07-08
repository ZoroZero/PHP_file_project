<?php
require_once "../includes/DbOperation.php";

$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['plant_name']) and isset($_POST['user_id']) and isset($_POST['buy_date']) 
            and isset($_POST['buy_location'])){
        // operate data
        $db = new DbOperator();
        $db->__contruct();
        $result = $db->removePlant($_POST['user_id'],$_POST['plant_name'],$_POST['buy_date'],
        $_POST['buy_location']);
        
        if($result == true){
            $response['error'] = false;
            $response['message'] = 'Successfully remove plant';
        }
        elseif($result == false){
            $response['error'] = true;
            $response['message'] = 'Some error occur';
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