<?php
$headers = ["Content-Type: application/json"];
    $ch = curl_init("https://socialhub.gplex.com/chat-webhook");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
    $response = curl_exec($ch);
    curl_close($ch);

    print_r($response);