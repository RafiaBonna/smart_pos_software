<?php
// NOTE: This file is a monolithic combination of dashboard logic and view
// as provided in your query, with necessary SQL fixes applied.

session_start();
include 'config.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include('include/header.php');
include('include/nav.php');
include('include/sidebar.php');
include('placholder.php'); 


// --- Dashboard Data Queries ---
$today_orders = 0;
$today_sales_revenue = 0;
$total_products = 0;
$out_of_stock_products = 0;
$recent_sales = [];
$bar_chart_labels_js = '';
$bar_chart_data_js = '';
$pie_chart_labels_js = '';
$pie_chart_data_js = '';

// Today's orders
$result_orders = $conn->query("SELECT COUNT(*) AS total FROM sales WHERE DATE(sales_date) = CURDATE()");
if($result_orders) $today_orders = $result_orders->fetch_assoc()['total'];

// Today's revenue
$result_revenue = $conn->query("SELECT SUM(total_amount) AS total_revenue FROM sales WHERE DATE(sales_date) = CURDATE()");
if($result_revenue) $today_sales_revenue = number_format($result_revenue->fetch_assoc()['total_revenue'] ?? 0,2);

// Total products
$result_products = $conn->query("SELECT COUNT(*) AS total FROM stock");
if($result_products) $total_products = $result_products->fetch_assoc()['total'];

// Low stock
$result_low_stock = $conn->query("SELECT COUNT(*) AS total FROM stock WHERE quantity <= 5");
if($result_low_stock) $out_of_stock_products = $result_low_stock->fetch_assoc()['total'];

// Recent sales (FIXED: REMOVED 's.status' from the SELECT clause to prevent the error)
$result_recent = $conn->query("
    SELECT s.id, s.sales_date, c.name AS customer_name, s.total_amount
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    ORDER BY s.sales_date DESC
    LIMIT 5
");
if($result_recent) $recent_sales = $result_recent->fetch_all(MYSQLI_ASSOC);

// Bar chart last 7 days (Logic for daily revenue over the last 7 days)
$result_bar = $conn->query("
    SELECT DATE(sales_date) AS sale_date, SUM(total_amount) AS daily_revenue
    FROM sales
    WHERE sales_date >= CURDATE() - INTERVAL 6 DAY
    GROUP BY sale_date
    ORDER BY sale_date ASC
");
$bar_labels = $bar_data = [];
if($result_bar) {
    while($row = $result_bar->fetch_assoc()) {
        $bar_labels[] = "'" . date('M d', strtotime($row['sale_date'])) . "'";
        $bar_data[] = $row['daily_revenue'];
    }
    $bar_chart_labels_js = implode(',', $bar_labels);
    $bar_chart_data_js = implode(',', $bar_data);
}

// Pie chart top 5 products (Logic remains correct)
$result_pie = $conn->query("
    SELECT p.product_name, SUM(si.quantity) AS total_quantity
    FROM sales_items si
    JOIN stock st ON si.stock_id = st.id
    JOIN products p ON st.product_id = p.id
    GROUP BY p.product_name
    ORDER BY total_quantity DESC
    LIMIT 5
");
$pie_labels = $pie_data = [];
if($result_pie) {
    while($row = $result_pie->fetch_assoc()) {
        $pie_labels[] = "'" . $row['product_name'] . "'";
        $pie_data[] = $row['total_quantity'];
    }
    $pie_chart_labels_js = implode(',', $pie_labels);
    $pie_chart_data_js = implode(',', $pie_data);
}
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Dashboard</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Info Boxes (Small Boxes) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo htmlspecialchars($today_orders); ?></h3>
                            <p>Today's Orders</p>
                        </div>
                        <div class="icon"><i class="ion ion-bag"></i></div>
                        <a href="home.php?page=27" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>৳<?php echo htmlspecialchars($today_sales_revenue); ?></h3>
                            <p>Today's Sales</p>
                        </div>
                        <div class="icon"><i class="ion ion-stats-bars"></i></div>
                        <a href="home.php?page=27" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo htmlspecialchars($total_products); ?></h3>
                            <p>Total Products (In Stock)</p>
                        </div>
                        <div class="icon"><i class="fas fa-boxes"></i></div>
                        <a href="home.php?page=9" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo htmlspecialchars($out_of_stock_products); ?></h3>
                            <p>Low Stock Products</p>
                        </div>
                        <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                        <a href="home.php?page=21" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Bar Chart for Last 7 Days Sales -->
                <section class="col-lg-7 connectedSortable">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Last 7 Days Sales Revenue</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Pie Chart for Top Selling Products -->
                <section class="col-lg-5 connectedSortable">
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">Top 5 Selling Products (by Quantity)</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="pieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Recent Sales Table Row -->
            <div class="row">
                <section class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Sales</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <!-- Removed Status column header -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_sales)): ?>
                                        <?php foreach ($recent_sales as $sale): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($sale['id']); ?></td>
                                                <td><?php echo htmlspecialchars(date('M d, Y', strtotime($sale['sales_date']))); ?></td>
                                                <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer'); ?></td>
                                                <td>৳<?php echo htmlspecialchars(number_format($sale['total_amount'], 2)); ?></td>
                                                <!-- Removed Status display column -->
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No recent sales found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</div>


<?php 
include('include/footer.php');
?>

<!-- Scripts (MUST be included at the end of the file that is loaded) -->
<script src="dist/plugins/jquery/jquery.min.js"></script>
<script src="dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/dist/js/adminlte.min.js"></script>
<script src="dist/plugins/chart.js/Chart.min.js"></script>

<script>
$(function () {
    // BAR CHART
    var barChartCanvas = $('#barChart').get(0).getContext('2d');
    var barChartData = {
        labels: [<?php echo $bar_chart_labels_js; ?>],
        datasets: [{
            label: 'Sales Revenue',
            backgroundColor: 'rgba(60,141,188,0.9)',
            borderColor: 'rgba(60,141,188,0.8)',
            data: [<?php echo $bar_chart_data_js; ?>]
        }]
    };
    var barChartOptions = { responsive: true, maintainAspectRatio: false };
    new Chart(barChartCanvas, { type: 'bar', data: barChartData, options: barChartOptions });

    // PIE CHART
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
    var pieData = {
        labels: [<?php echo $pie_chart_labels_js; ?>],
        datasets: [{ data: [<?php echo $pie_chart_data_js; ?>], backgroundColor: ['#f56954','#00a65a','#f39c12','#00c0ef','#3c8dbc'] }]
    };
    var pieOptions = { responsive: true, maintainAspectRatio: false };
    new Chart(pieChartCanvas, { type: 'pie', data: pieData, options: pieOptions });
});
</script>