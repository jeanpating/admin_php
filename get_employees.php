<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <title>Employee List</title>
    <style>
        body {
            /* padding: 20px; */
        }
    </style>
</head>
<body>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "employeesdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employees from the database
$sql = "SELECT emp_id, name, department FROM employees";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    ?>
    <div class="container">
        <h2>Employee List</h2>
        <div class="list-group">
            <?php
            while ($row = $result->fetch_assoc()) {
                ?>
                <a href='get_employee_details.php?emp_id=<?= $row["emp_id"] ?>' class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?= $row["name"] ?></h5>
                        <small><?= $row["department"] ?></small>
                    </div>
                    <p class="mb-1">Employee ID: <?= $row["emp_id"] ?></p>
                </a>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
} else {
    echo "0 results";
}

$conn->close();
?>

</body>
</html>
