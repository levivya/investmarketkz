<?
$HUI_WAM1 = "èõê ÷ù ôõô şôï-ôï òáúúäåîäéôå";
$HUI_WAM2 = "ÕÓÉ ÂÛ ÒÓÒ ×ÒÎ-ÒÎ ĞÀÇÇÄÅÍÄÈÒÅ";

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

$obj = new SiteObject("users");
$obj->Table = Array("table_name" => "users", "table_title" => $lang["page_title"]);
$page_title = $lang["page_title"];
$obj->Fields["users_id"]["field_title"] = "ID";
//$obj->Fields["users_groups_id"]["field_title"] = "Ãğóïïà ïîëüçîâàòåëÿ";
//$obj->Fields["users_groups_id"]["field_type"] = "enum";
$obj->Fields["users_name"]["field_title"] = $lang["title_users_fields_name"];
$obj->Fields["users_email"]["field_title"] = $lang["title_users_fields_email"];
$obj->Fields["users_login"]["field_title"] = $lang["title_users_fields_login"];
$obj->Fields["users_password"]["field_title"] = $lang["title_users_fields_password"];
$obj->Fields["users_password"]["field_type"] = "password";
$obj->Fields["users_active"]["field_title"] = $lang["title_users_fields_active"];

$obj->Options["objects_options_show_fields"] = "users_name,users_email,users_name as delete_name, IF(users_active = '', 'close_open.gif', 'old_close_open.gif') as action_image, IF(users_active = '', '".$lang["button_activate"]."', '".$lang["button_deactivate"]."') as action_title, IF(users_active = '', 'activate', 'deactivate') as action, IF(users_id = 1, '', '1') as `activate`, IF(users_id = 1, '', '1') as `delete`";

$obj->Options["objects_options_required_fields"] = Array(
                                                           Array("field_name" => "users_name", "js_error" => "users_name == ''", "as" => "!=''", "errormsg" => $lang["errors_users_fields_name"]),
                                                           Array("field_name" => "users_login", "js_error" => "!/^[a-zA-Z0-9_-]{5,10}$/.test(users_login)", "errormsg" => $lang["errors_users_fields_login"]),
                                                           Array("field_name" => "users_password", "js_error" => "users_password == ''", "errormsg" => $lang["errors_users_fields_password"])
                                                        );


//$obj->Options["objects_options_edit_template"] = "users.edit.html";
                                                                       //users_groups_id,
$obj->Options["objects_options_edit_fields"] = "users_name,users_email,users_login,users_password,users_active";
$obj->add = true;
$obj->activate = true;
$obj->edit = true;
$obj->delete = true;
$obj->items_per_page = 20;
$obj->HTTP_ROOT = $HTTP_ROOT; 

//$obj->Links["users_groups_id"] = Array("TableName" => "users_groups", "SourceFieldName" => "users_groups_id", "DestinationFieldName" => "users_groups_id", "DestinationTitleFieldName" => "users_groups_name", "Where" => "users_groups_id <> 2");
//$obj->Rubrikators["users_groups"] = Array("TableName" => "users_groups", "TableTitle" => "Ãğóïïà", "IDField" => "users_groups_id", "TitleField" => "users_groups_name", "Where" => "users_groups_id <> 2");
//if ($_SESSION["user"]->users_name != "root") $where[] = "users_id > 1";
switch($action) {
       case "group_action":
           if (is_array($_POST["selected"]) && in_array(1, $_POST["selected"])) $_POST["selected"] = array_diff($_POST["selected"], array(1));
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
           /*
           if ($_GET["item"] == 1 || $_POST["item"] == 1) {
              $obj->Options["objects_options_edit_fields"] = "users_name,users_email,users_password";
              $obj->Options["objects_options_required_fields"] = Array();
           }
           */
           if (!empty($_POST["users_password"]) && (($_POST["users_id"] > 0 && $_POST["users_password"] != mysql_result($db->query("select users_password from users where users_id = '".$_POST["users_id"]."'"), 0, 0)) || $_POST["users_id"] == 0)) {
              $_POST["users_password"] = mysql_result($db->query("select PASSWORD('".$_POST["users_password"]."')"), 0, 0);
           }
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