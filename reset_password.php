<?php
include 'config.php'; // Include your database connection
session_start();

// Check if the reset form is submitted
if (isset($_POST['reset'])) {
    $uname = mysqli_real_escape_string($conn, $_POST['uname']);
    $new_password = mysqli_real_escape_string($conn, md5($_POST['new_password'])); // Hash the new password

    // Update the password in the database
    $query = "UPDATE student SET pword='$new_password' WHERE uname='$uname'";
    if (mysqli_query($conn, $query)) {
        echo "Password has been reset successfully.";
    } else {
        echo "Error resetting password: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Password</h1>
    <form action="#" method="post">
        <label for="uname">Username:</label>
        <input type="text" name="uname" required>
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required>
        <button type="submit" name="reset">Reset Password</button>
    </form>
</body>
</html>
