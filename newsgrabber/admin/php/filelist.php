
<?
include ("../../config.php");
?>
<HEAD>
    <link href="<?=$HTTP_ROOT;?>tinymce/jscripts/tiny_mce/themes/advanced/css/editor_popup.css" rel="stylesheet" type="text/css" />
</HEAD>
<BODY>
<?
$filter = explode(",", strtolower($_GET['filter']));
if (sizeof($filter) < 1) $filter = false;
$i = 0;
$types = Array(1 => "GIF", 2 => "JPG", 3 => "PNG", 4 => "SWF", 5 => "PSD", 6 => "BMP", 7 => "TIFF", 8 => "TIFF", 9 => "JPC", 10 => "JP2", 11 => "JPX", 12 => "JB2", 13 => "SWC", 14 => "IFF", 15 => "WBMP", 16 => "XBM");

$dir = $_SERVER["DOCUMENT_ROOT"]."/";
$did = 0;
function getTree($dir) {
  global $types, $did;

  $files = array();
  echo "<table border=0>\n";
  $d = dir($dir);
  while (false !== ($entry = $d->read())) {
    clearstatcache();
    if($entry != "." && $entry != "..") {
      if (is_dir($dir.$entry)) {
         $http_dir = str_replace(substr(HOMEDIR, 0, -1*strlen(HTTP_ROOT)), "", $dir.$entry);
         echo "<tr><td style=\"cursor: hand;\" width=\"1\" align=\"left\"><img src=\"".HTTP_ROOT."admin/php/icons.php?name=dir\" onClick=\"document.getElementById(".$did.").style.display = (document.getElementById(".$did.").style.display == 'none') ? '' : 'none';\">&nbsp;<a href=\"javascript:parent.filelist_OnFileSelect('".$http_dir."/', '',0,0,'dir');\"><nobr>".$entry."</nobr></a></td></tr>\n";
         echo "<tr style=\"display: none;\" id=\"".$did."\"><td style=\"padding-left: 19px\">\n";
         $did++;
         getTree($dir.$entry."/");
         echo "</td></tr>\n";

      } elseif (!$GLOBALS["filter"] || in_array(strtolower(substr($entry, -3)), $GLOBALS["filter"])) {
         $file = $entry;
         $dir1 = str_replace("\\", "/", str_replace(substr(HOMEDIR, 0, -1*strlen(HTTP_ROOT)), "", $dir));
         if (substr($entry, -3) == "swf" && $t == "") {
            $temp = "'".$dir1."', '".$file."', '100', '100', 'SWF'";
         } else {
            $temp = "'".$dir1."', '".$file."', '".$t[0]."', '".$t[1]."', '".$types[$t[2]]."'";
         }

         $files[] = "<tr><td><nobr><a href=\"javascript:parent.filelist_OnFileSelect(".$temp.");\">".$entry."</a></nobr></td></tr>\n";
      }
    }
  }
  foreach($files as $file) {
     echo $file;
  }
  $d->close();
  echo "</table>\n";
}


getTree($dir);
?>
</BODY>
<?
die();
?>