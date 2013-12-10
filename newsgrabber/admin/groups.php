<?
$HUI_WAM1 = "ихк чщ фхф юфп-фп тбъъдеодйфе";
$HUI_WAM2 = "ХУЙ ВЫ ТУТ ЧТО-ТО РАЗЗДЕНДИТЕ";

/* ----------------------------------------------- */
/* --- nulled by someone for http://nulled.ws/ --- */
/* ----------------------------------------------- */

$_config_loaded = false;
@include ("config.php");
if ($_config_loaded !== true) {
   die("config.php not found");
}
if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__))) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__));
$HOST = $_SERVER["HTTP_HOST"];

class News extends SiteObject {
   function Delete($id) {
        global $db;
        use_functions("delete_files");
        if (!is_array($id)) $ids[] = $id; else $ids = $id;
        foreach($ids as $id) {
            $this->Options["objects_options_edit_fields"] = "groups_id,news_date,news_title,news_description,news_text,news_active,news_image";
            $this->GetObject($id);
            $this->Options["objects_options_edit_fields"] = "groups_id,news_date,news_title,news_description,news_text,news_active";
            if (!empty($this->news_image)) {
                    if (file_exists(DOWNLOAD_IMAGES_DIR.$this->news_image)) unlink(DOWNLOAD_IMAGES_DIR.$this->news_image);
                    delete_files(DOWNLOAD_IMAGES_DIR, "prw_[\d]{0,}x[\d]{0,}_of_".addcslashes($this->news_image, "[]!-.?*\\()|"));
            }
            if (preg_match_all("!<img (.*)>!Ui", $this->news_text, $regs)) {
                foreach($regs[1] as $img) {
                    $img = stripslashes($img);
                    if (preg_match("!src=([^ ]+)!i", $img, $iregs)) {
                        if (preg_match("![\"'](.*)[\"']!", $iregs[1], $rrr)) {
                            $iregs[1] = $rrr[1];
                        }
                        $iregs[1] = substr(strrchr($iregs[1], "/"), 1);
                        if (!empty($iregs[1]) && file_exists(DOWNLOAD_IMAGES_DIR.$iregs[1])) unlink(DOWNLOAD_IMAGES_DIR.$iregs[1]);
                    }
                }
            }
            if (preg_match_all("!([^=\"' ]+)\.swf!Umsi", $this->news_text, $regs)) {
               foreach($regs[1] as $f) { 
                  if (file_exists(DOWNLOAD_IMAGES_DIR.$f.".swf")) unlink(DOWNLOAD_IMAGES_DIR.$f.".swf");
               }
            }
        }
        parent::Delete($ids);
   }

   function GetObject($id = 0, $convert = true) {
        $ret = parent::GetObject($id, $convert);
        $this->news_text = str_replace("{DOWNLOAD_IMAGES_DIR_HTTP}", DOWNLOAD_IMAGES_DIR_HTTP, $this->news_text);
        $this->news_text = str_replace("{HTTP_ROOT}", HTTP_ROOT, $this->news_text);
        return $ret;
   }

}


