<?php
require "../includes/DbOperation.php";
require "../includes/phpMQTT.php";
set_time_limit(5);
date_default_timezone_set("Asia/Ho_Chi_Minh");
// if($_SERVER['REQUEST_METHOD'] == 'POST'){
if(!isset($_POST['topic']) or !isset($_POST['type']) or !isset($_POST['device_id'])){
	// operate data
	echo "Failed";
}
//Setup MQTT server
// $server = '13.67.44.229';     // change if necessary
// $port = 1883;                     // change if necessary
// $username = 'An';                   // set your username
// $password = '01597894561230';                   // set your password

// $server = '52.187.125.59';  
// $port = 1883;          
// $username = 'BKvm2';                   
// $password = 'Hcmut_CSE_2020'; 

$server = '52.163.220.103';  
$port = 1883;          
$username = 'BKvm2';                   
$password = 'Hcmut_CSE_2020'; 
$client_id = 'phpMQTT-subscriber'; 

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id.rand());
if(!$mqtt->connect(true, NULL, $username, $password)) {
	exit(1);
}

$mqtt->debug = true;
for($i = 0; $i < count($_POST['topic']); $i++){
	$topics[$_POST['topic'][$i]] = array('qos' => 0, 'function' => 'procMsg');
	$mqtt->subscribe($topics, 0);
}
while($mqtt->proc()) {

}
$response = array();		
$mqtt->close();		
function procMsg($topic, $msg){
	$messages = json_decode($msg);
	$db = new DbOperator();
	$db->__contruct();
	
	// echo json_encode($device_info[0]);
	// echo $device_info[0]["threshold"];

	$index = -1;
	for($i = 0; $i< count($_POST['topic']); $i++){
		if($_POST['topic'][$i] == $topic){
			$index = $i;
			break;
		}
	}

	if($index !== -1){
		$device_info = $db->getSensorInfo_FromDeviceID($_POST['user_id'], $_POST['device_id'][$index]);
		//echo $_POST['type'][$index];
		// $response[$_POST['device_id'][$index]] = array();
		$response["position"] = intval($_POST['position'][$index]);
		if(strpos($_POST['type'][$index], "TempHumi") !== false){
			$db->insertInputDeviceMeasurement($_POST['device_id'][$index], date("Y-m-d H:i:s"), "Temp", $messages[0]->values[0]);
			$db->insertInputDeviceMeasurement($_POST['device_id'][$index], date("Y-m-d H:i:s"), "Humid", $messages[0]->values[1]);
			if($device_info[0]['threshold'] < $messages[0]->values[0])
			{
				if(strpos($db->getOutputStatus($_POST['linked_device_id'][$index])[0]['status'], "On-0") !== false){
					$response['message'] = "Turn on";
				}
				else{
					$response['message'] = "No change";
				}
			}
			else{
				if(strpos($db->getOutputStatus($_POST['linked_device_id'][$index])[0]['status'], "On-255") !== false){
					$response['message'] = "Turn off";
				}
				else{
					$response['message'] = "No change";
				}
			}
		}
		else{
			$db->insertInputDeviceMeasurement($_POST['device_id'][$index], date("Y-m-d H:i:s"), $_POST['type'][$index], $messages[0]->values[0]);
			if($device_info[0]['threshold'] < $messages[0]->values[0])
			{
				if(strpos($db->getOutputStatus($_POST['linked_device_id'][$index])[0]['status'], "On-0") !== false){
					$response['message'] = "Turn on";
				}
				else{
					$response['message'] = "No change";
				}
			}
			else{
				if(strpos($db->getOutputStatus($_POST['linked_device_id'][$index])[0]['status'], "On-255") !== false){
					$response['message'] = "Turn off";
				}
				else{
					$response['message'] = "No change";
				}
			}
		}

		//  echo $msg;
		echo json_encode($response);
		echo "\n";	
	}
}


    