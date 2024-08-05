<?php
include '../../connect.php';
session_start();

if (isset($_POST['addCart'])) {
    if ($_SESSION['userid']) {
        $ProdId = mysqli_real_escape_string($conn, $_POST['productID']);
        $ProdName = mysqli_real_escape_string($conn, $_POST['productNAME']);
        $ProdPrice = mysqli_real_escape_string($conn, $_POST['productPRICE']);
        $ProdQuantity = mysqli_real_escape_string($conn, $_POST['productQUANTITY']);
        $UserId = $_SESSION['userid'];

        $stmt = $conn->prepare("SELECT * FROM `products` WHERE product_id = ?");
        $stmt->bind_param("i", $ProdId);
        $stmt->execute();
        $dataproducts1 = $stmt->get_result();

        if ($rows1 = mysqli_fetch_array($dataproducts1)) {
            $Image_Url = $rows1['image_url'];
            $id = $rows1['product_id'];

            $stmt = $conn->prepare("SELECT * FROM `users_cart` WHERE product_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $id, $UserId);
            $stmt->execute();
            $checkCart  = $stmt->get_result();

            if ($checkCart->num_rows > 0) {
                if ($checkCartResult = mysqli_fetch_array($checkCart)) {
                    $cartId = $checkCartResult['id'];
                    
                    $stmt = $conn->prepare("UPDATE `users_cart` SET `product_quantity` = `product_quantity` + ? WHERE id = ?");
                    $stmt->bind_param("ii", $ProdQuantity, $cartId);
                    $stmt->execute();

                    header("Location: ../products.php");
                    exit();
                }
            } else {
                $stmt = $conn->prepare("INSERT INTO `users_cart`(`user_id`, `product_id`, `product_name`, `product_price`, `product_quantity`, `image_url`) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iisdis", $UserId, $ProdId, $ProdName, $ProdPrice, $ProdQuantity, $Image_Url);
                $stmt->execute();

                header("Location: ../products.php");
            }
        }
    } else {
        echo "<script>alert('You Need To Login First!!')</script>";
        echo "<script>window.location.href = '../login.php';</script>";
    }
} else {
    header("Location: ../products.php");
    exit();
}
