<?php
include('config.php'); // Bao gồm tệp cấu hình

// Kiểm tra xem yêu cầu là lấy danh sách sản phẩm hay lấy giá sản phẩm
$approved_id = $_POST['approvedStock_id'] ?? '';
$disapproved_id = $_POST['disapprovedStock_id'] ?? '';

try {
    if ($approved_id) {
        // Truy vấn cơ sở dữ liệu để cập nhật trạng thái thành "Approved"
        $stmt = $pdo->prepare("UPDATE inventory SET status='Approved' WHERE id = :approved_id");
        $stmt->execute(['approved_id' => $approved_id]);
        echo json_encode(value: ['message' => 'Duyệt đơn thành công!']);
    } elseif ($disapproved_id) {
        // Truy vấn cơ sở dữ liệu để cập nhật trạng thái thành "Disapproved"
        $stmt = $pdo->prepare("UPDATE inventory SET status='Disapproved' WHERE id = :disapproved_id");
        $stmt->execute(['disapproved_id' => $disapproved_id]);
        echo json_encode(['message' => 'Từ chối thành công!']);
    } else {
        echo json_encode(['message' => 'Không có ID được gửi.']);
    }
} catch (PDOException $e) {
    http_response_code(500); // Mã lỗi 500 (Internal Server Error)
    echo json_encode(['error' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
}
?>