<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
  /* Fix navbar */
  .main-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1030;
  }

  /* Push content down so it’s not hidden under navbar */
  .content-wrapper {
    margin-top: 60px; /* adjust to your navbar height */
  }
</style>

</head>
<body>
  <!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    <!-- </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="#" class="nav-link">Home</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="#" class="nav-link">Contact</a>
    </li> -->
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Navbar Search -->
   

  

    

    <!-- Fullscreen -->
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>

  <li class="nav-item">
      <a href="logout.php" class="nav-link text-danger">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </li>
    
  </ul>
</nav>
<!-- /.navbar -->

</body>
</html>
