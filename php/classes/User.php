<?php
    ini_set('display_errors',1);
    error_reporting(E_ALL);
    require_once "DB.php";
    require_once "../../PHPMailer-master/PHPMailerAutoload.php";
    require_once "../../EmailPassword.php";
    require_once "views/email.php";
    require_once "views/passresetemail.php";
    require_once "views/acclocked.php";
    class User{
        public function register(){
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            //check if user record already exists
            $db = new DB();
            $sql = "SELECT Email FROM users WHERE Email = '$email';";  //count query for number of records with matching email
            $return = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
            if($return){  //record with email already exists in database
                return false;
            }
            else{
                //generate sql for insert statement
                $sql = "INSERT INTO users (Email, Password, FirstName, LastName) VALUES ('$email', MD5('$password'), '$firstName', '$lastName');";  //create user record in database with insert sql statement
                $db->execute($sql);
                $_POST['email'] = $email;
                $_POST['password'] = $password;
                $this->login();
                $this->sendValidationEmail();
                return true;  //add to logic so that this won't return if there is an error in db execution
            }

        }
        
        public function lockaccount($email){
            $db = new DB();
            $sqlinsert = "UPDATE users SET Locked = 1 WHERE Email = '$email';";  //insert new time stamp into failed LastFailedLogin
            $db->execute($sqlinsert);
            }
        
        public function unlockaccount($email){
            $db = new DB();
            $sqlinsert = "UPDATE users SET Locked = 0, SET LastFailedLogin = 0, NumFailedLogins = 0 WHERE Email = '$email';";  //Account unlocked
            $db->execute($sqlinsert);
            }

        public function login(){
            $email = $_POST['email'];
            $password = $_POST['password'];
            $db = new DB();
            $sql = "SELECT UserID, Locked FROM users WHERE Email = '$email';";  //query User record where email match given
            $returnLock = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
            if(!$returnLock){/*account does not exist.*/ echo "account does not exist";
            exit;}
            if ($returnLock['Locked'] == 1){
                //return account locked (prior to login attempt)
                echo "Account locked prior to login attempt";
                exit;
                }
            else {
                
                $sql = "SELECT UserID, EmailValidated FROM users WHERE Email = '$email' AND Password = MD5('$password');";  //query User record where email and password match those given
                $return = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
                if (!$return){
                    $curtime = time(); // Gets current time
                    
                     $sqlinsert = "UPDATE users SET LastFailedLogin = '$curtime', NumFailedLogins = NumFailedLogins +1 WHERE Email = '$email';";  //insert new time stamp into failed LastFailedLogin
                        $db->execute($sqlinsert);
                    
                    $timest = "SELECT LastFailedLogin, NumFailedLogins FROM users WHERE Email = '$email';"; //last failed login and num failed logins sql
                    $returnedquery = $db->query($timest)->fetch(PDO::FETCH_ASSOC); //last failed login and num failed logins in associative array
                    $singletimestamp = $returnedquery['LastFailedLogin']; //grab lastfailedlogin from array
                    $numfailedlogins = $returnedquery['NumFailedLogins']; //grab numfailedlogins from array
                    //curtime - singletimestamp = seconds since login failure. This requires a 5 minute waiting period.
                    
                    
                    if (($curtime-$singletimestamp) < 300 && $numfailedlogins > 2){
                         $this->lockaccount($email);
                        //return locked account page
                        echo "Login locked after login attempt";
                    }
                    
                    echo "Incorrect password";
                    }
                else {
                    $sqlinsert = "UPDATE users SET NumFailedLogins = 0 WHERE Email = '$email';";  //insert new failed login count
                    $db->execute($sqlinsert);
                    //login successful
                    $_SESSION["Current_User"] = $return["UserID"];
                    $_SESSION["Logged_In"] = true;
                    $_SESSION["Email_Validated"] = $return["EmailValidated"];

                    echo "Login successful";
                    }
                }
                return true;
            }

        

        public function logout(){
            session_destroy();
        }

        public function sendEmail($recipient, $emailbody, $emailsubject){
            $mail             = new PHPMailer();
            $body = $emailbody;
            //$body             = eregi_replace("[\]",'',$emailbody); //replace use of deprecated function
            $mail->IsSMTP(); // telling the class to use SMTP
            $mail->Host       = "smtp.gmail.com"; // SMTP server
            $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
            $mail->SMTPAuth   = true;                  // enable SMTP authentication
            $mail->SMTPSecure = "tls";                 // sets the prefix to the servier
            $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
            $mail->Port       = 587;                   // set the SMTP port for the GMAIL server
            $mail->Username   = "fresnostatebuynsell@gmail.com";  // GMAIL username
            $mail->Password   = GetEmailPassword();            // GMAIL password
            $mail->SetFrom('fresnostatebuynsell@gmail.com', 'Fresno State Buy N Sell');
            $mail->Subject    = $emailsubject;
            $mail->MsgHTML($body);
            $mail->AddAddress($recipient);
            $mail->Send();
            /*if(!$mail->Send()) {
                echo "Mailer Error: " . $mail->ErrorInfo;}
            else {
                echo "Message sent!";
            }*/
            //later update to return a boolean to whether sent or not
        }

        public function sendValidationEmail(){
            $db = new DB();
            if(isset($_SESSION["Current_User"])){
                $UserID = $_SESSION["Current_User"];
            }
            elseif(isset($_GET["user-id"])){
                $UserID = $_GET["user-id"];
            }
            else{
                return false;
            }
            $sql = "SELECT Email, EmailValidated, FirstName, LastName FROM users WHERE UserID = $UserID;";
            $return = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
            if(!$return || $return["EmailValidated"]){ //user email does not exist or is already validated
                return false;
            }
            else{
                $recipientEmail = $return["Email"];
                //create hash token
                $HashToken=  md5( rand(0,1000) );
                //store in database
                $sql = "UPDATE users SET HashToken='$HashToken' WHERE UserID = $UserID;";
                $db->execute($sql);
                $emailBody = getValidationEmailBody($UserID, $HashToken, $return['FirstName'], $return['LastName']);
                $this->sendEmail($recipientEmail, $emailBody, "Validate Email");
                return true; //change this later to if email was able to be sent
            }
        }

        public function sendPasswordResetEmail(){
            $db = new DB();
            $email = $_POST["email"];
            $sql = "SELECT * FROM users WHERE Email = '$email';";
            $return = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
            if(!$return){ //email does not exist
                return false;
            }
            else{
                $UserID = $return["UserID"];
                $HashToken=  md5( rand(0,1000) );
                //store in database
                $sql = "UPDATE users SET HashToken='$HashToken' WHERE UserID = $UserID;";
                $db->execute($sql);
                $emailBody = getPassResetEmailBody($UserID, $HashToken, $return['FirstName'], $return['LastName']);
                $this->sendEmail($email, $emailBody, "Reset Password");
                return true;
            }
        }
        
        public function sendAccUnlockEmail(){
            $db = new DB();
            $email = $_POST["email"];
            $sql = "SELECT * FROM users WHERE Email = '$email';";
            $return = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
            if(!$return){ //email does not exist
                return false;
            }
            else{
                $UserID = $return["UserID"];
                $HashToken=  md5( rand(0,1000) );
                //store in database
                $sql = "UPDATE users SET HashToken='$HashToken', Locked=1 WHERE UserID = $UserID;";
                $db->execute($sql);
                $emailBody = getPassResetEmailBody($UserID, $HashToken, $return['FirstName'], $return['LastName']);
                $this->sendEmail($email, $emailBody, "Unlock Account");
                return true;
            }
        }

        public function checkHashToken(){
            $db = new DB();
            //get user ID and hash token from GET
            $userID = $_GET["user-id"];
            $hashToken = $_GET["hash-token"];
            //search for match in the db -> $sql
            $sql = "SELECT * FROM users WHERE UserID = $userID and HashToken = '$hashToken';";
            $return = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
            return $return ? true : false;
        }

        public function validateEmail(){
            $db = new DB();
            $userID = $_GET["user-id"];
            if(!$this->checkHashToken()){
                //no match found, either userID dne or hash token is wrong
                return false;
            }
            else{
                //update emailValidated bit in db
                $sql = "UPDATE users SET EmailValidated=1 WHERE UserID = $userID;";
                $db->execute($sql);
                $_SESSION["Email_Validated"] = true;
                return true;
            }
        }

        public function resetPassword(){
            $db = new DB();
            $userID = $_GET["user-id"];
            $password = $_POST["password"];
            $sql = "UPDATE users SET Password=MD5('$password') WHERE UserID = $userID;";
            $db->execute($sql);
        }

        public function getUserProfile(){
            $db = new DB();
            $userID = (isset($_GET["user-id"]) ? $_GET["user-id"] : ($_SESSION["Current_User"]));
            $sql = "SELECT * FROM users WHERE '$userID' = UserID"; //getting user data from userID
            $return = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
            $sql = "SELECT * FROM reviews WHERE ProfileID = $userID;";
            $reviewReturn = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
            $reviewedYet = ($reviewReturn ? true : false);
            $averageRating = ($reviewedYet ? $this->getAverageRating($userID) : "No Reviews Yet");
            if($reviewedYet) {
                $numWholeStars = floor($averageRating);
                $halfStar = $averageRating - $numWholeStars;
                $reviews = $this->getReviews($userID);
            }
            $userImg = ($return["PicturePath"] != null ? $return["PicturePath"] : "/FresnoStateBuyNSell/img/default_user.png");

            require_once "../html/header_style2.html"; //header
            require_once "views/userprofile.php";
            require_once "../html/footer2.html"; //footer
        }

        public function review(){
            $db = new DB();
            $profileID = $_GET["user-id"];
            $commenterID = $_SESSION["Current_User"];
            $starRating = $_POST["rating"];
            $reviewText = $_POST["comment"];
            $sql = "INSERT INTO reviews (CommenterID, ProfileID, StarRating, ReviewText) VALUES ($commenterID, $profileID, $starRating, '$reviewText');"; //inserting review record
            $db->execute($sql);
        }

        public function getReviews($userID){
            $db = new DB();
            //updated query to for reviews to be sorted by time?
            $sql = "SELECT * FROM reviews WHERE $userID = ProfileID"; //get all reviews for specified userID
            $return = $db->query($sql);
            $reviews = array();
            while($row = $return->fetch(PDO::FETCH_ASSOC)){
                $commenterID = $row["CommenterID"];
                $sql = "SELECT FirstName, LastName FROM users WHERE $commenterID = UserID"; //get name of reviewer
                $userReturn = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
                $review = array(
                    "StarRating" => $row["StarRating"],
                    "ReviewText" => $row["ReviewText"],
                    "ReviewTimeStamp" => $row["ReviewTimeStamp"],
                    "FirstName" => $userReturn["FirstName"],
                    "LastName" => $userReturn["LastName"]
                );
                array_push($reviews, $review);
            }
            return $reviews;
        }

        public function getAverageRating($userID){
            $db = new DB();
            $sql = "SELECT ROUND(AVG(StarRating),2) AS StarRatingAverage FROM reviews WHERE $userID = ProfileID";  //get average review (star rating)
            $return = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
            return $return["StarRatingAverage"];
        }

        public function addProfilePic(){
            $db = new DB();
            $target_file = "/uploads/profile_pics/".basename($_FILES["pic"]["name"]);
            $target_dir =  $_SERVER['DOCUMENT_ROOT'].$target_file;
            move_uploaded_file($_FILES["pic"]["tmp_name"], $target_dir);
            $userID = $_SESSION["Current_User"];
            $sql = "UPDATE users SET PicturePath = '$target_file' where UserID = $userID;";
            $db->execute($sql);
        }

    }

 ?>
