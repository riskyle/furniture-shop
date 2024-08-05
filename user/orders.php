<?php
include '../connect.php';
include '../helpers/functions.php';
session_start();

if (isset($_SESSION['userid'])) {
  if (isset($_POST['cancelconfirmbtn'])) {
    $idProduct = $_POST['productID'];

    $selectData2 = mysqli_query($conn, "SELECT * FROM `users_buy` WHERE id='$idProduct'");
    if ($retrieveData1 = mysqli_fetch_array($selectData2)) {
      $productIds = $retrieveData1['product_id'];
      $quantity = $retrieveData1['quantity'];

      $cancelOrder = mysqli_query($conn, "DELETE FROM `users_buy` WHERE id='$idProduct'");
      $updateQuantity = mysqli_query($conn, "UPDATE `products` SET `quantity` = `quantity` + $quantity WHERE product_id = '$productIds'");
    }
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
  <title>Orders - Furniture Shop</title>
  <link rel="stylesheet" href="../css/user/style_orders.css">
</head>

<body>
  <form action="" method="post">
    <header>
      <div class="container">
        <h1 id="product-title">Furniture Shop</h1>
        <img id="img" src="../img/furniture_logo.png" height="60px" width="60px">
        <nav>
          <ul>
            <li><a href="index.php">Home</a></li> |
            <li><a href="products.php">Products</a></li> |
            <li><a href="addcart.php">Cart</a></li> |
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
                  echo "<a href='#'>Profile</a><a href='#orders'>Orders</a><a class='logbtn'><button type='submit' name='logout'>Logout</button></a>";
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
    <section id="orders" class="orders">
      <form action="" method="post">
        <h2>Orders</h2>
        <div class="buttonswitch">
          <input id="nCompBtn" type="submit" name="notcomp" value="Not Completed">
          <input id="CompBtn" type="submit" name="comp" value="Completed">
        </div>
        <?php
        if (isset($_POST['notcomp'])) {
          if (isset($_SESSION['userid'])) {
            $userId = $_SESSION['userid'];
            echo "<div class='content'>";

            $selectDataQuery = "SELECT * FROM `users_buy` WHERE user_id = ? AND isConfirm = '0'";
            $stmt = $conn->prepare($selectDataQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $selectData = $stmt->get_result();

            while ($prdct = mysqli_fetch_array($selectData)) {
              echo "<div class='card-container'>";
              echo "<input type='hidden' name='idProduct' value='" . sanitize($prdct['id']) . "'>";
              echo "<div class='card-header'><img id='header-image' src='../img/" . sanitize($prdct['image_url']) . "'>";
              echo "</div>";
              echo "<div class='card-body'>";
              echo "<div class='upper-body'><p>" . sanitize($prdct['product_name']) . "</p></div>";
              echo "<div class='middle-body'><p>₱" . sanitize($prdct['product_price']) . "</p><p>x " . sanitize($prdct['quantity']) . "</p></div>";
              echo "<div><input id='pending-btn' type='button' value='Pending..' disabled><button type='button' class='cancel-dialog-btn' data-product-id='" . sanitize($prdct['id']) . "' onclick='cancelOrder(this)'>Cancel</button> </div>";
              echo "</div>";
              echo "</div>";
            }
            echo "</div>";
          }
        }
        if (isset($_POST['comp'])) {
          if (isset($_SESSION['userid'])) {
            $userId = $_SESSION['userid'];
            echo "<div class='content'>";

            $selectDataQuery = "SELECT * FROM `users_buy` WHERE user_id = ? AND isConfirm = '1'";
            $stmt = $conn->prepare($selectDataQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $selectData = $stmt->get_result();

            while ($prdct = mysqli_fetch_array($selectData)) {
              $isConfirm = $prdct['isConfirm'];

              echo "<div class='card-container'>";
              echo "<input type='hidden' name='idProduct' value='" . sanitize($prdct['id']) . "'>";
              echo "<div class='card-header'><img id='header-image' src='../img/" . sanitize($prdct['image_url']) . "'>";
              echo "</div>";
              echo "<div class='card-body'>";
              echo "<div class='upper-body'><p>" . sanitize($prdct['product_name']) . "</p></div>";
              echo "<div class='middle-body'><p>₱" . sanitize($prdct['product_price']) . "</p><p>x " . sanitize($prdct['quantity']) . "</p></div>";
              if ($isConfirm = 1) {
                echo "<div><input id='pending-btn' type='button' value='Confirmed' disabled></div>";
              }
              echo "</div>";
              echo "</div>";
            }

            $selectDataQuery = "SELECT * FROM `users_buy` WHERE user_id = ? AND isDeclined = '1'";
            $stmt = $conn->prepare($selectDataQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $selectData1 = $stmt->get_result();

            while ($prdct1 = mysqli_fetch_array($selectData1)) {
              $isDecline = $prdct1['isDeclined'];

              echo "<div class='card-container'>";
              echo "<input type='hidden' name='idProduct' value='" . sanitize($prdct1['id']) . "'>";
              echo "<div class='card-header'><img id='header-image' src='../img/" . sanitize($prdct1['image_url']) . "'>";
              echo "</div>";
              echo "<div class='card-body'>";
              echo "<div class='upper-body'><p>" . sanitize($prdct1['product_name']) . "</p></div>";
              echo "<div class='middle-body'><p>₱" . sanitize($prdct1['product_price']) . "</p><p>x " . sanitize($prdct1['quantity']) . "</p></div>";
              if ($isDecline = 1) {
                echo "<div><input id='pending-btn' type='button' value='Declined' disabled></div>";
              }
              echo "</div>";
              echo "</div>";
            }
            echo "</div>";
          }
        }

        ?>
      </form>
    </section>
    <dialog id="cancelDialog" class="cancel-dialog">
      <form action="" method="post">
        <button id="closebtn" type="button" class="dialog-btn" onclick="document.getElementById('cancelDialog').close()">X</button>
        <h2>Cancel Order?</h2>
        <input id="product-id" name="productID" type="hidden">
        <input id="cancelbtn" type="submit" name="cancelconfirmbtn" value="Confirm">
        <button id="cancelclosebtn" type="button" class="dialog-btn" onclick="document.getElementById('cancelDialog').close()">CANCEL</button>
      </form>
    </dialog>
    <table>
      <footer>
        <div class="container">
          <p>&copy; 2024 Jess Furniture Shop</p>
        </div>
      </footer>
    </table>
    <script>
      let cancelModal = document.getElementById('cancelDialog');

      function cancelOrder(product) {
        let productId = product.getAttribute("data-product-id");

        document.getElementById('product-id').value = productId;

        cancelModal.showModal();
      }
    </script>
</body>

</html>