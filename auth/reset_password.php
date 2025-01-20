<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = mysqli_real_escape_string($conn, $_POST['token']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "UPDATE users SET password='$password', reset_token=NULL WHERE reset_token='$token'";
    if (mysqli_query($conn, $query)) {
        echo "Password reset successful. You can now <a href='login.php'>login</a>.";
    } else {
        echo "Invalid token.";
    }
} else if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
} else {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <form action="" method="POST">
        <h2>Reset Password</h2>
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <input type="password" name="password" placeholder="New Password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
