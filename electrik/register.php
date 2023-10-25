<?php
ini_set('display_errors', 1);

require_once('../rabbitmq_lib/path.inc');
require_once('../rabbitmq_lib/get_host_info.inc');
require_once('../rabbitmq_lib/rabbitMQLib.inc');

/* Client */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
	$user = $_POST['id'];
	$email = $_POST['email'];
	$pwd = $_POST['pwd'];
	$cfrmpwd = $_POST['cfrmpwd'];
	$error = false;
	$errorMsgs = array();

	// Validation
	if (isset($user) && isset($pwd) && isset($email)) {

		if (empty($user) || !isset($user)) { # MAKE AN ERROR MSG LOG HAVE TEMPLATE
			$error = true;
			$errorMsgs[] = "Username is empty.";
		} else {
			$user = htmlspecialchars($user, ENT_QUOTES, 'UTF-8'); # SANITIZE
		}
		if (empty($email) || !isset($email)) {
			$error = true;
			$errorMsgs[] = "Email is empty.";
		} else {
			$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
		}
		if (empty($pwd) || !isset($pwd)) {
			$error = true;
			$errorMsgs[] = "Password is empty.";
		}
		if (empty($cfrmpwd) || !isset($cfrmpwd)) {
			$error = true;
			$errorMsgs[] = "Confirmation Password is empty.";
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$error = true;
			$errorMsgs[] = "Invalid email format.";
		}
		if ($pwd != $cfrmpwd) {
			$error = true;
			$errorMsgs[] = "Passwords do not match.";
		}
	}
	if (!$error) {
		$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

		/* Send register request to server */
		$request = array();
		$request['type'] = "Register";
		$request['email'] = $email;
		$request['username'] = $user;
		$request['password'] = $pwd;
		$response = $client->send_request($request);

		echo $response;

        if ($stmt) {
            $stmt->bind_param("sss", $user, $email, $passHash);
            if ($stmt->execute()) {
                // Redirect the user to the home page after successful registration
                session_start(); //start a session
                $_SESSION['username'] = $user; //store the username in the session
                
                header("Location: home.html");
                exit;
            } else {
                echo "Registration failed. Please try again later.";
            }
            $stmt->close();
        } else {
            echo "Failed to prepare the SQL statement.";
        }

        $db->close();
    }
	else if ($error) {
		foreach ($errorMsgs as $error) {
			echo $error . '<br>';
			error_log($error, 3, "error.log");
		}
	}
}
?>
