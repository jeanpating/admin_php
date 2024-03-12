<?php include_once('conns/server.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url("bg/bawa-login-bg.jpg");
            background-size: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <form action="login.php" method="post">
        <h2>Admin Login</h2>

        <?php 
        include_once('conns/errors.php'); 
        if(isset($_GET['wup'])){
        echo "<div class='error' style='text-align: center; border-radius: 8px; background-color: #E21818; color: white;'>".$_GET['wup']."</div>";
        }
        if(isset($_SESSION['msg'])){
        echo "<div class='error'>".$_SESSION['msg']."</div>";
        } 
        if(isset($_GET['out'])){
        echo "<div class='error' style='text-align: center; background-color: #C1F2B0; border-radius: 8px;'> Successfully logged out</div><br>";
        }
        ?>

        <label for="in" class="userlabel">User:
            <input id="in" type="text" name="uname" 
                <?php   include_once('conns/server.php'); 
                        session_start();
                        if(isset($_GET['user'])){  echo 'value="'. $_GET['user'].'"';}
            ?>>
        </label>

        <label for="pass" class="passlabel">Password:
            <input id="pass" type="password" name="upass">
        </label>

        <input type="submit" value="Okay" class="oksbut" name="oksbut">
    </form>
</body>
</html>
