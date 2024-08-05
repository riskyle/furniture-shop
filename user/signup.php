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
  <title>Signup - Furniture Shop</title>
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

  <section class="signup-section">
    <div class="container">
      <h2>Signup</h2>
      <form action="loginsignupphp/usersignup.php" method="POST">
        <input type="text" name="firstname" value="<?= $_SESSION['save_credentials']['firstname'] ?? "" ?>" placeholder="Firstname" required>
        <input type="text" name="lastname" value="<?= $_SESSION['save_credentials']['lastname'] ?? "" ?>" placeholder="Lastname" required>
        <input type="text" name="address" value="<?= $_SESSION['save_credentials']['address'] ?? "" ?>" placeholder="Address" required>
        <input type="number" name="phone" value="<?= $_SESSION['save_credentials']['phone'] ?? "" ?>" placeholder="Phone" required>
        <input type="email" name="email" value="<?= $_SESSION['save_credentials']['email'] ?? "" ?>" placeholder="Email" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <span id="password-strength" style="padding-left: 10px;"></span>
        <span id="is-valid-password"></span>
        <button type="submit" name="signup">Signup</button>
      </form>
      <p>Already have an account? <a href="login.php">LOGIN</a></p>
    </div>
  </section>
</body>

<script>
  document.getElementById('password').addEventListener('input', (e) => {
    var password = e.target.value;
    var strengthIndicator = document.getElementById('password-strength');
    document.getElementById('is-valid-password').innerHTML = "";
    var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    var isStrong = regex.test(password);
    if (!password) {
      strengthIndicator.innerText = '';
      return;
    }
    if (isStrong) {
      strengthIndicator.innerText = 'Password is Strong';
      strengthIndicator.style.color = 'green';
    } else if (password.length >= 8) {
      strengthIndicator.innerText = 'Password is Medium';
      strengthIndicator.style.color = '#eeb600';
    } else {
      strengthIndicator.innerText = 'Password is Weak';
      strengthIndicator.style.color = 'red';
    }
  })
</script>

</html>