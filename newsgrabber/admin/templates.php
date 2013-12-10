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

$action = (!empty($_POST["action"])) ? ($_POST["action"] == "save" ? "edit" : $_POST["action"]) : ((!empty($_GET["action"])) ? $_GET["action"] : "");
$item = (!empty($_GET["item"])) ? $_GET["item"] : ((!empty($_POST["item"])) ? $_POST["item"] : "");
$action = !empty($_POST["group_action"]) ? "group_action" : $action;


class Templates extends SiteObject {

   function Delete($id) {
        global $db;
        if (!is_array($id)) $ids[] = $id; else $ids = $id;
        foreach($ids as $id) {
            $this->GetObject($id);
            if ($this->templates_type == 'css') {
                if (!empty($this->templates_name) && file_exists($GLOBALS["DOCUMENT_ROOT"]."/css/".$this->templates_name)) {
                    if (!@unlink($GLOBALS["DOCUMENT_ROOT"]."/css/".$this->templates_name)) {
                        $this->Error = true;
                        if (!empty($this->ErrorMessage)) $this->ErrorMessage .= "<br>";
                        $this->ErrorMessage .= $GLOBALS["lang"]["errors_cssnoaccess"];
                    }
                }
            }
        }
        parent::Delete($ids);
   }

   function GetObject($id = 0, $convert = true) {
        $ret = parent::GetObject($id, $convert);
        $this->istemplate = true;
        if ($this->templates_type == "css") {
           $this->istemplate = false;
           if (!file_exists($GLOBALS["DOCUMENT_ROOT"]."/css/".$this->templates_name)) {
              $this->Error = true;
              $this->ErrorMessage = $GLOBALS["lang"]["errors_cssnotexists"];
           } else {
              $this->templates_body = @file_get_contents($GLOBALS["DOCUMENT_ROOT"]."/css/".$this->templates_name);
              if (!$this->templates_body) {
                 $this->Error = true;
                 $this->ErrorMessage = $GLOBALS["lang"]["errors_cssnoaccess"];
              }
           }
           $ret = $this->Error;
        }
        if (!empty($this->templates_body) && get_magic_quotes_runtime()) $this->templates_body = stripslashes($this->templates_body);
        return $ret;
   }

   function SaveObject($array = array()) {
        if ($_POST["templates_id"] > 0) {
            $temp = $this->Options["objects_options_edit_fields"];
            $this->Options["objects_options_edit_fields"] = "templates_name,templates_title,templates_body,templates_type";
            $ret = parent::GetObject($_POST["templates_id"]);
            $this->Options["objects_options_edit_fields"] = $temp;
            $_POST["templates_type"] = "template";
            if ($this->templates_type == "css") {
               $_POST["templates_type"] = "css";
               $f = @fopen($GLOBALS["DOCUMENT_ROOT"]."/css/".$this->templates_name, "wb");
               if (is_resource($f)) {
                    $_POST["templates_body"] = get_magic_quotes_gpc() ? stripcslashes($_POST["templates_body"]) : $_POST["templates_body"];
                    if (fwrite($f, $_POST["templates_body"])) {
                       $_POST["templates_body"] = " ";
                       fclose($f);
                    } else {
                       $this->Error = true;
                       $this->ErrorMessage = $GLOBALS["lang"]["errors_cssnoaccess"];
                       return false;
                    }
               } else {
                    $this->Error = true;
                    $this->ErrorMessage = $GLOBALS["lang"]["errors_cssnoaccess"];
                    return false;
               }
            }
        }

        $save = parent::SaveObject($array);
        return $save;
   }

}

$obj = new Templates("templates");
$obj->Table = Array("table_name" => "templates", "table_title" => $lang["page_title"]);
$page_title = $lang["page_title"];
$obj->Fields["templates_id"]["field_title"] = $lang["title_templates_id"];
$obj->Fields["templates_name"]["field_title"] = $lang["title_templates_name"];
$obj->Fields["templates_title"]["field_title"] = $lang["title_templates_title"];
$obj->Fields["templates_body"]["field_title"] = $lang["title_templates_body"];

$obj->Options["objects_options_show_fields"] = "templates_name,templates_title, CONCAT(templates_title, ' (', templates_name, ')') as delete_name, IF(templates_type = 'css', '1', '') as `delete`";

