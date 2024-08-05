<?php
include '../connect.php';
include '../helpers/functions.php';
session_start();

if (isset($_POST['logout'])) {
  $_SESSION = array();

  session_destroy();

  $params = session_get_cookie_params();

  setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

  header("Location: index.php");
}
?>

<html>

<head>
  <title>Furniture Shop</title>
  <link rel="stylesheet" href="../css/user/styles.css">
</head>

<body>
  <form action="" method="post">
    <header>
      <div class="container">
        <h1 id="product-title">Furniture Shop</h1>
        <img src="../img/furniture_logo.png" height="60px" width="60px">
        <nav>
          <ul>
            <li><a href="index.php">Home</a></li> |
            <li><a href="products.php">Products</a></li> |
            <li><a href="#about">About Us</a></li> |
            <li><a href="#contact">Contact</a></li> |
            <li class="dropdown">
              <a class="dropbtn">
                <?php
                if (isset($_SESSION['users'])) {
                  $userEmail = $_SESSION['users'];
                  echo sanitize($userEmail);
                } else {
                  echo "Account";
                }
                ?>
              </a>
              <div class="dropdown-content">
                <?php
                if (isset($_SESSION['users'])) {
                  echo "<a href='#'>Profile</a><a href='orders.php'>Orders</a><a class='logbtn'><button type='submit' name='logout'>Logout</button></a>";
                } else {
                  echo "<a href='signup.php'>Sign Up</a><a href='login.php'>Login</a>";
                }
                ?>
              </div>
            </li>
          </ul>
        </nav>
      </div>
    </header>
  </form>
  <section id="home" class="hero">
    <div class="containers">
      <h2>Welcome to Our Furniture Shop</h2>
      <p>Discover our wide range of high-quality furniture for your home.</p>
      <a href="products.php" class="btn">Explore Products</a>
    </div>
    <div class="gif-contain">
      <img id="gif-sofa" src="../img/husky-sofa.gif">
    </div>
  </section>

  <section id="about" class="about">
    <div class="containers">
      <h2>About Us</h2>
      <p>We are dedicated to providing top-quality furniture to enhance your living space.</p>
    </div>
  </section>

  <section id="contact" class="contact">
    <div class="containers">
      <h2>Contact Us</h2>
      <p>If you have any questions, feel free to reach out to us.</p>
      <form action="#">
        <input type="text" placeholder="Your Name">
        <input type="email" placeholder="Your Email">
        <textarea placeholder="Your Message"></textarea>
        <div class="send-btn">
          <button type="submit" class="btn">Send</button>
        </div>
      </form>
    </div>
  </section>

  <footer>
    <div class="container">
      <p>&copy; 2024 Jess Furniture Shop</p>
    </div>
  </footer>
</body>

</html>