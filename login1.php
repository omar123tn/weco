<?php
session_start();
include('connection.php');

if(isset($_SESSION['username'])){
    header("Location: welcome.php");
    exit();
}

$error = '';
if (isset($_POST['submit'])) {
    $user = $_POST['user'];
    $password = $_POST['pass'];

    $stmt = $conn->prepare("SELECT * FROM signup WHERE (username = ? OR email = ?) AND password = ?");
    $stmt->bind_param("sss", $user, $user, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username']; // Create session with username
        header("Location: dashboard.php"); // Redirect to dashboard.php after successful login
        exit();
    } else {
        $error = "Login failed. Invalid username or password!";
    }
}

include('login_form.html');
?>
