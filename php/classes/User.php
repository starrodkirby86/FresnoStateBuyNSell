<?php
    ini_set('display_errors',1); 
    error_reporting(E_ALL);
    require_once "DB.php";
    class User{
        var $userID;
        var $firstName;
        var $lastName;
        var $email;
        private $password;
        
        public function register($regData){
            $this->firstName = $regData['firstName'];
            $this->lastName = $regData['lastName'];
            $this->email = $regData['email'];
            $this->password = $regData['password'];
            
            //check if user record already exists
            $db = new DB();
            //generate sql for query
            ////$sql = "$test"."hi".$var; //sample example of string in php
            $sql = "";  //count query for number of records with matching email
            $return = $db->query($sql);
            if(true/*record already exists*/){ //will specify this when the query is done
                return false;
            }
            else{
                //generate sql for insert statement
                $sql = "";  //create user record in database with insert sql statement
                $db->execute($sql);
                $this->login();
                return true;
            }
        }
        
        public function login(){
            session_start();
            $_SESSION["Current_User"] = $this;
        }
    
    }
    
 ?>
 