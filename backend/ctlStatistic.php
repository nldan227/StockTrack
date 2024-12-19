<?php
include("../backend/config.php"); // Kết nối cơ sở dữ liệu

$startDate = $_GET['start-date'] ?? '';
$endDate = $_GET['end-date'] ?? '';

if ($startDate && $endDate) {
 // So luong don hang theo nha cung cap theo thoi gian
    $stmt = $pdo->prepare("SELECT supplier_id, supplier.name, COUNT(inventory.id) AS count_inventory
                                  FROM inventory 
                                  INNER JOIN supplier 
                                  ON inventory.supplier_id = supplier.id 
                                  WHERE entry_date BETWEEN :start_date AND :end_date 
                                  GROUP BY supplier_id, name;");
    $stmt->execute(['start_date' => $startDate, 'end_date' => $endDate]);
    $supplierData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // So luong don nhap theo trang thai
    $stmt = $pdo->prepare("SELECT status, COUNT(id) AS count_status
                                  FROM `inventory` 
                                  WHERE entry_date BETWEEN :start_date AND :end_date
                                  GROUP BY status;");
     $stmt->execute(['start_date' => $startDate, 'end_date' => $endDate]);
     $statusData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // So luong don hang theo thoi gian
    $stmt = $pdo->prepare("SELECT entry_date, COUNT(*) AS count_inventory FROM inventory 
                                  WHERE entry_date BETWEEN :start_date AND :end_date
                                  GROUP BY entry_date;");

    $stmt->execute(['start_date' => $startDate, 'end_date' => $endDate]);
    $quantityData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT entry_date, SUM(total) AS total FROM inventory 
                                  WHERE entry_date BETWEEN :start_date AND :end_date
                                  GROUP BY entry_date;");

    $stmt->execute(['start_date' => $startDate, 'end_date' => $endDate]);
    $totalData = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $result = [
        'supplier_data' => $supplierData,
        'status_data' => $statusData,
        'quantity_data' => $quantityData,
        'total_data' => $totalData
    ];

    // Trả về dữ liệu dưới dạng JSON
    echo json_encode($result);
} else {
    echo json_encode([]);
}
?>