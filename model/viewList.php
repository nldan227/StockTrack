<?php 
    session_start(); // Gọi session_start() ở đầu tệp
    include("../backend/config.php");

    if(isset($_SESSION['id'])){
        $stmt = $pdo->prepare('SELECT user.full_name, role.name, user.ava FROM user INNER JOIN role ON user.role_id = role.id WHERE user.id = :id');
        $stmt->execute(['id' => $_SESSION['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            $role = $row['name'];
        }

    }

    if (isset($_GET['delete_id'])) {
        $delete_id = $_GET['delete_id'];

        // Chuẩn bị câu lệnh SQL
        $delete_query = "DELETE FROM inventory_item WHERE inventory_id = :id";
        $stmt = $pdo->prepare($delete_query);
        $stmt->execute(['id' => $delete_id]);

        $delete_query = "DELETE FROM inventory WHERE id = :id";
        $stmt = $pdo->prepare($delete_query);
        $stmt->execute(['id' => $delete_id]);
      
            header("Location: viewList.php");
            exit();
        
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackStock</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-pap+KfaePGSOyAlckCwmQHkK3t7s6+b9J+4TT2gN0vYkIta0NNX6ZHgMjgWgFktciW7jqW0K3tqCqmUGPqxHig=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <style class="">
        .form-list{
            width: 90%;
        }
        .form-list form{
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .form-list form .form-group{
            margin-left: 50px;
        }
        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            width: 4%;
            height: 2%;
            background-color: #c17400;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        #results {
            margin-top: 20px;
            margin-left: 20px;

        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
            text-align: center;
        }

        td a {
            color: #c17400;
            font-weight: bold;
        }
        th {
            background-color: #c17400;
            text-align: center;
            border-color: #ad6000;
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

        tr:nth-child(even) {
            background-color: #e8ccb7; /* Màu #ffdfba cho các dòng chẵn */
        }

        tr:nth-child(odd) {
            background-color: white; /* Màu trắng cho các dòng lẻ */
        }
    </style>
</head>
<body>
    <?php include '../layout/header.html' ?>
    <?php include '../layout/dashboard.php' ?>
        <div class="form-list">

            <?php include("../backend/ctlViewList.php"); ?>

            <div id="results">
                <p style="font-weight:600">Danh sách đơn nhập kho</p>
                <hr style="width: 95%">
                <br>
                <table id="product-list" style="margin-left: 40px; width: 90%">
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Nhà cung cấp</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach($lists as $list){
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($list['id']) . '</td>';
                            echo '<td>' . htmlspecialchars($list['name']) . '</td>';
                            echo '<td>' . htmlspecialchars($list['entry_date']) . '</td>';
                            echo '<td>' . htmlspecialchars($list['status']) . '</td>';
                            if ($role == 'manager'){
                                echo '<td><a href="editStock.php?id=' . htmlspecialchars(string: $list['id']) . '">Chi tiết</a> </td>';
                            }else{
                                echo '<td><a href="editStock.php?id=' . htmlspecialchars($list['id']) . '">Chỉnh sửa</a> |  <a href="?delete_id=' . $list['id'] . '" onclick="return confirm(\'Bạn có chắc chắn muốn xóa đơn này không?\');">Xóa</a></td>';
                            }
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

       
    </div>

    <iframe id="iframe-container" src=""></iframe>
    <div id="overlay"></div>

    <script>
    // Bắt sự kiện nhấn vào menu Nhập kho
    document.addEventListener("DOMContentLoaded", function() {
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
});

    </script>

</body>
</html>