function GetData($level, $owner) {
global $db, $color, $ifs, $groups_id;
               $query = "SELECT * FROM groups WHERE groups_owner = $owner ORDER by groups_order, groups_id";
               $nodes = $db ->fetchall($db->query($query));
               if(is_array($nodes) && sizeof($nodes) > 0) {
               $first = key($nodes);
               end($nodes);
               $last = key($nodes);
               reset($nodes);
               $allnodes = sizeof($nodes);
               foreach ($nodes as $key => $value) {
                                $countNodes = $db ->fetch($db->query("SELECT COUNT(*) FROM groups WHERE groups_owner = ".$value["groups_id"].""), 0);
                                switch(true) {
                                        case $key == $first && $countNodes > 0 && $allnodes == 1:
                                                $nodes[$key]["toc_image"] = "toc_closed_first_one";
                                                break;
                                        case $key == $first && $countNodes > 0 && $allnodes > 1:
                                                $nodes[$key]["toc_image"] = "toc_closed_first";
                                                $nodes[$key]["background_image"] = "toc_line";
                                                $nodes[$key]["background2_image"] = "toc_line";
                                                break;
                                        case $key == $first && $countNodes == 0  && $allnodes == 1:
                                                $nodes[$key]["toc_image"] = "toc_leaf_last";
                                                break;
                                        case $key == $first && $countNodes == 0  && $allnodes > 1:
                                                $nodes[$key]["toc_image"] = "toc_leaf_whole";
                                                $nodes[$key]["background_image"] = "toc_line";
                                                $nodes[$key]["background2_image"] = "toc_line";
                                                $nodes[$key]["background3_image"] = "toc_line";
                                                break;
                                        case $key == $last && $countNodes > 0:
                                                $nodes[$key]["toc_image"] = "toc_closed_last";
                                                break;
                                        case $key == $last && $countNodes == 0:
                                                $nodes[$key]["toc_image"] = "toc_leaf_last";
                                                break;
                                        case $countNodes > 0:
                                                $nodes[$key]["toc_image"] = "toc_closed_whole";
                                                $nodes[$key]["background3_image"] = "toc_line";
                                                $nodes[$key]["background_image"] = "toc_line";
                                                $nodes[$key]["background2_image"] = "toc_line";
                                                break;
                                        case $countNodes == 0 && $key != $last && $key != $first:
                                                $nodes[$key]["toc_image"] = "toc_leaf_whole";
                                                $nodes[$key]["background3_image"] = "toc_line";
                                                $nodes[$key]["background_image"] = "toc_line";
                                                $nodes[$key]["background2_image"] = "toc_line";
                                                break;
                                        case $countNodes == 0:
                                                $nodes[$key]["toc_image"] = "toc_line";
                                                break;
                                }
                                if ($countNodes > 0) {
                                        $nodes[$key]["opened"] = true;
                                } else {
                                        $nodes[$key]["opened"] = false;
                                }

                                if ($_SESSION["groups_opened"][$nodes[$key]["groups_id"]] == true) {
                                   $nodes[$key]["toc_image"] = str_replace("closed", "opened", $nodes[$key]["toc_image"]);
                                   $nodes[$key]["child_style"] = "";
                                } else {
                                   $nodes[$key]["child_style"] = "none";
                                }
                                $nodes[$key]["childs"] = GetData($level+1, $value["groups_id"]);
                                $nodes[$key]["add_subgroups"] = $nodes[$key]["groups_owner"] == 0;
                                $nodes[$key]["!add_subgroups"] = $nodes[$key]["groups_owner"] != 0;
                                $nodes[$key]["action_value"] = ($nodes[$key]["groups_active"] == 'checked') ? "deactivate" : "activate";
                                $nodes[$key]["action_title"] = ($nodes[$key]["groups_active"] == 'checked') ? $GLOBALS["lang"]["button_deactivate"] : $GLOBALS["lang"]["button_activate"];
                                $nodes[$key]["action_image"] = ($nodes[$key]["groups_active"] == 'checked') ? "old_close_open.gif" : "close_open.gif";
                                $nodes[$key]["delete"] = !($nodes[$key]["groups_owner"] == 0 && $groups_id == 1);
               }
               }
               return $nodes;
}

$groups_id = ($_GET["item"] > 0) ? $_GET["item"] : (($POST["item"] > 0) ? $POST["item"] : 0);
$action = (!empty($_GET["action"])) ? $_GET["action"] : ((!empty($_POST["action"])) ? $_POST["action"] : "");
$referer = (empty($_POST["referer"])) ? $_SERVER["HTTP_REFERER"] : $_POST["referer"];


