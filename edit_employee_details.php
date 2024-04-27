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
            if ($columnName === 'emp_id' || $columnName === 'name' || $columnName === 'picture_path' || $columnName === 'id') {
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
    $employeeId = filter_var($_GET['emp_id'], FILTER_VALIDATE_INT);
    if ($employeeId === false) {
        die("Invalid employee ID");
    }

    $updateableColumns = ['department', 'position', 'address', 'contact_number', 'email_address'];
    $sqlUpdates = [];
    $errors = [];

    // Validate contact number and email address
    if (isset($_POST['contact_number'])) {
        $contactNumber = $_POST['contact_number'];
        if (strlen($contactNumber) !== 11 || !ctype_digit($contactNumber)) {
            $errors[] = "Contact number must be exactly 11 digits.";
        }
    }

    if (isset($_POST['email_address'])) {
        $emailAddress = $_POST['email_address'];
        $allowedDomains = ['@gmail.com', '@yahoo.com', '@email.com'];
        $isEmailValid = false;

        foreach ($allowedDomains as $domain) {
            if (str_ends_with($emailAddress, $domain)) {
                $isEmailValid = true;
                break;
            }
        }

        if (!$isEmailValid) {
            $errors[] = "Email address must end with @gmail.com, @yahoo.com, or @email.com.";
        }
    }

    // If there are errors, show them and stop
    if (count($errors) > 0) {
        echo "<div class='error-messages'>";
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        echo "</div>";
        return; // Stop processing if there's an error
    }

    // Build the SQL updates for valid inputs
    foreach ($updateableColumns as $columnName) {
        if (isset($_POST[$columnName])) {
            $columnValue = $conn->real_escape_string($_POST[$columnName]);
            $sqlUpdates[] = "$columnName = '$columnValue'";

            // If the column is email_address, update the emp_acc table as well
            if ($columnName === 'email_address') {
                $updateEmpAccSql = "UPDATE emp_acc SET emp_email = '$columnValue' WHERE emp_id = $employeeId";
                if ($conn->query($updateEmpAccSql) !== TRUE) {
                    echo "Error updating emp_acc: " . $conn->error;
                    return;
                }
            }
        }
    }

    if (count($sqlUpdates) > 0) {
        $sql = "UPDATE employees SET " . implode(', ', $sqlUpdates) . " WHERE emp_id = $employeeId";

        if ($conn->query($sql) === TRUE) {
            header("Location: get_employee_details.php?emp_id=$employeeId");
            exit();
        } else {
            echo "Error updating employee details: " . $conn->error;
        }
    }
}
?>