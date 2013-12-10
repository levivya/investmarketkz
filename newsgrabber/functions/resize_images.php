<?
function resize_images($source) {
    clearstatcache();
    if (empty($source) || !file_exists(DOWNLOAD_IMAGES_DIR.$source) || !is_file(DOWNLOAD_IMAGES_DIR.$source)) return false;
    $isizes = !empty($GLOBALS["options"]["rss_sizes"]) ? explode(",", $GLOBALS["options"]["rss_sizes"]) : array();
    if (is_array($isizes)) {
       if (!empty($GLOBALS["options"]["rss_mogrify_path"]) && file_exists($GLOBALS["options"]["rss_mogrify_path"])) {
          foreach($isizes as $s) {
             $s = explode(":", trim($s));
             $wh_name = $s[0].$s[1];
             $wh_name = "prw_".$s[0]."x".$s[1]."_of_";
             $s[0] = intval($s[0]);
             $s[1] = intval($s[1]);
             $s = $s[0] > 0 && $s[1] > 0 ? $s[0]."x".$s[1] : ($s[0] == 0 ? "10000x".$s[1] : $s[0]."x10000");
             $s = $s;
             copy(DOWNLOAD_IMAGES_DIR.$source, DOWNLOAD_IMAGES_DIR.$wh_name.$source);
             @system($GLOBALS["options"]["rss_mogrify_path"]." -quality 75 -geometry $s ".DOWNLOAD_IMAGES_DIR.$wh_name.$source);
          }
       } elseif ($GLOBALS["options"]["rss_usegd"] == "checked") {
          foreach($isizes as $s) {
             $dst_size = explode(":", trim($s));
             $wh_name = $dst_size[0].$dst_size[1];
             $wh_name = "prw_".$dst_size[0]."x".$dst_size[1]."_of_";
             $width_dst = intval($dst_size[0]);
             $height_dst = intval($dst_size[1]);

             $src_size = getimagesize(DOWNLOAD_IMAGES_DIR.$source);
             $width_src = intval($src_size[0]);
             $height_src = intval($src_size[1]);

             if ($width_dst == 0 && $height_dst > 0) $width_dst = ceil(($height_dst*$width_src) / $height_src);
             if ($height_dst == 0 && $width_dst > 0) $height_dst = ceil(($width_dst*$height_src) / $width_src);
             if ($width_dst == 0) $width_dst = $width_src;
             if ($height_dst == 0) $height_dst = $height_src;
             $im1 = imagecreatetruecolor($width_dst, $height_dst);
             switch($src_size[2]) {
                case 1:
                    $im = imagecreatefromgif(DOWNLOAD_IMAGES_DIR.$source);
                    break;
                case 2:
                    $im = imagecreatefromjpeg(DOWNLOAD_IMAGES_DIR.$source);
                    break;
                case 3:
                    $im = imagecreatefrompng(DOWNLOAD_IMAGES_DIR.$source);
                    break;
             }
             imagecopyresized ($im1, $im, 0, 0, 0, 0, $width_dst, $height_dst, $width_src, $height_src);
             $image = DOWNLOAD_IMAGES_DIR.$wh_name.$source;
             if (file_exists($image)) @unlink($image);
             if (!file_exists($image))
             switch($src_size[2]) {
                case 1:
                    imagegif($im1, $image);
                    break;
                case 2:
                    imagejpeg($im1, $image, 75);
                    break;
                case 3:
                    imagepng($im1, $image);
                    break;
             }
             imagedestroy ($im1);
             imagedestroy ($im);
          }
       }
    }
}
?>