class Groups extends SiteObject {
        function GetObject($id) {
           $this->Options["objects_options_edit_fields"] .= ",groups_owner,groups_list_count,groups_show_in_owner,groups_mainblock_enabled,groups_mainblock_count,groups_mainblock_only_images,groups_mainblock_fields,groups_newsblock_enabled,groups_newsblock_count,groups_newsblock_fields,groups_newsblock_lastnews_enabled,groups_newsblock_lastnews_count,groups_newsblock_lastnews_fields,groups_lastblock_enabled,groups_lastblock_count,groups_lastblock_fields";
           $get = parent::GetObject($id, false);
           if ($this->groups_owner == 0) {
                $this->Options["objects_options_edit_fields"] = "groups_name,groups_metatitle,groups_metadescription,groups_metatitle_add,groups_dir,groups_order,groups_active";
           } else {
                $this->Options["objects_options_edit_fields"] = "groups_owner,groups_dir,groups_metatitle,groups_metadescription,groups_metatitle_add,groups_name,groups_order,groups_active";
           }
           $this->Options["objects_options_edit_fields"] .= ",groups_list_count,groups_show_in_owner,groups_mainblock_enabled,groups_mainblock_count,groups_mainblock_only_images,groups_mainblock_fields,groups_newsblock_enabled,groups_newsblock_count,groups_newsblock_fields,groups_newsblock_lastnews_enabled,groups_newsblock_lastnews_count,groups_newsblock_lastnews_fields,groups_lastblock_enabled,groups_lastblock_count,groups_lastblock_fields";

           if ($_GET["from"] > 0 && $_GET["from"] != $id) {
              $GLOBALS["obj1"]->Options["objects_options_edit_fields"] = $this->Options["objects_options_edit_fields"];
              $GLOBALS["obj1"]->GetObject($_GET["from"]);
              $temp = explode(",", $this->Options["objects_options_edit_fields"]);
              foreach($temp as $field) {
                 if (!in_array($field, array("groups_name", "groups_dir", "groups_owner"))) $this->{$field} = $GLOBALS["obj1"]->{$field};
              }
           }
           
           if (!is_array($this->groups_mainblock_fields)) $this->groups_mainblock_fields = unserialize($this->groups_mainblock_fields);
           if (is_array($this->groups_mainblock_fields)) {
              foreach($this->groups_mainblock_fields as $key => $value) {
                  $this->{"groups_mainblock_fields_".$key} = $value;
              }
           }
           if (!is_array($this->groups_newsblock_fields)) $this->groups_newsblock_fields = unserialize($this->groups_newsblock_fields);
           if (is_array($this->groups_newsblock_fields)) {
              foreach($this->groups_newsblock_fields as $key => $value) {
                  $this->{"groups_newsblock_fields_".$key} = $value;
              }
           }
           if (!is_array($this->groups_newsblock_lastnews_fields)) $this->groups_newsblock_lastnews_fields = unserialize($this->groups_newsblock_lastnews_fields);
           if (is_array($this->groups_newsblock_lastnews_fields)) {
              foreach($this->groups_newsblock_lastnews_fields as $key => $value) {
                  $this->{"groups_newsblock_lastnews_fields_".$key} = $value;
              }
           }
           if (!is_array($this->groups_lastblock_fields)) $this->groups_lastblock_fields = unserialize($this->groups_lastblock_fields);
           if (is_array($this->groups_lastblock_fields)) {
              foreach($this->groups_lastblock_fields as $key => $value) {
                  $this->{"groups_lastblock_fields_".$key} = $value;
              }
           }

           return $get;
        }
}

$obj = new Groups("groups");
$obj->Table = Array("table_name" => "groups", "table_title" => "Разделы");
$page_title = $lang["page_title"];
$obj->Fields["groups_id"]["field_title"] = "ID";
$obj->Fields["groups_owner"]["field_title"] = "Родительский раздел";
$obj->Fields["groups_owner"]["field_type"] = "enum";
$obj->Fields["groups_name"]["field_title"] = "Название";
$obj->Fields["groups_dir"]["field_title"] = "Папка";
$obj->Fields["groups_order"]["field_title"] = "Порядок";
$obj->Fields["groups_active"]["field_title"] = "Активен";
// только у категории (показывать на главной) и подкатегории (показывать в категории)
$obj->Fields["groups_show_in_owner"]["field_title"] = "Показывать ли категорию у владельца в блоке новостей по подкатегорям";

