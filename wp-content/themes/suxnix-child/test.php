<?php /* Template Name: Test */
get_header();
 

 function mergeImagesWithThirdImage1($staticImagePath, $imagePath2, $outputPath, $thirdImagePath, $zoomFactor = 1.0, $marginTop = 0, $marginLeft = 0) {
    $image1Info = getimagesize($staticImagePath);
    $image1Width = $image1Info[0];
    $image1Height = $image1Info[1];

    $image1 = @imagecreatefromstring(file_get_contents($staticImagePath));

    if (!$image1) {
        die('Error loading static image');
    }

    $image2Info = getimagesize($imagePath2);
    $image2Width = $image2Info[0];
    $image2Height = $image2Info[1];

    $image2 = @imagecreatefromstring(file_get_contents($imagePath2));

    if (!$image2) {
        die('Error loading uploaded image');
    }

    $thirdImageInfo = getimagesize($thirdImagePath);
    $thirdImageWidth = $thirdImageInfo[0];
    $thirdImageHeight = $thirdImageInfo[1];

    $thirdImage = @imagecreatefromstring(file_get_contents($thirdImagePath));

    if (!$thirdImage) {
        die('Error loading third image');
    }

    // Calculate center position with margin
    $positionX = ($image1Width - $image2Width * $zoomFactor) / 2 + $marginLeft;
    $positionY = ($image1Height - $image2Height * $zoomFactor) / 2 + $marginTop;

    // Create a blank image with the size of the larger image
    $mergedImage = imagecreatetruecolor($image1Width, $image1Height);

    // Copy the static image onto the merged image
    imagecopy($mergedImage, $image1, 0, 0, 0, 0, $image1Width, $image1Height);

    // Copy the uploaded image onto the merged image with zoom, centered position, and margin
    imagecopyresampled($mergedImage, $image2, $positionX, $positionY, 0, 0, $image2Width * $zoomFactor, $image2Height * $zoomFactor, $image2Width, $image2Height);

    // Add the third image at the top
    imagecopyresampled($mergedImage, $thirdImage, 0, 0, 0, 0, $image1Width, $image1Height, $thirdImageWidth, $thirdImageHeight);

    // Save the merged image
    imagejpeg($mergedImage, $outputPath);

    // Free up memory
    imagedestroy($image1);
    imagedestroy($image2);
    imagedestroy($thirdImage);
    imagedestroy($mergedImage);
}

