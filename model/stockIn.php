<?php
include("../backend/config.php");
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-pap+KfaePGSOyAlckCwmQHkK3t7s6+b9J+4TT2gN0vYkIta0NNX6ZHgMjgWgFktciW7jqW0K3tqCqmUGPqxHig=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <style>
        body {
            margin: 20px;
        }
        .form-container {
            width: 80%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
       
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-group input[type="date"] {
            font-size: 16px;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: #c17400;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #a65b00;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .btn-group{
            display: flex;
            justify-content: space-around;
        }
        
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        .delete-btn {
            color: #ff0000;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
    <?php
    include('../backend/ctlStockIn.php');

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Tạo token CSRF ngẫu nhiên
    }
    $csrf_token = $_SESSION['csrf_token'];
    
    if (isset($_SESSION['message'])) {
        $message = addslashes($_SESSION['message']);
        echo '<script type="text/javascript">
            window.onload = function(){
               alert("' . $message . '");
               // window.history.back(); // Có thể loại bỏ nếu không cần thiết
                document.getElementById("note").value = "";
            }
            </script>';
        unset($_SESSION['message']); // Xóa thông báo sau khi hiển thị
    }
    ?>
                             
<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('type-supplier').addEventListener('change', function() {
        var supplierId = this.value.split(' - ')[0]; // Lấy ID nhà cung cấp từ giá trị đã chọn
        console.log('Supplier ID:', supplierId);

        if (supplierId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../backend/getProduct.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Xử lý dữ liệu sản phẩm trả về
                    var products = JSON.parse(xhr.responseText);
                    console.log('Products:', products);
                    var productCodeDatalist = document.getElementById('product-code');
                    var typeProduct = document.getElementById('type-product-code');
                    typeProduct.value = '';
                    productCodeDatalist.innerHTML = ''; // Xóa các mục hiện có

                    products.forEach(function(product) {
                        var option = document.createElement('option');
                        option.value = product.id + ' - ' + product.name;
                        productCodeDatalist.appendChild(option);
                    });
                } else {
                    console.error('Lỗi khi lấy dữ liệu sản phẩm.');
                }
            };

            xhr.send('supplier_id=' + encodeURIComponent(supplierId));
        }
    });

    document.getElementById('type-product-code').addEventListener('change', function() {
        var selectedOption = this.value.split(' - '); // Tách Mã sản phẩm và Tên sản phẩm
        var productId = selectedOption[0];       
        console.log('productId:', productId);
        if (productId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../backend/getProduct.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Xử lý dữ liệu sản phẩm trả về
                         var product = JSON.parse(xhr.responseText);
                         var formattedPrice = product.price.toLocaleString('de-DE');
                        document.getElementById('price').value = formattedPrice;
                      
                } else {
                    console.error('Lỗi khi lấy dữ liệu sản phẩm.');
                }
            };

            xhr.send('product_id=' + encodeURIComponent(productId)); // Gửi mã sản phẩm để lấy giá
        }
    });

    document.getElementById('saveStock').addEventListener('click', function() {
    // Lấy dữ liệu từ bảng
    let table = document.getElementById('product-list');
    let rows = table.querySelectorAll('tbody tr');
    let productData = [];

    rows.forEach(row => {
        let cells = row.querySelectorAll('td');
        let product = {
            code: cells[0].innerText,
            name: cells[1].innerText,
            quantity: cells[2].innerText,
            price: cells[3].innerText,
            total: cells[4].innerText
        };
        productData.push(product);
    });

    // Cập nhật trường ẩn với dữ liệu sản phẩm
    document.getElementById('product-data').value = JSON.stringify(productData);

    // Gửi form
    document.querySelector('form').submit();
    
});

});
</script>
</head>

<body>
<div class="form-container">
    <h2 style="color: #a65b00; text-align: center;">PHIẾU NHẬP ĐƠN</h2>
  

    <form action="../backend/ctlStockIn.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <div class="form-group">
            <label for="import-date">Ngày Nhập</label>
            <input type="date" id="import-date" name="import-date">
        </div>

        <div class="form-group">
            <label for="supplier">Nhà Cung Cấp</label>
            <input list="suppliers" id="type-supplier" name="supplier" placeholder="Chọn hoặc nhập nhà cung cấp" autocomplete="off">
            <datalist id="suppliers">
                <!-- Các nhà cung cấp từ cơ sở dữ liệu -->
                <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?php echo htmlspecialchars($supplier['id']) . ' - ' .  htmlspecialchars($supplier['name']) ?>">
                
                            </option>
                <?php endforeach ?>
            </datalist>
        </div>

        <div class="form-group">
            <label for="product-code">Sản Phẩm</label>
            <input list="product-code" id="type-product-code" name="product-code" placeholder="Chọn hoặc nhập mã sản phẩm" autocomplete="off">
            <datalist id="product-code">
                <!-- Các mã sản phẩm từ cơ sở dữ liệu -->
                <?php foreach ($products as $product): ?>
                            <option value="<?php echo htmlspecialchars($product['id']) . ' - ' .  htmlspecialchars($product['name']) ?>">

                            </option>
                <?php endforeach ?>
            </datalist>
        </div>
        

        <div class="form-group">
            <label for="quantity">Số Lượng</label>
            <input type="number" id="quantity" name="quantity" min="1">
        </div>

        <div class="form-group">
            <label for="price">Giá</label>
            <input type="text" id="price" name="price">
        </div>

        <div class="form-group">
            <label for="note" class="">Ghi chú</label>
            <input type="text" id="note" name="note">
        </div>

        <div class="form-group">
            <button type="button" id="add-product">Thêm Sản Phẩm</button>
        </div>

        <table id="product-list">
            <thead>
                <tr>
                    <th>Mã Sản Phẩm</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Số Lượng</th>
                    <th>Giá</th>
                    <th>Tổng Tiền</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <div class="form-group">
            <label for="total-amount">Tổng Tiền Đơn Hàng</label>
            <input type="text" id="total-amount" name="total-amount" readonly>
        </div>
        
        <input type="hidden" id="product-data" name="product-data">

        <div class="btn-group">
            <div class="form-group">
                <button type="submit" id="saveStock" name="saveStock">Lưu Đơn</button>
            </div>

            <div class="form-group">
                <button type="button" id="reset" name="reset">Hoàn Tác</button>
            </div>

            <div class="form-group">
                <button type="button" id="close-iframe">Thoát</button>
            </div>
        </div>
    </form>
