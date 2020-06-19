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
        function createUser($username, $password, $email){
            if($this->userExist($username)){
                return 0;
            }
            else{
                $pass = md5($password);
                $stmt =$this->con->prepare("INSERT INTO `user` (`user_ID`, `username`, `password`, email) VALUES (NULL, ?, ?, ?);");
                $stmt->bind_param("sss", $username, $pass, $email);

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
            $stmt =$this->con->prepare("SELECT user_ID from user WHERE username = ? AND password = ?");
            $stmt->bind_param("ss", $username, $pass);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        // Get user data from user name
        function getUserByUserName($username){
            $stmt =$this->con->prepare("SELECT * from user WHERE username = ? ");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
        
        // Check if user already exist in DB
        private function userExist($username){
            $stmt =$this->con->prepare("SELECT user_ID from user WHERE username = ? ");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        
        ############################################# Device operation #############################################
        // Check if user have any device
        function checkOwnDevices($user_id){
            $id = (int)$user_id;
            $stmt =$this->con->prepare("SELECT * from devices WHERE user_id = ? ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();
            return  $stmt->num_rows > 0;
        }
        // Get device from user id
        function getDevices($user_id){
            $id = (int)$user_id;
            $stmt =$this->con->prepare("SELECT input_id as device_id, input_name as device_name, output_id as linked_device_id, output_name as linked_device_name 
            from devices where user_ID = ? UNION
            SELECT output_id as device_id, output_name as device_name, input_id as linked_device_id, input_name as linked_device_name 
            from devices where user_ID = ?;");
            $stmt->bind_param("ii", $id, $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        // Insert device
        function addDevice($user_id, $device_id, $device_name, $linked_device_id, $linked_device_name, $threshold){
            if($this->checkDeviceExist($device_id)){
                return 0;
            }
            else{
                $convert_threshold = (int)$threshold;
                $convert_user_id = (int)$user_id;
                $stmt =$this->con->prepare("INSERT INTO 
                devices(user_id, input_id, input_name, output_id, output_name, threshold) 
                VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssi", $convert_user_id, $device_id,  $device_name, $linked_device_id, $linked_device_name, $convert_threshold);
                if($stmt->execute()){
                    return 1;
                }
                else
                    return 2;
            }
        }

        // Check if device id exist
        function checkDeviceExist($device_id){
            $stmt =$this->con->prepare("SELECT input_id as device_id from devices where input_id = ? UNION
            SELECT output_id as device_id from devices where output_id = ?;");
            $stmt->bind_param("ss", $device_id, $device_id);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        // Change threshold on user_id and device id
        function changeThreshold($user_id, $device_id, $threshold){
            $convert_id = (int)$user_id;
            $convert_threshold = (int)$threshold;
            $stmt =$this->con->prepare("UPDATE devices SET device_measurement = ? WHERE user_id = ? AND device_id = ?");
            $stmt->bind_param("iii", $convert_measurement, $convert_id, $convert_threshold);
            if($stmt->execute()){
                return true;
            }
            else{
                return false;
            }
        }

        // Insert device measurement
        function insertInputDeviceMeasurement($device_id, $date, $type, $measurement){
            $convert_measurement = (int)$measurement;
            $stmt =$this->con->prepare("INSERT INTO input_devices(input_device_id, date, type, measurement) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $device_id, $date, $type, $measurement);
            if($stmt->execute()){
                return true;
            }
            else{
                return false;
            }
        }

        // Insert output device status history
        function insertOutputDeviceStatus($device_id, $date, $status){
            $stmt =$this->con->prepare("INSERT INTO output_devices(output_device_id, date, status) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $device_id, $date, $status);
            if($stmt->execute()){
                return true;
            }
            else{
                return false;
            }
        }

        // Check if device has last reading
        function checkIfHasMeasurement($device_id){
            $stmt =$this->con->prepare("SELECT * from input_devices where input_device_id = ?");
            $stmt->bind_param("s", $device_id);
            $stmt->execute();
            $stmt->store_result();
            return $stmt->num_rows > 0;
        }

        // Register input device
        function getMeasurement($device_id){
            $stmt =$this->con->prepare("SELECT input_device_id,  measurement, date, type, (TIMESTAMPDIFF(SECOND, SYSDATE(), date)) as different
            FROM input_devices 
            WHERE input_device_id = ?
            ORDER BY different DESC;");
            $stmt->bind_param("s", $device_id);
            $stmt->execute();
            //$result = $stmt->store_result();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        // check sensor device exist from device_id
        function checkSensorInfo_FromDeviceID($user_id, $device_id){
            $id = (int)$user_id;
            $stmt =$this->con->prepare("SELECT * from devices WHERE user_ID = ? and input_id = ?");
            $stmt->bind_param("is", $id, $device_id);
            $stmt->execute();
            $stmt->store_result();
            return  $stmt->num_rows > 0;
        }

        // get sensor device information from device_id
        function getSensorInfo_FromDeviceID($user_id, $device_id){
            $id = (int)$user_id;
            $stmt =$this->con->prepare("SELECT * from devices WHERE user_ID = ? and input_id = ?");
            $stmt->bind_param("is", $id, $device_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }


        function getOutputStatus($output_id){
            $stmt =$this->con->prepare("SELECT output_device_id, date, status, TIMESTAMPDIFF(SECOND, SYSDATE(), date) as different 
            FROM `output_devices` 
            WHERE output_device_id = ? 
            ORDER BY different DESC;");
            $stmt->bind_param("s", $output_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
        function addPlant($user_id , $plant_name, $buy_date, $buy_location, $amount, $linked_sensor_id){
            if($this->checkPlantExist($user_id, $plant_name, $buy_date)){
                return 0;
            }
            else{
                $convert_user_id = (int)$user_id;
                //$_newDate = date("Y-m-d",strtotime($buy_date));
                $convert_amount = (int)$amount;
                $stmt =$this->con->prepare("INSERT INTO plant(`User_ID`, `Plant_name`, `Buy_date`, `Buy_location`, `Amount`, linked_sensor_id) 
                VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssis", $convert_user_id, $plant_name, $buy_date, $buy_location, $convert_amount, $linked_sensor_id);
                if($stmt->execute()){
                    return 1;
                }
                else
                    return 2;
            }
        }

        // Get plant from user_id
        function fetchPlantInfo($user_id){
            $id = (int)$user_id;
            $stmt =$this->con->prepare("SELECT * from plant where User_ID = ?");
            $stmt->bind_param("i",$id);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
    }