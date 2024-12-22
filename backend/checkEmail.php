<?php
include("../backend/config.php");

if (isset($_GET['email'])) {
    $email = trim($_GET['email']);

    // Kiểm tra email trong cơ sở dữ liệu
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "exists"; // Email đã tồn tại
    } else {
        echo "available"; // Email chưa tồn tại
    }
}
?>