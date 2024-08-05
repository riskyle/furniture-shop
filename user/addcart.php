<?php
include '../connect.php';
include '../helpers/functions.php';
session_start();

if (isset($_POST['confirm'])) {
  if ($_SESSION['userid']) {
    $prodId =  mysqli_real_escape_string($conn, $_POST['productId']);
    $prodName =  mysqli_real_escape_string($conn, $_POST['productName']);
    $prodPrice =  mysqli_real_escape_string($conn, $_POST['productPrice']);
    $prodQuantity =  mysqli_real_escape_string($conn, $_POST['productQuantity']);
    $userAddress =  mysqli_real_escape_string($conn, $_POST['address']);
    $phonenumber =  mysqli_real_escape_string($conn, $_POST['userPhone']);
    $paymentmethod =  mysqli_real_escape_string($conn, $_POST['paymentSelect']);
    $userId =  mysqli_real_escape_string($conn, $_SESSION['userid']);

    $stmt = $conn->prepare("SELECT * FROM `users_cart` WHERE product_id = ?");
    $stmt->bind_param("i", $prodId);
    $stmt->execute();
    $dataproducts = $stmt->get_result();

    if ($rows = mysqli_fetch_array($dataproducts)) {
      $image_url = $rows['image_url'];
      $id = $rows['id'];

      $stmt = $conn->prepare("INSERT INTO `users_buy`(`product_id`, `user_id`, `product_name`, `product_price`, `quantity`, `address`, `phonenumber`, `payment_method`, `image_url`)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("iisdiisss", $prodId, $userId, $prodName, $prodPrice, $prodQuantity, $userAddress, $phonenumber, $paymentmethod, $image_url);
      $query1 = $stmt->execute();

      if ($query1) {
        $stmt = $conn->prepare("DELETE FROM `users_cart` WHERE id = ?");
        $stmt->bind_param("i", $id);
        $movefromcarts = $stmt->execute();

        if ($movefromcarts) {
          $stmt = $conn->prepare("SELECT * FROM `products` WHERE product_id = ?");
          $stmt->bind_param("i", $prodId);
          $stmt->execute();
          $getQuantity = $stmt->get_result();

          $stmt = $conn->prepare("UPDATE `products` SET `quantity` = `quantity` - ? WHERE product_id = ?");
          $stmt->bind_param("ii", $prodQuantity, $prodId);
          $updateResult = $stmt->execute();
        }
      }
    }
  }
}
if (isset($_POST['removeconfirm'])) {
  $prodIds = $_POST['productId'];

  $stmt = $conn->prepare("SELECT * FROM `users_cart` WHERE product_id = ?");
  $stmt->bind_param("i", $prodIds);
  $stmt->execute();
  $dataproducts1 = $stmt->get_result();

  if ($rows1 = mysqli_fetch_array($dataproducts1)) {
    $ids = $rows1['id'];

    $stmt = $conn->prepare("DELETE FROM `users_cart` WHERE id = ?");
    $stmt->bind_param("i", $ids);
    $stmt->execute();
  }
}


if (isset($_POST['addcart'])) {
  if ($_SESSION['id']) {
    header("Location: addtocart.php");
  } else {
    echo "<script>alert('You Need To Login First!!')</script>";
    echo "<script>window.location.href = 'login.php';</script>";
  }
}
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
  <title>Cart - Furniture Shop</title>
  <link rel="stylesheet" href="../css/user/style_addcart.css">
</head>

<body>
  <form action="" method="post">
    <table>
      <header>
        <div class="container">
          <h1 id="product-title">Furniture Shop</h1>
          <img id="img" src="../img/furniture_logo.png" height="60px" width="60px">
          <nav>
            <ul>
              <li><a href="index.php">Home</a></li> |
              <li><a href="products.php">Products</a></li> |
              <li><a href="#carts">Cart</a></li> |
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
                    echo "<a href='signup.php'>Sign Up</a>
                          <a href='login.php'>Login</a>";
                  }
                  ?>
                </div>
              </li>
            </ul>
          </nav>
        </div>
      </header>
  </form>
  <section id="carts" class="products">
    <h2>Cart</h2>
    <div class="content">
      <?php
      if (isset($_SESSION['userid'])) {
        $UserId = $_SESSION['userid'];

        $stmt = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
        $stmt->bind_param("i", $UserId);
        $stmt->execute();
        $resultUser = $stmt->get_result();

        if ($userInfo = mysqli_fetch_array($resultUser)) {
          $stmt = $conn->prepare("SELECT * FROM users_cart WHERE user_id = ?");
          $stmt->bind_param("i", $UserId);
          $stmt->execute();
          $result = $stmt->get_result();

          while ($prdct = mysqli_fetch_array($result)) {
            $imageUrl = sanitize($prdct['image_url']);
            $productId = sanitize($prdct['product_id']);
            $productName = sanitize($prdct['product_name']);
            $productPrice = sanitize($prdct['product_price']);
            $productQuantity = sanitize($prdct['product_quantity']);

            $userAddress = sanitize($userInfo['address']);
            $userPhone = sanitize($userInfo['phone']);

            echo "<div class='card-container'>";
            echo "<div class='card-header'><img src='../img/{$imageUrl}'></div>";
            echo "<div class='card-body'>";
            echo "<div class='card-upper-body'><p>" . $productName . "</p></div>";
            echo "<div class='card-lower-body'><p>₱" . $productPrice . "</p><p>x " . $productQuantity . "</p></div>";
            echo "<div>";
            echo "<div><button class='dialog-btn-checkout' type='button' data-product-id='{$productId}' data-product-name='{$productName}' data-product-price='{$productPrice}' data-product-quantity='{$productQuantity}' data-address='{$userAddress}' data-phone='{$userPhone}' onclick='checkoutProducts(this)'>Check Out</button>
                    <button class='dialog-btn-remove' type='button' data-product-ids='{$productId}' onclick='removeProduct(this)'>Remove</button></div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
          }
        }
      }
      ?>
    </div>
  </section>
  </table>
  <dialog id="checkoutDialog" class="checkout-dialog">
    <form action="" method="post">
      <button id="closebtn" type="button" class="dialog-btn" onclick="document.getElementById('checkoutDialog').close()">X</button>
      <h2>Check Out</h2>
      <input id="product-id" name="productId" type="hidden">
      <div>
        <input id='prdctnames' type="text" name="productName" readonly>
      </div>
      <div>
        <input id='prices' type="text" name="productPrice" readonly>
      </div>
      <div>
        <input id='prdctquantity' type="number" name="productQuantity" min="1" max="" placeholder="Quantity">
      </div>
      <div>
        <input id='address' type="text" name="address">
      </div>
      <div>
        <input id='pnum' type="text" name="userPhone">
      </div>
      <div>
        <select id="pmethod" name="paymentSelect">
          <option value="Cash-On-Delivery">Cash On Delivery</option>
          <option value="Gcash">Gcash</option>
        </select>
      </div>
      <input id="checkoutbtn" type="submit" name="confirm" value="Confirm">
      <button id="cancelbtn" type="button" class="dialog-btn" onclick="document.getElementById('checkoutDialog').close()">Cancel</button>
    </form>
  </dialog>
  <dialog id="removeDialog" class="remove-dialog">
    <form action="" method="post">
      <button id="closebtn" type="button" class="dialog-btn" onclick="document.getElementById('removeDialog').close()">X</button>
      <h2>Remove Cart?</h2>
      <input id="product-ids" name="productId" type="hidden">
      <input id="removebtn" type="submit" name="removeconfirm" value="Confirm">
      <button id="cancelbtn" type="button" class="dialog-btn" onclick="document.getElementById('removeDialog').close()">Cancel</button>
    </form>
  </dialog>
  <footer>
    <div class="container">
      <p>&copy; 2024 Jess Furniture Shop</p>
    </div>
  </footer>
  </table>
  <script>
    let checkoutModal = document.getElementById('checkoutDialog');
    let removeModal = document.getElementById('removeDialog');

    function checkoutProducts(product) {
      let productId = product.getAttribute("data-product-id");
      let productName = product.getAttribute("data-product-name");
      let productPrice = product.getAttribute("data-product-price");
      let productQuantity = product.getAttribute("data-product-quantity");
      let useraddress = product.getAttribute("data-address");
      let userphone = product.getAttribute("data-phone");

      document.getElementById('product-id').value = productId;
      document.getElementById('prdctnames').value = productName;
      document.getElementById('prices').value = productPrice;
      document.getElementById('prdctquantity').setAttribute("max", productQuantity);
      document.getElementById('prdctquantity').value = productQuantity;
      document.getElementById('address').value = useraddress;
      document.getElementById('pnum').value = userphone;

      checkoutModal.showModal();
    }

    function removeProduct(product) {
      let productId = product.getAttribute("data-product-ids");

      document.getElementById('product-ids').value = productId;

      removeModal.showModal();
    }
  </script>
</body>

</html>