<?php
include '../connect.php';

if (isset($_FILES['productImage']) && isset($_POST['add'])) {
    $img_name = $_FILES['productImage']['name'];
    $img_size = $_FILES['productImage']['size'];
    $tmp_name = $_FILES['productImage']['tmp_name'];
    $error = $_FILES['productImage']['error'];

    $allowedFile = array("jpg", "jpeg", "png");
    if (!$img_name) {
        echo "<script>alert('Sorry, no attached file!');</script>";
        echo "<script>window.location.href = 'products.php';</script>";
        exit();
    } elseif ($error === 0) {
        if ($img_size > 5000000) {
            echo "<script>alert('The Image is too large');</script>";
            echo "<script>window.location.href = 'products.php';</script>";
            exit();
        } else {
            $img_ex = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
            if (in_array($img_ex, $allowedFile)) {
                $productName =  mysqli_real_escape_string($conn, $_POST['productName']);
                $category =  mysqli_real_escape_string($conn, $_POST['category']);
                $productPrice =  mysqli_real_escape_string($conn, $_POST['productPrice']);
                $productQuantity =  mysqli_real_escape_string($conn, $_POST['productQuantity']);
                $new_name = uniqid("IMG", true) . '.' . $img_ex;
                $path = "../img/" . $new_name;

                $sql = "INSERT INTO products (product_name, category, price, quantity, image_url) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdis", $productName, $category, $productPrice, $productQuantity, $new_name);
                $result = $stmt->execute();

                if ($result) {
                    move_uploaded_file($tmp_name, $path);
                    echo "<script>alert('Product Added Successfully!');</script>";
                    echo "<script>window.location.href = 'products.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('Adding Product Failed!');</script>";
                    echo "<script>window.location.href = 'products.php';</script>";
                    exit();
                }
            } else {
                echo "<script>alert('Not Allowed To Upload This Image Type!');</script>";
                echo "<script>window.location.href = 'products.php';</script>";
                exit();
            }
        }
    } else {
    }
} else {
    header("Location: products.php");
    exit();
}
