<?php
/* Initialize the array that will hold all background image paths */
$bgArr = [];

/* Fetch the homepage background images from the database in display order */
$bgQuery = mysqli_query($conn, "
    SELECT ImagePath 
    FROM branch_images 
    WHERE Location = 'homepage'
    ORDER BY SortOrder ASC
");

/* Loop through the results and collect each image path into the array */
if ($bgQuery) {
    while ($row = mysqli_fetch_assoc($bgQuery)) {
        $bgArr[] = $row['ImagePath'];
    }
}
?>