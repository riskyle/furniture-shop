<?php
session_start();
include '../../connect.php';
if (isset($_POST['signup'])) {
	$fname =  mysqli_real_escape_string($conn, $_POST['firstname']);
	$lname =  mysqli_real_escape_string($conn, $_POST['lastname']);
	$address =  mysqli_real_escape_string($conn, $_POST['address']);
	$phone =  mysqli_real_escape_string($conn, $_POST['phone']);
	$email =  mysqli_real_escape_string($conn, $_POST['email']);
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

	$_SESSION['save_credentials'] = $_POST;

	$regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";

	if (!preg_match($regex, $_POST['password'])) {
		echo "<script>alert('Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.')</script>";
		echo "<script>window.location.href = '../signup.php';</script>";
		exit;
	}

	$stmt = $conn->prepare("INSERT INTO users (firstname, lastname, address, phone, email, password) VALUES (?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("ssssss", $fname, $lname, $address, $phone, $email, $password);
	$query = $stmt->execute();

	if ($query) {
		header("Location: ../login.php");
		unset($_SESSION['save_credentials']);
		exit();
	} else {
		echo "<script>alert('Failed to signup!!')</script>";
		echo "<script>window.location.href = '../signup.php';</script>";
	}
}