/*
function mergeImagesWithThirdImage1($staticImagePath, $imagePath2, $outputPath, $thirdImagePath, $zoomFactor = 1.0, $marginTop = 0, $marginLeft = 0) {
   $image1Info = getimagesize($staticImagePath);
    $image1Width = $image1Info[0];
    $image1Height = $image1Info[1];

    $image1 = @imagecreatefromstring(file_get_contents($staticImagePath));

    if (!$image1) {
        die('Error loading static image');
    }

    $image2Info = getimagesize($imagePath2);
    $image2Width = $image2Info[0];
    $image2Height = $image2Info[1];

    $image2 = @imagecreatefromstring(file_get_contents($imagePath2));

    if (!$image2) {
        die('Error loading uploaded image');
    }

    $thirdImageInfo = getimagesize($thirdImagePath);
    $thirdImageWidth = $thirdImageInfo[0];
    $thirdImageHeight = $thirdImageInfo[1];

    $thirdImage = @imagecreatefromstring(file_get_contents($thirdImagePath));

    if (!$thirdImage) {
        die('Error loading third image');
    }

    // Calculate center position with margin
    $positionX = ($image1Width - $image2Width * $zoomFactor) / 2 + $marginLeft;
    $positionY = ($image1Height - $image2Height * $zoomFactor) / 2 + $marginTop;

    // Create a blank image with the size of the larger image
    $mergedImage = imagecreatetruecolor($image1Width, $image1Height);

    // Copy the static image onto the merged image
    imagecopy($mergedImage, $image1, 0, 0, 0, 0, $image1Width, $image1Height);

    // Apply curvature to the second image
    $image2Curved = applyCurvature($image2, $image2Width, $image2Height, $zoomFactor);

    // Copy the curved second image onto the merged image with centered position and margin
    imagecopyresampled($mergedImage, $image2Curved, $positionX, $positionY, 0, 0, $image2Width * $zoomFactor, $image2Height * $zoomFactor, $image2Width, $image2Height);

    // Add the third image at the top
     imagecopyresampled($mergedImage, $thirdImage, 0, 0, 0, 0, $image1Width, $image1Height, $thirdImageWidth, $thirdImageHeight);

    // Save the merged image
    imagejpeg($mergedImage, $outputPath);

    // Free up memory
    imagedestroy($image1);
    imagedestroy($image2);
    imagedestroy($image2Curved);
    imagedestroy($thirdImage);
    imagedestroy($mergedImage);
}

function applyCurvature($image, $width, $height, $zoomFactor) {
    // Create a blank image for curved effect
    $curvedImage = imagecreatetruecolor($width, $height);

    // Loop through each pixel of the original image and apply curvature
    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
            $curvedX = $x;  // Apply your curvature logic here
            $curvedY = $y;  // Apply your curvature logic here

            // Copy the pixel from the original image to the curved image
            $color = imagecolorat($image, $x, $y);
            imagesetpixel($curvedImage, $curvedX, $curvedY, $color);
        }
    }

    // Perform resampling for the zoom factor
    $curvedImageResampled = imagecreatetruecolor($width * $zoomFactor, $height * $zoomFactor);
    imagecopyresampled($curvedImageResampled, $curvedImage, 0, 0, 0, 0, $width * $zoomFactor, $height * $zoomFactor, $width, $height);

    // Free up memory
    imagedestroy($curvedImage);

    return $curvedImageResampled;
}

 
 
  
/*
 function mergeImagesWithThirdImage1($staticImagePath, $imagePath2, $outputPath, $thirdImagePath, $zoomFactor = 1.0, $marginTop = 0, $marginLeft = 0) {
    $image1Info = getimagesize($staticImagePath);
    $image1Width = $image1Info[0];
    $image1Height = $image1Info[1];

    $image1 = @imagecreatefromstring(file_get_contents($staticImagePath));

    if (!$image1) {
        die('Error loading static image');
    }

    $image2Info = getimagesize($imagePath2);
    $image2Width = $image2Info[0];
    $image2Height = $image2Info[1];

    $image2 = @imagecreatefromstring(file_get_contents($imagePath2));

    if (!$image2) {
        die('Error loading uploaded image');
    }

    $thirdImageInfo = getimagesize($thirdImagePath);
    $thirdImageWidth = $thirdImageInfo[0];
    $thirdImageHeight = $thirdImageInfo[1];

    $thirdImage = @imagecreatefromstring(file_get_contents($thirdImagePath));

    if (!$thirdImage) {
        die('Error loading third image');
    }

    // Calculate center position with margin
    $positionX = ($image1Width - $image2Width * $zoomFactor) / 2 + $marginLeft;
    $positionY = ($image1Height - $image2Height * $zoomFactor) / 2 + $marginTop;

    // Create a blank image with the size of the larger image
    $mergedImage = imagecreatetruecolor($image1Width, $image1Height);

    // Copy the static image onto the merged image
    imagecopy($mergedImage, $image1, 0, 0, 0, 0, $image1Width, $image1Height);

    // Apply distortion effect to the second image along the y-axis
    $distortedImage = distortAndEmboss($image2, $image2Width, $image2Height, $zoomFactor);

    // Copy the distorted second image onto the merged image with zoom, centered position, and margin
    imagecopyresampled($mergedImage, $distortedImage, $positionX, $positionY, 0, 0, $image2Width * $zoomFactor, $image2Height * $zoomFactor, $image2Width, $image2Height);

    // Add the third image at the top
    imagecopyresampled($mergedImage, $thirdImage, 0, 0, 0, 0, $image1Width, $image1Height, $thirdImageWidth, $thirdImageHeight);

    // Save the merged image
    imagejpeg($mergedImage, $outputPath);

    // Free up memory
    imagedestroy($image1);
    imagedestroy($distortedImage);
    imagedestroy($thirdImage);
    imagedestroy($mergedImage);
}

function distortAndEmboss($image, $width, $height, $zoomFactor) {
    $distortedImage = imagecreatetruecolor($width, $height);

    for ($y = 0; $y < $height; $y++) {
        $offsetY = sin($y / $height * M_PI) * 52.5;

        // Apply curved effect
        imagecopyresampled($distortedImage, $image, 0, $y + $offsetY, 0, $y, $width, 1, $width, 1);
    }

    // Create a temporary image to apply emboss
    $embossedImage = imagecreatetruecolor($width, $height);
    imagecopy($embossedImage, $distortedImage, 0, 0, 0, 0, $width, $height);

    // Emboss the distorted image
   // imagefilter($embossedImage, IMG_FILTER_EMBOSS);

    // Copy the embossed region back to the distorted image
    imagecopy($distortedImage, $embossedImage, 0, 0, 0, 0, $width, $height);

    return $distortedImage;
}
*/
  

