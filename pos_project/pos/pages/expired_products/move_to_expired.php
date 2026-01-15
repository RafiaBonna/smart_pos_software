<?php
include_once __DIR__ . '/../../config.php';

// Form submit check
if (isset($_POST['move_to_expired'])) {
    $stock_id = intval($_POST['stock_id']);
    $qty_to_expire = intval($_POST['expired_qty']); // stock.php theke asha quantity
    $expiry_date = $_POST['expiry_date'];

    if ($stock_id > 0 && $qty_to_expire > 0) {
        $conn->begin_transaction();
        try {
            // ১. Expired Table-e data pathano
            $stmt = $conn->prepare("INSERT INTO expired_products (stock_id, quantity_expired, expiry_date) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $stock_id, $qty_to_expire, $expiry_date);
            $stmt->execute();

            // ২. Stock Table theke quantity minus kora
            $update_stock = $conn->prepare("UPDATE stock SET quantity = quantity - ? WHERE id = ?");
            $update_stock->bind_param("ii", $qty_to_expire, $stock_id);
            $update_stock->execute();

            $conn->commit();
            echo "<div class='alert alert-success mt-3 text-center'><h4>Success! $qty_to_expire units moved to Expired List.</h4></div>";
            echo "<script>setTimeout(function(){ window.location.href='home.php?page=24'; }, 2000);</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<div class="container-fluid mt-4">
    <div class="card card-outline card-warning text-center">
        <div class="card-body">
            <i class="fas fa-sync fa-spin fa-2x"></i>
            <p>Processing... If not redirected, <a href="home.php?page=24">click here</a>.</p>
        </div>
    </div>
</div>