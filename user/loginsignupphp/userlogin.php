<?php
include '../../connect.php';
session_start();

if (isset($_POST['login'])) {
    $enteredEmail = mysqli_real_escape_string($conn, $_POST['email']);
    $enteredPassword = mysqli_real_escape_string($conn, $_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $enteredEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($enteredPassword, $user['password'])) {

            $_SESSION['userid'] = $user['id'];
            $_SESSION['users'] = $enteredEmail;

            session_regenerate_id(true);
            header("Location: ../products.php");
            exit();
        } else {
            echo "<script>alert('Invalid username and password!!')</script>";
            echo "<script>window.location.href = '../login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid username and password!!')</script>";
        echo "<script>window.location.href = '../login.php';</script>";
    }

    $conn->close();
}
