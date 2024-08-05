<?php
include '../connect.php';
include '../helpers/functions.php';

session_start();

if (isset($_SESSION['admin'])) {
} else {
    header("Location: login.php");
    exit();
}
if (isset($_POST['logout'])) {
    $_SESSION = array();

    session_destroy();

    $params = session_get_cookie_params();

    setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

    header("Location: login.php");
}

?>
<html>

<head>
    <title>Products - Admin</title>
    <link rel="stylesheet" href="../css/admin/styles_products.css">
</head>

<body>
    <header>
        <form action="" method="post">
            <div class="title-contain">
                <div class="dropdown">
                    <a class="dropbtn">
                        <h1>Products</h1>
                    </a>
                    <div class="dropdown-content">
                        <a href="dashboard.php">Dashboard</a>
                        <a href="#products">Products</a>
                        <a href="transactions.php">Transactions</a>
                        <a href="">Account</a>
                        <button name="logout">Logout</button>
                    </div>
                </div>
            </div>

            <div class="admin-name">
                <h3><?php echo sanitize($_SESSION['adminname']) ?></h3>
            </div>
        </form>
    </header>
    <section id="products" class="products">
        <div class="products-title">
            <h2>Products</h2>
        </div>
        <div class="products-content">
            <div class="products-actions">
                <button class="addBtn" type="button" onclick="document.getElementById('addDialog').showModal()">+</button>
            </div>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Product Id</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    include '../connect.php';

                    $sql = "SELECT * FROM `products`";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $selectData = $stmt->get_result();

                    while ($displayData = mysqli_fetch_array($selectData)) {
                        echo "<tr>";
                        echo "<input type='hidden' name='productId' value='" . sanitize($displayData['product_id']) . "'>";
                        echo "<td>" . sanitize($displayData['product_id']) . "</td>";
                        echo "<td>" . sanitize($displayData['product_name']) . "</td>";
                        echo "<td>" . sanitize($displayData['category']) . "</td>";
                        echo "<td>₱" . sanitize($displayData['price']) . "</td>";
                        echo "<td>" . sanitize($displayData['quantity']) . "</td>";
                        echo "<td id='actions'><button type='button'id='update-action' data-product-Id='" . sanitize($displayData['product_id']) . "' data-product-name='" . sanitize($displayData['product_name']) . "' data-product-price='" . sanitize($displayData['price']) . "' data-product-quantity='" . sanitize($displayData['quantity']) . "' data-product-image='" . sanitize($displayData['image_url']) . "' onclick='EditProduct(this)'>✏️ Edit</button>
                                <button class='removeProduct' id='remove-action' data-product-id='" . sanitize($displayData['product_id']) . "' type='button' onclick='RemoveProduct(this)'>❌ Remove</button></td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </section>
    <dialog id="addDialog" class="add-dialog">
        <form action="add-product.php" method="post" enctype="multipart/form-data">
            <button type="button" name="exitButton" class="exit-btn" onclick="document.getElementById('addDialog').close()">X</button>
            <h2>Add A Product</h2>
            <div class="dialog-section1">
                <input type="text" name="productName" placeholder="Product Name">
            </div>
            <div class="dialog-section2">
                <input type="text" name="productPrice" placeholder="Product Price">
            </div>
            <div class="dialog-section6">
                <input type="text" name="category" placeholder="Category">
            </div>
            <div class="dialog-section3">
                <input type="number" name="productQuantity" min="1" max="" placeholder="Product Quantity">
            </div>
            <div class="dialog-section4">
                <input id="image-product" type="file" name="productImage">
            </div>
            <div class="dialog-section5">
                <input type="submit" name="add" value="ADD">
            </div>
        </form>
    </dialog>

    <dialog id="editDialog" class="edit-dialog">
        <form action="edit-product.php" method="post" enctype="multipart/form-data">
            <button type="button" name="exitButton" class="exit-btn" onclick="document.getElementById('editDialog').close()">X</button>
            <h2>Edit Product</h2>
            <input id="product-ids" type="hidden" name="productId">
            <div class="dialog-section1">
                <input id="product-name" type="text" name="productname" placeholder="Product Name">
            </div>
            <div class="dialog-section2">
                <input id="product-price" type="text" name="productprice" placeholder="Product Price">
            </div>
            <div class="dialog-section6">
                <input type="text" name="category" placeholder="Category">
            </div>
            <div class="dialog-section3">
                <input id="product-quantity" type="number" name="productquantity" min="1" max="" placeholder="Product Quantity">
            </div>
            <div class="dialog-section4">
                <input id="product-image" type="file" name="productimage">
                <input id="upload-btn" type="submit" name="upload" value="Upload">
            </div>
            <div class="dialog-section5">
                <input type="submit" name="edit" value="UPDATE">
            </div>
        </form>
    </dialog>

    <dialog id="removeDialog" class="remove-dialog">
        <form action="remove-product.php" method="post">
            <input id="product-id" type="hidden" name="productID">
            <h2>Remove Product?</h2>
            <div class="confirm-buttons">

                <button id="confirm-btn" name="remove">✔️ Confirm</button>
                <button type="button" name="cancelButton" id="cancel-btn" onclick="document.getElementById('addDialog').close()">❌ Cancel</button>

            </div>
        </form>
    </dialog>

    <script>
        let editModal = document.getElementById('editDialog');
        let removeModal = document.getElementById('removeDialog');

        function EditProduct(product) {
            editModal.showModal();
            let productId = product.getAttribute("data-product-Id");
            let productName = product.getAttribute("data-product-name");
            let productPrice = product.getAttribute("data-product-price");
            let productQuantity = product.getAttribute("data-product-quantity");
            let productImage = product.getAttribute("data-product-image");

            document.getElementById('product-ids').value = productId;
            document.getElementById('product-name').value = productName;
            document.getElementById('product-price').value = productPrice;
            document.getElementById('product-quantity').value = productQuantity;
            document.getElementById('product-image').value = productImage;
        }

        function RemoveProduct(product) {
            removeModal.showModal();
            let productId = product.getAttribute("data-product-id");

            document.getElementById('product-id').value = productId;
        }
    </script>
</body>
<footer>

</footer>

</html>