<?php
include '../connect.php';
include '../helpers/functions.php';
session_start();
if (isset($_POST['addcart'])) {
    if ($_SESSION['userid']) {
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
    <title>Products - Furniture Shop</title>
    <link rel="stylesheet" href="../css/user/styles_products.css">
</head>

<body>
    <form action="" method="post">
        <header>
            <div class="container">
                <h1 id="product-title">Furniture Shop</h1>
                <img id='logo' src="../img/furniture_logo.png">
                <nav>
                    <ul>
                        <li><a href="index.php">Home</a></li> |
                        <li><a href="products.php">Products</a></li> |
                        <?php
                        $searchbox = isset($_GET['searchbox']) ? $_GET['searchbox'] : '';
                        if (empty($searchbox) && isset($searchbox)) {
                            echo "<li class='dropdown'>";
                            echo "<a href=''>Category</a>";
                            echo "<div class='dropdown-content'>";

                            $stmt = $conn->prepare("SELECT DISTINCT category FROM products ORDER BY category ASC");
                            $stmt->execute();
                            $getCategory = $stmt->get_result();

                            while ($getData = mysqli_fetch_array($getCategory)) {
                                $category = sanitize($getData['category']);

                                echo "<a class='ddBtn' href='#product-{$category}'>" . $category . "</a>";
                            }
                            echo "</div>";
                            echo "</li>";
                        } else {
                        }
                        ?>
                        <li><a href="addcart.php">Cart</a></li> |
                        <li class="dropdown">
                            <a class="dropbtn">
                                <?php
                                if (isset($_SESSION['userid'])) {
                                    $userEmail = $_SESSION['users'];
                                    echo sanitize($userEmail);
                                } else {
                                    echo "Account";
                                }
                                ?>
                            </a>
                            <div class="dropdown-content">
                                <?php
                                if (isset($_SESSION['userid'])) {
                                    echo "<a href='#'>Profile</a><a href='orders.php'>Orders</a><a class='ddBtn'><button type='submit' name='logout'>Logout</button></a>";
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
    <div class="search-contain">
        <form method="get" action="">
            <input id="search-box" type="search" name="searchbox" value="<?php echo isset($_GET['searchbox']) ? htmlspecialchars($_GET['searchbox']) : ''; ?>">
            <input id="search-btn" type="submit" value="SEARCH" name="searchbtn">
            <a id="viewall-btn" href="products.php"><button type="button">VIEW ALL</button></a>
        </form>
    </div>
    <?php
    $searchbox = isset($_GET['searchbox']) ? $_GET['searchbox']  : '';
    if (!empty($searchbox) && isset($searchbox)) {
        $searchbox = sanitize($searchbox);

        $stmt = $conn->prepare("SELECT * FROM products WHERE product_name LIKE ? ORDER BY product_name ASC");
        $search = '%' . $searchbox . '%';
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $searchQuery = $stmt->get_result();

        echo "<section id='product-{$searchbox}' class='products'>";
        echo "<h2>{$searchbox}</h2>";
        echo "<div class='content'>";
        if ($searchQuery->num_rows > 0) {
            if (isset($_SESSION['userid'])) {
                $userId = $_SESSION['userid'];

                $stmt = $conn->prepare("SELECT * FROM `users` WHERE id=?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $selectuser = $stmt->get_result();

                while ($userinfo = mysqli_fetch_array($selectuser)) {
                    while ($row = mysqli_fetch_array($searchQuery)) {
                        $prdctQuantity = $row['quantity'];
                        echo "<div class='card_container'>";
                        echo "<div class='card_header'><img src='../img/" . sanitize($row['image_url']) . "'></div>";
                        echo "<div class='card_body'>";
                        echo "<div class='card_title'><p>" . sanitize($row['product_name']) . "</p></div>";
                        echo "<div class='card_price'><input id='price-box' type='text' value='₱" . sanitize($row['price']) . "' readonly></div>";
                        echo "<div class='card_btn'>";
                        if ($prdctQuantity > 0) {
                            echo "<button class='dialog-btn-buy' type='button' data-product-id='" . sanitize($row['product_id']) . "' data-product-name='" . sanitize($row['product_name']) . "' data-product-price='" . sanitize($row['price']) . "' data-product-quantity='" . sanitize($row['quantity']) . "' data-address='" . sanitize($userinfo['address']) . "' data-phone='" . sanitize($userinfo['phone']) . "' onclick='BuyProducts(this)'>BUY</button>
                                    <button class='dialog-btn-cart' type='button' data-product-ids='" . sanitize($row['product_id']) . "' data-product-names='" . sanitize($row['product_name']) . "' data-product-prices='" . sanitize($row['price']) . "' data-product-quantitys='" . sanitize($row['quantity']) . "' onclick='CartProducts(this)'>ADD TO CART</button>";
                        } else {
                            echo "<button class='dialog-btn-buy' type='button'>---SOLD OUT---</button>";
                        }
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                }
            } else {
                while ($row = mysqli_fetch_array($searchQuery)) {
                    $prdctQuantity = $row['quantity'];
                    echo "<div class='card_container'>";
                    echo "<div class='card_header'><img src='../img/" . sanitize($row['image_url']) . "'></div>";
                    echo "<div class='card_body'>";
                    echo "<div class='card_title'><p>" . sanitize($row['product_name']) . "</p></div>";
                    echo "<div class='card_price'><input id='price-box' type='text' value='₱" . sanitize($row['price']) . "' readonly></div>";
                    echo "<div class='card_btn'>";
                    if ($prdctQuantity > 0) {
                        echo "<button class='dialog-btn-buy' type='button' data-product-id='" . sanitize($row['product_id']) . "' data-product-name='" . sanitize($row['product_name']) . "' data-product-price='" . sanitize($row['price']) . "' data-product-quantity='" . sanitize($row['quantity']) . "' onclick='BuyProducts(this)'>BUY</button>
                                <button class='dialog-btn-cart' type='button' data-product-ids='" . sanitize($row['product_id']) . "' data-product-names='" . sanitize($row['product_name']) . "' data-product-prices='" . sanitize($row['price']) . "' data-product-quantitys='" . sanitize($row['quantity']) . "' onclick='CartProducts(this)'>ADD TO CART</button>";
                    } else {
                        echo "<button class='dialog-btn-buy' type='button'>---SOLD OUT---</button>";
                    }
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            }
        }
        echo "</div>";
        echo "</section>";
    } else {
        $stmt = $conn->prepare("SELECT DISTINCT category FROM products ORDER BY category ASC");
        $stmt->execute();
        $selectCategory = $stmt->get_result();

        while ($row = mysqli_fetch_array($selectCategory)) {
            $category = $row['category'];
            echo "<section id='product-{$category}' class='products'>";
            echo "<h2>" . sanitize($category) . "</h2>";
            echo "<div class='content'>";

            $stmt = $conn->prepare("SELECT * FROM products WHERE category=?");
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $query = $stmt->get_result();

            if ($query->num_rows > 0) {
                if (isset($_SESSION['userid'])) {
                    $userid = $_SESSION['userid'];

                    $stmt = $conn->prepare("SELECT * FROM `users` WHERE id=?");
                    $stmt->bind_param("i", $userid);
                    $stmt->execute();
                    $selectuser = $stmt->get_result();

                    while ($userinfo = mysqli_fetch_array($selectuser)) {
                        while ($prdct = mysqli_fetch_array($query)) {
                            $prdctQuantity = $prdct['quantity'];
                            echo "<div class='card_container'>";
                            echo "<div class='card_header'><img src='../img/" . sanitize($prdct['image_url']) . "'></div>";
                            echo "<div class='card_body'>";
                            echo "<div class='card_title'><p>" . sanitize($prdct['product_name']) . "</p></div>";
                            echo "<div class='card_price'><input id='price-box' type='text' value='₱" . sanitize($prdct['price']) . "' readonly></div>";
                            echo "<div class='card_btn'>";
                            if ($prdctQuantity > 0) {
                                echo "<button class='dialog-btn-buy' type='button' data-product-id='" . sanitize($prdct['product_id']) . "' data-product-name='" . sanitize($prdct['product_name']) . "' data-product-price='" . sanitize($prdct['price']) . "' data-product-quantity='" . sanitize($prdct['quantity']) . "' data-address='" . sanitize($userinfo['address']) . "' data-phone='" . sanitize($userinfo['phone']) . "' onclick='BuyProducts(this)'>BUY</button>
                                        <button class='dialog-btn-cart' type='button' data-product-ids='" . sanitize($prdct['product_id']) . "' data-product-names='" . sanitize($prdct['product_name']) . "' data-product-prices='" . sanitize($prdct['price']) . "' data-product-quantitys='" . sanitize($prdct['quantity']) . "' onclick='CartProducts(this)'>ADD TO CART</button>";
                            } else {
                                echo "<button class='dialog-btn-buy' type='button'>---SOLD OUT---</button>";
                            }
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    }
                } else {
                    while ($prdct = mysqli_fetch_array($query)) {
                        $prdctQuantity = $prdct['quantity'];
                        echo "<div class='card_container'>";
                        echo "<div class='card_header'><img src='../img/" . sanitize($prdct['image_url']) . "'></div>";
                        echo "<div class='card_body'>";
                        echo "<div class='card_title'><p>" . sanitize($prdct['product_name']) . "</p></div>";
                        echo "<div class='card_price'><input id='price-box' type='text' value='₱" . sanitize($prdct['price']) . "' readonly></div>";
                        echo "<div class='card_btn'>";
                        if ($prdctQuantity > 0) {
                            echo "<button class='dialog-btn-buy' type='button' data-product-id='" . sanitize($prdct['product_id']) . "' data-product-name='" . sanitize($prdct['product_name']) . "' data-product-price='" . sanitize($prdct['price']) . "' data-product-quantity='" . sanitize($prdct['quantity']) . "'  onclick='BuyProducts(this)'>BUY</button>
                                    <button class='dialog-btn-cart' type='button' data-product-ids='" . sanitize($prdct['product_id']) . "' data-product-names='" . sanitize($prdct['product_name']) . "' data-product-prices='" . sanitize($prdct['price']) . "' data-product-quantitys='" . sanitize($prdct['quantity']) . "' onclick='CartProducts(this)'>ADD TO CART</button>";
                        } else {
                            echo "<button class='dialog-btn-buy' type='button'>---SOLD OUT---</button>";
                        }
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                }
            }
            echo "</div>";
            echo "</section>";
        }
    }
    ?>


    <dialog id="buyDialog" class="buy-dialog">
        <form action="productphp/buy.php" method="post">
            <button id="closebtn" type="button" class="dialog-btn" onclick="document.getElementById('buyDialog').close()">X</button>
            <h2>Buy Product</h2>
            <input id="product-id" name="productId" type="hidden">
            <div>
                <input id='prdctname' type="text" name="productName" readonly>
            </div>
            <div>
                <input id='price' type="text" name="productPrice" readonly>
            </div>
            <div>
                <input id='prdctquantity' type="number" name="productQuantity" min="1" max="" placeholder="Quantity">
            </div>
            <div>
                <input id='address' type="text" name="Address" placeholder="Address">
            </div>
            <div>
                <input id='pnum' type="text" name="phonenumber" placeholder="Phone Number">
            </div>
            <div>
                <select id="pmethod" name="payment">
                    <option value="Cash-On-Delivery">Cash On Delivery</option>
                    <option value="Gcash">Gcash</option>
                </select>
            </div>
            <input id="buybtn" type="submit" name="buyConfirm" value="BUY">
            <button id="cancelbtn" type="button" class="dialog-btn" onclick="document.getElementById('buyDialog').close()">CANCEL</button>
        </form>
    </dialog>

    <dialog id="cartDialog" class="cart-dialog">
        <form action="productphp/cart.php" method="post">
            <button id="closebtn" type="button" class="dialog-btn" onclick="document.getElementById('cartDialog').close()">X</button>
            <h2>Add To Cart</h2>
            <input id="product-ids" name="productID" type="hidden">
            <div>
                <input id='prdctnames' type="text" name="productNAME" readonly>
            </div>
            <div>
                <input id='prices' type="text" name="productPRICE" readonly>
            </div>
            <div>
                <input id='prdctquantity' type="number" name="productQUANTITY" min="1" max="" placeholder="Quantity">
            </div>
            <input id="cartbtn" type="submit" name="addCart" value="ADD">
            <button id="cancelbtn" type="button" class="dialog-btn" onclick="document.getElementById('cartDialog').close()">CANCEL</button>
        </form>
    </dialog>
    <footer>
        <div class="container">
            <p>&copy; 2024 Jess Furniture Shop</p>
        </div>
    </footer>
    <script>
        let buyModal = document.getElementById('buyDialog');
        let cartModal = document.getElementById('cartDialog');

        function BuyProducts(product) {
            buyModal.showModal();
            let productId = product.getAttribute("data-product-id");
            let productName = product.getAttribute("data-product-name");
            let productPrice = product.getAttribute("data-product-price");
            let productQuantity = product.getAttribute("data-product-quantity");
            let useraddress = product.getAttribute("data-address");
            let userphone = product.getAttribute("data-phone");

            document.getElementById('product-id').value = productId;
            document.getElementById('prdctname').value = productName;
            document.getElementById('price').value = productPrice;
            document.getElementById('prdctquantity').setAttribute("max", productQuantity);
            document.getElementById('address').value = useraddress;
            document.getElementById('pnum').value = userphone;

        }

        function CartProducts(product) {
            cartModal.showModal();
            let productId = product.getAttribute("data-product-ids");
            let productName = product.getAttribute("data-product-names");
            let productPrice = product.getAttribute("data-product-prices");
            let productQuantity = product.getAttribute("data-product-quantitys");

            document.getElementById('product-ids').value = productId;
            document.getElementById('prdctnames').value = productName;
            document.getElementById('prices').value = productPrice;
            document.getElementById('prdctquantitys').setAttribute("max", productQuantity);
        }
    </script>
</body>

</html>