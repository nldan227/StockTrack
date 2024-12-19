<?php 
    include("../backend/config.php");
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
<div class="main">
    <div class="menu-left">
        <div class="avatar">
            <img src="<?php echo $avatar ? $avatar : '../img/ava.jpg'; ?>" alt="Avatar" class="">
        </div>
        <div class="hello">
            <?php 
                if ($name) {
                    echo "Hello, " . htmlspecialchars($name);
                    
                }else{
                    echo "Hello, guest!";
                }
            ?>
        </div>
        <div class="menu">
            <ul class="" style="list-style-type: none;">
                <li class="sub-menu" data-href="profile.php">
                    <div class="sub">
                        <i class="fa-solid fa-user"></i>
                        <a href="profile.php">Trang cá nhân</a>
                    </div>
                </li>
                <?php if ($role == 'manager'): ?>
                <li class="sub-menu" data-href="manager.php">
                    <div class="sub">
                        <i class="fa-solid fa-people-roof"></i>
                        <a href="managerUser.php">Nhân viên</a>
                    </div>
                </li>
                <?php endif; ?>
                <li class="sub-menu dropdown-toggle" >
                    <div class="sub">
                        <i class="fa-solid fa-list-check"></i>
                        <a href="javascript:void(0);">Kho</a>
                    </div>

                    <ul class="dropdown">
                        <?php if ($role == 'user'): ?>
                        <li><a href="" id="nhap-kho-link">Nhập kho</a></li>
                        <li><a href="../model/viewList.php">Danh sách</a></li>
                        <?php endif; ?>

                        <?php if ($role == 'manager'): ?>
                        <li><a href="../model/viewList.php">Danh sách</a></li>
                        <li><a href="../model/statistic.php" id="">Thống kê</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <li class="sub-menu" data-href="">
                    <div class="sub">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <a href="../backend/ctlLogout.php">Đăng xuất</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>