<?php 
    include("../backend/config.php");
    session_start(); // Gọi session_start() ở đầu tệp
    if(isset($_SESSION['id'])){
        $stmt = $pdo->prepare('SELECT user.full_name, role.name, user.ava FROM user INNER JOIN role ON user.role_id = role.id WHERE user.id = :id');
        $stmt->execute(['id' => $_SESSION['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            $name = $row['full_name'];
            $role = $row['name'];
            $avatar = $row['ava']; // Đường dẫn avatar
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>TrackStock</title>
    <!-- Font Awesome CDN từ cdnjs -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-pap+KfaePGSOyAlckCwmQHkK3t7s6+b9J+4TT2gN0vYkIta0NNX6ZHgMjgWgFktciW7jqW0K3tqCqmUGPqxHig=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <style>
        .formNhapKho{
            width: 85%;
        }

        .overall{
            background-color: #c17400;
            width: 100%;
            margin: 1%;
            height: 5%;
            display: flex;
            align-items: center;
            padding: 2px;
        }

        .titleOverall p{
            margin-left:20px;
        }

        .list{
            display: flex;
            justify-content: flex-end;
            padding: 0.5%;
            margin-bottom: 1%;
            max-height: 20%; /* Giới hạn chiều cao của bảng */
            overflow-y: auto; /* Kích hoạt cuộn dọc nếu vượt quá chiều cao */
            position: relative;


        }
        table {
            width: 90%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            position: sticky; /* Làm cho tiêu đề cố định */
            top: 0; /* Vị trí cố định của tiêu đề ở đầu */
            z-index: 2;
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
</head>
<body>
    <?php include '../layout/header.html' ?>
    <?php include '../layout/dashboard.php' ?>
        <div class="formNhapKho">
            <div class="overall">
                <i class="fa-solid fa-house-chimney" style="margin-left: 5px; color: #fff"></i>
                <p class="" style="margin-left: 5px; color: #fff">Tổng quan</p>
            </div>
            <?php if ($role == 'manager'): ?>
                <div class="titleOverall" id="actOrd">
                    <p style="font-weight:600">Tình trạng đơn nhập kho</p>
                    <hr>
                </div>

                <div class="list" id="listActOrd">
                    <table>
                    
                    </table>
                 </div>
            <?php endif ?>

            <?php if ($role == 'user'): ?>
                <div class="titleOverall" id="actOrd">
                    <p style="font-weight:600">Tình trạng đơn nhập kho</p>
                    <hr>
                </div>

                <div class="list" id="listActOrd">
                    <table>
                    
                    </table>
                 </div>
                
            <?php endif ?>
            
           
        </div>
       
    </div>

    <iframe id="iframe-container" src=""></iframe>
    <div id="overlay"></div>

    <script>
    // Bắt sự kiện nhấn vào menu Nhập kho
    document.getElementById("nhap-kho-link").addEventListener("click", function (event) {
        event.preventDefault(); // Ngăn chặn hành vi mặc định của liên kết
        var iframe = document.getElementById("iframe-container");
        var randomParam = new Date().getTime(); // Tạo tham số ngẫu nhiên dựa trên thời gian hiện tại
        document.getElementById('overlay').style.display = 'block';
        iframe.src = "stockIn.php?t=" + randomParam; // Thêm tham số ngẫu nhiên vào URL
        iframe.style.display = "block"; // Hiển thị iframe
    });

    window.addEventListener("message", function(event) {
        if (event.data === 'closeIframe') {
            // Hide the iframe and overlay
            document.getElementById('iframe-container').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
    });


    </script>

</body>
</html>