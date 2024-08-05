<?php
include '../../connect.php';
session_start();

if (isset($_POST['buyConfirm'])) {
    if ($_SESSION['userid']) {
        $prodId = mysqli_real_escape_string($conn, $_POST['productId']);
        $prodName = mysqli_real_escape_string($conn, $_POST['productName']);
        $prodPrice = mysqli_real_escape_string($conn, $_POST['productPrice']);
        $prodQuantity = mysqli_real_escape_string($conn, $_POST['productQuantity']);
        $address = mysqli_real_escape_string($conn, $_POST['Address']);
        $phonenumber = mysqli_real_escape_string($conn, $_POST['phonenumber']);
        $paymentmethod = mysqli_real_escape_string($conn, $_POST['payment']);
        $userId = $_SESSION['userid'];

        $stmt = $conn->prepare("SELECT * FROM `products` WHERE product_id = ?");
        $stmt->bind_param("i", $prodId);
        $stmt->execute();
        $dataproducts = $stmt->get_result();

        if ($rows = mysqli_fetch_array($dataproducts)) {
            $image_url = $rows['image_url'];
            $category = $rows['category'];

            $stmt = $conn->prepare("SELECT *, CONCAT(firstname, ' ', lastname) AS fullname FROM `users` WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $selectUser = $stmt->get_result();

            if ($userSelected = mysqli_fetch_array($selectUser)) {
                $userfullname = $userSelected['fullname'];

                $stmt = $conn->prepare("INSERT INTO `users_buy` (`product_id`, `product_name`, `product_price`, `category`, `quantity`, `address`, `phonenumber`, `payment_method`, `user_id`, `user_name`, `image_url`) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isdsisssiss", $prodId, $prodName, $prodPrice, $category, $prodQuantity, $address, $phonenumber, $paymentmethod, $userId, $userfullname, $image_url);
                $query1 = $stmt->execute();

                if ($query1) {
                    $stmt = $conn->prepare("SELECT * FROM `products` WHERE product_id = ?");
                    $stmt->bind_param("i", $prodId);
                    $stmt->execute();
                    $getQuantity = $stmt->get_result();

                    $updateQuantity  = "UPDATE `products` SET `quantity` = `quantity` - ? WHERE product_id = ?";
                    $stmt = $conn->prepare($updateQuantity);
                    $stmt->bind_param("ii", $prodQuantity, $prodId);
                    $updateResult = $stmt->execute();

                    header("Location: ../products.php");
                }
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
