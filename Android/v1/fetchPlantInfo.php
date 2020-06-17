<?php
require_once "../includes/DbOperation.php";

$response = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['user_id'])){
        $db = new DbOperator();
        $db->__contruct();
        $list_plant = $db->fetchPlantInfo($_POST['user_id']);
        $response['error'] = false;
        $response['plant_list'] = $list_plant;
    }
    else{
        $response['error'] = true;
        $response['message'] = 'Required field are missing';
    }
}

echo json_encode($response);