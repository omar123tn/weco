<?php
session_start();
if(isset($_SESSION['username'])){
    header("Location: welcome.php");
    exit();
}

include("connection.php");

if(isset($_POST['submit'])){
    $username = mysqli_real_escape_string($conn, $_POST['user']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['pass']);
    $cpassword = mysqli_real_escape_string($conn, $_POST['cpass']);
    
    $sql = "SELECT * FROM signup WHERE username='$username'";
    $result = mysqli_query($conn, $sql);
    $count_user = mysqli_num_rows($result);

    $sql = "SELECT * FROM signup WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    $count_email = mysqli_num_rows($result);

    if($count_user == 0 && $count_email == 0){
        if($password == $cpassword){
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO signup(username, email, password) VALUES('$username', '$email', '$hash')";
            $result = mysqli_query($conn, $sql);
            if($result){
                header("Location: login.php");
                exit();
            }
        } else {
            echo '<script>
                alert("Passwords do not match");
                window.location.href = "signup.php";
            </script>';
        }
    } else {
        if($count_user > 0){
            echo '<script>
                alert("Username already exists!!");
                window.location.href = "signup.php";
            </script>';
        }
        if($count_email > 0){
            echo '<script>
                alert("Email already exists!!");
                window.location.href = "signup.php";
            </script>';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>s'inscrire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background-image: url('background3.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            animation: bgAnimation 30s infinite alternate;
        }

        @keyframes bgAnimation {
            0% { filter: brightness(0.9); }
            100% { filter: brightness(1); }
        }

        #form {
            background-color: rgba(255, 255, 255, 0.85);
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            margin: 50px auto;
            box-shadow: 0px 0px 15px 5px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1.5s ease-in-out, bounceIn 2s ease;
            transition: transform 0.3s ease-in-out, background-color 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes bounceIn {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-30px);
            }
            60% {
                transform: translateY(-15px);
            }
        }

        #form:hover {
            transform: scale(1.025);
            background-color: rgba(255, 255, 255, 0.95);
        }

        #heading {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
            animation: slideIn 1s ease-in-out;
        }

        @keyframes slideIn {
            from { margin-left: 100%; }
            to { margin-left: 0%; }
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border: 1px solid #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        input[type="submit"] {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <?php include("navbar.php"); ?>
    <br><br>
    <div id="form" class="container">
        <h1 id="heading">s'inscrire</h1>
        <form name="form" action="signup.php" method="POST">
            <label for="user">Saisissez votre nom d'utilisateur:</label>
            <input type="text" id="user" name="user" required><br>
            <label for="email">Entrez votre e-mail : </label>
            <input type="email" id="email" name="email" required><br>
            <label for="pass">Créer un mot de passe: </label>
            <input type="password" id="pass" name="pass" required><br>
            <label for="cpass">Retaper le mot de passe: </label>
            <input type="password" id="cpass" name="cpass" required><br>
            <input type="submit" id="btn" value="s'inscrire" name="submit">
        </form>
    </div>

    <script>
        function isValid() {
            var user = document.getElementById('user').value.trim();
            var email = document.getElementById('email').value.trim();
            var pass = document.getElementById('pass').value.trim();
            var cpass = document.getElementById('cpass').value.trim();

            if(user === "" || email === "" || pass === "" || cpass === "") {
                alert("All fields are required!");
                return false;
            }

            if(pass !== cpass) {
                alert("Passwords do not match!");
                return false;
            }

            return true;
        }

        document.forms['form'].onsubmit = isValid;
    </script>
</body>
</html>
