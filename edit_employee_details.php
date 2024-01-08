<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link rel="stylesheet" type="text/css" href="styles/edit_employee_details.css">
</head>

<body>

<div class="container">

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeesdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleEditForm($conn);
}

$employeeId = isset($_GET['emp_id']) ? $_GET['emp_id'] : null;
$employeeId = filter_var($employeeId, FILTER_VALIDATE_INT);

if ($employeeId === false) {
    die("Invalid employee ID");
}

$sql = "SELECT * FROM employees WHERE emp_id = $employeeId";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    ?>
    <h2>Edit Employee</h2>
    <form method="post" action="">
        <?php
        foreach ($row as $columnName => $value) {
            if ($columnName === 'emp_id' || $columnName === 'picture_path' || $columnName === 'id') {
                continue;
            }
            ?>
            <label for="<?php echo $columnName; ?>"><?php echo ucfirst($columnName); ?>:</label>
            <input type="text" id="<?php echo $columnName; ?>" name="<?php echo $columnName; ?>" value="<?php echo htmlspecialchars($value); ?>" required>
        <?php } ?>

        <input type="submit" value="Save Changes" class="submit-button">
    </form>
    <div class="back-link">
        <a href='get_employee_details.php?emp_id=<?php echo $employeeId; ?>'>Back to Employee Details</a>
    </div>
    <?php
} else {
    echo "<p>No details found for the employee.</p>";
}

$conn->close();
?>

</div>

</body>
</html>

<?php
function handleEditForm($conn) {
    $employeeId = $_GET['emp_id'];
    
    // Define an array to store the columns that can be updated
    $updateableColumns = ['name', 'department', 'position', 'address', 'contact_number', 'email_address'];

    $sqlUpdates = [];

    // Loop through each potential updateable column
    foreach ($updateableColumns as $columnName) {
        // Skip columns that are not in the POST data
        if (isset($_POST[$columnName])) {
            $columnValue = $conn->real_escape_string($_POST[$columnName]);
            $sqlUpdates[] = "$columnName = '$columnValue'";
        }
    }

    // Construct the SQL query for updating
    $sql = "UPDATE employees SET " . implode(', ', $sqlUpdates) . " WHERE emp_id = $employeeId";

    if ($conn->query($sql) === TRUE) {
        // Redirect to the employee details page after the update
        header("Location: get_employee_details.php?emp_id=$employeeId");
        exit();
    } else {
        echo "Error updating employee details: " . $conn->error;
    }
}
?>
