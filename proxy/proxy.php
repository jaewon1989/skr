<?php
$audioResource = $_POST['audioResource'] ?? '';

if ($audioResource) {

    $ch = curl_init($audioResource);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

    $response = curl_exec($ch);

    curl_close($ch);

    header("Content-Type: audio/wav");
    header("Content-Length: " . strlen($response));

    echo $response;
} else {
    header("HTTP/1.1 400 Bad Request");
    echo "audioResource parameter is missing.";
}