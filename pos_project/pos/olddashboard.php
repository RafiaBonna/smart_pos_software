<?php
// Include the database connection file
include 'config.php';

// Initialize variables with default values to prevent errors
$today_orders = 0;
$today_sales_revenue = 0;
$total_products = 0;
$out_of_stock_products = 0;

// Fetch total number of orders today from the 'sales' table
$sql_today_orders = "SELECT COUNT(*) AS total FROM sales WHERE DATE(sale_date) = CURDATE()";
$result_orders = $conn->query($sql_today_orders);
if ($result_orders && $result_orders->num_rows > 0) {
    $row = $result_orders->fetch_assoc();
    $today_orders = $row['total'];
}

// Fetch total sales revenue today from the 'sales' table
$sql_today_sales_revenue = "SELECT SUM(total_amount) AS total_revenue FROM sales WHERE DATE(sale_date) = CURDATE()";
$result_revenue = $conn->query($sql_today_sales_revenue);
if ($result_revenue && $result_revenue->num_rows > 0) {
    $row = $result_revenue->fetch_assoc();
    $today_sales_revenue = number_format($row['total_revenue'], 2) ?? '0.00';
}

// Fetch total number of products from the 'stock' table
$sql_total_products = "SELECT COUNT(*) AS total FROM stock";
$result_products = $conn->query($sql_total_products);
if ($result_products && $result_products->num_rows > 0) {
    $row = $result_products->fetch_assoc();
    $total_products = $row['total'];
}

// Fetch number of products with quantity below a certain threshold (e.g., 5)
$sql_out_of_stock = "SELECT COUNT(*) AS total FROM stock WHERE quantity <= 5";
$result_out_of_stock = $conn->query($sql_out_of_stock);
if ($result_out_of_stock && $result_out_of_stock->num_rows > 0) {
    $row = $result_out_of_stock->fetch_assoc();
    $out_of_stock_products = $row['total'];
}

// Fetch data for the bar chart (last 7 days sales)
$bar_chart_labels = [];
$bar_chart_data = [];

$sql_bar_chart = "SELECT DATE(sale_date) AS sale_date, SUM(total_amount) AS daily_revenue 
                  FROM sales 
                  WHERE sale_date >= CURDATE() - INTERVAL 6 DAY
                  GROUP BY DATE(sale_date)
                  ORDER BY sale_date ASC";

$result_bar_chart = $conn->query($sql_bar_chart);

if ($result_bar_chart && $result_bar_chart->num_rows > 0) {
    while ($row = $result_bar_chart->fetch_assoc()) {
        $bar_chart_labels[] = "'" . date('M d', strtotime($row['sale_date'])) . "'";
        $bar_chart_data[] = $row['daily_revenue'];
    }
}

$bar_chart_labels_js = implode(', ', $bar_chart_labels);
$bar_chart_data_js = implode(', ', $bar_chart_data);

// Fetch data for the pie chart (top 5 selling products by quantity)
$pie_chart_labels = [];
$pie_chart_data = [];

$sql_pie_chart = "SELECT p.product_name, SUM(si.quantity) AS total_quantity
                  FROM sale_items si
                  JOIN stock s ON si.stock_id = s.id
                  JOIN products p ON s.product_id = p.id
                  GROUP BY p.product_name
                  ORDER BY total_quantity DESC
                  LIMIT 5";

$result_pie_chart = $conn->query($sql_pie_chart);

if ($result_pie_chart && $result_pie_chart->num_rows > 0) {
    while ($row = $result_pie_chart->fetch_assoc()) {
        $pie_chart_labels[] = "'" . htmlspecialchars($row['product_name']) . "'";
        $pie_chart_data[] = $row['total_quantity'];
    }
}

$pie_chart_labels_js = implode(', ', $pie_chart_labels);
$pie_chart_data_js = implode(', ', $pie_chart_data);

// Fetch recent sales for the table
$recent_sales = [];

$sql_recent_sales = "SELECT s.id, s.sale_date, c.name AS customer_name, s.total_amount 
                     FROM sales s
                     LEFT JOIN customers c ON s.customer_id = c.id
                     ORDER BY s.sale_date DESC
                     LIMIT 5";

$result_recent_sales = $conn->query($sql_recent_sales);

if ($result_recent_sales && $result_recent_sales->num_rows > 0) {
    while ($row = $result_recent_sales->fetch_assoc()) {
        $recent_sales[] = $row;
    }
}
?>

<!-- Dashboard UI -->

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo htmlspecialchars($today_orders); ?></h3>
                <p>Today's Orders</p>
            </div>
            <a href="home.php?page=reports_sales" class="small-box-footer">More info</a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>৳<?php echo htmlspecialchars($today_sales_revenue); ?></h3>
                <p>Today's Sales</p>
            </div>
            <a href="home.php?page=reports_sales" class="small-box-footer">More info</a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo htmlspecialchars($total_products); ?></h3>
                <p>Total Product</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?php echo htmlspecialchars($out_of_stock_products); ?></h3>
                <p>Low Stock</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->

<div class="row">
    <section class="col-lg-7 connectedSortable">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Last 7 Days Sales</h3>
            </div>
            <div class="card-body">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </section>

    <section class="col-lg-5 connectedSortable">
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Top Selling Products</h3>
            </div>
            <div class="card-body">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </section>
</div>

<!-- Recent Sales -->

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
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($recent_sales) > 0): ?>
                            <?php foreach ($recent_sales as $sale): ?>
                                <tr>
                                    <td><?php echo $sale['id']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($sale['sale_date'])); ?></td>
                                    <td><?php echo $sale['customer_name'] ?? 'N/A'; ?></td>
                                    <td>৳<?php echo number_format($sale['total_amount'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center">No recent sales found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script src="dist/plugins/jquery/jquery.min.js"></script>
<script src="dist/plugins/chart.js/Chart.min.js"></script>

<script>
$(function () {
    // BAR CHART
    new Chart($('#barChart'), {
        type: 'bar',
        data: {
            labels: [<?php echo $bar_chart_labels_js; ?>],
            datasets: [{
                label: 'Sales Revenue',
                backgroundColor: 'rgba(60,141,188,0.9)',
                data: [<?php echo $bar_chart_data_js; ?>]
            }]
        }
    });

    // PIE CHART
    new Chart($('#pieChart'), {
        type: 'pie',
        data: {
            labels: [<?php echo $pie_chart_labels_js; ?>],
            datasets: [{
                data: [<?php echo $pie_chart_data_js; ?>],
                backgroundColor: ['#f56954','#00a65a','#f39c12','#00c0ef','#3c8dbc']
            }]
        }
    });
});
</script>
