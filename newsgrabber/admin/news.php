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

use_functions("get_content,get_proxy,get_agent,rss_get_content,rss_get_swf,utf_to_win,get_remote_filesize,to_translit,resize_images,delete_files");

$action = (!empty($_GET["action"])) ? $_GET["action"] : ((!empty($_POST["action"])) ? $_POST["action"] : "");
$action = !empty($_POST["group_action"]) ? "group_action" : $action;

class News extends SiteObject {

        function Update($id, $content = false, $return = false) {
            global $rss, $parsed_link,$news_error,$news_errormessage;
            if (!is_array($id)) $ids[] = $id; else $ids = $id;
            foreach($ids as $id) {
                $rss = $GLOBALS["db"]->fetch($GLOBALS["db"]->query("select * from news left join rss using (rss_id) where news.rss_id <> 0 and news_link <> '' and news_id = $id"));
                if ($rss["news_id"] > 0) {
                    $item = $rss;
                    $news_error = "";
                    $news_errormessage = "";
                    $news_text = "";
                    $GLOBALS["images"] = array();
                    
                    if (!empty($item["news_link"]) && $item["rss_id"] > 0) {
                        if (!empty($rss["rss_print_link_from"])) {
                            $item["news_link_old"] = $item["news_link"];
                            $item["news_link"] = preg_replace("!".$rss["rss_print_link_from"]."!", $rss["rss_print_link_to"], $item["news_link"]);
                        }
                        $item["link"] = $item["news_link"];
                        $item_url = parse_url($item["link"]);
                        $item_url["dir_path"] = "http://".$item_url["host"].$item_url["path"];
                        if (preg_match("!\.[a-z0-9]{2,5}$!i", $item_url["dir_path"])) $item_url["dir_path"] = substr($item_url["dir_path"], 0, strpos($item_url["dir_path"], basename($item_url["dir_path"])));
                        $item["dir_url"] = $item_url["dir_path"];
                        //$parsed_link = parse_url($item["link"]);
                        //$parsed_link["dir_url"] = $item["dir_url"];

                        $news_text = rss_get_content($item, $content && !empty($item["news_text"]) ? $item["news_text"] : false);
                        if (!empty($rss["rss_replacement"]) && !empty($news_text)) {
                            $rss["rss_replacement"] = explode("\n", $rss["rss_replacement"]);
                            foreach($rss["rss_replacement"] as $r) {
                                $r = trim($r);
                                if (!empty($r)) {
                                   $r = explode("|", $r);
                                   $news_text = preg_replace("!".$r[0]."!i", $r[1], $news_text);
                                }
                            }
                        }

                        $news_active = (!empty($news_error)) ? "" : "checked";
                        if (empty($news_text)) {
                            $news_error = "checked";
                            $news_errormessage = $GLOBALS["lang"]["errors_news_parse"];
                            $news_active = '';
                        } else {
                            $news_text = str_replace('\\"', '"', $news_text);
                            if (preg_match_all("!<a (.*)>(.*)</a>!Ui", $news_text, $url_regs)) {
                                foreach($url_regs[0] as $url_key => $url) {
                                    if (preg_match("!href=[\"']{0,1}([^ '\">]+)!i", $url, $href_regs)) {
                                        $href = $href_regs[1];
                                    }
                                    $news_text = str_replace($url_regs[1][$url_key], "href=\"".$href."\"", $news_text);
                                }
                            }
                        }
                        if (empty($news_text)) {
                            $news_error = "checked";
                            $news_errormessage = $GLOBALS["lang"]["errors_news_parse"];
                            $news_active = '';
                        } else {
                            $news_text = str_replace('\\"', '"', $news_text);
                        }
                    } else {
                        $news_error = "checked";
                        $news_errormessage = $rss["rss_id"] > 0 ? $GLOBALS["lang"]["errors_news_no_link"] : $GLOBALS["lang"]["errors_news_no_rss"];
                        $news_active = '';
                    }

                    if ($return) {
                        return $news_text;
                    } else {
                        $news_active = (!empty($news_error) || $GLOBALS["options"]["grab_manual_moderate"] == "checked") ? "" : "checked";
                        if (empty($item["news_description"]) && $GLOBALS["options"]["grab_text_first_chars_use"] > 0 && !empty($news_text)) {
                                $item["news_description"] = substr(strip_tags(trim(preg_replace("![ ]{2,}!", " ", preg_replace("!</p>|<br>!i", " ", $news_text)))), 0, $GLOBALS["options"]["grab_text_first_chars_use"]);
                                $item["news_description"] = str_replace(chr(160), "", $item["news_description"]);
                                $item["news_description"] = preg_split("!([\.\!\?]+)!", $item["news_description"], -1, PREG_SPLIT_DELIM_CAPTURE);
                                if (sizeof($item["news_description"]) > 2) {
                                    array_pop($item["news_description"]);
                                    if (sizeof($item["news_description"]) > 2)
                                    for($i = sizeof($item["news_description"])-1; $i >= 0; $i--) {
                                       $ccc = strpos(strrev($item["news_description"][$i-1]), " ");
                                       if ($ccc < 3) {
                                          $item["news_description"][$i] = "";
                                          $item["news_description"][$i-1] = "";
                                          $i--;
                                       } else {
                                          break;
                                       }
                                    }
                                } elseif (sizeof($item["news_description"]) == 1) {
                                   $item["news_description"][0] = explode(" ", $item["news_description"][0]);
                                   array_pop($item["news_description"][0]);
                                   $item["news_description"][0] = implode(" ", $item["news_description"][0])."...";
                                }
                                $item["news_description"] = trim(implode("", $item["news_description"]));
                           
                        }

                        if (get_magic_quotes_gpc()) {
                           $news_text = addslashes($news_text);
                           $item["news_description"] = addslashes($item["news_description"]);
                        }

                        $GLOBALS["db"]->query("update news set news_description = '".$item["news_description"]."', news_text = '".$news_text."', news_active = '$news_active', news_image = IF(news_image = '', '".addslashes($GLOBALS["images"][0]['image_source'])."', news_image), news_imagealt = IF(news_imagealt = '', '".addslashes($GLOBALS["images"][0]['alt'])."', news_imagealt), news_error = '$news_error', news_errormessage = '$news_errormessage', hash = IF('".$news_active."' = 'checked', CONCAT(IF('".$GLOBALS["images"][0]['image_source']."' = '' and news_image = '', '0', '1'), groups_id), NULL) where news_id = $id");

                        if ($options["shingle_check"] == "checked") {
                            $GLOBALS["db"]->query("delete from news_shingles where news_id = ".$id." and type <> 'title'");
                            use_functions("create_shingles");
                            $shingles = create_shingles($news_text);
                            foreach($shingles as $ss) {
                                $GLOBALS["db"]->query("insert into news_shingles values(".$id.", ".$ss.", '', NOW())");
                            }
                        }
                        if (!empty($GLOBALS["images"][0]['image_source'])) resize_images($GLOBALS["images"][0]['image_source']);
                    }
                }
            }
        }

