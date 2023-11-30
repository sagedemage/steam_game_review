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

<?php include('navbar.php'); ?>

<!-- Bootstrap Carousel -->
<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner" id="carouselInner">
    <?php

    $json_data = file_get_contents('php://input');

    error_log(print_r($json_data, true));

    echo json_encode($json_data);  

    if (!empty($json_data)) {
        $jsonData = json_decode($json_data, true);

        if (isset($jsonData['response']['apps']) && is_array($jsonData['response']['apps'])) {
            $apps = $jsonData['response']['apps'];

            foreach ($apps as $index => $app) {
                $appId = $app['appid'];
                $imageUrl = "https://steamcdn-a.akamaihd.net/steam/apps/{$appId}/header.jpg";

                $activeClass = ($index === 0) ? 'active' : '';

                echo '<div class="carousel-item ' . $activeClass . '">';
                echo '<img src="' . $imageUrl . '" class="d-block w-100" alt="Card ' . $appId . '" style="height: 25rem;">';
                echo '</div>';
            }

            echo '<script>';
            echo 'console.log("Data received from the server:", ' . json_encode($jsonData) . ');';
            echo '</script>';
        } else {
            echo '<script>';
            echo 'console.error("Invalid JSON data structure from the server");';
            echo '</script>';
        }
    } else {
        echo '<script>';
        echo 'console.error("No JSON data received from the server");';
        echo '</script>';
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="upcomingGames.js"></script>

</body>
</html>
