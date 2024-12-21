<?php 
    session_start(); // Gọi session_start() ở đầu tệp
    include("../backend/config.php");
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
        .container {
        width: 85%;
        }

        .listUsers{
            display: flex;
            justify-content: center;
        }

        .container p {
            text-align: left;
            color: #000000;
            margin: 10px;
            font-size: 17px;
            font-weight: bold;
        }

        table {
            width: 80%;
            border-collapse: separate;
            border-spacing: 0;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #c17400;
            border: none;
        }


        td {
            background-color: white;
            border: none;
        }

        td a {
            color:#c17400;
            font-weight: bold;
        }
     

        .field{
            display: flex;
            flex-direction: column;
            margin-bottom: 5px;
        }

        .field input{
            border: none;
            padding: 5px;
            border-radius: 20px;
            background-color: #ebebeb;
        }
        .field select{
            border: none;
            padding: 5px;
            border-radius: 20px;
            background-color:  #ebebeb;

        }
        .field option{
            border: none;
            border-radius: 20px;
            background-color: #ebebeb;

        }
        .field-btn{
           display: flex;
           justify-content: center;
        }
        .field-btn input{
            margin: 10px;
            width: 90px;
            height: 30px;
            border-radius: 20px;
            background-color: #c17400;
            color: #f4f4f4

        }
        input[type="submit"] {
            border: 2px solid #c17400;
            background-color: white;
            color: #c17400;
            font-weight: bold;
        }

        input[type="button"] {
            border: none;
            background-color: #c17400;
            color: white;
            font-weight: bold;
        }
        .input{
            display: flex;
            justify-content: space-around;
        }

    </style>

<?php

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Tạo token CSRF ngẫu nhiên
}
$csrf_token = $_SESSION['csrf_token'];

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Chuẩn bị câu lệnh SQL
    $delete_query = "DELETE FROM user WHERE id = :id";
    $stmt = $pdo->prepare($delete_query);
    if ($stmt->execute(['id' => $delete_id])) {
        header("Location: managerUser.php");
        exit();
    } 
}

if(isset($_SESSION['id'])){
    $roles_stmt = $pdo->prepare("SELECT * FROM role");
    $roles_stmt->execute();
    $roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
       
        const cancelButton = document.querySelector("input[value='Hủy bỏ']");
        const formInputs = document.querySelectorAll("form[name='frm-addUsers'] input, form[name='frm-addUsers'] select");

        cancelButton.addEventListener("click", function () {
            formInputs.forEach((input) => {
                if (input.type === "text" || input.type === "password") {
                    input.value = ""; // Đặt giá trị rỗng cho các trường input
                } else if (input.id === "role_id") {
                    input.value = document.querySelector("#role_id option[selected]").value; // Đặt giá trị mặc định
                }
            });

            // Xóa thông báo lỗi (nếu có)
            const errorSpans = document.querySelectorAll("span[id$='-error']");
            errorSpans.forEach((span) => {
                span.textContent = "";
            });
    });

    // Lấy tất cả các trường cần kiểm tra
    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    const emailInput = document.getElementById("email");
    const fullnameInput = document.getElementById("full_name");

    // Kiểm tra Username
    usernameInput.addEventListener("blur", function () {
        const errorSpan = document.getElementById("username-error");
        const username = usernameInput.value.trim();

        if (username === "") {
            errorSpan.textContent = "Tên tài khoản không được để trống.";
        } else {
            // Kiểm tra nếu username đã tồn tại
            fetch("checkUsername.php?username=" + encodeURIComponent(username))
                .then((response) => response.text())
                .then((data) => {
                    if (data === "exists") {
                        errorSpan.textContent = "Tên tài khoản đã được sử dụng.";
                    } else {
                        errorSpan.textContent = "";
                    }
                });
        }
    });

         // Kiểm tra fullname 
    fullnameInput.addEventListener("blur", function () {
        const errorSpan = document.getElementById("fullname-error");
        const fullname = fullnameInput.value.trim();

        if (fullname=== "") {
            errorSpan.textContent = "Tên không được để trống.";
        }else{
            errorSpan.textContent = "";
        }
    });

    // Kiểm tra Password
    passwordInput.addEventListener("blur", function () {
        const errorSpan = document.getElementById("password-error");
        const password = passwordInput.value.trim();

        if (password === "") {
            errorSpan.textContent = "Password không được để trống.";
        } else {
            // Kiểm tra độ mạnh của mật khẩu
            const hasLetter = /[A-Za-z]/.test(password); // Có ít nhất một chữ cái
            const hasNumber = /[0-9]/.test(password);    // Có ít nhất một số
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password); // Có ít nhất một ký tự đặc biệt

            if (password.length < 8 || !hasLetter || !hasNumber || !hasSpecialChar) {
                errorSpan.textContent = "Mật khẩu chưa đủ mạnh (ít nhất 8 ký tự, bao gồm chữ, số và ký tự đặc biệt).";
            } else {
                errorSpan.textContent = "";
            }
        }
    });

    // Kiểm tra Email
    emailInput.addEventListener("blur", function () {
        const errorSpan = document.getElementById("email-error");
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            errorSpan.textContent = "Email sai định dạng.";
        } else {
            errorSpan.textContent = "";
        }
    });
});
</script>
</head>

