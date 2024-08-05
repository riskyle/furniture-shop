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
    <title>Transactions - Admin</title>
    <link rel="stylesheet" href="../css/admin/styles_transactions.css">
</head>

<body>
    <header>
        <form action="" method="post">
            <div class="title-contain">
                <div class="dropdown">
                    <a class="dropbtn">
                        <h1>Transactions</h1>
                    </a>
                    <div class="dropdown-content">
                        <a href="dashboard.php">Dashboard</a>
                        <a href="products.php">Products</a>
                        <a href="">Transactions</a>
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
            <h2>Transactions</h2>
        </div>
        <div class="products-content">
            <div class="table-container">
                <table>
                    <tr>
                        <th>Product Id</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Buyer Name</th>
                        <th id='buyerad'>Buyer Address</th>
                        <th id="actions">Actions</th>
                    </tr>
                    <form action="accept.php" method="post">
                        <?php
                        include '../connect.php';

                        $selectBuysQuery = "SELECT * FROM `users_buy` WHERE isConfirm = ? AND isDeclined = ?";
                        $stmt = $conn->prepare($selectBuysQuery);
                        $isConfirm = 0;
                        $isDeclined = 0;
                        $stmt->bind_param("ii", $isConfirm, $isDeclined);
                        $stmt->execute();
                        $selectbuys = $stmt->get_result();

                        while ($displayBuys = mysqli_fetch_array($selectbuys)) {
                            echo "<tr>";
                            echo "<input type='hidden' name='buyid' value='" . sanitize($displayBuys['id']) . "'>";
                            echo "<td>" . sanitize($displayBuys['product_id']) . "</td>";
                            echo "<td>" . sanitize($displayBuys['product_name']) . "</td>";
                            echo "<td>" . sanitize($displayBuys['category']) . "</td>";
                            echo "<td>" . sanitize($displayBuys['product_price']) . "</td>";
                            echo "<td>" . sanitize($displayBuys['quantity']) . "</td>";
                            echo "<td>" . sanitize($displayBuys['user_name']) . "</td>";
                            echo "<td>" . sanitize($displayBuys['address']) . "</td>";
                            echo "<td><input id='acceptbtn' type='submit' value='Accept' name='accept'><input id='declinebtn' type='submit' value='Decline' name='decline'></td>";
                            echo "</tr>";
                        }

                        ?>
                    </form>
                </table>
            </div>
        </div>
    </section>
</body>
<footer>

</footer>

</html>