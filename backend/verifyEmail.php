<?php
include("../backend/config.php");

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Sử dụng API kiểm tra email tồn tại (Hunter.io)
    function isEmailValid($email) {
        $api_key = "249665095b404f73aabd4c925438be86";
        $url = "https://emailvalidation.abstractapi.com/v1/?api_key=$api_key&email=$email";

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data['deliverability'] === "DELIVERABLE") {
            return true;
        }
        return false;
    }

    if (isEmailValid($email)) {
        echo "valid";
    } else {
        echo "invalid";
    }
}
?>