</div>

<script>
    
   document.getElementById('add-product').addEventListener('click', function() {
    // Lấy dữ liệu từ form
    var selectedOption = document.getElementById('type-product-code').value.split(' - ');
    var productID = selectedOption[0];
    var productName = selectedOption[1];
    var quantity = parseFloat(document.getElementById('quantity').value) || 0;
    var price = parseFloat(document.getElementById('price').value.replace(/\./g, '').replace(',', '.')) || 0;
    var totalAmount = quantity * price;

    if (productID && productName && quantity > 0 && price > 0) {
        var table = document.getElementById('product-list').getElementsByTagName('tbody')[0];
        var rows = table.getElementsByTagName('tr');
        var productExists = false;

        // Kiểm tra xem sản phẩm đã có trong bảng chưa
        for (var i = 0; i < rows.length; i++) {
            var cells = rows[i].getElementsByTagName('td');
            var existingProductID = cells[0].innerText;
            
            if (existingProductID === productID) {
                // Nếu sản phẩm đã tồn tại, cập nhật số lượng và tổng tiền
                var existingQuantity = parseFloat(cells[2].innerText);
                var existingTotalAmount = parseFloat(cells[4].innerText.replace(' VNĐ', '').replace(/\./g, '').replace(',', '.'));

                existingQuantity += quantity;
                existingTotalAmount = existingQuantity * price;

                cells[2].innerText = existingQuantity;
                cells[4].innerText = existingTotalAmount.toLocaleString('de-DE', { minimumFractionDigits: 0 }) + ' VNĐ';

                // Cập nhật tổng tiền đơn hàng
                updateTotalAmount((quantity * price));
                productExists = true;
                break;
            }
        }

        if (!productExists) {
            // Thêm sản phẩm mới vào bảng nếu chưa tồn tại
            var newRow = table.insertRow();
            newRow.innerHTML = `
                <td>${productID}</td>
                <td>${productName}</td>
                <td>${quantity}</td>
                <td>${price.toLocaleString('de-DE', { minimumFractionDigits: 0 })} VNĐ</td>
                <td>${totalAmount.toLocaleString('de-DE', { minimumFractionDigits: 0 })} VNĐ</td>
                <td><span class="delete-btn" style="cursor:pointer; color: red;">Xóa</span></td>
            `;

            updateTotalAmount(totalAmount);
            
            // Gán sự kiện xóa cho nút mới
            var deleteBtn = newRow.querySelector('.delete-btn');
            deleteBtn.addEventListener('click', function() {
                var row = this.closest('tr'); // Xác định dòng chứa nút "Xóa"
                var rowTotal = parseFloat(row.cells[4].innerText.replace(' VNĐ', '').replace(/\./g, '').replace(',', '.')) || 0;
                
                // Trừ đi tổng tiền của hàng bị xóa
                updateTotalAmount(-rowTotal);

                // Xóa dòng sản phẩm khỏi bảng
                row.remove();
            });
        }

        // Xóa dữ liệu form sau khi thêm
        document.getElementById('type-product-code').value = '';
        document.getElementById('quantity').value = '';
        document.getElementById('price').value = '';

    } else {
        alert("Vui lòng nhập đầy đủ thông tin và đảm bảo số lượng và giá đều lớn hơn 0.");
    }
});
    document.getElementById('reset').addEventListener('click', function(){
        document.getElementById('import-date').value = '';
        document.getElementById('type-supplier').value = '';
        document.getElementById('type-product-code').value = '';
        document.getElementById('quantity').value = '';
        document.getElementById('price').value = '';
    }); 
    // Hàm cập nhật tổng tiền
    function updateTotalAmount(amount) {
        var totalAmountElement = document.getElementById('total-amount');
        var currentTotal = parseFloat(totalAmountElement.value.replace(' VNĐ', '').replace(/\./g, '').replace(',', '.')) || 0;
        console.log("Current Total Before: ", currentTotal);
        currentTotal += amount;
        console.log("Current Total After: ", currentTotal);

        totalAmountElement.value = currentTotal.toLocaleString('de-DE', { minimumFractionDigits: 0 }) + ' VNĐ';
    }

    document.getElementById("close-iframe").addEventListener("click", function() {
        window.parent.postMessage('closeIframe', '*');
    });

    document.getElementById("close-iframe").addEventListener("click", function() {
        window.parent.postMessage('closeIframe', '*');
    });


</script>

</body>
</html>