        function Parse($id) {

            return Array("error" => $news_error, "message" => $news_errormessage);
        }

   function Delete($id) {
        global $db;
        if (!is_array($id)) $ids[] = $id; else $ids = $id;
        foreach($ids as $id) {
            $this->Options["objects_options_edit_fields"] = "groups_id,news_date,news_title,news_description,news_text,news_active,news_image";
            $this->GetObject($id);
            $this->Options["objects_options_edit_fields"] = "groups_id,news_date,news_title,news_description,news_text,news_active";
            //if (!empty($this->news_image) && file_exists(DOWNLOAD_IMAGES_DIR.$this->news_image)) unlink(DOWNLOAD_IMAGES_DIR.$this->news_image);
            //if (!empty($this->news_image) && file_exists(DOWNLOAD_IMAGES_DIR."sm_".$this->news_image)) unlink(DOWNLOAD_IMAGES_DIR."sm_".$this->news_image);
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
                        //delete_files(DOWNLOAD_IMAGES_DIR, addcslashes($iregs[1], "[]!-.?*\\()|"));
                        //if (file_exists(DOWNLOAD_IMAGES_DIR.$iregs[1])) unlink(DOWNLOAD_IMAGES_DIR.$iregs[1]);
                        //if (file_exists(DOWNLOAD_IMAGES_DIR."sm_".$iregs[1])) unlink(DOWNLOAD_IMAGES_DIR."sm_".$iregs[1]);
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

   function SaveObject($array = array()) {
        $save = parent::SaveObject($array);
        if ($this->KeyValue > 0) $GLOBALS["db"]->query("UPDATE `news` SET hash = IF(news_active = 'checked', CONCAT(IF(news_image = '', '0', '1'), groups_id), NULL) WHERE news_id = ".$this->KeyValue);
        return $save;
   }

   function Activate($id = 0) {
        if ($id == 0) return false;
        if (is_array($id)) {
                  if (sizeof($id) > 0) {
                    foreach($id as $key => $value) $id[$key] = addslashes($value);
                    @mysql_query("UPDATE ".$this->Table["table_name"]." SET ".$this->Table["table_name"]."_active = 'checked', hash = CONCAT(IF(news_image = '', '0', '1'), groups_id) WHERE ".$this->KeyName." IN ('".implode("','", $id)."')");
                  }
               } else {
                  @mysql_query("UPDATE ".$this->Table["table_name"]." SET ".$this->Table["table_name"]."_active = 'checked', hash = CONCAT(IF(news_image = '', '0', '1'), groups_id) WHERE ".$this->KeyName." = '".$id."'");
               }
               if (mysql_error()) {
                    return $this->OnError("dbError");
               }
      }

      function DeActivate($id = 0) {
               if ($id == 0) return false;
               if (is_array($id)) {
                  if (sizeof($id) > 0) {
                    foreach($id as $key => $value) $id[$key] = addslashes($value);
                    @mysql_query("UPDATE ".$this->Table["table_name"]." SET ".$this->Table["table_name"]."_active = '', hash = NULL WHERE ".$this->KeyName." IN ('".implode("','", $id)."')");
                  }
               } else {
                  @mysql_query("UPDATE ".$this->Table["table_name"]." SET ".$this->Table["table_name"]."_active = '', hash = NULL WHERE ".$this->KeyName." = '".$id."'");
               }
               if (mysql_error()) {
                    return $this->OnError("dbError");
               }
      }
 /* function GetItems($where = Array(), $convert = true) {
                       global $db;
                       $this->Options["objects_options_show_fields"] = "news_error,news_errormessage,news_id,news_date,DATE_FORMAT(news_date, '%d.%m.%Y %H:%i') as news_date,news_title,news_title as delete_name, IF(news_active = '', 'close_open.gif', 'old_close_open.gif') as action_image, IF(news_active = '', 'активировать', 'деактивировать') as action_title, IF(news_active = '', 'activate', 'deactivate') as action";
                       $get = parent::GetItems($where, $convert);
                       $this->Options["objects_options_show_fields"] = "news_id,news_date,DATE_FORMAT(news_date, '%d.%m.%Y %H:%i') as news_date,news_title,news_title as delete_name, IF(news_active = '', 'close_open.gif', 'old_close_open.gif') as action_image, IF(news_active = '', 'активировать', 'деактивировать') as action_title, IF(news_active = '', 'activate', 'deactivate') as action";
                       foreach($get as $key => $item) {

                       }
                       return $get;
              }
              */
}

$obj = new News("news");
$obj->Table = Array("table_name" => "news", "table_title" => $lang["page_title"]);
$page_title = $lang["page_title"];
$obj->Fields["news_id"]["field_title"] = "ID";
$obj->Fields["groups_id"]["field_title"] = $lang["title_groups_id"];
$obj->Fields["groups_id"]["field_type"] = "enum";
$obj->Fields["news_source"]["field_title"] = $lang["title_news_source"];
$obj->Fields["news_source"]["field_type"] = "enum";
$obj->Fields["news_date"]["field_title"] = $lang["title_news_date"];
$obj->Fields["news_title"]["field_title"] = $lang["title_news_title"];;
$obj->Fields["news_title"]["no_convert"] = true;
$obj->Fields["news_description"]["field_title"] = $lang["title_news_description"];;
$obj->Fields["news_text"]["field_title"] = $lang["title_news_text"];;
$obj->Fields["news_active"]["field_title"] = $lang["title_news_active"];;

$obj->Options["objects_options_show_fields"] = "news_id,news_date,DATE_FORMAT(news_date, '%d.%m.%Y %H:%i') as news_date,news_title, news_title as delete_name, IF(news_active = '', 'close_open.gif', 'old_close_open.gif') as action_image, IF(news_active = '', '".$lang["button_activate"]."', '".$lang["button_deactivate"]."') as action_title, IF(news_active = '', 'activate', 'deactivate') as action, IF(news_error = 'checked', CONCAT('<a href=\"', news_link,'\" target=\"_blank\" ".($options["manual_show_description"] == "checked" ? "title=\"', REPLACE(news_description, '\"', '&quot;'),'\"" : "")."><font color=red>', REPLACE(news_title, '\"', '&quot;'), '</font></a>&nbsp;<span style=\"padding: 0px 5px;background: #ffcc00;font-weight: bold;cursor : help;\" title=\"', news_errormessage,'\">?</span> <a href=\"?action=clear&item=', news_id, '\" title=\"".$lang["button_clear_error_alt"]."\">".$lang["button_clear_error"]."</a>'), CONCAT('<a href=\"', news_link,'\" target=\"_blank\" ".($options["manual_show_description"] == "checked" ? "title=\"', REPLACE(news_description, '\"', '&quot;'),'\"" : "").">', REPLACE(news_title, '\"', '&quot;'), '</a>')) as news_title, IF(news_link <> '', '1', '') as `update`, IF(news_link <> '', '1', '') as `parse`";
$obj->Options["objects_options_required_fields"] = Array(Array("field_name" => "news_text", "js_error" => "news_text == ''", "as" => "!=''", "errormsg" => $lang["errors_news_text"]));
$obj->Options["objects_options_edit_fields"] = "groups_id,news_date,news_title,news_description,news_text,news_active";
$obj->Options["objects_options_edit_fields"] = "groups_id,news_date,news_title,news_trans_title,news_description,news_text,news_active";
$obj->Options["objects_options_edit_template"] = "news.edit.html";
$obj->Options["objects_options_list_template"] = "news.list.html";

$obj->news_date_hour = date("H");
$obj->news_date_minute = date("m");
$obj->news_date_second = date("s");
$obj->news_date_day = date("d");
$obj->news_date_month = date("m");
$obj->news_date_year = date("Y");

$obj->add = true;
$obj->activate = true;
$obj->edit = true;
$obj->delete = true;
$obj->items_per_page = intval($_GET["items_per_page"]) > 0 ? intval($_GET["items_per_page"]) : 50;
$obj->OrderBy = "news_id desc";
$obj->HTTP_ROOT = $HTTP_ROOT;

$obj->Actions = Array(Array("action" => "update", "action_image" => "news_new.gif", "action_title" => $lang["button_get_text"]),
                      /*Array("action" => "parse", "action_image" => "news.gif", "action_title" => $lang["button_parse_text"])*/);

$obj->Links["groups_id"] = Array("TableName" => "groups", "SourceFieldName" => "groups_id", "DestinationFieldName" => "groups_id", "DestinationTitleFieldName" => "groups_name", "Where" => "");
$obj->Rubrikators["groups"] = Array("TableName" => "groups", "TableTitle" => $lang["title_groups_id"], "IDField" => "groups_id", "TitleField" => "groups_name", "Where" => "");
$obj->Rubrikators["rss"] = Array("TableName" => "rss", "TableTitle" => $lang["title_news_source"], "IDField" => "rss_id", "TitleField" => "rss_title", "Where" => ($_GET["groups_id"] > 0) ? "groups_id = '".$_GET["groups_id"]."'" : "");

$obj->GroupActions = Array(Array("action" => "update", "action_title" => $lang["button_get_text"]));

if ($_GET["moderate"] == 1) {
   $obj->moderate = true;
   $obj->links = "moderate=1&";
   $obj->add_info = "&moderate=1";
   $page_title = $lang["page_title_moderate"];
   $obj->Fields["news_title"]["no_convert"] = false;
   $obj->Options["objects_options_show_fields"] = "news_id,news_date,DATE_FORMAT(news_date, '%d.%m.%Y %H:%i') as news_date, news.groups_id, news_description, news_title, news_link";
   $where[] = "news_active <> 'checked' and news_error = ''";
} else {
   $obj->moderate = false;
}

$groups = $db->fetchall($db->query("select groups_id, groups_name from groups where groups_owner = 0 order by groups_order"));
foreach($groups as $group) {
        $obj->groups_id_values[] = $group["groups_id"];
        $obj->groups_id_titles[] = $group["groups_name"];
        $obj->Rubrikators["groups"]["values"][] = $group;
        $subgroups = $db->fetchall($db->query("select groups_id, CONCAT('>> ', groups_name) as groups_name from groups where groups_owner = ".$group["groups_id"]." order by groups_order"));
        foreach($subgroups as $subgroup) {
                $obj->groups_id_values[] = $subgroup["groups_id"];
                $obj->groups_id_titles[] = $subgroup["groups_name"];
                $obj->Rubrikators["groups"]["values"][] = $subgroup;
        }
}

switch($action) {
       case "group_action":
           if (is_array($_POST["selected"]) && sizeof($_POST["selected"]) > 0) {
               if (substr($_POST["group_action"], 0, 4) == "move") {
                  $to = substr($_POST["group_action"], 4);
                  $_POST["group_action"] = "move";
               }
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
                   case "update":
                        set_time_limit(180 * sizeof($_POST["selected"]));
                        $obj->Update($_POST["selected"]);
                        break;
                   case "move":
                        foreach($_POST["selected"] as $key => $value) $_POST["selected"][$key] = addslashes($value);
                        @mysql_query("UPDATE news SET groups_id = '".$to."', hash = CONCAT(IF(news_image = '', '0', '1'), '".$to."') WHERE news_id IN ('".implode("','", $_POST["selected"])."')");
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
      case "update":
           set_time_limit(180);
           if ($_GET["item"] > 0) $obj->Update($_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "parse":
           if ($_GET["item"] > 0) $obj->Update($_GET["item"], true);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "clear":
           if ($_GET["item"] > 0) $db->query("update news set news_error = '', news_errormessage = '', news_active = 'checked' where news_id = ".$_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "delete":
           if ($_GET["item"] > 0) $obj->Delete($_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "edit":
           $obj->Fields["news_title"]["no_convert"] = false;
           if (is_array($_POST) && sizeof($_POST) > 0) {
              $_POST["news_trans_title"] = to_translit($_POST["news_title"]);
              if (ini_get("magic_quotes_gpc")) {
                $_POST["news_title"] = stripslashes($_POST["news_title"]);
                $_POST["news_description"] = stripslashes($_POST["news_description"]);
                $_POST["news_text"] = stripslashes($_POST["news_text"]);
              }

              $_POST["news_text"] = preg_replace("!(href|src)[ ]{0,1}=([ \"']{0,2})".DOWNLOAD_IMAGES_DIR_HTTP."!Umsi", "\\1=\\2{DOWNLOAD_IMAGES_DIR_HTTP}", $_POST["news_text"]);
              $_POST["news_text"] = preg_replace("!(href|src)[ ]{0,1}=([ \"']{0,2})".$HTTP_ROOT."!Umsi", "\\1=\\2{HTTP_ROOT}", $_POST["news_text"]);
              /*
              $_POST["news_title"] = $db->data_encode($_POST["news_title"]);
              $_POST["news_description"] = $db->data_encode($_POST["news_description"]);
              $_POST["news_text"] = $db->data_encode($_POST["news_text"]);
              //if (ini_get("magic_quotes_gpc")) {
                $_POST["news_title"] = addslashes($_POST["news_title"]);
                $_POST["news_description"] = addslashes($_POST["news_description"]);
                $_POST["news_text"] = addslashes($_POST["news_text"]);
              //}
              */
           }
           include("php/edit.php");
           break;
      default:
           //$tpl->files["extend"] = "asd";
           if (!empty($_GET["from_date"])) {
              list($obj->from_year, $obj->from_month, $obj->from_day) = explode("-", $_GET["from_date"]);
              $obj->links .= "from_date=".$_GET["from_date"]."&";
              $where[] = "news_date >= '".addslashes($_GET["from_date"])." 00:00:00'";
           }
           if (!empty($_GET["to_date"])) {
              list($obj->to_year, $obj->to_month, $obj->to_day) = explode("-", $_GET["to_date"]);
              $obj->links .= "to_date=".$_GET["to_date"]."&";
              $where[] = "news_date < '".addslashes($_GET["to_date"])." 23:59:59'";
           }
           if (intval($_GET["items_per_page"]) > 0) {
              $obj->links .= "items_per_page=".$_GET["items_per_page"]."&";
           }

           if ($_GET["filter"] == "delete" && is_array($where) && sizeof($where) > 0) {
              $ids = $db->fetchccol($db->query("select news_id from news WHERE ".implode(" and ", $where)));
              if ($_GET["shingles"] == 1) {
                 $wh = array();
                 if (!empty($_GET["from_date"])) $wh[] = "date >= '".$_GET["from_date"]."'";
                 if (!empty($_GET["to_date"])) $wh[] = "date < '".$_GET["to_date"]."'";
                 //$db->query("DELETE from news_shingles WHERE news_id in (".implode(", ", $ids).")");
                 if (sizeof($wh) > 0) $db->query("DELETE from news_shingles WHERE ".implode(" and ", $wh));
                 
              }
              //$db->query("DELETE from news WHERE ".implode(" and ", $where));
              if (is_array($ids) && sizeof($ids) > 0) $obj->Delete($ids);
              header("Location: news.php");
              exit;
           }

           foreach($groups as $group) {
              $obj->GroupActions[] = Array("action" => "move".$group["groups_id"], "action_title" => $lang["caption_move"]." ".$group["groups_name"]);
              $subgroups = $db->fetchall($db->query("select groups_id, CONCAT('>> ', groups_name) as groups_name from groups where groups_owner = ".$group["groups_id"]." order by groups_order"));
              foreach($subgroups as $subgroup) {
                 $obj->GroupActions[] = Array("action" => "move".$subgroup["groups_id"], "action_title" => $lang["caption_move"]." ".$subgroup["groups_name"]);
              }
           }

           if ($_GET["moderate"] == 1 && sizeof($_POST) > 0) {
              $ids = array();
              foreach($_POST["news"] as $key => $item) {
                 $key = intval($key);
                 if ($key > 0) {
                    if ($item["action"] != "delete") {
                        $db->query("update news set groups_id = '{groups_id}', news_title = '{news_title}', news_description = '{news_description}', hash = IF('".($item["action"] == "activate" ? "checked" : "")."' = 'checked', CONCAT(IF(news_image = '', '0', '1'), '{groups_id}'), NULL), news_active = '".($item["action"] == "activate" ? "checked" : "")."' where news_id = ".$key, $item);
                    }
                    if ($item["action"] == "delete") {
                        $ids[] = $key;
                    }
                 }
              }
              if (is_array($ids) && sizeof($ids) > 0) $obj->Delete($ids);
           }
           $obj->count_moderated = $db->fetch("select count(*) from news where news_active <> 'checked' and news_error = ''", 0);

           $order = (!empty($_GET["order"])) ? $_GET["order"] : ((!empty($_POST["order"])) ? $_POST["order"] : "news_id");
           $sort = (!empty($_GET["sort"])) ? $_GET["sort"] : ((!empty($_POST["sort"])) ? $_POST["sort"] : "DESC");
           require("php/items.php");
           if ($_GET["moderate"] == 1) {
              foreach($items as $key => $item) {
                 $shingles = $db->fetchccol("select shingle from news_shingles where news_id = ".$item["news_id"]." and type <> 'title'");
                 if (is_array($shingles ) && sizeof($shingles ) > 0) $ex = $db->fetchall("SELECT news_id, COUNT( DISTINCT shingle ) AS ccc FROM news_shingles WHERE news_id <> ".$item["news_id"]." and shingle IN ( ".implode(", ", $shingles)." ) GROUP BY news_id HAVING ( ccc *100 ) / ".sizeof($shingles)." > 25");
                 $nnn = array();
                 if (is_array($ex)) {
                    foreach($ex as $eex) {
                        $n = $db->fetch("select news_title, news_description from news where news_id = ".$eex["news_id"]);
                        $nnn[] = "[".$eex["news_id"]."] (".$eex["ccc"]." из ".sizeof($shingles).") ".str_replace("\"", "&quot;", $n["news_title"])."\n".str_replace("\"", "&quot;", $n["news_description"]);
                    }
                 }
                 if (sizeof($nnn) > 0) $nnn = implode("\n\n\n", $nnn); else $nnn = "";
                 $sss["shingles".$item["news_id"]] = "<font title=\"$nnn\">Похожих новостей: ".sizeof($ex)."</font>";
                 $tpl->fid_select("content", "news[".$item["news_id"]."][groups_id]", $obj->Rubrikators["groups"]["values"], $item["groups_id"]);
              }
              $tpl->fid_array("content", $sss);
           }
}

$tpl->fid_load("main", "index.main.html", "page_title");

foreach($tpl->files as $key => $value) {
   $tpl->fid_array($key, $lang);
}

$tpl->fid_show("main");

?>