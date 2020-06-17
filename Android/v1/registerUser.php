<?php
require_once "../includes/DbOperation.php";

$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['username']) and isset($_POST['password']) and isset($_POST['email'])){
        // operate data
        $db = new DbOperator();
        $db->__contruct();
        $result = $db->createUser($_POST['username'], $_POST['password'], $_POST['email']);
        if($result == 1){
            $response['error'] = false;
            $response['message'] = 'Successfully register new user';
        }
        elseif($result == 2){
            $response['error'] = true;
            $response['message'] = 'Some error occur';
        }
        elseif($result == 0){
            $response['error'] = true;
            $response['message'] = 'Username already exist on system';
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