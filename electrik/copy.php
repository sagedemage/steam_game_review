<!DOCTYPE html>

<html lang="en">
<head>
	<title>Page Title</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="style.css">
</head>
<body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!--
<script src="upcomingGames.js"></script>
-->
<?php include('navbar.php'); ?>

<!-- Bootstrap Carousel -->
<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
	<div class="carousel-inner" id="carouselInner">
<?php

ini_set('display_errors', 1);

require_once('../rabbitmq_lib/path.inc');
require_once('../rabbitmq_lib/get_host_info.inc');
require_once('../rabbitmq_lib/rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$request = array();
$request['type'] = "GetAppList";
$response = $client->send_request($request);

// Since $response is already an array, no need to decode it
// Directly access the elements of the array
$returnCode = $response['returnCode'];
$message = $response['message'];

// Display or use $returnCode and $message as needed
echo '<script>';
echo 'console.log("Return Code:", ' . json_encode($returnCode) . ');';
echo 'console.log("Message:", ' . json_encode($message) . ');';
echo '</script>';

/*
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

 Send register request to server
$request = array();
$request['type'] = "GetAppList";
$response = $client->send_request($request);
var_dump($response);
$jsonResponse = json_decode($response);

$json_data = json_decode(file_get_contents('php://input'), true);
*/

echo '<script>';
echo 'console.log("Received JSON data:", ' . json_encode(['apps' => $jsonResponse]) . ');';
echo 'console.log("Received JSON data:", ' . '{'. 'appid:' . $jsonResponse->response->apps[0]->appid . '}' . ');';
echo 'console.log("Received JSON data:", ' . '{'. 'last_modified:' . $jsonResponse->response->apps[0]->last_modified . '}' . ');';
echo 'console.log("Received JSON data:", ' . '{'. 'name:' . $jsonResponse->response->apps[0]->name . '}' . ');';
echo 'console.log("Received JSON data:", ' . '{'. 'price_change_number:' . $jsonResponse->response->apps[0]->price_change_number . '}' . ');';
echo '</script>';

/*
echo '<table>';
  echo '<tr>';
    echo '<th>App ID</th>';
    echo '<th>Last Modified</th>';
    echo '<th>Name</th>';
    echo '<th>Price Change Number</th>';
  echo '</tr>';
  echo '<tr>';
    echo '<td>' . $jsonResponse->response->apps[0]->appid . '</td>';
    echo '<td>' . $jsonResponse->response->apps[0]->last_modified . '</td>';
    echo '<td>' . $jsonResponse->response->apps[0]->name . '</td>';
    echo '<td>' . $jsonResponse->response->apps[0]->price_change_number . '</td>';
  echo '</tr>';
echo '</table>'; 

appid: 10
last_modified: 1666823513
name: "Counter-Strike"
price_change_number: 21319021
 */

//echo $jsonResponse->response->apps[0];

//echo $response;

if ($json_data === null && json_last_error() !== JSON_ERROR_NONE) {
	echo '<script>';
	echo 'console.error("JSON decoding error:", ' . json_encode(['error' => json_last_error_msg()]) . ');';
	echo '</script>';
} else {
	echo '<script>';
	echo 'console.log("JSON decoding successful:", ' . json_encode(['jsonData' => $jsonData]) . ');';
	echo '</script>';

	if (isset($jsonData['jsonData']) && is_array($jsonData['jsonData'])) {
		$apps = $jsonData['jsonData'];

		foreach ($apps as $index => $app) {
			$appId = $app['appid'];
			$imageUrl = "https://steamcdn-a.akamaihd.net/steam/apps/{$appId}/header.jpg";
			$activeClass = ($index === 0) ? 'active' : '';
			echo '<div class="carousel-item ' . $activeClass . '">';
			echo '<img src="' . $imageUrl . '" class="d-block w-100" alt="Card ' . $appId . '" style="height: 25rem;">';
			echo '</div>';
		}
		echo '<script>' . 'console.log("Data received from the server:", ' . json_encode($jsonData) . ');' . '</script>';
	} else {
		echo '<script>' . 'console.error("Invalid JSON data structure from the server");' . '</script>';
	}
}
?>
	</div>
	<button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
		<span class="carousel-control-prev-icon" aria-hidden="true"></span>
		<span class="visually-hidden">Previous</span>
	</button>
	<button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
		<span class="carousel-control-next-icon" aria-hidden="true"></span>
		<span class="visually-hidden">Next</span>
	</button>
</div>

<!-- Cards below the carousel -->
<div class="container mt-5">
	<div class="card-group">
		<div class="card">
			<img src="images/image1.jpg" class="card-img-top" alt="Card 1" style="height: 18rem;">
			<div class="card-body">
				<h5 class="card-title">Card 1</h5>
				<p class="card-text">Some text for Card 1.</p>
			</div>
		</div>
		<div class="card">
			<img src="images/image2.jpg" class="card-img-top" alt="Card 2" style="height: 18rem;">
			<div class="card-body">
				<h5 class="card-title">Card 2</h5>
				<p class="card-text">Some text for Card 2.</p>
			</div>
		</div>
		<div class="card">
			<img src="images/image4.jpg" class="card-img-top" alt="Card 3" style="height: 18rem;">
			<div class="card-body">
				<h5 class="card-title">Card 3</h5>
				<p class="card-text">Some text for Card 3.</p>
			</div>
		</div>
	</div>
</div>

<div class="footer">
   &copy; 2023 Electrik.com. All rights reserved. <a class="terms-link" href="terms.php">Terms of Service</a>
</div>

</body>
</html>