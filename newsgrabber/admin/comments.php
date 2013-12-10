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
$item = (!empty($_GET["item"])) ? $_GET["item"] : ((!empty($_POST["item"])) ? $_POST["item"] : "");
$action = !empty($_POST["group_action"]) ? "group_action" : $action;


class Comments extends SiteObject {

   function GetObject($id = 0, $convert = true) {
        $ret = parent::GetObject($id, $convert);
        $this->news_title = $GLOBALS["db"]->fetch("select news_title from news where news_id = ".$this->news_id);
        if (empty($this->news_title)) $this->news_title = "<font color=\"red\">not found</font>";
        return $ret;
   }


  function GetItems($where = Array(), $convert = true) {
        $get = parent::GetItems($where, $convert);
        foreach($get as $key => $item) {
            $n[] = $item["news_id"];
        }
        if (is_array($n) && sizeof($n) > 0) $titles = $GLOBALS["db"]->fetchall($GLOBALS["db"]->query("select news_id, news_title from news where news_id IN (".implode(", ", $n).")"), "news_id");
        foreach($get as $key => $item) {
            $get[$key]["news_title"] = !empty($titles[$item["news_id"]]["news_title"]) ? "<a href=/news/id_".$item["news_id"].">".$titles[$item["news_id"]]["news_title"]."</a>" /*"<a href=news.php?action=edit&item=".$item["news_id"].">".$titles[$item["news_id"]]["news_title"]."</a>"*/ : "<font color=\"red\">not found</font>";
            $get[$key]["comments_text"] = nl2br(htmlspecialchars($get[$key]["comments_text"], ENT_QUOTES));
        }
        return $get;
  }
}


$obj = new Comments("comments");
$obj->Table = Array("table_name" => "comments", "table_title" => $lang["page_title"]);
$page_title = $lang["page_title"];
$obj->Fields["comments_id"]["field_title"] = "ID";
$obj->Fields["comments_author"]["field_title"] = $lang["title_comments_fields_author"];
$obj->Fields["comments_email"]["field_title"] = $lang["title_comments_fields_email"];
$obj->Fields["comments_date"]["field_title"] = $lang["title_comments_fields_date"];
$obj->Fields["comments_text"]["field_title"] = $lang["title_comments_fields_text"];
$obj->Fields["comments_text"]["no_convert"] = true;

$obj->Options["objects_options_list_template"] = "comments.list.html";

$obj->Options["objects_options_show_fields"] = "comments_id,comments_date, news_id, comments_author, comments_text, comments_author as delete_name, CONCAT('<a href=\"mailto:', comments_email,'\">', comments_author,'</a>') as comments_author, DATE_FORMAT(comments_date, '%H:%i %d.%m.%Y') as comments_date";

$obj->Options["objects_options_edit_fields"] = "comments_author,comments_email,comments_text";
$obj->add = false;
$obj->activate = false;
$obj->edit = true;
$obj->delete = true;
$obj->items_per_page = 100;
$obj->HTTP_ROOT = $HTTP_ROOT;

switch($action) {
       case "group_action":
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
           if (!empty($_GET["search"])) {
              if (is_numeric($_GET["search"])) {
                 $where[] = "news_id = ".$_GET["search"];
              } else {
                 $n = $db->fetch($db->query("select news_id from news where news_trans_title = '".addslashes($_GET["search"])."'"), 0);
                 $where[] = $n > 0 ? "news_id = ".$n : "news_id = -1";
              }
              $obj->search = htmlspecialchars($_GET["search"], ENT_QUOTES);
           }
           $obj->Fields["comments_author"]["no_convert"] = true;
           $obj->Fields["news_title"]["no_convert"] = true;
           $order = (!empty($_GET["order"])) ? $_GET["order"] : ((!empty($_POST["order"])) ? $_POST["order"] : "");
           $sort = (!empty($_GET["sort"])) ? $_GET["sort"] : ((!empty($_POST["sort"])) ? $_POST["sort"] : "DESC");
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