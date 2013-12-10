<?
$HUI_WAM1 = "èõê ÷ù ôõô þôï-ôï òáúúäåîäéôå";
$HUI_WAM2 = "ÕÓÉ ÂÛ ÒÓÒ ×ÒÎ-ÒÎ ÐÀÇÇÄÅÍÄÈÒÅ";

/* ----------------------------------------------- */
/* --- nulled by someone for http://nulled.ws/ --- */
/* ----------------------------------------------- */

$_config_loaded = false;
@include ("config.php");
if ($_config_loaded !== true) {
   die("config.php not found");
}

if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__))) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__));

$page_title = $GLOBALS["options"]["page_title"];

$tpl->fid_load("content", "options.html", "page_title");

if ($_POST["action"] == "default") {
   $db->query("update options set options_value = options_default where options_editable = 'checked'");
   $new_lang = $db->fetch($db->query("select options_default from options where options_name = 'user_lang'"), 0);
   $_SESSION["set_lang"] = $GLOBALS["options"]["user_lang"] = $new_lang;
   if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/global.php")) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/global.php");
   if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__))) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__));
   $_POST = array();
}

if ($_POST["action"] == "save" && is_array($_POST["options"])) {
   $db->query("update options set options_value = '' where options_editable = 'checked' and options_type = 'checkbox'");
   foreach($_POST["options"] as $key => $value) {
        if ($key == "news_fields") {
           $temp = array();
           foreach($value as $k => $v) $temp[] = $k;
           $value = implode("|", $temp);
        }

        if ($key == "rss_imageurl" && $_POST["rss_imageurl_delete"] == "1") {
           if (file_exists($DOCUMENT_ROOT."/img/".$value)) !@unlink($DOCUMENT_ROOT."/img/".$value) ? $error = array("uploaderror" => "1", "errorMessage" => $lang["error_rss_imageurl_delete"]) : "";
           $value = "";
        }
        if ($key == "rss_imageurl" && !empty($_FILES["rss_imageurl"]["name"])) {
           if ($_FILES["rss_imageurl"]["error"] > 0) {
              $error = array("uploaderror" => "1", "errorMessage" => empty($error["errorMessage"]) ? $lang["error_rss_imageurl_load"] : $error["errorMessage"]."<br>".$lang["error_rss_imageurl_load"]);
           } elseif (!@copy($_FILES["rss_imageurl"]["tmp_name"], $DOCUMENT_ROOT."/img/".$_FILES["rss_imageurl"]["name"])) {
              $error = array("uploaderror" => "1", "errorMessage" => empty($error["errorMessage"]) ? $lang["error_rss_imageurl_save"] : $error["errorMessage"]."<br>".$lang["error_rss_imageurl_save"]);
           } else {
              $value = $_FILES["rss_imageurl"]["name"];
           }
           if (is_array($error)) $tpl->fid_array("content", $error, true);
        }
        $db->query("update options set options_value = '$value' where options_editable = 'checked' and options_name = '$key'");
   }
   $_SESSION["set_lang"] = $GLOBALS["options"]["user_lang"] = $_POST["options"]["user_lang"];
   if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/global.php")) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/global.php");
   if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__))) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__));
}

$options = $db->fetchall($db->query("select options.*, IF(options_type = 'text', '1', '') as `text`, IF(options_type = 'checkbox', '1', '') as `checkbox`, IF(options_type = 'plain', '1', '') as `plain` from options where options_editable = 'checked' order by options_name"));
foreach($options as $key => $opt) {
    if ($opt["options_name"] == "news_fields") {
       $temp = explode("|", $options[$key]["options_value"]);
       foreach($temp as $value) {
          $ttemp[$value] = "checked";
       }
       $temp = $ttemp;
       $options[$key] = array_merge($options[$key], $ttemp);
   }
   if ($opt["options_name"] == "user_lang") {
      $tpl->fid_select("content", "options[user_lang]", array(array("ru", "ru"), array("en", "en")), $opt["options_value"]);
   }
   if (is_string($opt["options_value"])) {
      $opt["options_value"] = htmlspecialchars($opt["options_value"]);
      $opt["options_value"] = str_replace("{", "&#123;", $opt["options_value"]);
      $opt["options_value"] = str_replace("}", "&#125;", $opt["options_value"]);
   }
   $options[$key][$opt["options_name"]] = $opt["options_value"];
   $tpl->fid_array("content", $options[$key], true);
}

//$tpl->fid_loop("content", "options", $options);

$tpl->fid_load("main", "index.main.html", "page_title");

foreach($tpl->files as $key => $value) {
   $tpl->fid_array($key, $lang);
}
$tpl->fid_show("main");

?>