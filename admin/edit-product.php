<?php
include '../connect.php';

if (isset($_FILES['productimage']) && isset($_POST['upload'])) {
    $img_names = $_FILES['productimage']['name'];
    $img_sizes = $_FILES['productimage']['size'];
    $tmp_names = $_FILES['productimage']['tmp_name'];
    $errors = $_FILES['productimage']['error'];

    $productid = mysqli_real_escape_string($conn, $_POST['productId']);

    $allowedFile = array("jpg", "jpeg", "png");
    if (!$img_names) {
        echo "<script>alert('Sorry, no attached file!');</script>";
        echo "<script>window.location.href = 'products.php';</script>";
        exit();
    } elseif ($errors === 0) {
        if ($img_sizes > 5000000) {
            echo "<script>alert('The Image is too large');</script>";
            echo "<script>window.location.href = 'products.php';</script>";
            exit();
        } else {
            $img_ex = strtolower(pathinfo($img_names, PATHINFO_EXTENSION));
            if (in_array($img_ex, $allowedFile)) {
                $new_names = uniqid("IMG", true) . '.' . $img_ex;
                $paths = "../img/" . $new_names;

                $sql = "UPDATE `products` SET `image_url` = ? WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $new_names, $productid);
                $result = $stmt->execute();

                if ($result) {
                    move_uploaded_file($tmp_names, $paths);
                    echo "<script>alert('Product Update Successfully!');</script>";
                    echo "<script>window.location.href = 'products.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('Update Product Failed!');</script>";
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
        echo "<script>alert('Update Product Failed!');</script>";
        echo "<script>window.location.href = 'products.php';</script>";
        exit();
    }
}
if (isset($_POST['edit'])) {
    $productid =  mysqli_real_escape_string($conn, $_POST['productId']);
    $productname =  mysqli_real_escape_string($conn, $_POST['productname']);
    $productprice =  mysqli_real_escape_string($conn, $_POST['productprice']);
    $productCategory =  mysqli_real_escape_string($conn, $_POST['category']);
    $productquantity =  mysqli_real_escape_string($conn, $_POST['productquantity']);

    $sql = "UPDATE `products` SET `product_name`=?, `price`=?, `category`=?, `quantity`=? WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $productname, $productprice, $productCategory, $productquantity, $productid);
    $result1 = $stmt->execute();

    if ($result1) {
        move_uploaded_file($tmp_names, $paths);
        echo "<script>alert('Product Update Successfully!');</script>";
        echo "<script>window.location.href = 'products.php';</script>";
    } else {
        echo "<script>alert('Update Product Failed!');</script>";
    }
}

if (!isset($_POST['upload']) && !isset($_POST['edit'])) {
    header("Location: products.php");
    exit();
}
