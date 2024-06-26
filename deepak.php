<?php
// Create image instances
$dest = imagecreatefromgif(
'https://media.geeksforgeeks.org/wp-content/uploads/animateImages.gif');
$src = imagecreatefromgif(
'https://media.geeksforgeeks.org/wp-content/uploads/slider.gif');
 
// Copy and merge
imagecopymerge($dest, $src, 10, 10, 0, 0, 500, 200, 75);
 
// Output and free from memory
header('Content-Type: image/gif');
imagegif($dest);
 
imagedestroy($dest);
imagedestroy($src);
?>