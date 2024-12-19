<?php
session_start();
include('config.php');
$id = $_SESSION['id'];
echo "Giá trị của ID: " . $id;
if(isset($_POST['submit'])){
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $hoTen =  filter_input(INPUT_POST, 'hoTen', FILTER_SANITIZE_STRING);
    $sdt =  filter_input(INPUT_POST, 'sdt', FILTER_SANITIZE_STRING);
    $diaChi =  filter_input(INPUT_POST, 'diaChi', FILTER_SANITIZE_STRING);

    $sql = "UPDATE user SET username = :username, full_name = :hoTen, address = :diaChi, phone_number = :sdt WHERE id = :id";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute([
        'username' => $username,
        'hoTen' => $hoTen,
        'sdt' => $sdt,
        'diaChi' => $diaChi,
        'id' => $id,
    ]);

    if($stmt){
        $_SESSION['message'] = "Cập nhật thành công!";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }else {
        echo("Không thành công");
    }


}else{
    echo("Không thành công");
}
?>