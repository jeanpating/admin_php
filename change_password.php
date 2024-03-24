<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeesdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if(isset($_SESSION['username1']) && isset($_POST['newPassword'])) {
    $newPassword = $_POST['newPassword'];
    // Validate and sanitize the new password here if necessary

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $username = $_SESSION['username1'];
    $sql = "UPDATE users SET user_pwd = ? WHERE user_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashedPassword, $username);
    $stmt->execute();

    if($stmt->affected_rows > 0) {
        echo json_encode(array("status" => "success"));
        exit;
    } else {
        echo json_encode(array("status" => "error", "message" => "Failed to update password: " . $conn->error));
        exit;
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request"));
    exit;
}

$conn->close();
?>
