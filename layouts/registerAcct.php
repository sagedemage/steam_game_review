<?php
    $id = $_POST['id'];
    $email = $_post['email'];
    $pwd = $_POST['pwd'];
    $error = false;
    if (isset($id) && isset($pwd) && isset($email)){
        if (!preg_match("/^[a-zA-Z-' ]*$/",$name)){
            $error = true;
        }
    }
    if(!$error){
        $db = new mysqli('127.0.0.1', 'testUser', 'test', 'testdb');

        if ($mydb->errno != 0) {
            echo "failed to connect database: ". $mydb->error . PHP_EOL;
            exit(0);
        }
        
        $request = "INSERT INTO Users VALUES id, email, passHash";
        $response = $mydb->query($query);
        if ($mydb->errno != 0) {

            echo "failed to execute query:".PHP_EOL;
            echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
            exit(0);
        }	
    }
?>
        