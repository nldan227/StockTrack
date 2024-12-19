<?php
session_set_cookie_params([
    'httponly' => true, // Ngăn JavaScript truy cập cookie
    'samesite' => 'Strict' // Ngăn việc chia sẻ session giữa các site khác nhau
]);
session_start();
include("config.php");
$error_message = '';

if(isset($_POST['submit'])){
    // Lấy và làm sạch dữ liệu từ người dùng nhập
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Truy vấn để lấy chuỗi băm của mật khẩu từ cơ sở dữ liệu
    $sql = "SELECT * FROM user WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':username' => $username
    ));

    // Kiểm tra xem có người dùng không
    if($stmt->rowCount() > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashedPasswordFromDB = $row['password']; // Mật khẩu băm từ CSDL

        // Kiểm tra mật khẩu bằng password_verify
        if(password_verify($password, $hashedPasswordFromDB)){
            // Mật khẩu đúng, tạo session
            $_SESSION['id'] = $row['id'];
            header("Location: ../model/viewList.php");
            exit();
        } else {
            // Mật khẩu không đúng
            $error_message = 'Tên đăng nhập hoặc mật khẩu không đúng';
            header("Location: ../model/login.php?error=" . urlencode($error_message));
            exit();
        }
    } else {
        // Không tìm thấy người dùng
        $error_message = 'Tên đăng nhập hoặc mật khẩu không đúng';
        header("Location: ../model/login.php?error=" . urlencode($error_message));
        exit();
    }
}
?>