<?php
include '../connect.php';

if (isset($_POST['accept'])) {
    $buyId = mysqli_real_escape_string($conn, $_POST['buyid']);

    $acceptProductQuery = "UPDATE `users_buy` SET `isConfirm` = ? WHERE id = ?";
    $stmt = $conn->prepare($acceptProductQuery);
    $isConfirm = 1;
    $stmt->bind_param("ii", $isConfirm, $buyId);
    $acceptproduct = $stmt->execute();

    if ($acceptproduct) {
        header("Location: transactions.php");
    }
}

if (isset($_POST['decline'])) {
    $buyId = mysqli_real_escape_string($conn, $_POST['buyid']);

    $declineProductQuery = "UPDATE `users_buy` SET `isDeclined` = ? WHERE id = ?";
    $stmt = $conn->prepare($declineProductQuery);
    $isDeclined = 1;
    $stmt->bind_param("ii", $isDeclined, $buyIds);
    $declineproduct = $stmt->execute();

    if ($declineproduct) {
        header("Location: transactions.php");
    }
} else {
    header("Location: transactions.php");
}
