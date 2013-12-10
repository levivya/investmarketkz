<?
$HUI_WAM1 = "ËıÍ ˜˘ ÙıÙ ˛ÙÔ-ÙÔ Ú·˙˙‰ÂÓ‰ÈÙÂ";
$HUI_WAM2 = "’”… ¬€ “”“ ◊“Œ-“Œ –¿««ƒ≈Õƒ»“≈";

/* ----------------------------------------------- */
/* --- nulled by someone for http://nulled.ws/ --- */
/* ----------------------------------------------- */

$_config_loaded = false;
@include ("config.php");
if ($_config_loaded !== true) {
   die("config.php not found");
}

if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__))) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__));
use_functions("get_content,get_proxy,get_agent,rss_get_content,utf_to_win");

function getRSSFields($rss_url) {
        global $rss_item_tag_name;

        $temp_url = str_replace("http://", "", $rss_url);
        $surl = parse_url($rss_url);
        $sUrl = substr($temp_url, strpos($temp_url, "/"), strlen($temp_url));
        $sHost = substr($temp_url, 0, strpos($temp_url, "/"));
        $xml = @file_get_contents($rss_url);
        if (!$xml) return false;
        if (strpos($xml, "<item>") !== false || strpos($xml, "<item ") !== false) {
            $rss_item_tag_name = "item";
        } elseif (strpos($xml, "<entry>") !== false || strpos($xml, "<entry ") !== false) {
            $rss_item_tag_name = "entry";
        }

        $items = array();
        $links_n = 0;

        $xmlParser = xml_parser_create();
        xml_parser_set_option($xmlParser,XML_OPTION_CASE_FOLDING,TRUE);
        xml_parser_set_option($xmlParser,XML_OPTION_SKIP_WHITE,TRUE);
        xml_set_element_handler($xmlParser,"se","ee");
        xml_set_character_data_handler($xmlParser,"cd");
        xml_parse($xmlParser,$xml);
        xml_parser_free($xmlParser);

        global $items;
        $array = array();
        if (is_array($items))
        foreach($items as $item) {
          foreach($item as $key => $value) {
             if ($key != $rss_item_tag_name) $array[$key] = $key;
             if (is_array($value["attributes"])) {
                foreach($value["attributes"] as $a_key => $a_value) {
                   $array[$key.":".$a_key] = $key.":".$a_key;
                }
             }
          }
        }
        $temp = array(array("", ""));
        foreach($array as $key => $value) {
          $temp[] = array($key, $value);
        }
        return $temp;
}


$action = (!empty($_GET["action"])) ? $_GET["action"] : ((!empty($_POST["action"])) ? $_POST["action"] : "");
$action = !empty($_POST["group_action"]) ? "group_action" : $action;

$obj = new SiteObject("rss");
$obj->Table = Array("table_name" => "rss", "table_title" => "RSS ÎÂÌÚ˚");
$page_title = $lang["page_title"];
$obj->Fields["rss_id"]["field_title"] = $lang["title_rss_id"];
$obj->Fields["rss_title"]["field_title"] = $lang["title_rss_title"];
$obj->Fields["rss_title"]["no_convert"] = true;
$obj->Fields["rss_url"]["field_title"] = $lang["title_rss_url"];
$obj->Fields["groups_id"]["field_title"] = $lang["title_groups_id"];
$obj->Fields["groups_id"]["field_type"] = "enum";
$obj->Fields["rss_last_update"]["field_title"] = $lang["title_rss_last_update"];
$obj->Fields["rss_active"]["field_title"] = $lang["title_rss_active"];
$obj->Fields["rss_striptags_description"]["field_title"] = $lang["title_rss_striptags_description"];
$obj->Fields["rss_striptags_text"]["field_title"] = $lang["title_rss_striptags_text"];
$obj->Fields["rss_recivetext"]["field_title"] = $lang["title_rss_recivetext"];
$obj->Fields["rss_reciveimages"]["field_title"] = $lang["title_rss_reciveimages"];
$obj->Fields["rss_texttemplate"]["field_title"] = $lang["title_rss_texttemplate"];
$obj->Fields["rss_uniq_id"]["field_title"] = $lang["title_rss_uniq_id"];
$obj->Fields["rss_print_link_from"]["field_title"] = $lang["title_rss_print_link_from"];
$obj->Fields["rss_print_link_to"]["field_title"] = $lang["title_rss_print_link_to"];
$obj->Fields["rss_titletemplate"]["field_title"] = $lang["title_rss_titletemplate"];
$obj->Fields["rss_interval"]["field_title"] = $lang["title_rss_interval"];
$obj->Fields["rss_fields"]["no_convert"] = true;

