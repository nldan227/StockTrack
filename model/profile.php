<?php
  session_start();
  include("../backend/config.php");
  if (isset($_SESSION['message'])) {
    $message = addslashes($_SESSION['message']);
    echo '<script type="text/javascript">
        window.onload = function(){
           alert("' . $message . '");
           window.history.back(); // Có thể loại bỏ nếu không cần thiết
        }
        </script>';
    unset($_SESSION['message']); // Xóa thông báo sau khi hiển thị
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-pap+KfaePGSOyAlckCwmQHkK3t7s6+b9J+4TT2gN0vYkIta0NNX6ZHgMjgWgFktciW7jqW0K3tqCqmUGPqxHig=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <style>
        .main-profile{
            height: 80%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-content{
            display: flex;
            border-radius: 20px;
            box-shadow: 0 0 128px 0 rgba(0,0,0,0.1), 0 32px 64px -48px rgba(0,0,0,0.5);
        }

        .body{
            background-color: #E0E0E0;
        }
        .avatar{
            width: 40%;
            height: 30%;
            padding: 60px;
        }
        .avatar img{
            border-radius: 100px;
            width: 100%;
            height: auto;
        }
        
        .left-profile{
            width: 40%;
            height: 100%;
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;

        }

        .right-profile{
            height: 100%;
            width: 60%;
            display: flex;
            justify-content: center;
            margin-top: 50px;
        }

        .left-profile .avatar p{
            margin-top: 10px;
            text-align: center;
            font-weight: 600;

        }

        .avatar {
            display: flex;
            flex-direction: column;
        }
        
        .container header{
            text-align: center;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .field {
            display: flex;
            flex-direction: column;
        }

        .input input {
            margin-top: 2px;
            margin-bottom: 10px;
            border: none;
            border-radius: 10px;
            background-color: #fae4bb; 
            padding: 5px;
            height: 20px;
            font-size: 16px;
        }

        .container{
            width: 50%;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .button{
            display: flex;
            justify-content: space-around;
            padding: 20px;
        }

        .button .btn{
            padding: 10px 20px;
            border: 0;
            border-radius: 30px;
            color: #fff;
            background-color: #c17400;
            font-weight: 800;
        }

        .button .btn:hover{
            opacity: 0.7;
        }

        .label i {
            margin-right: 5px;
        }

        .back{
            padding: 10px;
        }

        .back i{
            color:#F4A460;
        }

        #upload-avatar-form {
        display: none; 
        margin-top: 10px; 
        width: 130%;
        border: 1px solid #ddd;
        border-radius: 5px; 
        padding: 10px;
        background-color: #f9f9f9; 
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ cho form */
    }

    #upload-avatar-form input[type="file"] {
        display: block;
        margin-bottom: 10px; 
        padding: 5px; 
    }

    #upload-avatar-form input[type="submit"] {
        height: 30px;
        width: 150px;
        background-color: #c17400; 
        color: white; 
        border: none; 
        border-radius: 5px; 
        padding: 10px; 
        cursor: pointer; 
        font-size: 16px; 
        transition: background-color 0.3s ease; /* Hiệu ứng chuyển màu nền */
    }

    #upload-avatar-form input[type="submit"]:hover {
        opacity: 0.7; /* Màu nền khi di chuột qua nút gửi */
    }

     
    #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Màu đen với độ mờ 50% */
            display: none; /* Ẩn overlay lúc đầu */
            z-index: 1000;
        }

        #iframe-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            height: 70%;
            border: 1px solid #ccc;
            background-color: white;
            display: none;
            z-index: 1100; /* Đặt z-index cao hơn overlay */
        }

        #close-iframe {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            border: none;
            padding: 5px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }

        #overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6); /* Màu đen với độ mờ 70% */
        z-index: 10;
        display: none;
        }
    </style>
    <script>

        
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('change-avatar').addEventListener('click', function() {
                document.getElementById('upload-avatar-form').style.display = 'block';
            });

            document.getElementById("change-avatar").addEventListener("click", function (event) {
            event.preventDefault(); // Ngăn chặn hành vi mặc định của liên kết
            var iframe = document.getElementById("iframe-container");
            var randomParam = new Date().getTime(); // Tạo tham số ngẫu nhiên dựa trên thời gian hiện tại
            document.getElementById('overlay').style.display = 'block';
            iframe.src = "uploadAva.php?t=" + randomParam; // Thêm tham số ngẫu nhiên vào URL
            iframe.style.display = "block"; // Hiển thị iframe

    });



    window.addEventListener("message", function(event) {
        if (event.data === 'closeIframe') {
            // Hide the iframe and overlay
            document.getElementById('iframe-container').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        };

        if (event.data === 'uploadSuccess'){
            // Kiểm tra nếu localStorage có giá trị để ẩn overlay
          
                document.getElementById('iframe-container').style.display = 'none';
                document.getElementById('overlay').style.display = 'none';
                location.reload();

        }
    });
    });

    
    </script>   
    <?php 
         $username = $name = $address = $phone_number = $name_role = $avatar = '';
        if( isset($_SESSION['id'])){
            $stmt = $pdo->prepare("SELECT ava, full_name, username, address, phone_number, role.name FROM user INNER JOIN role ON user.role_id = role.id WHERE user.id = :id");
            $stmt->execute(['id' => $_SESSION['id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row){
                $avatar = $row['ava']; // Đường dẫn avatar
                $name = $row['full_name'];
                $username = $row['username'];
                $address = $row['address'];
                $name_role = $row['name'];
                $phone_number = $row['phone_number'];
            }
        }
    ?>
</head>
<body>
    
    <?php include '../layout/header.html' ?>
    <div class="main-profile">
    <div class="profile-content">
        <div class="back">
            <a href="index.php" class="">
            <i class="fa-solid fa-arrow-left"></i> 
            </a>
        </div>
        <div class="left-profile">
            <div class="avatar">
                <img src="<?php echo $avatar ? $avatar : '../img/ava.jpg'; ?>" alt="Avatar" class="">

                <p class="p-ava">
                    <?php
                    echo 'Hello, '. $name;
                    ?>
                </p>

                <p class="p-ava" id="change-avatar" style="color: #c17400; font-weight:600;cursor: pointer;">
                    Thay đổi ảnh đại diện
                </p>
            </div>
        </div>

        <iframe id="iframe-container" src=""></iframe>
        <div id="overlay"></div>

        <div class="right-profile">
            <div class="container">
                <header style="font-weight:600; font-size:20px;">THÔNG TIN CÁ NHÂN</header>
                <form id="lopForm" action="../backend/ctlEditProfile.php" method="post">
                    <div class="field input">
                        <div class="label">
                            <i class="fa-solid fa-user"></i>
                            <label for="username" class="">Username</label>
                        </div>
                        
                        <input type="text" name="username" id="username" value="<?php echo $username ?>" readonly>
                    </div>

                    <div class="field input">
                        <div class="label">
                            <i class="fa-solid fa-file-signature"></i>
                            <label for="hoTen" class="">Họ tên</label>
                        </div>
                        <input type="text" name="hoTen" id="hoTen" value="<?php echo $name?>">
                    </div>

                    <div class="field input">
                        <div class="label">
                            <i class="fa-solid fa-hat-cowboy"></i>
                            <label for="chucVu" class="">Chức vụ</label>
                        </div>
                        <input type="text" name="" id="chucVu" value="<?php echo $name_role?>" readonly>
                    </div>

                    <div class="field input">
                        <div class="label">
                            <i class="fa-solid fa-phone-volume"></i>
                            <label for="sdt" class="">SĐT</label>
                        </div>
                        <input type="text" name="sdt" id="sdt" value="<?php echo $phone_number?>">
                    </div>

                    <div class="field input">
                        <div class="label">
                            <i class="fa-solid fa-address-book"></i>
                            <label for="diaChi" class="">Địa chỉ</label>
                        </div>
                        <input type="text" name="diaChi" id="diaChi" value="<?php echo $address?>">
                    </div>

                    <div class="button">
                        <input type="submit" class="btn" name="submit" value="Cập nhật">
                        <input type="button" class="btn" onclick="window.location.href='viewList.php';" value="Hủy bỏ">
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
  

</body>
</html>