// Example usage:
//mergeImagesWithThirdImage1('static.jpg', 'label.jpg', 'output.jpg', 'third.jpg', 0.1, 0, 0);

 /*
function mergeImagesWithThirdImage1($staticImagePath, $imagePath2, $outputPath, $thirdImagePath, $zoomFactor = 1.0, $marginTop = 0, $marginLeft = 0, $curveAngle = 30) {
    $image1Info = getimagesize($staticImagePath);
    $image1Width = $image1Info[0];
    $image1Height = $image1Info[1];

    $image1 = @imagecreatefromstring(file_get_contents($staticImagePath));

    if (!$image1) {
        die('Error loading static image');
    }

    $image2Info = getimagesize($imagePath2);
    $image2Width = $image2Info[0];
    $image2Height = $image2Info[1];

    $image2 = @imagecreatefromstring(file_get_contents($imagePath2));

    if (!$image2) {
        die('Error loading uploaded image');
    }

    $thirdImageInfo = getimagesize($thirdImagePath);
    $thirdImageWidth = $thirdImageInfo[0];
    $thirdImageHeight = $thirdImageInfo[1];

    $thirdImage = @imagecreatefromstring(file_get_contents($thirdImagePath));

    if (!$thirdImage) {
        die('Error loading third image');
    }

    // Calculate center position with margin
    $positionX = ($image1Width - $image2Width * $zoomFactor) / 2 + $marginLeft;
    $positionY = ($image1Height - $image2Height * $zoomFactor) / 2 + $marginTop;

    // Create a blank image with the size of the larger image
    $mergedImage = imagecreatetruecolor($image1Width, $image1Height);

    // Copy the static image onto the merged image
    imagecopy($mergedImage, $image1, 0, 0, 0, 0, $image1Width, $image1Height);

    // Copy the uploaded image onto the merged image with zoom, centered position, and margin
    $rotatedImage = imagerotate($image2, $curveAngle, 0);
    imagecopyresampled($mergedImage, $rotatedImage, $positionX, $positionY, 0, 0, $image2Width * $zoomFactor, $image2Height * $zoomFactor, imagesx($rotatedImage), imagesy($rotatedImage));

    // Add the third image at the top
    //imagecopyresampled($mergedImage, $thirdImage, 0, 0, 0, 0, $image1Width, $image1Height, $thirdImageWidth, $thirdImageHeight);

    // Save the merged image
    imagejpeg($mergedImage, $outputPath);

    // Free up memory
    imagedestroy($image1);
    imagedestroy($rotatedImage); // Free up memory for the rotated image
    imagedestroy($thirdImage);
    imagedestroy($mergedImage);
} 

 */


$img1 = $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/background11.png";
$img2 = $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/capsules_png.png";
$img3 = $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/joint1.png";

$targetDir = $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$mergedFile = $targetDir ."output_merged_image.png";
 

// Emboss image2 based on the transparency of image3
//mergeImages1($img1, $img2, $mergedFile,$img3);
 // mergeImagesWithThirdImage1($img1, $img2, $mergedFile, $img3, 0.9, 10, 0); 
mergeImagesWithThirdImage1($img1, $img2, $mergedFile, $img3, 0.9, 70, 20);
 
// Display the result
echo '<img src="https://vitalaunch.io/wp-content/uploads/output_merged_image.png" alt="Merged Image" width="50%">';


  $src = media_sideload_image( 'https://vitalaunch.io/wp-content/uploads/2024/02/Sleep-Well-Gummies-1.jpg', null, null, 'src' ); 
                            
                            // convert the url to image id
                           echo  $image_id = attachment_url_to_postid( $src );
       echo 'hh';
   $order = new WC_Order( 6734 );
   print_r($order);
 get_footer();
?>
 