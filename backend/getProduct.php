<?php
include('config.php'); // Bao gồm tệp cấu hình

// Kiểm tra xem yêu cầu là lấy danh sách sản phẩm hay lấy giá sản phẩm
$supplier_id = $_POST['supplier_id'] ?? '';
$product_id = $_POST['product_id'] ?? '';

if ($supplier_id) {
    // Truy vấn cơ sở dữ liệu để lấy sản phẩm theo ID nhà cung cấp
    $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE supplier_id = :supplier_id");
    $stmt->execute(['supplier_id' => $supplier_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trả về danh sách sản phẩm dưới dạng JSON
    echo json_encode($products);
} elseif ($product_id) {
    // Truy vấn cơ sở dữ liệu để lấy giá của sản phẩm dựa trên mã sản phẩm
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = :product_id");
    $stmt->execute(['product_id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Trả về giá sản phẩm dưới dạng JSON
    echo json_encode($product);
} else {
    echo json_encode([]); // Nếu không có tham số nào được gửi
}
?>