<?php include '../layout/header.html' ?>
<?php include '../layout/dashboard.php' ?>
    <div class="container">
        <div class="top">
            <p>Thêm người dùng</p>
            <div class="addUsers">
                <form action="../backend/ctladdUser.php" method="post" name="frm-addUsers">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <div class="input">
                        <div class="right-input">
                            <div class="field input">
                                <label for="username">Tên tài khoản</label>
                                <input type="text" id="username" name="username" autocomplete="off">
                                <span id="username-error" style="color: red; font-size: 12px;"></span>
                            </div>

                            <div class="field input">
                                <label for="password">Mật khẩu</label>
                                <input type="password" id="password" name="password" autocomplete="off">
                                <span id="password-error" style="color: red; font-size: 12px;"></span>
                            </div>

                            <div class="field input">
                                <label for="full_name" >Họ tên</label>
                                <input type="text" id="full_name" name="full_name" autocomplete="off">
                                <span id="fullname-error" style="color: red; font-size: 12px;"></span>
                            </div>

                            <div class="field input">
                                <label for="email" >Email</label>
                                <input type="text" id="email" name="email" autocomplete="off">
                                <span id="email-error" style="color: red; font-size: 12px;"></span>
                            </div>
                        </div>
                    
                        <div class="left-input">
                            <div class="field input">
                                <label for="role_id">Vai trò</label>
                                <select id="role_id" name="role_id" required>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo htmlspecialchars($role['id']); ?>" <?php echo ($role['name'] === 'user') ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($role['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="field input">
                                <label for="phone">Số điện thoại</label>
                                <input type="text" id="phone" name="phone" autocomplete="off">
                            </div>

                            <div class="field input">
                                <label for="address">Địa chỉ</label>
                                <input type="text" id="address" name="address" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="field-btn">
                        <input type="submit" name="submit" class="" value="Thêm">
                        <input type="button" class="" value="Hủy bỏ">
                    </div>
                    <?php
                    
                   if (isset($_SESSION['message'])) {
                    $message = addslashes($_SESSION['message']);
                    echo '<script type="text/javascript">
                        window.onload = function(){
                           alert("' . $message . '");
                           // window.history.back(); // Có thể loại bỏ nếu không cần thiết
                        }
                        </script>';
                    unset($_SESSION['message']); // Xóa thông báo sau khi hiển thị
                }
                    ?>
                </form>
            </div>
        </div>
        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector("form[name='frm-addUsers']");
            const errorSpans = document.querySelectorAll("span[id$='-error']"); // Lấy tất cả các span có id kết thúc bằng '-error'

            form.addEventListener("submit", function (event) {
                let hasError = false;

                // Kiểm tra nếu có thông báo lỗi trong các span
                errorSpans.forEach((span) => {
                    if (span.textContent.trim() !== "") {
                        hasError = true;
                    }
                });

                // Ngăn gửi form nếu có lỗi
                if (hasError) {
                    event.preventDefault();
                    alert("Thông tin không hợp lệ. Vui lòng nhập lại.");
                }
            });
        });
    </script>
        <hr>

        <div class="bottom">
            <p>Danh sách người dùng</p>
            <div class="listUsers">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usernamecolen</th>
                            <th>Họ tên</th>
                            <th>Số điện thoại</th>
                            <th>Vai Trò</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <!-- Dữ liệu người dùng sẽ được chèn vào đây -->
                        <?php 
                        
                            $stmt = $pdo->prepare("SELECT user.id, user.username, user.full_name, user.phone_number, role.name AS role_name FROM user INNER JOIN role ON user.role_id = role.id ORDER BY user.id ASC");
                            $stmt->execute();
                            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                            // Hiển thị dữ liệu trong bảng
                            if ($users) {
                                foreach ($users as $user) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($user['id']) . '</td>';
                                    echo '<td>' . htmlspecialchars($user['username']) . '</td>';
                                    echo '<td>' . htmlspecialchars($user['full_name']) . '</td>';
                                    echo '<td>' . htmlspecialchars($user['phone_number']) . '</td>';
                                    echo '<td>' . htmlspecialchars($user['role_name']) . '</td>';
                                    echo '<td><a href="editUser.php?id=' . htmlspecialchars($user['id']) . '">Chỉnh sửa</a> |  <a href="?delete_id=' . $user['id'] . '" onclick="return confirm(\'Bạn có chắc chắn muốn xóa người dùng này không?\');">Xóa</a></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5">Không có người dùng nào</td></tr>';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>