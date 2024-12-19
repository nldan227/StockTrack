<?php
// Kết nối với cơ sở dữ liệu
include("../backend/config.php");

// Kiểm tra nếu nhận được yêu cầu GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Lấy giá trị username từ tham số GET
    $username = trim($_GET['username'] ?? '');

    // Kiểm tra xem username có rỗng không
    if ($username === '') {
        echo "invalid"; // Trả về invalid nếu username không hợp lệ
        exit;
    }

    try {
        // Thực hiện truy vấn kiểm tra username trong cơ sở dữ liệu
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo "exists"; // Trả về exists nếu username đã tồn tại
        } else {
            echo "available"; // Trả về available nếu username chưa tồn tại
        }
    } catch (PDOException $e) {
        // Xử lý lỗi kết nối hoặc câu lệnh SQL
        echo "error"; // Trả về error trong trường hợp lỗi
    }
} else {
    // Nếu không phải yêu cầu GET, trả về lỗi
    echo "invalid_request";
}
?>