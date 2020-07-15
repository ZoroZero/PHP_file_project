<?php
require "../includes/DbOperation.php";
require "../includes/phpMQTT.php";
require '../includes/Constants.php';
set_time_limit(10);
date_default_timezone_set("Asia/Ho_Chi_Minh");
// if($_SERVER['REQUEST_METHOD'] == 'POST'){

function checkOverlimit($measurement, $threshold, $margin){
	return intval(($measurement - $threshold)/$margin);
}

if(!isset($_POST['topic']) or !isset($_POST['type']) or !isset($_POST['device_id'])){
	// operate data
	echo "Failed";
}
//Setup MQTT server
$client_id = 'phpMQTT-subscriber'; 
$server = '13.67.44.229';   
$port = 1883;                   
$username = 'An';                  
$password = '01597894561230';                

// $server = '52.187.125.59';  
// $port = 1883;          
// $username = 'BKvm2';                   
// $password = 'Hcmut_CSE_2020'; 

// Server trí
// $server = '52.163.220.103';  
// $port = 1883;          
// $username = 'BKvm2';                   
// $password = 'Hcmut_CSE_2020'; 


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

// When meassages received
function procMsg($topic, $msg){
	$messages = json_decode($msg);
	$db = new DbOperator();
	$db->__contruct();
	

	// Find device index
	$index = -1;
	for($i = 0; $i< count($_POST['topic']); $i++){
		if($_POST['topic'][$i] == $topic){
			$index = $i;
			break;
		}
	}

	// If index found
	if($index !== -1){
		$device_info = $db->getSensorInfo_FromDeviceID($_POST['user_id'], $_POST['device_id'][$index]);
		$response["position"] = intval($_POST['position'][$index]);
		$diff = strtotime(date("Y-m-d H:i:s")) - strtotime($db->getOutputStatus($_POST['linked_device_id'][$index])[0]['date']);
		if(strpos($_POST['type'][$index], "Temperature") !== false){
			// Insert data into database
			$measurement = strval($messages[0]->values[0]).":".strval($messages[0]->values[1]);
			$db->insertInputDeviceMeasurement($_POST['device_id'][$index], date("Y-m-d H:i:s"),  $_POST['type'][$index], $measurement);

			$threshold = explode(":",$device_info[0]['threshold']);
			// Check if there are any activity within 30s span
			if($diff > 30){
				// If there is check if need to turn on
				if(checkOverlimit($messages[0]->values[0], intval($threshold[0]), TEMPERATURE_ERROR_MARGIN) != 0 
				|| checkOverlimit($messages[0]->values[1], intval($threshold[1]), HUMIDITY_ERROR_MARGIN) != 0)
				{
					if(strpos($db->getOutputStatus($_POST['linked_device_id'][$index])[0]['status'], "On-0") !== false){
						$response['message'] = "Turn on";
					}
					else{
						$response['message'] = "No change";
					}
				}

				// Check if need to turn off
				else if(checkOverlimit($messages[0]->values[0], intval($threshold[0]), TEMPERATURE_ERROR_MARGIN) == 2 
				|| checkOverlimit($messages[0]->values[1], intval($threshold[1]), HUMIDITY_ERROR_MARGIN) == 2){
					if(strpos($db->getOutputStatus($_POST['linked_device_id'][$index])[0]['status'], "On-255") !== false ){
						$response['message'] = "Turn off";
					}
					else{
						$response['message'] = "No change";
					}
				}
				else{
					$response['message'] = "No change";
				}

			}
			else{
				$response['message'] = "No change";
			}
		}
		else{
			$db->insertInputDeviceMeasurement($_POST['device_id'][$index], date("Y-m-d H:i:s"), $_POST['type'][$index], $messages[0]->values[0]);
			// Check if there are any control activity within 30s span
			if($diff > 30){
				// If no then check the current status if overlimit
				if(checkOverlimit($messages[0]->values[0], intval($device_info[0]['threshold']), LIGHT_ERROR_MARGIN) == 1)
				{
					// Check status 
					if(strpos($db->getOutputStatus($_POST['linked_device_id'][$index])[0]['status'], "On-0") !== false){
						$response['message'] = "Turn on";
					}
					else{
						$response['message'] = "No change";
					}
				}
				// If not overlimit check if underlimit
				else if(checkOverlimit($messages[0]->values[0], intval($device_info[0]['threshold']), LIGHT_ERROR_MARGIN) == 2){
					if(strpos($db->getOutputStatus($_POST['linked_device_id'][$index])[0]['status'], "On-255") !== false){
						$response['message'] = "Turn off";
					}
					else{
						$response['message'] = "No change";
					}
				}
				else{
					$response['message'] = "No change";
				}
			}
			else{
				$response['message'] = "No change";
			}
		}

		//  echo $msg;
		echo json_encode($response);
		echo "\n";	
	}
}


    