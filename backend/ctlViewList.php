<?php
     if(isset($_SESSION['id'])){
        $stmt = $pdo->prepare('SELECT user.full_name, role.name, user.ava FROM user INNER JOIN role ON user.role_id = role.id WHERE user.id = :id');
        $stmt->execute(['id' => $_SESSION['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            $role = $row['name'];
        }
    }
    
    if ($role == 'manager') {
        $sql = "SELECT inventory.id, entry_date, supplier.name, status 
                FROM inventory 
                INNER JOIN supplier
                ON inventory.supplier_id = supplier.id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } else {
        $sql = "SELECT inventory.id, entry_date, supplier.name, status 
                FROM inventory 
                INNER JOIN supplier
                ON inventory.supplier_id = supplier.id
                WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $_SESSION['id']]);
    }

    // Lấy danh sách kết quả
    $lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>