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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-pap+KfaePGSOyAlckCwmQHkK3t7s6+b9J+4TT2gN0vYkIta0NNX6ZHgMjgWgFktciW7jqW0K3tqCqmUGPqxHig=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <style>
        .chart-container p {
            margin-top: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .title {
            display: flex;
            margin: 20px;
        }
        .search {
            height: 220%;
            background-color: #f2f2f2;
        }
        .top{
            background-color: white;
            padding-top: 2px;
            padding-bottom: 12px;
            margin-top: 10px;
            margin-left: 10px;
            width: 98%;
            border: 1px solid #ccc;
            border-radius: 5px; /* Làm tròn các góc */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Đổ bóng */
        }

        
        .search {
            width: 88%;
        }

        .title p {
            margin-left: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #a86800;
        }

        .search i {
            margin-top: 10px;
            color: #c17400;
        }

        form {
            display: flex;
            justify-content: center;
        }

        form .field {
            margin-left: 20px;
            display: flex; 
            align-items: center;
        }

        .input input {
            margin-left: 5px;
            height: 20px;
            width: 110px;
            padding: 5px;
        }

        canvas {
            max-width: 400px;
            max-height: 300px;
            margin: 20px;
        }

        .chart-top{
            margin-top: 5px;
            display: flex;
            width: 100%;
            flex-direction: row;
            justify-content: space-around;
          
        }

        .chart-top .chart{
            width: 48%;
            border: 1px solid #ccc;
            border-radius: 5px; /* Làm tròn các góc */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.07); /* Đổ bóng */
            background-color: white;

            text-align: center;
        }

        .chart-bottom{
            margin-top: 5px;
            display: flex;
            width: 100%;
            flex-direction: row;
            justify-content: space-around;
          
        }

        .chart-bottom .chart{
            width: 48%;
            border: 1px solid #ccc;
            border-radius: 5px; /* Làm tròn các góc */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.07); /* Đổ bóng */
            background-color: white;

            text-align: center;
        }

        #supplierChart, #statusChart, #quantityChart, #valueChart{
            margin-left: 10%;
        }

    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
         var supplierChart, statusChart, quantityChart, totalChart; // Khai báo biến toàn cục

        document.addEventListener("DOMContentLoaded", function() {
            var today = new Date();
            var vietnamTime = new Date(today.getTime() + (7 * 60 * 60 * 1000)); // Thêm 7 giờ vào giờ GMT
            // Định dạng ngày theo kiểu YYYY-MM-DD
            var formattedDate = vietnamTime.toISOString().split('T')[0];
            // Thiết lập giá trị cho các trường ngày
            document.getElementById("start-date").value = formattedDate; // Ngày bắt đầu
            document.getElementById("end-date").value = formattedDate;   // Ngày kết thúc

            // Khi người dùng click vào biểu tượng mắt
            document.getElementById("submit-icon").addEventListener("click", function() {
                var startDate = document.getElementById("start-date").value;
                var endDate = document.getElementById("end-date").value;

                if (startDate && endDate) {
                    $.ajax({
                        url: '../backend/ctlStatistic.php',
                        method: 'GET',
                        data: {
                            'start-date': startDate,
                            'end-date': endDate
                        },
                        success: function(response) {
                            console.log(response); // In ra phản hồi để kiểm tra
                            try {
                                var data = JSON.parse(response);
                                drawCharts(data);
                            } catch (error) {
                                console.error("Lỗi phân tích dữ liệu JSON:", error);
                                alert('Dữ liệu trả về không hợp lệ.');
                            }
                        },
                        error: function() {
                            alert('Có lỗi xảy ra khi lấy dữ liệu.');
                        }
                    });
                } else {
                    alert('Vui lòng chọn khoảng thời gian.');
                }
            });

            // Gọi hàm để hiển thị thống kê cho ngày hiện tại
            document.getElementById("submit-icon").click(); // Giả lập click để lấy thống kê
        });

        // Hàm vẽ biểu đồ

        function drawCharts(data) {
            if (supplierChart) {
                supplierChart.destroy();
            }
            if (statusChart) {
                statusChart.destroy();
            }
            if (quantityChart) {
                quantityChart.destroy();
            }
            if (totalChart){
                totalChart.destroy();
            }

            // Biểu đồ nhà cung cấp
            document.getElementById('supplier-info').style.display = 'block';
            var supplierCtx = document.getElementById('supplierChart').getContext('2d');
            var supplierLabels = [];
            var supplierCounts = [];

            data.supplier_data.forEach(function(item) {
                    supplierLabels.push(item.name);
                    supplierCounts.push(item.count_inventory);
            });

            supplierChart =new Chart(supplierCtx, {
                type: 'bar',
                data: {
                    labels: supplierLabels,
                    datasets: [{
                        label: 'Số lượng đơn hàng theo nhà cung cấp',
                        data: supplierCounts,
                        backgroundColor: 'rgba(242, 166, 61, 0.5)', 
                        borderColor: 'rgb(209, 144, 73)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1, // Đặt bước nhảy cho các giá trị trên trục Y
                                min: 0,      // Giá trị tối thiểu
                              
                            }
                        }
                    }
                }
            });

            document.getElementById('status-info').style.display = 'block';
            // Biểu đồ trạng thái
            var statusCtx = document.getElementById('statusChart').getContext('2d');
            var statusLabels = ['Pending', 'Approved', 'Disapproved'];
            var statusCounts = [0, 0, 0];

            data.status_data.forEach(function(item) {
                if (item.status === 'Pending') {
                    statusCounts[0] = item.count_status;
                } else if (item.status === 'Approved')  {
                    statusCounts[1] = item.count_status;
                } else {
                    statusCounts[2] = item.count_status;
                }
            });

            statusChart=new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        label: 'Trạng thái đơn hàng',
                        data: statusCounts,
                        backgroundColor: ['rgba(204, 207, 205, 0.5)', 'rgba(54, 224, 79, 0.5)','rgba(255, 31, 31, 0.5)'],
                        borderColor: ['rgb(150, 144, 144)', 'rgb(75, 156, 97)','rgb(240, 77, 77)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                    }
                }
            });

            document.getElementById('quantity-info').style.display = 'block';
            // Biểu đồ số lượng đơn hàng theo thời gian
            var quantityCtx = document.getElementById('quantityChart').getContext('2d');
            var quantityLabels = [];
            var quantityCounts = [];

            data.quantity_data.forEach(function(item) {
                quantityLabels.push(item.entry_date); // Giả sử bạn có trường order_date
                quantityCounts.push(item.count_inventory);
            });

            quantityChart=new Chart(quantityCtx, {
                type: 'line',
                data: {
                    labels: quantityLabels,
                    datasets: [{
                        label: 'Số lượng đơn hàng theo thời gian',
                        data: quantityCounts,
                        fill: false,
                        borderColor: 'rgba(12, 49, 235, 0.5)',
                        tension: 0.1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1, // Đặt bước nhảy cho các giá trị trên trục Y
                                min: 0,      // Giá trị tối thiểu
                              
                            }
                        }
                    }
                }
            });


            document.getElementById('value-info').style.display = 'block';
            var valueCtx = document.getElementById('valueChart').getContext('2d');
            var valueLabels = [];
            var valueCounts = [];

            data.total_data.forEach(function(item) {
                valueLabels.push(item.entry_date);
                valueCounts.push(item.total);
            });

             totalChart = new Chart(valueCtx, {
                type: 'line',
                data: {
                    labels: valueLabels,
                    datasets: [{
                        label: 'Tổng giá trị hàng hóa nhập kho',
                        data: valueCounts,
                        fill: false,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        tension: 0.1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 200000,
                                
                                min: 0,
                            }
                        }
                    }
                }
            });
        }
    </script>
