<?php
session_start();
require_once '../connection.php';

// login error
$error = "";

// handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // inputs
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // get admin
    $adminCheck = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT * FROM admins WHERE Username = '$username'"
    ));

    // check password
    if ($adminCheck && password_verify($password, $adminCheck['Password'])) {

        // set session
        $_SESSION['admin'] = $adminCheck['Username'];
        $_SESSION['adminID'] = $adminCheck['AdminID'];

        // redirect
        header('Location: admin.php');
        exit();
    }

    // error
    $error = "Invalid email or password.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Comodo</title>
    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="login-wrap">
    <div class="login-box">

        <!-- title -->
        <h2>Comodo</h2>

        <!-- subtitle -->
        <p style="color:#888; font-size:12px; margin-bottom:24px;">
            Sign in to your account
        </p>

        <!-- error -->
        <?php if($error): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>

        <!-- form -->
        <form method="POST">

            <!-- username -->
            <div class="form-field">
                <label>Email</label>
                <input type="text" name="username" required>
            </div>

            <!-- password -->
            <div class="form-field">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <!-- submit -->
            <button type="submit" style="width:100%; margin-top:10px;">
                Sign In
            </button>

        </form>
    </div>
</div>

</body>
</html>