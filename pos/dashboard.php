<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Include database connection
include_once '../config.php';

// --------------------
// 1. Today's sales count
$result_today_sales = $conn->query("
    SELECT COUNT(*) AS total_sales 
    FROM sales 
    WHERE sales_date = CURDATE()
");
$row_today_sales = $result_today_sales->fetch_assoc();
$total_sales_today = $row_today_sales['total_sales'] ?? 0;

// 2. Today's total amount
$result_today_amount = $conn->query("
    SELECT SUM(s.total_price) AS total_amount
    FROM sale_items s
    JOIN sales sa ON s.sale_id = sa.id
    WHERE sa.sales_date = CURDATE()
");
$row_today_amount = $result_today_amount->fetch_assoc();
$total_amount_today = $row_today_amount['total_amount'] ?? 0;

// 3. Top 5 most sold products
$result_top_products = $conn->query("
    SELECT st.product_name, SUM(s.quantity) AS total_qty
    FROM sale_items s
    JOIN stock st ON s.stock_id = st.id
    GROUP BY s.stock_id
    ORDER BY total_qty DESC
    LIMIT 5
");

$top_products = [];
if ($result_top_products) {
    while ($row = $result_top_products->fetch_assoc()) {
        $top_products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard | DREAM POS</title>
<link rel="stylesheet" href="../dist/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../dist/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar, Sidebar etc. can be included here -->

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Dashboard</h1>
    </section>

    <section class="content">
      <div class="row">
        <!-- Today's Sales -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?php echo $total_sales_today; ?></h3>
              <p>Today's Sales</p>
            </div>
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
          </div>
        </div>

        <!-- Today's Total Amount -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?php echo number_format($total_amount_today, 2); ?></h3>
              <p>Today's Total Amount</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
          </div>
        </div>
      </div>

      <!-- Top 5 Products -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Top 5 Most Sold Products</h3>
        </div>
        <div class="card-body">
          <ul>
          <?php if (!empty($top_products)): ?>
            <?php foreach($top_products as $prod): ?>
              <li><?php echo htmlspecialchars($prod['product_name']); ?> (<?php echo $prod['total_qty']; ?> sold)</li>
            <?php endforeach; ?>
          <?php else: ?>
              <li>No sales data available.</li>
          <?php endif; ?>
          </ul>
        </div>
      </div>
    </section>
  </div>

</div>

<script src="../dist/plugins/jquery/jquery.min.js"></script>
<script src="../dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/dist/js/adminlte.min.js"></script>
</body>
</html>
