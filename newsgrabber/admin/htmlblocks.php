<?
$HUI_WAM1 = "θυκ χω τυτ ώτο-το ςαϊϊδεξδιτε";
$HUI_WAM2 = "ΥΣΙ ΒΫ ÒΣÒ ΧÒΞ-ÒΞ ΠΐΗΗΔΕΝΔΘÒΕ";

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
$item = (!empty($_GET["item"])) ? $_GET["item"] : ((!empty($_POST["item"])) ? $_POST["item"] : "");
$action = !empty($_POST["group_action"]) ? "group_action" : $action;

$obj = new SiteObject("html_blocks");
$obj->Table = Array("table_name" => "html_blocks", "table_title" => $lang["page_title"]);
$page_title = $lang["page_title"];
$obj->Fields["html_blocks_id"]["field_title"] = $lang["title_html_blocks_id"];
$obj->Fields["html_blocks_name"]["field_title"] = $lang["title_html_blocks_name"];
$obj->Fields["html_blocks_text"]["field_title"] = $lang["title_html_blocks_text"];
$obj->Fields["html_blocks_page"]["field_title"] = $lang["title_html_blocks_page"];
$obj->Fields["html_blocks_tplname"]["field_title"] = $lang["title_html_blocks_tplname"];

$obj->Options["objects_options_show_fields"] = "html_blocks_name,html_blocks_tplname,html_blocks_page, html_blocks_name as delete_name";

$obj->Options["objects_options_required_fields"] = Array(
                                                           Array("field_name" => "html_blocks_name", "js_error" => "html_blocks_name == ''", "as" => "!=''", "errormsg" => $lang["error_html_blocks_name"]),
                                                           Array("field_name" => "html_blocks_tplname", "js_error" => "html_blocks_tplname == ''", "as" => "!=''", "errormsg" => $lang["error_html_blocks_tplname"])
                                                        );

$obj->Options["objects_options_edit_fields"] = "html_blocks_name,html_blocks_text,html_blocks_page,html_blocks_tplname";
$obj->add = true;
$obj->activate = false;
$obj->edit = true;
$obj->delete = true;
$obj->items_per_page = 20;
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
           if ($_GET["item"] > 1) $obj->Activate($_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "deactivate":
           if ($_GET["item"] > 1) $obj->DeActivate($_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "delete":
           if (is_numeric($_GET["item"]) && $_GET["item"] > 1) {
                $obj->Delete($_GET["item"]);
           }
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "edit":
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