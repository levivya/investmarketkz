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

$action = (!empty($_GET["action"])) ? $_GET["action"] : ((!empty($_POST["action"])) ? $_POST["action"] : "");
$action = !empty($_POST["group_action"]) ? "group_action" : $action;

class proxy_lists extends SiteObject {
        function GetItems($where, $convert = false) {
           global $db, $tpl;
           $get = parent::GetItems($where, $convert);
           foreach($get as $key => $list) {
              $get[$key]["proxy_lists_found"] = ($list["proxy_lists_status"] == "process") ? $list["proxy_lists_found"]."%" : $list["proxy_lists_found"];
              //$list[$key]["proxy_lists_status"] = ($list[$key]["proxy_lists_status"] == "start") ? "wait" : (($list[$key]["proxy_lists_status"] == "start") ? "" : "");
           }
           return $get;
        }
}



$obj = new proxy_lists("proxy_lists");
$obj->Table = Array("table_name" => "proxy_lists", "table_title" => $lang["page_title"]);
$page_title = $lang["page_title"];
$obj->Fields["proxy_lists_id"]["field_title"] = $lang["title_proxy_lists_id"];
$obj->Fields["proxy_lists_url"]["field_title"] = $lang["title_proxy_lists_url"];
$obj->Fields["proxy_lists_interval"]["field_title"] = $lang["title_proxy_lists_interval"];
$obj->Fields["proxy_lists_mask"]["field_title"] = $lang["title_proxy_lists_mask"];
$obj->Fields["proxy_lists_updated"]["field_title"] = $lang["title_proxy_lists_updated"];
$obj->Fields["proxy_lists_found"]["field_title"] = $lang["title_proxy_lists_found"];
$obj->Fields["proxy_lists_status"]["field_title"] = $lang["title_proxy_lists_status"];
                                                                                                    //proxy_lists_updated,proxy_lists_found,proxy_lists_status,
$obj->Options["objects_options_show_fields"] = "proxy_lists_id,proxy_lists_url,proxy_lists_interval,proxy_lists_url as delete_name";
$obj->Options["objects_options_required_fields"] = Array(
                                                           Array("field_name" => "proxy_lists_url", "js_error" => "proxy_lists_url == ''", "as" => "!=''", "errormsg" => $lang["error_proxy_lists_url"]),
                                                           Array("field_name" => "proxy_lists_interval", "js_error" => "proxy_lists_url == ''", "as" => "!=''", "errormsg" => $lang["error_proxy_lists_interval"]),
                                                           Array("field_name" => "proxy_lists_mask", "js_error" => "proxy_lists_mask == ''", "as" => "!=''", "errormsg" => $lang["error_proxy_lists_mask"])
                                                        );
$obj->Options["objects_options_edit_fields"] = "proxy_lists_url,proxy_lists_interval,proxy_lists_mask";

$obj->Options["objects_options_edit_template"] = "edit.proxy.lists.html";

$obj->add = true;
$obj->activate = false;
$obj->edit = true;
$obj->delete = true;
$obj->items_per_page = 20;
$where[] = "proxy_lists_deleted <> 'checked'";
$obj->HTTP_ROOT = $HTTP_ROOT;

switch($action) {
       case "group_action":
           if (is_array($_POST["selected"]) && sizeof($_POST["selected"]) > 0) {
               switch($_POST["group_action"]) {
                   case "delete":
                        $obj->Delete($_POST["selected"]);
                        break;
                   case "activate":
                        $obj->Activate($_POST["selected"]);
                        break;
                   case "deactivate":
                        $obj->DeActivate($_POST["selected"]);
                        break;
               }
           }
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
           break;
      case "activate":
           if ($_GET["item"] > 0) $obj->Activate($_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "deactivate":
           if ($_GET["item"] > 0) $obj->DeActivate($_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "delete":
           if ($_GET["item"] > 0) $obj->Delete($_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "test":
           if (strpos($_POST["proxy_lists_mask"], "host") < strpos($_POST["proxy_lists_mask"], "port")) {$host_key = 1; $port_key = 2;} else {$host_key = 2; $port_key = 1;}
           if (get_magic_quotes_runtime()) $_POST["proxy_lists_mask"] = stripslashes($_POST["proxy_lists_mask"]);

           /*
           $_POST["proxy_lists_mask"] = str_replace("{host}", "([a-z0-9\.\-_]+)", $_POST["proxy_lists_mask"]);
           $_POST["proxy_lists_mask"] = str_replace("{port}", "([\d]{1,})", $_POST["proxy_lists_mask"]);
           */
             $_POST["proxy_lists_mask"] = str_replace("{host}", "([\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3})", $_POST["proxy_lists_mask"]);
             $_POST["proxy_lists_mask"] = str_replace("{port}", "([\d]{2,4})", $_POST["proxy_lists_mask"]);
           $_POST["proxy_lists_mask"] = str_replace("|", "\\|", $_POST["proxy_lists_mask"]);
           echo $_POST["proxy_lists_mask"]."<br><br>";
           echo $_POST["proxy_lists_url"]."<br>";
           flush();

           $content = file_get_contents($_POST["proxy_lists_url"]);
           $content = preg_replace("![\s]{1,}!", "|", $content);
           $db->query("update proxy_lists set proxy_lists_updated = NOW() where proxy_lists_id = ".$_POST["proxy_lists_id"]);
           preg_match_all("!".$_POST["proxy_lists_mask"]."!ims", $content, $matches, PREG_SET_ORDER);
           echo "found: ".sizeof($matches)."<br>";
           foreach($matches as $proxy) {
             echo $proxy[$host_key].":".$proxy[$port_key]."<br>";
           }

           exit;
      case "edit":
           /*
           if (($_GET["item"] == "new" && sizeof($_POST) == 0) || !empty($_POST["proxy_list"])) {
              $obj->Options["objects_options_edit_template"] = "add.proxy.lists.html";
              if (!empty($_POST["proxy_list"])) {
                 $proxy_listsList = explode("\n", $_POST["proxy_list"]);
                 foreach($proxy_listsList as $proxy_lists) {
                    $proxy_lists = trim($proxy_lists);
                    if ($db->fetch($db->query("select count(proxy_lists_id) from proxy_lists where proxy_lists_url = '".$proxy_lists."'"), 0) == 0) {
                       $db->query("insert into proxy_lists (proxy_lists_url) values('".$proxy_lists."')");
                    }
                 }
                 header("Location: ".$_POST["referer"]);
                 exit;
              }
              $_POST["return"] = 1;
           }
           $_POST["proxy_lists_mask"] = $_POST["proxy_lists_mask"];
           */
           include("php/edit.php");
           break;
      default:
           $order = (!empty($_GET["order"])) ? $_GET["order"] : ((!empty($_POST["order"])) ? $_POST["order"] : "");
           $sort = (!empty($_GET["sort"])) ? $_GET["sort"] : ((!empty($_POST["sort"])) ? $_POST["sort"] : "ASC");
           include("php/items.php");
}

$tpl->fid_load("main", "index.main.html", "page_title");
$tpl->fid_array("content", $options);
$tpl->fid_array("main", $options);

foreach($tpl->files as $key => $value) {
   $tpl->fid_array($key, $lang);
}

$tpl->fid_show("main");

?>