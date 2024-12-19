<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">

    <title>Document</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-pap+KfaePGSOyAlckCwmQHkK3t7s6+b9J+4TT2gN0vYkIta0NNX6ZHgMjgWgFktciW7jqW0K3tqCqmUGPqxHig=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <style>
    #upload-avatar-form {
        margin-top: 10px; 
        height: 90%;
        width: 90%;
        margin-left: 3%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 5px; 
        padding: 10px;
        background-color: #f9f9f9; 
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Đổ bóng nhẹ cho form */
    }

    #upload-avatar-form input[type="file"] {
        display: block; /* Hiển thị input file theo chiều dọc */
        margin-bottom: 10px; /* Khoảng cách dưới input file */
        padding: 5px; /* Khoảng cách bên trong input file */
    }

    #upload-avatar-form input[type="submit"], #upload-avatar-form input[type="button"] {
        text-align: center;
        height: 25px;
        width: 100px;
        background-color: #c17400; /* Màu nền nút gửi */
        color: white; /* Màu chữ nút gửi */
        border: none; /* Không viền */
        border-radius: 10px; /* Bo góc nút gửi */
        margin-right: 10px;
        cursor: pointer; /* Hiển thị con trỏ chuột kiểu tay khi di chuột qua nút gửi */
        font-size: 16px; /* Kích thước chữ */
        transition: background-color 0.3s ease; /* Hiệu ứng chuyển màu nền */
    }

    #upload-avatar-form input[type="submit"]:hover {
        opacity: 0.7; /* Màu nền khi di chuột qua nút gửi */
    }

    .frmUp{
        height: 100%;
        display: flex;
        flex-direction: center;
    }

    .grp-Btn{
        display: flex;
    }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById("close-iframe").addEventListener("click", function() {
            // Send a message to the parent window
            window.parent.postMessage('closeIframe', '*');
            });
        });
    </script>
</head>
<body>
    <div class="frmUp">
        <form action="../backend/upload.php" method="POST" enctype="multipart/form-data" id="upload-avatar-form" >
            <input type="file" name="avatar" id="avatar">
            <div class="grp-Btn">
                <input type="submit" id="sucess" value="Tải lên">
                <input type="button" id="close-iframe" value="Hủy bỏ">
            </div>
        </form>
    </div>

    
</body>
</html>