$obj->Options["objects_options_show_fields"] = "rss_id,rss_title,rss_last_update,DATE_FORMAT(rss_last_update, '%d.%m.%Y %H:%i') as rss_last_update,rss_title as delete_name, IF(rss_active = '', 'close_open.gif', 'old_close_open.gif') as action_image, IF(rss_active = '', '".$lang["button_activate"]."', '".$lang["button_deactivate"]."') as action_title, IF(rss_active = '', 'activate', 'deactivate') as action, '1' as `update`, IF(rss_status <> '', CONCAT('<font color=red>', rss_title, '</font>&nbsp;<span style=\"padding: 0px 5px;background: #ffcc00;font-weight: bold;cursor : help;\" title=\"', rss_status,'\">?</span>'), rss_title) as rss_title";
$obj->Options["objects_options_required_fields"] = Array(Array("field_name" => "rss_url", "js_error" => "rss_url == ''", "as" => "!=''", "errormsg" => $lang["errors_rss_fields_url"]));
$obj->Options["objects_options_edit_fields"] = "groups_id,rss_title,rss_url,rss_texttemplate,rss_uniq_id,rss_print_link_from,rss_print_link_to,rss_titletemplate,rss_interval,rss_active,rss_fields,rss_striptags_description,rss_striptags_text,rss_recivetext,rss_reciveimages,rss_tags_leave,rss_ignore_global_manual,rss_replacement,rss_reciveswf";
$obj->Options["objects_options_edit_template"] = "rss.edit.html";


$count = $obj->CountItems();
$obj->add = ($_globalLimits["rss_source_items"] == -1 || $count < $_globalLimits["rss_source_items"]);
$obj->activate = true;
$obj->edit = true;
$obj->delete = true;
$obj->items_per_page = 50;
$obj->HTTP_ROOT = $HTTP_ROOT;

$obj->rss_interval = 5;
/*
$obj->rss_striptags_description
$obj->rss_striptags_text
$obj->rss_recivetext
$obj->rss_reciveimages
*/

$obj->Links["groups_id"] = Array("TableName" => "groups", "SourceFieldName" => "groups_id", "DestinationFieldName" => "groups_id", "DestinationTitleFieldName" => "groups_name", "Where" => "");
$obj->Rubrikators["groups"] = Array("TableName" => "groups", "TableTitle" => $lang["title_groups_id"], "IDField" => "groups_id", "TitleField" => "groups_name", "Where" => "");

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

$obj->Actions = Array(Array("action" => "update", "action_image" => "misc.gif", "action_title" => $lang["button_rss_update"]));
$obj->GroupActions = Array(Array("action" => "update", "action_title" => $lang["button_rss_update"]));

