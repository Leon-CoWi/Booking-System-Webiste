<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit(); }
require_once '../connection.php';

$success = "";
$error = "";

/* HANDLE FORMS */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /* CHANGE PASSWORD */
    if (isset($_POST['change_password'])) {
        $current  = $_POST['current_password'];
        $new      = $_POST['new_password'];
        $confirm  = $_POST['confirm_password'];
        $adminID  = $_SESSION['adminID'];

        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM admins WHERE AdminID = $adminID"));

        if (!$check || !password_verify($current, $check['Password'])) {
            $error = "Current password is incorrect.";
        } elseif ($new !== $confirm) {
            $error = "New passwords do not match.";
        } else {
            $hashedNew = password_hash($new, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE admins SET Password = '$hashedNew' WHERE AdminID = $adminID");
            $success = "Password changed successfully!";
        }
    }

    /* ADD ADMIN */
    if (isset($_POST['add_admin'])) {
        $uname = mysqli_real_escape_string($conn, $_POST['new_username']);
        $pass  = $_POST['new_admin_password'];
        $confirm = $_POST['confirm_admin_password'];

        if ($pass !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            $exists = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM admins WHERE Username = '$uname'"));

            if ($exists) {
                $error = "Username already exists.";
            } else {
                $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
                mysqli_query($conn, "INSERT INTO admins (Username, Password) VALUES ('$uname', '$hashedPassword')");
                $success = "New admin added successfully!";
            }
        }
    }
}

/* LOAD ADMINS */
$admins = mysqli_query($conn, "SELECT * FROM admins");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings - Admin</title>
    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-shell">
    <?php include 'sidebar.php'; ?>

    <div class="admin-content">

        <!-- HEADER -->
        <div class="admin-topbar">
            <h2>Settings</h2>
        </div>

        <!-- MESSAGES -->
        <?php if($success): ?><div class="success-msg"><?= $success ?></div><?php endif; ?>
        <?php if($error): ?><div class="error-msg"><?= $error ?></div><?php endif; ?>

        <div class="settings-grid">

            <!-- CHANGE PASSWORD -->
            <div class="settings-card">
                <h3 style="color:#c9a84c; margin-bottom:16px;">Change Password</h3>

                <form method="POST">
                    <div class="form-field">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>

                    <div class="form-field">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                    </div>

                    <div class="form-field">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>

                    <button type="submit" name="change_password" style="margin-top:10px;">
                        Save Password
                    </button>
                </form>
            </div>

            <!-- ADD ADMIN -->
            <div class="settings-card">
                <h3 style="color:#c9a84c; margin-bottom:16px;">Add Admin Account</h3>

                <form method="POST">
                    <div class="form-field">
                        <label>Username</label>
                        <input type="text" name="new_username" required>
                    </div>

                    <div class="form-field">
                        <label>Password</label>
                        <input type="password" name="new_admin_password" required>
                    </div>

                    <div class="form-field">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_admin_password" required>
                    </div>

                    <button type="submit" name="add_admin" style="margin-top:10px;">
                        Add Admin
                    </button>
                </form>
            </div>

            <!-- ADMIN LIST -->
            <div class="settings-card" style="grid-column: span 2;">
                <h3 style="color:#c9a84c; margin-bottom:16px;">Admin Accounts</h3>

                <div class="table-wrap">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Action</th>
                        </tr>

                        <?php while($a = mysqli_fetch_assoc($admins)): ?>
                        <tr>
                            <td>#<?= $a['AdminID'] ?></td>
                            <td><?= htmlspecialchars($a['Username']) ?></td>
                            <td>
                                <?php if($a['AdminID'] != $_SESSION['adminID']): ?>
                                    <a href="delete_admin.php?id=<?= $a['AdminID'] ?>" onclick="return confirm('Delete this admin?')">
                                        <button class="btn-xs del">Delete</button>
                                    </a>
                                <?php else: ?>
                                    <span style="color:#555; font-size:11px;">Current</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>

            </div>

        </div>

    </div>
</div>
</body>
</html>