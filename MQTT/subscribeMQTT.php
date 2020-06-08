<?php
require_once "../includes/DbOperation.php";
require "../includes/phpMQTT.php";
set_time_limit(10);
date_default_timezone_set("Asia/Ho_Chi_Minh");
// if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if(isset($_POST['topic'])){
		
		$response = array(); 
		// operate data
		// $db = new DbOperator();
		// $db->__contruct();

		// Setup MQTT server
		$server = '13.67.44.229';     // change if necessary
		$port = 1883;                     // change if necessary
		$username = 'An';                   // set your username
		$password = '01597894561230';                   // set your password
		$client_id = 'phpMQTT-subscriber'; // make sure this is unique for connecting to sever - you could use uniqid()

		$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id.rand());
		if(!$mqtt->connect(true, NULL, $username, $password)) {
			exit(1);
		}

		$mqtt->debug = true;

		$topics['ls/ls1'] = array('qos' => 0, 'function' => 'procMsg');
		$mqtt->subscribe($topics, 0);
		while($mqtt->proc()) {

		}
				
		$mqtt->close();
				
		function procMsg($topic, $msg){
			// $response['msg'] = $msg;
			// $response['topic'] = $topic;
					// echo 'Msg Recieved: ' . date('r') . "\n";
					// echo "Topic: {$topic}\n\n";
			//echo json_encode($msg);
			$messages = json_decode($msg);
			$db = new DbOperator();
			$db->__contruct();
			$t=time();
			echo date("Y-m-d H:i:s");
			//echo json_decode($mesages);;
			$db->insertInputDeviceMeasurement($messages->device_id, date("Y-m-d H:i:s"), $messages->reading);
			//echo json_decode($mesages);
			//echo $messages;
			echo $msg;
		}
	}
	

    