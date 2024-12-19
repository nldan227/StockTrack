<?php
    session_start();
    include ('config.php');
    if(isset($_POST['submit'])){

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo 'CSRF token không hợp lệ.';
            exit();
        }
        
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
        $role_id =  filter_input(INPUT_POST, 'role_id', FILTER_SANITIZE_STRING);
        $phone =  filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
         // Kiểm tra các trường bắt buộc
        if (empty($username) || empty($password) || empty($email) || empty($role_id) || empty($full_name)) {
            $_SESSION['message'] = "Vui lòng nhập các trường bắt buộc như username, password, email, role!";
            header("Location: ../model/managerUser.php");
            exit();
        }
        $sql = "INSERT INTO user (username, full_name, role_id, phone_number, address, password, email) VALUES (:username, :full_name, :role_id, :phone, :address, :hashedPassword, :email);";
        $stmt = $pdo->prepare($sql);
        if ($stmt -> execute(array(
            ':username' => $username,
            ':full_name' => $full_name,
            ':role_id' => $role_id,
            ':phone' => $phone,
            ':address' => $address,
            ':hashedPassword' => $hashedPassword,
            ':email' => $email
            ))){
                $_SESSION['message'] = "Người dùng đã được thêm thành công!";
                unset($_SESSION['csrf_token']);
                header("Location: ../model/managerUser.php");
                exit();
        } else {
            $_SESSION['message'] = "Có lỗi xảy ra khi thêm người dùng.";
            unset($_SESSION['csrf_token']);
            header("Location: ../model/managerUser.php");
            exit();
        }

    }
?>