<?php
require_once '../connection.php';

$admins = mysqli_query($conn, "SELECT AdminID, Password FROM admins");

while ($row = mysqli_fetch_assoc($admins)) {
    $adminID       = $row['AdminID'];
    $plainPassword = $row['Password'];

    // Skip if already hashed
    if (str_starts_with($plainPassword, '$2y$')) {
        echo "Admin ID $adminID is already hashed, skipping.<br>";
        continue;
    }

    $hashed = password_hash($plainPassword, PASSWORD_DEFAULT);
    mysqli_query($conn, "UPDATE admins SET Password = '$hashed' WHERE AdminID = $adminID");
    echo "Admin ID $adminID hashed successfully.<br>";
}

echo "<br>Done! Delete this file now.";
?>