<?php
    class DbOperator{
        private $con;

        function __contruct(){
            require_once dirname(__FILE__).'/DbConnector.php';

            $db = new DbConnector();

            $this->con = $db->connect();
        }

        ############################################# User operation #############################################
        // Register user
        function createUser($username, $password){
            if($this->userExist($username)){
                return 0;
            }
            else{
                $pass = md5($password);
                $stmt =$this->con->prepare("INSERT INTO `user_login` (`user_ID`, `username`, `password`) VALUES (NULL, ?, ?);");
                $stmt->bind_param("ss", $username, $pass);

                if($stmt->execute()){
                    return 1;
                }
                else
                    return 2;
            }
        }

        // Login user
        function userLogin($username, $password){
            $pass = md5($password);
            $stmt =$this->con->prepare("SELECT user_ID from user_login WHERE username = ? AND password = ?");
            $stmt->bind_param("ss", $username, $pass);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        // Get user data from user name
        function getUserByUserName($username){
            $stmt =$this->con->prepare("SELECT * from user_login WHERE username = ? ");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
        
        // Check if user already exist in DB
        private function userExist($username){
            $stmt =$this->con->prepare("SELECT user_ID from user_login WHERE username = ? ");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        ############################################# Device operation #############################################
        // Check if user have any device
        function checkOwnDevices($user_id){
            $id = (int)$user_id;
            $stmt =$this->con->prepare("SELECT * from device WHERE user_id = ? ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();
            return  $stmt->num_rows > 0;
        }
        // Get device from user id
        function getDevices($user_id){
            $id = (int)$user_id;
            $stmt =$this->con->prepare("SELECT * from device WHERE user_id = ? ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        // Insert device
        function addDevice($device_id, $user_id , $device_name, $linked_device_id, $linked_device_name){
            if($this->checkDeviceExist($device_id)){
                return 0;
            }
            else{
                $convert_user_id = (int)$user_id;
                $stmt =$this->con->prepare("INSERT INTO device(user_id, device_id, device_name, linked_device_id, linked_device_name) 
                VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $convert_user_id, $device_id,  $device_name, $linked_device_id, $linked_device_name);
                if($stmt->execute()){
                    return 1;
                }
                else
                    return 2;
            }
        }

        // Check if device id exist
        function checkDeviceExist($device_id){
            $stmt =$this->con->prepare("SELECT device_id from device WHERE device_id = ? ");
            $stmt->bind_param("s", $device_id);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        // Change measurement on user_id and device id
        function changeMeasurement($user_id, $device_id, $measurement){
            $convert_id = (int)$user_id;
            $convert_device_id = $device_id;
            $convert_measurement = (int)$measurement;
            $stmt =$this->con->prepare("UPDATE device SET device_measurement = ? WHERE user_id = ? AND device_id = ?");
            $stmt->bind_param("iii", $convert_measurement, $convert_id, $convert_device_id);
            if($stmt->execute()){
                return true;
            }
            else{
                return false;
            }
        }

        // Register input device
        function insertInputDeviceMeasurement($device_id, $date, $measurement){
            $convert_measurement = (int)$measurement;
            $stmt =$this->con->prepare("INSERT INTO input_devices(input_device_id, date, measurement) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $device_id, $date, $measurement);
            if($stmt->execute()){
                return true;
            }
            else{
                return false;
            }
        }
        ############################################# Plant operation #############################################
        // Check if plant all ready exist
        function checkPlantExist($user_id, $plant_name, $buydate){
            $convert_id = (int)$user_id;
            $stmt =$this->con->prepare("SELECT user_id from plant WHERE User_ID = ? AND Plant_name = ? AND bUY_DATE = ?");
            $stmt->bind_param("iss", $convert_id, $plant_name, $buydate);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        // Add plant to database
        function addPlant($user_id , $plant_name, $buy_date, $buy_location, $amount){
            if($this->checkPlantExist($user_id, $plant_name, $buy_date)){
                return 0;
            }
            else{
                $convert_user_id = (int)$user_id;
                //$_newDate = date("Y-m-d",strtotime($buy_date));
                $convert_amount = (int)$amount;
                $stmt =$this->con->prepare("INSERT INTO plant(`User_ID`, `Plant_name`, `Buy_date`, `Buy_location`, `Amount`) 
                VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("isssi", $convert_user_id, $plant_name, $buy_date, $buy_location, $convert_amount);
                if($stmt->execute()){
                    return 1;
                }
                else
                    return 2;
            }
        }
    }