switch($action) {
       case "group_action":
           if (is_array($_POST["selected"]) && sizeof($_POST["selected"]) > 0) {
               switch($_POST["group_action"]) {
                   case "update":
                        set_time_limit(0);
                        if(is_array($_POST["selected"])) {
                          $_SERVER["PHP_SELF"] = $DOCUMENT_ROOT."/admin/cron/source.getcontent.php";
                          $_GET["id"] = $_POST["selected"];
			  $executeByManualCall = true;
                          require("cron/source.getcontent.php");
                        }
                        break;
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
      case "update":
           if ($_GET["item"] > 0) {
              $_GET["id"] = $_GET["item"];
              $_SERVER["PHP_SELF"] = $DOCUMENT_ROOT."/admin/cron/source.getcontent.php";
              $executeByManualCall = true;
              include("cron/source.getcontent.php");
           }
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
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
      case "edit":
           if (!$obj->add && $_GET["item"] == "new") {
              header("Location: rss.php");
              exit();
           }

           if (sizeof($_POST) > 0 && $_POST["rss_interval"] < 1) $_POST["rss_interval"] = 5;
           if ($_GET["item"] < 1 && sizeof($_POST) < 1) {
              $obj->full_edit = false;
              $obj->Options["objects_options_edit_fields"] = "groups_id,rss_title,rss_url,rss_tags_leave";
              $obj->show_return = false;
              $_POST["return"] = "";
           } else {
              $obj->full_edit = true;
              $obj->show_return = true;
              if (empty($_POST["rss_tags_leave"])) $obj->rss_tags_leave = $_POST["rss_tags_leave"] = "<a><img><table><tr><td><th><b><i><u><h1><h2><h3><h4><strong><ol><ul><li><p><br>";
           }

           if (sizeof($_POST) > 0) {
                $temp["rss_news_title"] = $_POST["rss_news_title"];
                $temp["rss_news_description"] = $_POST["rss_news_description"];
                $temp["rss_news_text"] = $_POST["rss_news_text"];
                $temp["rss_news_link"] = $_POST["rss_news_link"];
                $temp["rss_news_image"] = $_POST["rss_news_image"];
                $_POST["rss_fields"] = serialize($temp);
           }

           include("php/edit.php");

           if (empty($obj->rss_titletemplate) && $obj->full_edit) {
              $items = getRSSFields(html_entity_decode($obj->rss_url));
              if (!$items) {
                 $obj->rss_error = true;
              } else {
                 $temp = unserialize($obj->rss_fields);
                 $tpl->fid_select("content", "rss_news_title", $items, $temp["rss_news_title"]);
                 $tpl->fid_select("content", "rss_news_description", $items, $temp["rss_news_description"]);
                 $tpl->fid_select("content", "rss_news_text", $items, $temp["rss_news_text"]);
                 $tpl->fid_select("content", "rss_news_link", $items, $temp["rss_news_link"]);
              }
              $tpl->fid_if_obj("content", $obj);
           }
           break;
      case "preview_titles":
           $tpl->fid_load("content", "rss.title.preview.html");

           if ($_POST["item"] > 0) $rss = $db->fetch($db->query("select * from rss where rss_id = ".$_POST["item"]));
           $rss["rss_titletemplate"] = get_magic_quotes_gpc() ? stripslashes($_POST["rss_titletemplate"]) : $_POST["rss_titletemplate"];
           $rss["rss_url"] = get_magic_quotes_gpc() ? stripslashes($_POST["rss_url"]) : $_POST["rss_url"];
           //$fp = fopen($rss["rss_url"], "r");
           $items = array();
           //echo "ÀÂÌÚ‡†: ".$rss["rss_title"]." (".$rss["rss_url"].")<br><br>\n";
           //echo "<script>focus();</script>\n\n";
           //flush();
           $result = get_content($rss["rss_url"]);
           if (strpos(strtolower($result["headers"]), "404") !== false || empty($result["content"])) {
                //die("<font color=red>".$lang["errors_rss_htmlopen"]."</font>");
                $rss["errorMessage"] = $lang["errors_rss_htmlopen"];
           } else {
                $headers = $result["headers"];
                $xml = $content = $result["content"];
                if (strpos(strtolower($headers), "koi8-r") !== false || strpos(strtolower(substr($xml, strpos(strtolower($xml), "<head>"), strpos(strtolower($xml), "</head>"))), "koi8-r")) $xml = convert_cyr_string($xml, "k", "w");
                if (strpos(strtolower($headers), "utf-8") !== false || strpos(strtolower(substr($xml, strpos(strtolower($xml), "<head>"), strpos(strtolower($xml), "</head>"))), "utf-8")) $xml = utf_to_win($xml);

                preg_match_all("!{(title|image|link|description|text)}!U", $rss["rss_titletemplate"], $regs);
                $positions["image"] = $positions["link"] = $positions["description"] = -1;
                $positions["title"] = array();
                foreach($regs[1] as $k => $r) {
                    if ($r == "title") {
                       $positions[$r][] = $k+1;
                    } else {
                       $positions[$r] = $k+1;
                    }
                }
                if (preg_match_all("!{([^}]{0,})}{title}!U", $rss["rss_titletemplate"], $regs)) {
                    $delimeters = array();
                    foreach($regs[1] as $k => $r) {
                       $delimeters[] = $r;
                       $rss["rss_titletemplate"] = str_replace($regs[0][$k], "{title}", $rss["rss_titletemplate"]);
                    }
                }


                $rss["rss_titletemplate"] = addcslashes($rss["rss_titletemplate"], "[]!-.?*\\()|");
                $rss["rss_titletemplate"] = preg_replace("!{title}|{image}|{link}|{description}|{text}!U", "(.*)", $rss["rss_titletemplate"]);
                $rss["rss_titletemplate"] = str_replace("{skip}", ".*", $rss["rss_titletemplate"]);
                $rss["rss_titletemplate"] = preg_replace("![\n\r\t]!s", "", $rss["rss_titletemplate"]);
                $rss["rss_titletemplate"] = preg_replace("!>[ ]{1,}<!s", "><", $rss["rss_titletemplate"]);
                $xml = preg_replace("![\n\r\t]!s", "", $xml);
                $xml = preg_replace("!>[ ]{1,}<!s", "><", $xml);
                $parsedURL = parse_url($rss["rss_url"]);

                if (preg_match_all("!".$rss["rss_titletemplate"]."!Ums", $xml, $regs, PREG_SET_ORDER)) {
                   foreach($regs as $found) {
                        if ($positions["link"] > 0) {
                            if (substr($found[$positions["link"]], 0, 1) == "/") $found[$positions["link"]] = "http://".$parsedURL["host"].$found[$positions["link"]];
                            if (substr($found[$positions["link"]], 0, 4) != "http") {
                                $page = substr(strrchr($parsedURL["path"], "/"), 1);
                                $dir = strlen($page) > 0 ? substr($parsedURL["path"], 0, -1*strlen($page)) : $parsedURL["path"];
                                if (substr($dir, 0, 1) != "/") $dir = "/".$dir;
                                $found[$positions["link"]] = "http://".$parsedURL["host"].$dir.$found[$positions["link"]];
                            }
                        }

                        $title = "";
                        if (sizeof($positions["title"]) > 0) {
                           foreach($positions["title"] as $pkey => $pos) {
                              $title .= $found[$pos].$delimeters[$pkey];
                           }
                        }

                        $rss["items"][] = array("title" => strip_tags($title),
                                                "link" => $positions["link"] ? $found[$positions["link"]] : "",
                                                "description" => $positions["description"] > 0 ? strip_tags($found[$positions["description"]]) : "",
                                                "text" => $positions["text"] > 0 ? strip_tags($found[$positions["text"]]) : "",
                                                "image" => $positions["image"] > 0 ? htmlspecialchars($found[$positions["image"]]) : "");
                        //echo "<li>".$found[$positions["title"]]."<br>link: <a href=".$found[$positions["link"]].">".$found[$positions["link"]]."</a><br>\n".$found[$positions["description"]]."<br>\nimage: ".(($positions["image"] > 0) ? htmlspecialchars($found[$positions["image"]]) : "")."<br><br>\n";
                   }
                }
           }
           $tpl->fid_loop("content", "items", $rss["items"]);
           $tpl->fid_array("content", $rss, true);
           $tpl->fid_show("content");
           exit;
      case "preview_text":
           $tpl->fid_load("content", "rss.text.preview.html");
           if ($_POST["item"] > 0) $rss = $db->fetch($db->query("select * from rss where rss_id = ".$_POST["item"]));
           $rss["rss_texttemplate"] = get_magic_quotes_gpc() ? stripslashes($_POST["rss_titletemplate"]) : $_POST["rss_titletemplate"];
           $rss["rss_print_link_from"] = get_magic_quotes_gpc() ? stripslashes($_POST["rss_print_link_from"]) : $_POST["rss_print_link_from"];
           $rss["rss_print_link_to"] = get_magic_quotes_gpc() ? stripslashes($_POST["rss_print_link_to"]) : $_POST["rss_print_link_to"];
           $rss["rss_url"] = get_magic_quotes_gpc() ? stripslashes($_POST["rss_url"]) : $_POST["rss_url"];
           $rss["news_link"] = get_magic_quotes_gpc() ? stripslashes($_POST["prrss_url"]) : $_POST["prrss_url"];
           $rss["rss_replacement"] = get_magic_quotes_gpc() ? stripslashes($_POST["rss_replacement"]) : $_POST["rss_replacement"];
           //echo "ÀÂÌÚ‡†: ".$rss["rss_title"]." <a href=".$rss["rss_url"].">".$rss["rss_url"]."</a><br><br>";
           //echo "<script>focus();</script>";
           //flush();
           $rss["setNewLink"] = true;
           $rss["changelink"] = false;
           if (!empty($rss["rss_print_link_from"]) && !empty($rss["rss_print_link_to"])) {
              $rss["news_link_old"] = $rss["news_link"];
              $rss["changelink"] = true;
              $rss["news_link"] = preg_replace("!".$rss["rss_print_link_from"]."!", $rss["rss_print_link_to"], $rss["news_link"]);
              //echo "<br>«‡ÏÂÌ‡ ÒÒ˚ÎÍË Ì‡ ".$rss["news_link"]."<br>";
              //$rss["news_link"]array_merge(array("link" => ), $rss)
           }

           $rss["news_text"] = rss_get_content($rss, false, false, false);
           if (empty($rss["news_text"])) {
                $rss["errorMessage"] = $lang["errors_rss_htmlopen"];
           } elseif (!empty($rss["rss_replacement"])) {
                $rss["rss_replacement"] = explode("\n", $rss["rss_replacement"]);
                foreach($rss["rss_replacement"] as $r) {
                   $r = trim($r);
                   if (!empty($r)) {
                      $r = explode("|", $r);
                      $rss["news_text"] = str_replace($r[0], $r[1], $rss["news_text"]);
                   }
                }
           }
           //echo $news_text;
           $tpl->fid_array("content", $rss, true);
           $tpl->fid_show("content");
           exit;
      case "update":
           $rss_id = $_GET["item"];
           include("source.getcontent.php");
           break;
      default:
           $order = (!empty($_GET["order"])) ? $_GET["order"] : ((!empty($_POST["order"])) ? $_POST["order"] : "");
           $sort = (!empty($_GET["sort"])) ? $_GET["sort"] : ((!empty($_POST["sort"])) ? $_POST["sort"] : "ASC");
           require("php/items.php");
}

$tpl->fid_load("main", "index.main.html", "page_title");
foreach($tpl->files as $key => $value) {
   $tpl->fid_array($key, $lang);
}

$tpl->fid_show("main");

?>