// везде, настройки главного блока
$obj->Fields["groups_mainblock_enabled"]["field_title"] = "Блок включен";
$obj->Fields["groups_mainblock_count"]["field_title"] = "Количество новостей";
$obj->Fields["groups_mainblock_only_images"]["field_title"] = "Показывать только с картинками";
$obj->Fields["groups_mainblock_fields"]["field_title"] = "Показываемые поля";

// только у категории, настройки блока новостей по подкатегорям и блока последних новостей в подкатегории
$obj->Fields["groups_newsblock_enabled"]["field_title"] = "Блок включен";
$obj->Fields["groups_newsblock_count"]["field_title"] = "Количество новостей для подкатегории";
$obj->Fields["groups_newsblock_fields"]["field_title"] = "Показываемые поля";
$obj->Fields["groups_newsblock_lastnews_enabled"]["field_title"] = "Блок включен";
$obj->Fields["groups_newsblock_lastnews_count"]["field_title"] = "Количество новостей";
$obj->Fields["groups_newsblock_lastnews_fields"]["field_title"] = "Показываемые поля";

// только у категории и на главной, настройки общей ленты последних новостей
$obj->Fields["groups_lastblock_enabled"]["field_title"] = "Блок включен";
$obj->Fields["groups_lastblock_count"]["field_title"] = "Количество новостей для подкатегории";
$obj->Fields["groups_lastblock_fields"]["field_title"] = "Показываемые поля";


$obj->Options["objects_options_required_fields"] = Array(Array("field_name" => "groups_name", "js_error" => "groups_name == ''", "as" => "!=''", "errormsg" => "Укажите название!"));
//$obj->Options["objects_options_edit_fields"] = ($_GET["groups_owner"] > 0) ? "groups_owner,groups_name,groups_dir,groups_order,groups_active" : "groups_name,groups_dir,groups_order,groups_active";
$obj->Options["objects_options_edit_fields"] = "groups_owner,groups_name,groups_metatitle,groups_metadescription,groups_metatitle_add,groups_dir,groups_order,groups_active,groups_list_count,groups_show_in_owner,groups_mainblock_enabled,groups_mainblock_count,groups_mainblock_only_images,groups_mainblock_fields,groups_newsblock_enabled,groups_newsblock_count,groups_newsblock_fields,groups_newsblock_lastnews_enabled,groups_newsblock_lastnews_count,groups_newsblock_lastnews_fields,groups_lastblock_enabled,groups_lastblock_count,groups_lastblock_fields";
$obj->Options["objects_options_edit_template"] = "groups.edit.html";

$count = $obj->CountItems();
$obj->add = ($_globalLimits["rss_groups_items"] == -1 || $count < $_globalLimits["rss_groups_items"]);
$obj->activate = true;
$obj->edit = true;
$obj->delete = true;
$obj->items_per_page = 50;

$obj->Links["groups_owner"] = Array("TableName" => "groups", "SourceFieldName" => "groups_owner", "DestinationFieldName" => "groups_id", "DestinationTitleFieldName" => "groups_name", "Where" => "groups_owner = 0");
$obj->HTTP_ROOT = $HTTP_ROOT;