</head>

    <?php include '../layout/header.html'; ?>
    <?php include '../layout/dashboard.php'; ?>

    <div class="search">
        <div class="top">
            <div class="title">
                <i class="fa-solid fa-chart-simple fa-lg"></i>
                <p>THỐNG KÊ</p>
            </div>
            

            <div class="form">
                <form id="statistic-form">
                    <div class="field input">
                        <label for="start-date" class="">Từ:</label>
                        <input type="date" id="start-date" name="start-date" class="" required>
                    </div>

                    <div class="field input">
                        <label for="end-date" class="">Đến:</label>
                        <input type="date" id="end-date" name="end-date" class="" required>
                    </div>

                    <div class="field">
                        <i class="fa-solid fa-eye" id="submit-icon" style="cursor: pointer;"></i>  
                    </div>
                </form>
            </div>
        </div>

        <div class="bottom">
        <!-- Phần hiển thị biểu đồ -->
            <div class="chart-container chart-top">
                <div class="chart supplierChart">
                    <p id="supplier-info" style="display: none;">THỐNG KÊ SỐ LƯỢNG ĐƠN NHẬP KHO THEO NHÀ CUNG CẤP</p>
                    <canvas id="supplierChart"></canvas>
                </div>
         
                <div class="chart statusChart">
                    <p id="status-info" style="display: none;">THỐNG KÊ SỐ LƯỢNG TRẠNG THÁI ĐƠN NHẬP KHO</p>
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div class="chart-container chart-bottom">
                <div class="chart quantityChart">
                    <p id="quantity-info" style="display: none;">THỐNG KÊ SỐ LƯỢNG ĐƠN NHẬP KHO THEO THỜI GIAN </p>
                    <canvas id="quantityChart"></canvas>
                </div>

                <div class="chart valueChart">
                    <p id="value-info" style="display: none;">THỐNG KÊ TỔNG GIÁ TRỊ HÀNG HÓA NHẬP KHO THEO THỜI GIAN</p>
                    <canvas id="valueChart"></canvas>
                </div>

            </div>
        </div>
    </div>
    </div>
    
   
</body>
</html>