<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}

include_once 'config.php';

$error = '';
$email = $_POST['email'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $conn->prepare("SELECT u.id, u.full_name, u.email, u.password_hash, u.role_id, r.role_name 
                                 FROM users u
                                 JOIN roles r ON u.role_id = r.id
                                 WHERE u.email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role_name'];

                header('Location: home.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DREAM | POS</title>

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="dist/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="dist/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<link rel="stylesheet" href="dist/dist/css/adminlte.min.css">

<style>
/* ===========================
   LOGIN PAGE STYLES
   =========================== */
.login-page {
    background-image: url('dist/dist/img/pos2.png');
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;

    display: flex;
    align-items: flex-center;      
    justify-content: flex-center; 
    padding-bottom:0.5%;              
    padding-left: 60%;            
    height: 100vh;
    min-width: 100%;
    box-sizing: border-box;
}

.login-box {
    margin: 0;
    width: 360px;
}

.card {
    background-color: rgba(255, 255, 255, 0.15) !important; /* প্রায় স্বচ্ছ */
    border: 4px solid #000; /* কালো এবং বোল্ড বর্ডার */
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* হালকা ছায়া */
}



.card-header, .card-body {
    background-color: transparent !important;
}

.form-control {
    color: #000 !important;
    background-color: #fff !important;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 5px;
}

.input-group-text {
    color: #333;
    background-color: #fff;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-left: none;
    border-radius: 0 5px 5px 0;
}

.mb-1 a {
    color: #000 !important;
    font-weight: bold;
}

.h1, .login-box-msg, .text-center {
    color: #333 !important;
}

/* ===========================
   RESPONSIVE FIX
   =========================== */
@media (max-width: 768px) {
    .login-page {
        justify-content: center;
        align-items: center;
        padding-top: 0;
        padding-right: 0;
    }
}
</style>
</head>
<body class="hold-transition login-page">

<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="dist/index2.html" class="h1"><b>DREAM</b>POS</a>
        </div>
        <div class="card-body">
            <p class="login-box-msg"><b>Sign in to start your session</b></p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password" name="password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">Remember Me</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                </div>
            </form>

            <p class="mb-1">
                <a href="forgot-password.html">I forgot my password</a>
            </p>
            <p class="mb-0">
                <a href="register.html" class="text-center">Register a new membership</a>
            </p>
        </div>
    </div>
</div>

<script src="dist/plugins/jquery/jquery.min.js"></script>
<script src="dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/dist/js/adminlte.min.js"></script>
</body>
</html>
