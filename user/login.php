<?php
include '../connect.php';
session_start();

if (isset($_SESSION['users'])) {
  header("Location: products.php");
  exit();
}

?>
<html>

<head>
  <title>Login - Furniture Shop</title>
  <link rel="stylesheet" href="../css/user/styles_ls.css">
</head>

<body>
  <header>
    <div class="container">
      <h1>Furniture Shop</h1>
      <img id="img" src="../img/furniture_logo.png" height="60px" width="60px">
      <nav>
        <ul>
          <li><a href="index.php">Home</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section class="login-section">
    <div class="container">
      <h2>Login</h2>
      <form action="loginsignupphp/userlogin.php" method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
      </form>
      <p>Don't have an account? <a href="signup.php">SIGNUP</a></p>
    </div>
  </section>
</body>

</html>