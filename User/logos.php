<?php
$casaLogo = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT ImagePath 
     FROM branch_images 
     WHERE Location = 'logos_casa' 
     ORDER BY SortOrder DESC 
     LIMIT 1"
));

$vfLogo = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT ImagePath 
     FROM branch_images 
     WHERE Location = 'logos_vf' 
     ORDER BY SortOrder DESC 
     LIMIT 1"
));

$websiteLogo = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT ImagePath 
     FROM branch_images 
     WHERE Location = 'website_logo' 
     ORDER BY SortOrder DESC 
     LIMIT 1"
));
?>
