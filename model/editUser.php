<?php
session_start();
include("../backend/config.php");

// Kiểm tra xem ID của người dùng đã được gửi chưa
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo 'ID người dùng không hợp lệ.';
    exit();
}

$user_id = $_GET['id'];

// Lấy thông tin người dùng từ cơ sở dữ liệu
$stmt = $pdo->prepare("SELECT * FROM user WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo 'Không tìm thấy người dùng.';
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Tạo token CSRF ngẫu nhiên
}
$csrf_token = $_SESSION['csrf_token'];

// Xử lý cập nhật dữ liệu khi form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   
        // Kiểm tra token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo 'CSRF token không hợp lệ.';
            exit();
        }

    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $role_id = $_POST['role_id'];
  
    // Cập nhật thông tin người dùng
    $update_query = "UPDATE user SET username = :username, full_name = :full_name, role_id = :role_id WHERE id = :id";
    $stmt = $pdo->prepare($update_query);
    if ($stmt->execute([
        'username' => $username,
        'full_name' => $full_name,
        'role_id' => $role_id,
        'id' => $user_id
    ])) {
        echo '<script>
                alert("Cập nhật thành công!");
                window.location.href = "managerUser.php";
            </script>';
        unset($_SESSION['csrf_token']);
        exit();
    } else {
        echo 'Cập nhật không thành công.';
    }

}

// Lấy danh sách vai trò (roles) để hiển thị trong form
$roles_stmt = $pdo->prepare("SELECT * FROM role");
$roles_stmt->execute();
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);
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

        .body{
            background-color: #E0E0E0;
        }
        .main {
            height: 80%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .content{
            height: 75%;
            width: 60%;
            padding: 50px;
            display: flex;
            border-radius: 20px;
            justify-content: center;
            box-shadow: 0 0 128px 0 rgba(0,0,0,0.1), 0 32px 64px -48px rgba(0,0,0,0.5);
        }
        .container{
            width: 50%;
            margin-bottom: 10px;
            font-weight: 700;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
        }

        input, select {
            border:none;
            background-color: #e8e8e8;
            border-radius: 20px;
            padding: 10px;
            margin-top: 5px;
        }

        button {
            margin-top: 20px;
            padding: 5px;
            border-radius: 40px;
            width: 80px;
            background-color: #c17400;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.7;
        }

        .btnGroup{
            display: flex;
            justify-content: space-around;

        }
    </style>

 
</head>
<body>
    <?php include '../layout/header.html' ?>
    <div class="main">
        <div class="content">

            <div class="container">
                <h2 style="text-align: center;">Chỉnh Sửa Người Dùng</h2>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                    <label for="full_name">Họ Tên:</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

                    <label for="role_id">Vai Trò:</label>
                    <select id="role_id" name="role_id" required>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo htmlspecialchars($role['id']); ?>"<?php echo ($role['id'] == $user['role_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($role['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="btnGroup">
                        <button type="submit" class="btn">Cập nhật</button>
                        <button type="button" class="btn" onclick="window.location.href='managerUser.php';">Hủy bỏ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../layout/footer.html' ?>

</body>
</html>