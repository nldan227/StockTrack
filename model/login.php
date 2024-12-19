
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/styleLogin.css">

    <title>Đăng nhập</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-pap+KfaePGSOyAlckCwmQHkK3t7s6+b9J+4TT2gN0vYkIta0NNX6ZHgMjgWgFktciW7jqW0K3tqCqmUGPqxHig=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <?php

    include("../backend/config.php");
    
    $error_message = isset($_GET['error']) ? $_GET['error'] : '';
    ?>

    <style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    </style>
</head>

<body>
    <div class="nav">
        <div class="title"><a href="" class="" style="font-family:Bungee Spice; font-size: 170%; text-decoration: none;">STOCKTRACK</a></div>
    </div>

    <div class="main">
        <div class="banner">
            <img src="../img/banner.avif" alt="" class="">
        </div>
        <form action="../backend/ctlLogin.php" class="" method="post">
            <header style="text-align: center; font-size: 22px; margin-bottom: 20px"> <b>Đăng nhập</b> </header>
            <hr style="margin-bottom: 20px">
            <div class="input">
                <div class="label">
                    <i class="fa-solid fa-user"></i>
                    <label for="username"  style="font-weight: 600">Username</label>
                </div>
                <input type="text" name="username" id="username">
            </div>

            <div class="input">
                <div class="label">
                    <i class="fa-solid fa-lock"></i>
                    <label for="password" style="font-weight: 600">Password</label>
                </div>
                <input type="password" name="password" id="password">
            </div>

            <span class="error" id="Error" style="text-align: center; color: red; margin-bottom:2%"><?php echo $error_message; ?></span>


            <div class="submit">
                <input type="submit" style = "font-weight: 600; font-size: 16px" class="btn" name="submit" value="Login" required>
            </div>

        </form>
    </div>


    <div class="footer">
        <div class="infor footer-left">
            <p>Email: stocktrack@gmail.com</p>
            <p>Địa chỉ: Chiến Thắng - Văn Quán - Hà Đông</p>
        </div>

        <div class="infor footer-right">
            <p>Điện thoại: 0123456789</p>
            <p>Hotline: 011888888</p>

        </div>
    </div>
</body>
</html>