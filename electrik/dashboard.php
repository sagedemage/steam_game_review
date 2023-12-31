<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Dashboard</title>
   <link rel="stylesheet" href="style.css"> <!-- Link to your CSS stylesheet for additional styling -->
</head>
<body>

<!-- Include the common header and navbar -->
<?php include('dashnav.php'); ?>

<div class="container mt-5">
   <h1 class="text-center display-4 fw-bold text-primary">Game Recommendation Portal</h1>
</div>

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

if (is_string($response)) {
	$jsonResponse = json_decode($response);
	if (json_last_error() === JSON_ERROR_NONE && isset($jsonResponse->response->apps)) {
		foreach ($jsonResponse->response->apps as $index => $app) {
			$appId = $app->appid;
			$gameName = $app->name;
			$imageUrl = "https://steamcdn-a.akamaihd.net/steam/apps/{$appId}/header.jpg";
			$activeClass = ($index === 0) ? 'active' : '';
			echo '<div class="carousel-item ' . $activeClass . '">';
			echo '<a href="review.php?appid=' . $appId . '&name=' . urlencode($gameName) . '">';
			echo '<img src="' . $imageUrl . '" class="d-block w-100" alt="' . $gameName . '" style="height: 25rem;">';
			echo '</a>';
			echo '</div>';
		}
	} else {
		echo 'Response is not a valid JSON string.';
	}
} else {
	echo '<script>';
	echo 'console.error("Response is not a string: ", ' . json_encode($response) . ');';
	echo '</script>';
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['gameSearch'])) {
	$gameSearch = strtolower($_GET['gameSearch']);
	$foundGame = false;

	foreach ($jsonResponse->response->apps as $app) {
		if (strtolower($app->name) == $gameSearch) {
			header("Location: review.php?appid={$app->appid}&name=" . urlencode($app->name));
			$foundGame = true;
			break;
		}
	}

	if (!$foundGame) {
		echo '<p>Game not found. Please try another search.</p>';
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

<!-- Include the common footer -->
<?php include('footer.php'); ?>

<!-- Your existing script for user session verification -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="verify_user_session.js"></script>

</body>
</html>


