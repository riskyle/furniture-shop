<?php
include '../connect.php';

if (isset($_POST['remove'])) {
    $pId = mysqli_real_escape_string($conn, $_POST['productID']);

    $selectProductQuery = "SELECT * FROM `products` WHERE product_id = ?";
    $stmt = $conn->prepare($selectProductQuery);
    $stmt->bind_param("i", $pId);
    $stmt->execute();
    $selectProduct = $stmt->get_result();

    if ($getProduct = mysqli_fetch_array($selectProduct)) {
        $prodId = $getProduct['product_id'];

        $removeProductQuery = "DELETE FROM `products` WHERE product_id = ?";
        $stmt = $conn->prepare($removeProductQuery);
        $stmt->bind_param("i", $prodId);
        $removeProduct =  $stmt->execute();

        if ($removeProduct) {
            echo "<script>alert('Product Remove Successfully!');</script>";
            echo "<script>window.location.href = 'products.php';</script>";
        }
    }
} else {
    header("Location: products.php");
    exit();
}
