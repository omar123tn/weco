<?php
session_start();
include('connection.php');

if(isset($_SESSION['username'])){
    header("Location: welcome.php");
    exit();
}

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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Se connecter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        .text-danger {
            color: #dc3545;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include "navbar.php"; ?>
    <br><br>
    <div id="form" class="container">
        <h1 id="heading">connecter</h1>
        <form name="form" action="login.php" method="POST" onsubmit="return isValid()">
            <label for="user">Entrez votre nom d'utilisateur/e-mail : </label>
            <input type="text" id="user" name="user" required><br>
            <label for="pass">Mot de passe: </label>
            <input type="password" id="pass" name="pass" required><br>
            <input type="submit" id="btn" value="connecter" name="submit">
            <?php if(isset($error)) { echo "<p class='text-danger'>$error</p>"; } ?>
        </form>
    </div>

    <script>
        function isValid() {
            var user = document.getElementById('user').value.trim();
            if(user === "") {
                alert("Enter username or email id!");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
