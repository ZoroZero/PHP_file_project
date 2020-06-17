<?php
require_once "../includes/DbOperation.php";

$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['username']) and isset($_POST['password'])){
        $db = new DbOperator();
        $db->__contruct();
        $result = $db->userLogin($_POST['username'], $_POST['password']);
        if($result){
            $user = $db->getUserByUserName($_POST['username']);
            $response['error'] = false;
            $response['user_ID'] = $user['user_ID'];
            $response['username'] = $user['username'];
            $response['email'] = $user['email'];
        }
        else{
            $response['error'] = true;
            $response['message'] = 'Incorrect username or password';
        }

    }
    else{
        $response['error'] = true;
        $response['message'] = 'Required field are missing';
    }
}

echo json_encode($response);