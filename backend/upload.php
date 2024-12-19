<?php
 session_start();
 include("../backend/config.php");
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "../img/";
    $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra xem tệp có phải là hình ảnh không
    $check = getimagesize($_FILES["avatar"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "Tệp không phải là hình ảnh.";
        $uploadOk = 0;
    }

    // Kiểm tra kích thước tệp
    if ($_FILES["avatar"]["size"] > 500000) {
        echo "Tệp quá lớn.";
        $uploadOk = 0;
    }

    // Chỉ cho phép một số định dạng tệp nhất định
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Chỉ cho phép các định dạng JPG, JPEG, PNG & GIF.";
        $uploadOk = 0;
    }

    // Kiểm tra xem uploadOk có phải là 0 không
    if ($uploadOk == 0) {
        echo "Xin lỗi, tệp của bạn không được tải lên.";
    // Nếu mọi thứ đều ổn, tải tệp lên
    } else {
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
            $stmt = $pdo->prepare("UPDATE user SET ava = :ava WHERE id = :id");
            $stmt->execute([
                'ava' => $target_file,
                'id' => $_SESSION['id']
            ]);
        echo "<script>
                    window.parent.postMessage('uploadSuccess', '*');
            </script>";

        exit();
        } else {
            echo "Có lỗi xảy ra khi tải tệp.";
        }
    }
}

?>