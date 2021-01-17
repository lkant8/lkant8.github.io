<?php
// Set the header
header("Content-type: image/png");

// Start the session
session_start();

// The string to select random characters
$seed = str_split('abcdefghijklmnopqrstuvwxyz0123456789');

// Randomize the characters
shuffle($seed);

// Select the first n characters
$text = null;
for($i = 1; $i <= 6; $i++) {
	$text .= $seed[$i];
}
$text = substr(mt_rand(100000, 999999), 0, 4);
// Explode the letters into separate array elements
$letters = str_split($text);

// Store the generated code into the _SESSION captcha
$_SESSION['captcha'] = $text;
 
// Define the Image Height & Width
$width = 55;
$height = 37;  

// Create the Image
$image = imagecreate($width, $height); 

// Set the background color
$black = imagecolorallocate($image, 255, 255, 255);
// Set the text color
$white = imagecolorallocate($image, 0, 0, 0);

$lg_color = mt_rand(130, 160);
$light_gray = imagecolorallocate($image, $lg_color, $lg_color, $lg_color);

// Set the font size
$font_size = 1;

// Letter position
$position = array(8, 18, 28, 38, 48, 58);

for($i = 0; $i < count($letters); $i++) {
	// Generate an rgb random value, from light gray to white
	$color = rand(0, 150);
	
	// Output the letters
	imagestring($image, 5, $position[$i], mt_rand(9, 11), $letters[$i], imagecolorallocate($image, $color, $color, $color));
}

// Output the $image, don't save the file name, set quality
imagepng($image, null, 9);
?>