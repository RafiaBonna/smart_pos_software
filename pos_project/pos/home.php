<?php
// Start session once at the very beginning
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Define base include path
define('BASE_PATH', __DIR__ . '/include/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DREAM POS</title>

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="dist/plugins/fontawesome-free/css/all.min.css">

    <!-- Theme style -->
    <link rel="stylesheet" href="dist/dist/css/adminlte.min.css">

    <!-- Date Range Picker -->
    <link rel="stylesheet" href="dist/plugins/daterangepicker/daterangepicker.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Header + Navbar + Sidebar -->
    <?php
        include BASE_PATH . 'header.php';
        include BASE_PATH . 'nav.php';
        include BASE_PATH . 'sidebar.php';
    ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">

        <!-- Page Header -->
        <section class="content-header">
            <div class="container-fluid"></div>
        </section>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">
                <?php include "placholder.php"; ?>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include BASE_PATH . 'footer.php'; ?>

</div>
<!-- ./wrapper -->

<!-- JS Scripts -->
<script src="dist/plugins/jquery/jquery.min.js"></script>
<script src="dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/dist/js/adminlte.min.js"></script>

<!-- Moment + Date Range Picker -->
<script src="dist/plugins/moment/moment.min.js"></script>
<script src="dist/plugins/daterangepicker/daterangepicker.js"></script>

<!-- Chart.js -->
<script src="dist/plugins/chart.js/Chart.min.js"></script>

<!-- Custom scripts -->
<!-- <script src="dist/dist/js/demo.js"></script> -->
<script src="dist/dist/js/salescript.js"></script>

</body>
</html>
