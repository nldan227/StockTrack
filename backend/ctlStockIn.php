
<?php
    session_start();
    include ('config.php');

    $sql = 'SELECT id, name FROM supplier';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute();
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = 'SELECT id, name FROM products';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);



    if (isset($_POST['saveStock'])){
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo 'CSRF token không hợp lệ.';
            exit();
        }
        $note = $_POST['note'];
        $entry_date = $_POST['import-date'];
        $user_id = $_SESSION['id'];

        $supplier_full = $_POST['supplier'];
        $supplier_parts = explode(' - ', $supplier_full);
        $supplier_id = trim($supplier_parts[0]);  // NCC001

        $total_full = $_POST['total-amount'];
        $total_parts = explode('VNĐ', $total_full);
        $total_1 = trim($total_parts[0]);
        $total_price = preg_replace('/[^0-9]/', '', $total_1);

        $supplier_full = $_POST['supplier'];
        $supplier_parts = explode(' - ', $supplier_full);
        $supplier_id = trim($supplier_parts[0]);
        $supplier_name = trim($supplier_parts[1]);

        $productData = isset($_POST['product-data']) ? json_decode($_POST['product-data'], true) : [];
        if (empty($user_id) || empty($entry_date) || empty($supplier_id)) {
            echo '<script type="text/javascript">
               alert("Các trường bắt buộc không được để trống!");
               window.history.back(); // Có thể loại bỏ nếu không cần thiết
            </script>';
            exit;
        }

        $sql = "SELECT * FROM supplier WHERE id = :supplier_id";
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute([':supplier_id'=> $supplier_id]);
        if ($stmt -> rowCount() == 0){
            $sql = "INSERT INTO supplier (id, name) VALUES (:supplier_id, :name)";
            $stmt = $pdo -> prepare($sql);
            $stmt -> execute([':supplier_id'=> $supplier_id, ':name' => $supplier_name]);
        }

        $sql = "SELECT * FROM products WHERE id = :product_id";
        $stmt = $pdo -> prepare($sql);
        $product_ids_to_update = []; 
        foreach ($productData as $product) {
            $product_id = $product['code'];
            $product_name = $product['name'];
            $quantity = $product['quantity'];
            $price1 = $product['price'];
            $cleaned_price = str_replace('.', '', $price1); 
            $price = floatval($cleaned_price);

            $stmt -> execute([':product_id'=> $product_id]); 
            if ($stmt -> rowCount() == 0){
                $sql = "INSERT INTO products (id, name, price, quantity, supplier_id) VALUES (:product_id, :name, :price, :quantity, :supplier_id)";
                $stmt = $pdo -> prepare($sql);
                $stmt -> execute([':product_id'=> $product_id, ':name' =>  $product_name, ':price' => $price, ':quantity' => $quantity,':supplier_id' => $supplier_id]);
            }else {
                // Nếu sản phẩm đã tồn tại, thêm vào mảng để cập nhật sau này
                $product_ids_to_update[] = $product_id;
            }
        }
       
        try {
            // Lưu vào bảng inventory
            $sql = "INSERT INTO inventory (entry_date, user_id, supplier_id, total, note) VALUES (:entry_date, :user_id, :supplier_id, :total, :note)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([':entry_date' => $entry_date, ':user_id' => $user_id, ':supplier_id' => $supplier_id, ':total' => $total_price, ':note' => $note])) {
                $inventory_id = $pdo->lastInsertId();

                echo "<script>
                console.log('Inventory ID: " . addslashes($inventory_id) . "');
                </script>";
                // Xử lý dữ liệu sản phẩm
              
                $sql = "INSERT INTO inventory_item (inventory_id, product_id, quantity) VALUES (:inventory_id, :product_id, :quantity)";
                $stmt = $pdo->prepare(query: $sql);
                foreach ($productData as $product) {
                    $stmt->execute([
                        ':inventory_id' => $inventory_id,
                        ':product_id' => $product['code'],
                        ':quantity' => $product['quantity']
                    ]);
                }
                if (!empty($product_ids_to_update)) {
                    $sql = "UPDATE products 
                            SET quantity = quantity + :quantity
                            WHERE id = :product_id";
                    $stmt = $pdo->prepare($sql);
                    foreach ($productData as $product) {
                        if (in_array($product['code'], $product_ids_to_update)) {
                            $stmt->execute([
                                ':quantity' => $product['quantity'],
                                ':product_id' => $product['code']
                            ]);
                        }
                    }
                }
                $_SESSION['message'] = "Phiếu đã được lưu thành công!";
                unset($_SESSION['csrf_token']);
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            } else {
                throw new Exception('Có lỗi xảy ra khi thêm đơn hàng.');
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    if (isset($_POST['updateStock'])){
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo 'CSRF token không hợp lệ.';
            exit();
        }
        if(isset($_GET['id'])){
            $inventory_id = $_GET['id'];

            $stmt = $pdo->prepare("SELECT inventory.id AS inventory_id, entry_date, products.supplier_id, total, price, supplier.name AS supplier_name, products.name AS products_name, products.id AS products_id, inventory_item.quantity 
                                  FROM `inventory` 
                                  INNER JOIN inventory_item 
                                  ON inventory.id = inventory_item.inventory_id 
                                  INNER JOIN products 
                                  ON inventory_item.product_id = products.id 
                                  INNER JOIN supplier 
                                  ON products.supplier_id = supplier.id 
                                  WHERE inventory.id = :stock_id");
            $stmt->execute([':stock_id' => $inventory_id]);
            $listStock = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $total_price = number_format($listStock[0]['total'], 0, ',', '.') . ' VNĐ';

            $stmt = $pdo -> prepare("UPDATE products SET quantity = quantity - :quantity WHERE id = :products_id");
            foreach($listStock as $stock){    
                $stmt->execute([':quantity' => $stock['quantity'], ':products_id' => $stock['products_id']]);
            }

            $stmt = $pdo -> prepare("UPDATE inventory_item SET quantity = quantity - :quantity WHERE product_id = :products_id AND inventory_id = :inventory_id");
            foreach($listStock as $stock){
                $stmt->execute([':quantity' => $stock['quantity'], ':products_id' => $stock['products_id'], ':inventory_id' => $stock['inventory_id']]);
            }
            $total_full = $_POST['total-amount'];
            $total_parts = explode('VNĐ', $total_full);
            $total_1 = trim($total_parts[0]);
            $total_price = preg_replace('/[^0-9]/', '', $total_1);
            
            $sql = "UPDATE inventory SET total = :total, entry_date = :entry_date WHERE id = :inventory_id";
            $stmt = $pdo -> prepare($sql);
            $stmt -> execute([':total' => $total_price,':entry_date' => $_POST['import-date'],':inventory_id'=> $inventory_id]);

            $sql = "SELECT * FROM inventory_item WHERE inventory_id = :inventory_id;";
            $stmt = $pdo -> prepare($sql);
            $stmt -> execute([':inventory_id'=>$inventory_id]);
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($row)){
               
                $productData = isset($_POST['product-data']) ? json_decode($_POST['product-data'], true) : [];
                if (isset($_POST['product-data'])) {
                    echo "Product Data: " . $_POST['product-data']; // Kiểm tra giá trị
                } else {
                    echo "Không có product data được gửi!";
                }
            
                foreach ($productData as $product) {
                    $sql = "SELECT * FROM inventory_item WHERE inventory_id = :inventory_id AND product_id = :product_id;";
                    $stmt = $pdo -> prepare($sql);
                    $product_id = $product['code'];
                    $quantity = $product['quantity'];
                    $stmt -> execute([':inventory_id'=>$inventory_id, ':product_id'=>$product_id]);
                    if($stmt -> rowCount() > 0){
                        $sql = "UPDATE inventory_item SET quantity = :quantity WHERE inventory_id = :inventory_id AND product_id = :product_id;";
                        $stmt = $pdo -> prepare($sql);
                        $stmt -> execute([':quantity'=> $quantity, ':product_id' => $product_id, ':inventory_id'=> $inventory_id]);
                        
                        $rowCount = $stmt->rowCount();
                        $stmt = $pdo -> prepare("UPDATE products SET quantity = quantity + :quantity WHERE id = :products_id;");
                        $stmt -> execute([':quantity'=>$quantity, ':products_id'=> $product_id]);
                    }else{
                        $sql = "INSERT INTO inventory_item (inventory_id, product_id, quantity) VALUES (:inventory_id, :product_id, :quantity)";
                        $stmt = $pdo->prepare(query: $sql);
                        $stmt->execute([
                                ':inventory_id' => $inventory_id,
                                ':product_id' => $product['code'],
                                ':quantity' => $product['quantity']
                        ]);

                        $rowCount = $stmt->rowCount();
                        $stmt = $pdo -> prepare("UPDATE products SET quantity = quantity + :quantity WHERE id = :products_id;");
                        $stmt -> execute([':quantity'=>$quantity, ':products_id'=> $product_id]);

                    }
                }
                $sql = "DELETE FROM inventory_item WHERE quantity = 0";
                $stmt = $pdo -> prepare($sql);
                $stmt -> execute();

                $sql = "DELETE FROM inventory WHERE total = 0";
                $stmt = $pdo -> prepare($sql);
                $stmt -> execute();
                $_SESSION['message'] = "Phiếu đã được cập nhật thành công!";
                unset($_SESSION['csrf_token']);
                header('Location: ../model/viewList.php');
                exit();

            }
        }
    };
?>


