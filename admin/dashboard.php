<?php
include '../connect.php';
include '../helpers/functions.php';
session_start();

if (isset($_SESSION['admin'])) {
    # code...
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
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="../css/admin/styles_dashboard.css">
</head>

<body>

    <header>
        <form action="" method="post">
            <div class="title-contain">
                <div class="dropdown">
                    <a class="dropbtn">
                        <h1>Dashboard</h1>
                    </a>
                    <div class="dropdown-content">
                        <a href="#dashboard">Dashboard</a>
                        <a href="products.php">Products</a>
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
    <section id="dashboard" class="dashboard">
        <div class="dashboard-title">
            <h2>Dashboard</h2>
        </div>
        <div class="dashboard-content">
            <div class="upper-card">
                <div class="cards">
                    <?php

                    $sql = "SELECT COUNT(product_id) AS total_products FROM products";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $totalproducts = $stmt->get_result();

                    while ($result1 = mysqli_fetch_array($totalproducts)) {
                        $products = $result1['total_products'];
                    }
                    ?>
                    <div class="card-contain">
                        <h2>Total Products</h2>
                        <p><?php echo sanitize($products) ?></p>
                    </div>
                </div>
                <div class="cards">
                    <?php

                    $sql = "SELECT COUNT(id) AS total_users FROM users";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $totalusers = $stmt->get_result();

                    while ($result2 = mysqli_fetch_array($totalusers)) {
                        $users = $result2['total_users'];
                    }
                    ?>
                    <div class="card-contain">
                        <h2>Total Users</h2>
                        <p><?php echo sanitize($users) ?></p>
                    </div>
                </div>
                <div class="cards">
                    <?php

                    $sql = "SELECT SUM(product_price * quantity) AS total_profit FROM users_buy WHERE isConfirm = ?";
                    $stmt = $conn->prepare($sql);
                    $isConfirm = 1;
                    $stmt->bind_param("i", $isConfirm);
                    $stmt->execute();
                    $calculateprofit = $stmt->get_result();

                    while ($result3 = mysqli_fetch_array($calculateprofit)) {
                        $profit = $result3['total_profit'];
                    }
                    ?>
                    <div class="card-contain">
                        <h2>Total Profit</h2>
                        <p>₱<?php echo sanitize($profit) ?></p>
                    </div>
                </div>
            </div>
            <div class="lower-card">
                <div class="cards">
                    <h3>Top 5 Products</h3>
                    <table>
                        <tr>
                            <th>Product Id</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Solds</th>
                        </tr>
                        <?php

                        $sql = "SELECT *, SUM(quantity) AS total_sold FROM users_buy WHERE isConfirm = ? GROUP BY product_id ORDER BY total_sold DESC LIMIT ?";
                        $stmt = $conn->prepare($sql);
                        $isConfirm = 1;
                        $limit = 5;
                        $stmt->bind_param("ii", $isConfirm, $limit);
                        $stmt->execute();
                        $selectTop = $stmt->get_result();

                        while ($result4 = mysqli_fetch_array($selectTop)) {
                            echo "<tr>";
                            echo "<td id='dataid'>" . sanitize($result4['product_id']) . "</td>";
                            echo "<td id='dataname'>" . sanitize($result4['product_name']) . "</td>";
                            echo "<td id='dataprice'>₱" . sanitize($result4['product_price']) . "</td>";
                            echo "<td id='datasold'>" . sanitize($result4['total_sold']) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <footer>

    </footer>
</body>

</html>