$obj->Options["objects_options_edit_fields"] = "templates_name,templates_title,templates_body,templates_type";
$obj->Options["objects_options_save_fields"] = "templates_title,templates_body,templates_type";
$obj->Options["objects_options_edit_template"] = "templates.edit.html";
$obj->Options["objects_options_list_template"] = "templates.list.html";
$obj->add = false;
$obj->activate = false;
$obj->edit = TEMPLATES_EDIT;
$obj->delete = true;
$obj->items_per_page = 20;
$obj->HTTP_ROOT = $HTTP_ROOT;
$where[] = "templates_type <> ''";

switch($action) {
       case "group_action":
           if (is_array($_POST["selected"]) && sizeof($_POST["selected"]) > 0) {
               switch($_POST["group_action"]) {
                   case "delete":
                        if ($obj->delete) $obj->Delete($_POST["selected"]);
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
           if (is_numeric($_GET["item"]) && $_GET["item"] > 1 && $obj->delete) {
                $obj->Delete($_GET["item"]);
           }
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "preview":
           $_GET["item"] = intval($_GET["item"]);
           $obj->GetObject($_GET["item"]);
           $_SESSION["use_template"] = array();
           switch(true) {
             case $obj->templates_name == "index.main": $template_name = $obj->templates_name.".html"; $url = $HTTP_ROOT."?template_preview=1"; break;
             case $obj->templates_name == "main": $template_name = $obj->templates_name.".html"; $url = $HTTP_ROOT."?template_preview=1"; break;
             case $obj->templates_name == "404": $template_name = $obj->templates_name.".html"; $url = $HTTP_ROOT."unknown.html?template_preview=1"; break;
             case $obj->templates_name == "news.groups":
                  $template_name = "news.groups.html";
                  $g = $db->fetch($db->query("SELECT groups_dir, COUNT(*) as `count` FROM `groups` LEFT JOIN news USING (groups_id) WHERE groups_owner = 0 GROUP by groups.groups_id ORDER by `count` DESC LIMIT 1"), 0);
                  $url = $HTTP_ROOT."".$g."/";
                  break;
             case $obj->templates_name == "calendar" || $obj->templates_name == "menu" || $obj->templates_name == "news.subgroups":
                  $template_name = "news.subgroups.html";
                  $g1 = $db->fetch($db->query("SELECT groups_owner, groups_dir, COUNT(*) as `count` FROM `groups` LEFT JOIN news USING (groups_id) WHERE groups_owner > 0 GROUP by groups.groups_id ORDER by `count` DESC LIMIT 1"));
                  if ($g1["groups_owner"] > 0) {
                        $g = $db->fetch($db->query("SELECT groups_dir FROM `groups` WHERE groups_id = ".$g1["groups_owner"]." LIMIT 1"), 0);
                        $url = $HTTP_ROOT."".$g."/".$g1["groups_dir"]."/";
                  } else {
                        $g = $db->fetch($db->query("SELECT groups_dir FROM `groups` LIMIT 1"), 0);
                        $url = $HTTP_ROOT."".$g."/";
                  }
                  break;
             case $obj->templates_name == "news.view":
                  $template_name = "news.view.html";
                  $g = $db->fetch($db->query("SELECT groups_dir, news_id, COUNT(*) as `count` FROM `groups` LEFT JOIN news USING (groups_id) WHERE groups_owner = 0 GROUP by groups.groups_id ORDER by `count` DESC LIMIT 1"));
                  $url = $HTTP_ROOT."".$g["groups_dir"]."/id_".$g["news_id"]."/";
                  break;
             case $obj->templates_name == "content":
                  $template_name = "content";
                  $url = $HTTP_ROOT."test/";
                  break;
             case $obj->templates_name == "news.all" || $obj->templates_name == "pages":
                  $template_name = "news.all.html";
                  $url = $HTTP_ROOT."desc/";
                  break;
             case $obj->templates_name == "catalog":
                  $template_name = "catalog";
                  $url = $HTTP_ROOT."catalog/";
                  break;
           }
           if (get_magic_quotes_gpc()) $_POST["templates_body"] = stripslashes($_POST["templates_body"]);
           $_SESSION["use_template"][$obj->templates_name.".html"] = $_POST["templates_body"];
           header("Location: $url");
           exit;
      case "restore":
           $_GET["item"] = intval($_GET["item"]);
           if ($_GET["item"] > 0) $db->query("update templates set templates_body = templates_source where templates_id = ".$_GET["item"]);
      case "edit":
           if (!$obj->add && $_GET["item"] == "new") {
              header("Location: ".$HTTP_ROOT."admin/templates.php");
              exit;
           }
           if ($obj->add || $obj->edit) include("php/edit.php");
           $tpl->fid_object("content", $obj);
           break;
      default:
           if (is_uploaded_file($_FILES["newcss"]["tmp_name"])) {
              switch(true) {
                 case $_FILES["newcss"]["error"] > 0:
                      $obj->Error = true;
                      $obj->ErrorMessage = $lang["errors_cssload"];
                      break;
                 case strtolower(substr($_FILES["newcss"]["name"], -3)) != "css":
                      $obj->Error = true;
                      $obj->ErrorMessage = $lang["errors_csstypeerror"];
                      break;
                 case preg_match("!echo|foreach|for(|for (|if (|if(|print (|print(|die\(|mysql_|<\?|\?>!ms", file_get_contents($_FILES["newcss"]["tmp_name"]), $r):
                      $obj->Error = true;
                      $obj->ErrorMessage = $lang["errors_csstypeerror"];
                      break;
                 case !@copy($_FILES["newcss"]["tmp_name"], $DOCUMENT_ROOT."/css/".$_FILES["newcss"]["name"]):
                      $obj->Error = true;
                      $obj->ErrorMessage = $lang["errors_csssave"];
                      break;
                 default:
                      if ($db->fetch("select count(*) from templates where templates_groups_id = '".$options["templates_groups"]."' and templates_name = '".addslashes($_FILES["newcss"]["name"])."'", 0) == 0) {
                         $db->query("INSERT into templates (templates_groups_id, templates_name, templates_title, templates_type) VALUES ('".$options["templates_groups"]."', '".addslashes($_FILES["newcss"]["name"])."', '".addslashes($_FILES["newcss"]["name"])."', 'css')");
                         if (mysql_errno() > 0) {
                            $obj->Error = true;
                            $obj->ErrorMessage = $lang["errors_csssavedb"]."<br>".mysql_error();
                         }
                      }
              }
           }

           $obj->TEMPLATES_GROUPS_ADD = TEMPLATES_GROUPS_ADD;
           if (!empty($_POST["setTemplate"])) {
              $options["templates_groups"] = $_POST["templates_groups"];
              $db->query("update options set options_value = '".$_POST["templates_groups"]."' where options_name = 'templates_groups'");
              header("Location: ".$_SERVER["REQUEST_URI"]);
              exit;
           }
           if (!empty($_POST["copyTemplate"])) {
              $_POST["template_name"] = empty($_POST["template_name"]) ? "Íîâûé íàáîð" : $_POST["template_name"];
              $db->query("INSERT into templates_groups (templates_groups_name) VALUES ('{template_name}')", $_POST);
              $g = mysql_insert_id();
	      $db->query("INSERT into templates (templates_groups_id, templates_name, templates_title, templates_body, templates_source, templates_type) SELECT $g, templates_name, templates_title, templates_body, templates_source, templates_type FROM templates where templates_type <> 'css' and templates_groups_id = '{templates_groups}'", $_POST);
              header("Location: ".$_SERVER["REQUEST_URI"]);
              exit;
           }

           if (!empty($_POST["deleteTemplate"])) {
              $db->query("DELETE FROM templates_groups WHERE templates_groups_id = '{templates_groups}'", $_POST);
              $ttt = $db->fetchccol($db->query("SELECT templates_id FROM templates WHERE templates_groups_id = '{templates_groups}'", $_POST));
              $obj->Delete($ttt);
              if ($options["templates_groups"] == $_POST["templates_groups"]) {
                  $options["templates_groups"] = $db->fetch("select templates_groups_id from templates_groups order by templates_groups_id asc limit 1", 0);
                  $db->query("update options set options_value = '".$options["templates_groups"]."' where options_name = 'templates_groups'");
              }
              header("Location: ".$_SERVER["REQUEST_URI"]);
              exit;
           }
           $where[] = "templates_groups_id = '".$options["templates_groups"]."'";

           $order = (!empty($_GET["order"])) ? $_GET["order"] : ((!empty($_POST["order"])) ? $_POST["order"] : "templates_type");
           $sort = (!empty($_GET["sort"])) ? $_GET["sort"] : ((!empty($_POST["sort"])) ? $_POST["sort"] : "ASC");
           include("php/items.php");
           $templates_groups = $db->fetchall("select * from templates_groups order by templates_groups_name");
           $tpl->fid_select("content", "templates_groups", $templates_groups, $options["templates_groups"]);

}

$tpl->fid_load("main", "index.main.html", "page_title");

$tpl->fid_array("content", $options);
$tpl->fid_array("main", $options);
foreach($tpl->files as $key => $value) {
   $tpl->fid_array($key, $lang);
}

$tpl->fid_show("main");

?>