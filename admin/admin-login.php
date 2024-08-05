<?php
include '../connect.php';
session_start();

if (isset($_POST['login'])) {
    $enteredUsername = mysqli_real_escape_string($conn, $_POST['username']);
    $enteredPassword = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM `admin` WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $enteredUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $rows = mysqli_fetch_array($result);

        if (!password_verify($enteredPassword, $rows['password'])) {
            echo "<script>alert('Invalid username and password!!')</script>";
            echo "<script>window.location.href = 'login.php';</script>";
        } else {
            $_SESSION['admin'] = $enteredUsername;
            $_SESSION['adminname'] = $rows['admin_name'];

            session_regenerate_id(true);
            header("Location: dashboard.php");
            exit();
        }
    } else {
        echo "<script>alert('Invalid username and password!!')</script>";
        echo "<script>window.location.href = 'login.php';</script>";
    }

    $conn->close();
}