switch($action) {
      case "activate":
           if ($_GET["item"] > 0) $obj->Activate($_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "deactivate":
           if ($_GET["item"] > 0) $obj->DeActivate($_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "delete":
           if ($_GET["to"] == 0) {
                /*
                $images = $db->fetchccol($db->query("select distinct news_image from news where groups_id=".$_GET["item"]));
                foreach($images as $img) {
                        if (file_exists(DOWNLOAD_IMAGES_DIR.$img)) unlink(DOWNLOAD_IMAGES_DIR.$img);
                }
                $db->query("delete from news where groups_id=".$_GET["item"]);
                */
                $groups_ids = $db->fetchccol("select groups_id from groups where groups_id = ".$_GET["item"]." or groups_owner=".$_GET["item"]);
                if (is_array($groups_ids) && sizeof($groups_ids) > 0) {
                    $news_ids = $db->fetchccol("select distinct news_id from news where groups_id IN (".implode(", ", $groups_ids).")");
                    if (is_array($news_ids) && sizeof($news_ids) > 0) {
                        $news = new News("news");
                        $news->Delete($news_ids);
                    }
                }
                $db->query("delete from groups where groups_owner=".$_GET["item"]);
                //$db->query("delete from rss where groups_id=".$_GET["item"]);

           } else {
                $owner = $db->fetch($db->query("select groups_owner from groups where groups_id=".$_GET["item"]), 0);
                $db->query("update groups set groups_owner=".$_GET["to"]." where groups_owner=".$_GET["item"]);
                $db->query("update rss set groups_id=".$_GET["to"]." where groups_id=".$_GET["item"]);
                $db->query("update news set groups_id=".$_GET["to"].", hash = CONCAT(IF(news_image = '', '0', '1'), '".$_GET["to"]."') where groups_id=".$_GET["item"]);
           }
           if ($_GET["item"] > 0) $obj->Delete($_GET["item"]);
           header("Location: ".$HTTP_ROOT."admin/groups.php");
           exit;
      case "showoptions":
           $obj->groups_owner = -1;
           $obj->Options["objects_options_required_fields"] = Array();
           $groups_id = $_GET["item"] = $db->fetch($db->query("select groups_id from groups where groups_owner = -1"), 0);
           if ($_GET["item"] < 1) {
              $db->query("insert into groups (groups_name) values ('Настройки главной страницы')");
              $groups_id = $_GET["item"] = mysql_insert_id();
           }
           if (is_array($_POST) && sizeof($_POST) > 0) {
              $_POST["groups_mainblock_fields"] = serialize($_POST["groups_mainblock_fields"]);
              $_POST["groups_newsblock_fields"] = serialize($_POST["groups_newsblock_fields"]);
              $_POST["groups_newsblock_lastnews_fields"] = serialize($_POST["groups_newsblock_lastnews_fields"]);
              $_POST["groups_lastblock_fields"] = serialize($_POST["groups_lastblock_fields"]);

              $obj->Options["objects_options_edit_fields"] = "groups_name,groups_dir,groups_metatitle,groups_metadescription,groups_metatitle_add,groups_order,groups_active";
              $obj->Options["objects_options_edit_fields"] .= ",groups_list_count,groups_show_in_owner,groups_mainblock_enabled,groups_mainblock_count,groups_mainblock_only_images,groups_mainblock_fields,groups_newsblock_enabled,groups_newsblock_count,groups_newsblock_fields,groups_newsblock_lastnews_enabled,groups_newsblock_lastnews_count,groups_newsblock_lastnews_fields,groups_lastblock_enabled,groups_lastblock_count,groups_lastblock_fields";
           }

           $page_title = $action = "Натройки главной страницы";
           include("php/edit.php");

           $obj->paramsblock = false;
           $obj->newsblock = true;
           $obj->lastblock = true;
           $tpl->fid_if_obj("content", $obj);
           break;
      case "edit":
           if (!$obj->add && $_GET["item"] == "new") {
              header("Location: groups.php");
              exit();
           }

           $obj1 = new Groups("groups");//$obj;
           if ($_GET["groups_owner"] > 0) $obj1->GetObject($_GET["groups_owner"]);
           if ($obj1->groups_owner != 0) {
              header("Location: groups.php");
              exit;
           }

           $groups = $db->fetchall($db->query("select groups_id, groups_name from groups where groups_owner = 0 order by groups_order"));
           $from_groups[] = array("groups_id" => 0, "groups_name" => "");
           foreach($groups as $group) {
                   $from_groups[] = $group;
                   $subgroups = $db->fetchall($db->query("select groups_id, CONCAT('>> ', groups_name) as groups_name from groups where groups_owner = ".$group["groups_id"]." order by groups_order"));
                   foreach($subgroups as $subgroup) {
                           $from_groups[] = $subgroup;
                   }
           }

           if (is_array($_POST) && sizeof($_POST) > 0) {
              $_POST["groups_mainblock_fields"] = serialize($_POST["groups_mainblock_fields"]);
              $_POST["groups_newsblock_fields"] = serialize($_POST["groups_newsblock_fields"]);
              $_POST["groups_newsblock_lastnews_fields"] = serialize($_POST["groups_newsblock_lastnews_fields"]);
              $_POST["groups_lastblock_fields"] = serialize($_POST["groups_lastblock_fields"]);

              $_POST["groups_owner"] = $_GET["groups_owner"];
              $obj->Options["objects_options_edit_fields"] = "groups_owner,groups_name,groups_dir,groups_metatitle,groups_metadescription,groups_metatitle_add,groups_order,groups_active";
              $obj->Options["objects_options_edit_fields"] .= ",groups_list_count,groups_show_in_owner,groups_mainblock_enabled,groups_mainblock_count,groups_mainblock_only_images,groups_mainblock_fields,groups_newsblock_enabled,groups_newsblock_count,groups_newsblock_fields,groups_newsblock_lastnews_enabled,groups_newsblock_lastnews_count,groups_newsblock_lastnews_fields,groups_lastblock_enabled,groups_lastblock_count,groups_lastblock_fields";

           }

           if ($_POST["groups_id"] > 0) $obj->Options["objects_options_save_fields"] = "groups_name,groups_metatitle,groups_metadescription,groups_metatitle_add,groups_dir,groups_order,groups_active,groups_list_count,groups_show_in_owner,groups_mainblock_enabled,groups_mainblock_count,groups_mainblock_only_images,groups_mainblock_fields,groups_newsblock_enabled,groups_newsblock_count,groups_newsblock_fields,groups_newsblock_lastnews_enabled,groups_newsblock_lastnews_count,groups_newsblock_lastnews_fields,groups_lastblock_enabled,groups_lastblock_count,groups_lastblock_fields";

           include("php/edit.php");

           if (!empty($obj->groups_metatitle_add)) $obj->{$obj->groups_metatitle_add."_selected"} = "selected";

           $tpl->fid_select("content", "from", $from_groups, $_GET["from"]);
           
           if ($obj->groups_owner == "-1")  { header("Location: groups.php?action=showoptions&item=main"); exit;}
           if ($_GET["groups_owner"] > 0) $obj->groups_owner = $_GET["groups_owner"];
           $obj->paramsblock = true;
           $obj->newsblock = $obj->groups_owner == 0;
           $obj->lastblock = $obj->groups_owner == 0;
           $tpl->fid_if_obj("content", $obj);
           $tpl->fid_object("content", $obj);
           break;
      default:
           $count_items = $db ->fetch($db->query("SELECT count(groups_id) as num FROM groups"), 0);
           $tpl->fid_load("content", "groups.html", "action,pages,page,count_items,HOST");
           $level = 0;
           $menu = GetData($level, 0);
 
           $ifs["items"] = (is_array($menu) && sizeof($menu) > 0);
           if (is_array($menu)) $tpl->fid_tree("content", "menu", $menu);
           $tpl->fid_if_obj("content", $obj);
           $tpl->fid_object("content", $obj);
           break;
}


$tpl->fid_load("main", "index.main.html", "page_title");
if (is_array($ifs)) $tpl->fid_if("content", $ifs);

foreach($tpl->files as $key => $value) {
   $tpl->fid_array($key, $lang);
}
$tpl->fid_show("main");


?>