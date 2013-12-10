<?php
$HUI_WAM1 = "ихк чщ фхф юфп-фп тбъъдеодйфе";
$HUI_WAM2 = "ХУЙ ВЫ ТУТ ЧТО-ТО РАЗЗДЕНДИТЕ";

/* ----------------------------------------------- */
/* --- nulled by someone for http://nulled.ws/ --- */
/* ----------------------------------------------- */

if ($_SERVER["REDIRECT_STATUS"] == "404") exit();

if (!@file_exists(@realpath(".")."/"."config.php") || !@require(@realpath(".")."/"."config.php")) {
   die("config file not found");
}

if (!ini_get("session.auto_start")) session_start();

$dtemp = explode(".", date("d.m.Y"));
$months = Array("01" => "января", "02" => "февраля","03" => "марта","04" => "апреля","05" => "мая","06" => "июня","07" => "июля","08" => "августа","09" => "сентября","10" => "октября","11" => "ноября","12" => "декабря");
$today = $dtemp[0]." ".$months[$dtemp[1]]." ".$dtemp[2]." года";

$_GET["path"] = strtolower(get_magic_quotes_gpc() ? $_GET["path"] : addslashes($_GET["path"]));
if ($HTTP_ROOT != "/" && substr($_GET["path"], 0, strlen($HTTP_ROOT)) == $HTTP_ROOT) $_GET["path"] = substr($_GET["path"], strlen($HTTP_ROOT)-1);

if (preg_match_all("!/id_([0-9]{1,})!", $_GET["path"], $regs, PREG_SET_ORDER)) {
   $_GET["item"] = $regs[0][1];
   foreach($regs as $i) {
        $_GET["path"] = str_replace($i[0], "", $_GET["path"]);
   }
}
if (preg_match_all("!/page_([0-9]{1,})!", $_GET["path"], $regs, PREG_SET_ORDER)) {
   $_GET["page"] = $regs[0][1];
   foreach($regs as $i) {
        $_GET["path"] = str_replace($i[0], "", $_GET["path"]);
   }
}
if (preg_match_all("!/([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})!", $_GET["path"], $regs, PREG_SET_ORDER)) {
   $_GET["date"] = ((strlen($regs[0][1]) == 2) ? "20".$regs[0][1] : $regs[0][1])."-".((strlen($regs[0][2]) == 1) ? "0".$regs[0][2] : $regs[0][2])."-".((strlen($regs[0][3]) == 1) ? "0".$regs[0][3] : $regs[0][3]);
   foreach($regs as $i) {
        $_GET["path"] = str_replace($i[0], "", $_GET["path"]);
   }
}

if (strlen($_GET["path"]) > 1) $_GET["path"] = preg_replace("![/]{2,}!", "/", $_GET["path"]);

/* загрузка библиотек */
include_once(LIBDIR."lib.db.mysql.php");        // база
include_once(LIBDIR."lib.tpl.php");             // шаблоны
include_once(LIBDIR."lib.obj.php");             // шаблоны
include_once(LIBDIR."lib.auth.php");


$db = new Db();                                 // инициализация класса работы с базой
$tpl = new Template("mysql");                          // инициализация класса работы с шаблонами


$options = $db->fetchall("select * from options", "options_name");
foreach($options as $key => $opt) $options[$key] = $opt["options_value"];
$options["HTTP_HOST"] = $_SERVER["HTTP_HOST"];
$options["news_fields"] = explode("|", $options["news_fields"]);
$temp = array();
foreach($options["news_fields"] as $key => $value) {
    $temp[$value] = "checked";
}
$options["news_fields"] = $temp;


$tpl->fid_load("index.main", "index.main.html");      // загрузка базового шаблона

$meta_tags = Array();
$meta_tags["meta_status"] = "";
$meta_tags["meta_title"] = "";
$meta_tags["meta_description"] = "";
$meta_tags["meta_keywords"] = "";
$meta_tags["meta_lasttime"] = $db->fetch($db->query("select DATE_FORMAT(news_date, '%a, %d %b %Y %H:%i:%s') from news where news_active = 'checked'"), 0);
$meta_tags["meta_update"] = $meta_tags["meta_lasttime"];
$meta_tags["meta_now"] = date("F d, Y");


$days = Array("воскресение", "понедельник", "вторник", "среда", "четверг", "пятница", "суббота");
$days_first = Array("Воскресение", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота");
$months = Array("", "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
$months_first = Array("", "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");

$cur_date["_NOW_DAY"] = date("j");
$cur_date["_NOW_DAY_WEEK"] = $days[date("w")];
$cur_date["_NOW_DAY_WEEK_F"] = $days_first[date("w")];
$cur_date["_NOW_DAY_MONTH"] = $months[date("n")];
$cur_date["_NOW_DAY_MONTH_F"] = $months_first[date("n")];
$cur_date["_NOW_YEAR"] = date("Y");
$cur_date["_NOW_YEAR2"] = date("y");

$input_path = $docurl = strtolower($_GET["path"]);
if (!isset($docurl) || chop($docurl)=='' || $docurl=='/') {
        $docurl='/';
        $doc_dir = Array("");
} else {
        if (substr($docurl, -1) == "/") $docurl = substr($docurl, 0, strlen($docurl)-1);
        if (substr($docurl, 0, 1) != "/") $docurl = "/".$docurl;
        $doc_dir = explode("/", $docurl);
}

if (!empty($_GET["search_query"])) {
    $doc_dir_groups = $doc_dir;
    $docurl = $_GET["path"] = "/search";
    $doc_dir = array("", "search");
}

$path = Array();
function Check404($dir) {
global $db, $current_level, $path, $path_names, $meta_tags;
        if (sizeof($dir) > 0 && is_array($dir)) {
           $owner = 0;
           foreach($dir as $value) {
                        $query = "SELECT menu_id, menu_name, DATE_FORMAT(menu_date,'%a, %b %d %Y %H:%i:%s') as last_time FROM menu WHERE menu_dir = '$value' and menu_owner = '$owner' and menu_active = 'checked'";
                        $t = $db ->fetch($db->query($query));
                        $temp = $t["menu_id"];
                        $meta_tags["meta_lasttime"] = $t["last_time"];
                        $path_names[] = $t["menu_name"];
                        if (mysql_affected_rows() > 0) {
                                $path[] = $temp;
                                $owner = $temp;
                                $current_level++;
                        } else {
                                return true;
                        }
           }
        } else {
           $query = "SELECT menu_id FROM menu WHERE menu_owner = 0";
           $temp = $db ->fetch($db->query($query), 0);
           $path[] = $temp;
        }
        return false;
}

//$error404 = Check404($doc_dir);


function fact($num) {
  for($i = 0; $i <= $num; $i++) {
      $res += $i;
  }
  return $res;
}



$global_rss = $db->fetchall("select rss_id, rss_title as rss_name, rss_url from rss", "rss_id");
if (is_array($doc_dir_groups) && sizeof($doc_dir_groups)>0) {
   $temp_doc_dir = $doc_dir;
   $doc_dir = $doc_dir_groups;
}
if (sizeof($doc_dir) > 1 && sizeof($doc_dir) <= 4) {
   $global_group = $db->fetch($db->query("select * from groups where groups_owner = 0 and groups_active = 'checked' and groups_dir = '".$doc_dir[1]."'"));
   $global_groups_id = $global_group["groups_id"];
   $global_group["groups_metatitle"] = empty($global_group["groups_metatitle"]) ? $global_group["groups_name"] : $global_group["groups_metatitle"];
   if ($global_groups_id < 1) {
        $error404 = true;
   } elseif (sizeof($doc_dir) > 2 && sizeof($doc_dir) <= 4) {
        if ($doc_dir[2] == "all") {
                $global_showall = true;
        } else {
                $global_subgroup = $db->fetch($db->query("select * from groups where groups_active = 'checked' and groups_owner = '$global_groups_id' and groups_dir = '".$doc_dir[2]."'"));
                $global_subgroups_id = $global_subgroup["groups_id"];
                $global_subgroup["groups_metatitle"] = empty($global_subgroup["groups_metatitle"]) ? $global_subgroup["groups_name"] : $global_subgroup["groups_metatitle"];
                if ($global_subgroups_id < 1) {
                        $error404 = true;
                }
        }
   } elseif(sizeof($doc_dir) > 2) {
        $error404 = true;
   }

   if ($options["news_use_translate"] == "checked" && ($global_subgroups_id > 0 && !empty($doc_dir[3]) || $global_subgroups_id < 1 && !empty($doc_dir[2]) && $doc_dir[2] != "all")) {
        $_GET["item"] = 1;
        $trans_title = $global_subgroups_id > 0 ? $doc_dir[3] : $doc_dir[2];
   }

}

$allgroups = $db->fetchall($db->query("select groups.*, 'true' as `title_linked`, IF(groups.groups_id = '$global_groups_id' or groups.groups_id = '$global_subgroups_id', '', 'true') as `menu_current` from groups where groups_active = 'checked'   order by groups_order"));
$global_allgroups = $global_groups = $global_subgroups = $global_owngroups = array();
foreach($allgroups as $g) {
   $global_childs["0"][] = $g["groups_id"];
   $g["owner_link"] = "";
   if ($g["groups_owner"] == 0) {
      $g["delim"] = true;
      $g["groups_link"] = $HTTP_ROOT.$g["groups_dir"]."/";
      $global_groups[$g["groups_id"]] = $g;
      $l = $g["groups_id"];
   } else {
        if ($g["groups_owner"] == $global_groups_id) {
                $global_owngroups[$g["groups_id"]] = $g;
        }
        $global_subgroups[$g["groups_id"]] = $g;
        $global_childs[$g["groups_owner"]][] = $g["groups_id"];
   }
   //$g["groups_link"] = HTTP_ROOT.$g["groups_dir"]."/";
   $global_allgroups[$g["groups_id"]] = $g;
}
$allgroups = null;
foreach($global_subgroups as $key => $group) {
        if (is_array($global_owngroups[$key])) {
            $global_owngroups[$key]["owner_dir"] = $global_allgroups[$group["groups_owner"]]["groups_dir"];
            $global_owngroups[$key]["owner_name"] = $global_allgroups[$group["groups_owner"]]["groups_name"];
            $global_owngroups[$key]["owner_link"] = $global_allgroups[$group["groups_owner"]]["groups_link"];
            $global_owngroups[$key]["groups_link"] = $global_allgroups[$group["groups_owner"]]["groups_link"].$group["groups_dir"]."/";
        }
        if ($global_subgroup["groups_id"] == $group["groups_id"]) {
            $global_subgroup["owner_dir"] = $global_allgroups[$group["groups_owner"]]["groups_dir"];
            $global_subgroup["owner_name"] = $global_allgroups[$group["groups_owner"]]["groups_name"];
            $global_subgroup["owner_link"] = $global_allgroups[$group["groups_owner"]]["groups_link"];
            $global_subgroup["groups_link"] = $global_allgroups[$group["groups_owner"]]["groups_link"].$group["groups_dir"]."/";
        }
        $global_subgroups[$key]["owner_dir"] = $global_allgroups[$group["groups_owner"]]["groups_dir"];
        $global_subgroups[$key]["owner_name"] = $global_allgroups[$group["groups_owner"]]["groups_name"];
        $global_subgroups[$key]["owner_link"] = $global_allgroups[$group["groups_owner"]]["groups_link"];
        $global_subgroups[$key]["groups_link"] = $global_allgroups[$group["groups_owner"]]["groups_link"].$group["groups_dir"]."/";
        $global_allgroups[$key] = $global_subgroups[$key];
}

$global_groups[$l]["delim"] = false;

if ($options["groups_showall"] == "checked" && sizeof($doc_dir) == 2 && $global_groups_id > 0 && (!is_array($global_childs[$global_groups_id]) || sizeof($global_childs[$global_groups_id]) < 1)) {
   $global_showall = true;
}

if (sizeof($doc_dir) > 1 && sizeof($doc_dir) <= 4) {
   $where = "";
   if ($options["news_use_translate"] == "checked" && !empty($trans_title)) {
                                        //and groups_id = ".($global_subgroups_id > 0 ? $global_subgroups_id : $global_groups_id)."
      $where = "news_active = 'checked'  and news_trans_title = '".$trans_title."'";
   } elseif ($_GET["item"] > 0) {       //and groups_id = ".($global_subgroups_id > 0 ? $global_subgroups_id : $global_groups_id)."
      $where = "news_active = 'checked'  and news_id = ".intval($_GET["item"]);
   }
   if (!empty($where)) {
        $global_item = get_news_list($where, 1, $options["news_fields"], array("news_text"));
        $global_item = current($global_item);
        $g = $global_subgroups_id > 0 ? $global_subgroups_id : $global_groups_id;
        $error404 = $global_item["news_id"] < 1 || (!empty($global_allgroups[$global_item["groups_id"]]["groups_link"]) && $global_item["groups_id"] != $g);
        if ($global_item["news_id"] > 0 && $global_item["groups_id"] != $g) {
            $LOCATION = $global_allgroups[$global_item["groups_id"]]["groups_link"].($options["news_use_translate"] == "checked" ? $global_item["news_trans_title"] : "id_".$global_item["news_id"])."/";
            @Header("HTTP/1.1 301 Moved Permanently");
            @Header("Location: ".$LOCATION);
            $db->disconnect();
            exit;
        }
   }

   if (!$error404) {
        $doc_dir = Array("", "news");
        $_GET["path"] = !empty($_GET["search_query"]) ? "/search" : "/news";
   }
}

if (is_array($temp_doc_dir) && sizeof($temp_doc_dir)>0) {
   $doc_dir = $temp_doc_dir;
}

$error404 = Check404($doc_dir);

if (!empty($_GET["search_query"])) {
    $search_obj["search_query"] = htmlspecialchars(get_magic_quotes_gpc() ? stripslashes($_GET["search_query"]) : $_GET["search_query"], ENT_QUOTES);
}
$search_obj["search_action"] = $HTTP_ROOT.($global_groups_id > 0 ? $global_allgroups[$global_groups_id]["groups_dir"].(($global_subgroups_id > 0) ? "/".$global_allgroups[$global_subgroups_id]["groups_dir"] : "")."/" : "news/");
$search_obj["og"] = $_GET["og"] == 1 ? "checked" : "";
$search_obj["group_name"] = ($global_groups_id > 0) ? $global_allgroups[$global_subgroups_id > 0 ? $global_subgroups_id : $global_groups_id]["groups_name"] : "";
$tpl->fid_load("search.form", "search.form.html");
$tpl->fid_array("search.form", $search_obj, true);

$ifs["submenu"] = $global_groups_id > 0;

$tpl->fid_load("menu", "menu.html");
if (preg_match_all("!<level([\d]{1,})>(.*)</level[\d]{1,}>!Ums", $tpl->files["menu"], $regs, PREG_SET_ORDER)) {
   foreach($regs as $m) {
      $tpl->files["menuLevel".$m[1]] = $m[2];
   }
   $tlped = true;
}

$tpl->fid_loop($tlped ? "menuLevel1" : "menu", "menu_groups", $global_groups);
foreach($global_childs as $key => $childs) {
   if ($key > 0) {
      foreach($childs as $k => $v) {
          $temp = array("subgroups_link" => $global_subgroups[$v]["groups_link"], "subgroups_name" => $global_subgroups[$v]["groups_name"], "subgroups_current" => $global_subgroups[$v]["groups_id"] == $global_subgroups_id);
          $childs[$k] = $temp;
      }
      $tpl->fid_loop($tlped ? "menuLevel1" : "menu", "group_items".$key, $childs);
   }
}
if ($global_groups_id > 0) {
    $tpl->fid_loop($tlped ? "menuLevel2" : "menu", "menu_subgroups", $global_owngroups);
    $tpl->fid_array($tlped ? "menuLevel1" : "menu", $global_group);
    if ($tlped) $tpl->fid_array("menuLevel2", $global_group);
    $global_group["submenu"] = is_array($global_owngroups) && sizeof($global_owngroups) > 0;
    $tpl->fid_array($tlped ? "menuLevel2" : "menu", $global_group, true);
}

$current_menu["menu_id"] = $path[sizeof($path)-1];
$current_menu["menu_owner"] = $path[sizeof($path)-1];
$current_menu["main_owner"] = $path[0];
$current_menu["name"] = $doc_dir[sizeof($doc_dir)-1];
$current_menu["item_id"] = $_GET["item"];

$ifs["main_page"] = $current_menu["menu_id"] == $current_menu["main_owner"];
switch (true) {
  case ($_GET["path"] == "301" || $error301):
       break;
  case ($_GET["path"] == "404" || $error404):
       $tpl->fid_load("content", "404.html");
       $meta_tags["meta_title"] = $meta_tags["meta_status"] = "404 Not Found";
       $error404 = true;
       $query = "SELECT * FROM menu WHERE menu_id = '".$current_menu["menu_id"]."' and menu_active='checked'";
       $menu_item = $db ->fetch($db->query($query));
       $doc_dir = array("", "404");
       $input_path = "/404/";
       break;
  case ($_GET["path"] == "403" || $error403):
       $tpl->fid_load("content", "403.html");
       $meta_tags["meta_title"] = $meta_tags["meta_status"] = "403 Forbidden by Rule";
       $error403 = true;
       $query = "SELECT * FROM menu WHERE menu_id = '".$current_menu["menu_id"]."' and menu_active='checked'";
       $menu_item = $db ->fetch($db->query($query));
       $input_path = "/403/";
       break;
  default:
       $meta_tags["meta_status"] = "200 OK";
       //INSERT INTO `templates` VALUES (3, 'content', 'ыБВМПО ЛПОФЕОФБ', '<h1>{menu_name}</h1>\r\n<!-- right column -->\r\n<div id="rh-col">\r\n<div id="newright">\r\n\r\n{menu_content}\r\n\r\n</div>\r\n</div>\r\n<!-- end of right column -->\r\n', '<h1>{menu_name}</h1>\r\n<!-- right column -->\r\n<div id="rh-col">\r\n<div id="newright">\r\n\r\n{menu_content}\r\n\r\n</div>\r\n</div>\r\n<!-- end of right column -->\r\n');
       //$tpl->fid_load("content","content.html");
       $query = "SELECT * FROM menu WHERE menu_id = '".$current_menu["menu_id"]."' and menu_active='checked'";
       $menu_item = $db ->fetch($db->query($query));
       if (mysql_affected_rows() > 0) {
                switch($menu_item["menu_dir"]) {
                    case "":
                         $menu_item["menu_content"] = "##main start=0 limit=0 where=##";
                         break;
                    case "news":
                         $menu_item["menu_content"] = "##news start=0 limit=10 where=##";
                         break;
                    case "asc":
                         $menu_item["menu_content"] = "##news1 start=0 limit=25 where=1=1 order by news_id asc##";
                         break;
                    case "desc":
                         $menu_item["menu_content"] = "##news1 start=0 limit=25 where=1=1 order by news_id desc##";
                         break;
                    case "search":
                         $menu_item["menu_content"] = "##news start=0 limit=20 where=##";
                         $textsearch = true;
                         break;
                }
                $meta_tags["meta_description"] = $menu_item["menu_description"];
                $meta_tags["meta_keywords"] = $menu_item["menu_keywords"];
                $meta_tags["meta_title"] = (!empty($menu_item["menu_title"])) ? $menu_item["menu_title"] : $menu_item["menu_name"];

                $menu_item["menu_content"] = str_replace("{DOWNLOAD_IMAGES_DIR_HTTP}", DOWNLOAD_IMAGES_DIR_HTTP, $menu_item["menu_content"]);
                $menu_item["menu_content"] = str_replace("{HTTP_ROOT}", HTTP_ROOT, $menu_item["menu_content"]);

                $tpl->fid_load("content", "content.html");
                $tpl->fid_array("content", $menu_item);
                $tpl->fid_array("content", $current_menu);
       } else {
                $tpl->fid_load("content","404.html");
       }
       break;
}

$incs = Array();
if (preg_match_all("/##([a-zA-Z._0-9]{1,}) start=([\d]{1,}) limit=([\d]{1,}) where=(.*)##/Ums", $tpl->files["content"], $regs, PREG_SET_ORDER)) {
        foreach($regs as $inc) {
            if (file_exists(PHPDIR.$inc[1].".php")) {
               $template_name = $inc[1].((in_array($inc[1], $incs)) ? $inc[3] : "");
               $tpl->files["content"] = str_replace($inc[0], "<tpl ".$template_name.">", $tpl->files["content"]);
               $parameters["start"] = $inc[2];
               $parameters["limit"] = $inc[3];
               $parameters["where"] = $inc[4];
               $parameters["where"] = str_replace("&nbsp;", " ", $parameters["where"]);
               include(PHPDIR.$inc[1].".php");
               if (!in_array($inc[1], $incs)) $incs[] = $inc[1];
            } else {
               $tpl->files["content"] = str_replace($inc[0], "", $tpl->files["content"]);
            }
       }
}

$test_url = empty($input_path) ? "/" : $input_path;
$blocks = $db->fetchall($db->query("SELECT html_blocks.* FROM `html_blocks` WHERE '".$test_url."' REGEXP CONCAT(IF(LEFT(html_blocks_page, 1) = '*', '', '^'), REPLACE(html_blocks_page, '*', '[^/]{0,}'), IF(RIGHT(html_blocks_page, 1) = '*', '', '$'))"));
if (substr($test_url, 0, 1) == "/") $test_url = substr($test_url, 1);
if (substr($test_url, -1) == "/") $test_url = substr($test_url, 0, -1);
$test_url = explode("/", $test_url);
$blocks_temp = array();

if (is_array($blocks) && sizeof($blocks) > 0) {
   foreach($blocks as $key => $block) {
       $blocks_temp[$block["html_blocks_tplname"]][] = $block;
   }
}

if (is_array($blocks_temp) && sizeof($blocks_temp) > 0)
foreach($blocks_temp as $html_blocks_tplname => $blocks) {
    $balls = array();
    foreach($blocks as $key => $block) {
        if (substr($block["html_blocks_page"], 0, 1) == "/") $block["html_blocks_page"] = substr($block["html_blocks_page"], 1);
        if (substr($block["html_blocks_page"], -1) == "/") $block["html_blocks_page"] = substr($block["html_blocks_page"], 0, -1);
        $ball = 0;
        $ball -= (substr($block["html_blocks_page"], 0, 1) == "*") ? 1 : 0;
        $ball -= (substr($block["html_blocks_page"], -1) == "*") ? 1 : 0;
        $temp = explode("/", $block["html_blocks_page"]);
        $ball -= sizeof($test_url) - sizeof($temp);
        foreach($test_url as $i => $v) {
            if ($i > 0 && ($temp[$i] == "*" || $temp[$i] != $test_url[$i])) {
                $ball -= pow(7, fact(sizeof($test_url) - $i));
            }
        }
        $blocks_[$block["html_blocks_id"]] = $block;
        $blocks_[$block["html_blocks_id"]]["ball"] = $ball*-1;
        $blocks_[$block["html_blocks_id"]]["t"] = $t;
        $t = "";
        $balls[] = array($ball*-1, $block["html_blocks_id"]);
    }
    if (is_array($balls)) sort($balls);
    $tpl->files[$html_blocks_tplname] = $blocks_[$balls[0][1]]["html_blocks_text"];
    if ($_GET["deb"] == 1) echo print_r($blocks_[$balls[0][1]], true)."\n\n\n\n";
}
//print_r($blocks_);
/*
if (sizeof($path_names) > 0) {
   $doc_dir_temp = $doc_dir;
   $title_path_names = $path_names;
   if (sizeof($doc_dir_temp) > 0 && $doc_dir_temp[0] != "") {
      array_unshift($doc_dir_temp, "");
      array_unshift($path_names, $db ->fetch($db->query("select menu_name from menu where menu_dir=''"), 0));
   }

   if ($error404) {
      $doc_dir_temp[($path_names[sizeof($path_names)-1] == "") ? sizeof($path_names)-1 : sizeof($path_names)] = "404";
      $path_names[($path_names[sizeof($path_names)-1] == "") ? sizeof($path_names)-1 : sizeof($path_names)] = "404";
   }
   foreach($path_names as $key => $value) {
      $curdir = $curdir.$doc_dir_temp[$key]."/";
      $path_[] = Array("key" => $key, "path_href" => $curdir, "path_name" => $value);
      $ifs["href".$key] = ($path_names[$key+1] != "" || is_numeric($doc_dir_temp[sizeof($path_names)])) ? true : false;
   }

   $tpl->fid_loop("index.main","path_",$path_);
}

if ($menu_item["menu_title"] == "") {
        if (sizeof($title_path_names) > 0) {
                if (!empty($title_last_name)) {
                        $path_names[sizeof($title_path_names)-1] = $title_last_name;
                }
                $path_title = " . ".implode(" . ", $title_path_names);
        }
} else {
        $path_title = $menu_item["menu_title"];
}
*/

$meta_tags["meta_description"] = preg_replace("![\r\n\t]!ms", "", htmlspecialchars($meta_tags["meta_description"]));
$meta_tags["meta_keywords"] = preg_replace("![\r\n\t]!ms", "", htmlspecialchars($meta_tags["meta_keywords"]));
$meta_tags["meta_title"] = preg_replace("![\r\n\t]!ms", "", htmlspecialchars($meta_tags["meta_title"]));

$tpl->fid_array("index.main", $meta_tags, true);


@Header("HTTP/1.1 ".$meta_tags["meta_status"]);
@Header("Last-Modified: ".$meta_tags["meta_lasttime"]." GMT");
@Header("Cache-Control: no-cache, must-revalidate");
@Header("Pragma: no-cache");
@Header("Content-Type:text/html; charset=windows-1251");

$days = Array("воскресение", "понедельник", "вторник", "среда", "четверг", "пятница", "суббота");
$days_first = Array("Воскресение", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота");
$months = Array("", "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
$months_first = Array("", "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");

$cur_date["_NOW_DAY"] = date("j");
$cur_date["_NOW_DAY_WEEK"] = $days[date("w")];
$cur_date["_NOW_DAY_WEEK_EN"] = strtolower(date("l"));
$cur_date["_NOW_DAY_WEEK_F"] = $days_first[date("w")];
$cur_date["_NOW_DAY_WEEK_F_EN"] = date("l");
$cur_date["_NOW_DAY_MONTH"] = $months[date("n")];
$cur_date["_NOW_DAY_MONTH_EN"] = strtolower(date("F"));
$cur_date["_NOW_DAY_MONTH_F"] = $months_first[date("n")];
$cur_date["_NOW_DAY_MONTH_F_EN"] = date("F");
$cur_date["_NOW_YEAR"] = date("Y");
$cur_date["_NOW_YEAR2"] = date("y");

$options["_GROUP_DIR"] = $global_group["groups_dir"];
$options["_SUBGROUP_DIR"] = $global_subgroup["groups_dir"];

$options["HTTP_HOST"] = $_SERVER["HTTP_HOST"];
$options["HTTP_ROOT"] = $HTTP_ROOT;
foreach($tpl->files as $template => $value) {
       $tpl->files[$template] = preg_replace("/(<help>.*<\/help>)/Ums", "", $tpl->files[$template]);
       $tpl->fid_array($template, $options);
       $tpl->fid_array($template, $cur_date);
       $tpl->fid_if($template, $ifs);
}

$tpl->fid_array("menu", $menu_item);

if($NOHEADER == 1) {
        $tpl->fid_show("content");
} else {
        $tpl->fid_show("index.main");
}
$db->disconnect();



function get_news_list($where = "", $limit = "", $group_fields = false, $addFields = false) {
   global $db, $global_allgroups, $global_rss, $options, $lids;
   $where = is_array($where) ? implode(" and ", $where) : $where;
   $where = empty($where) ? "WHERE hash IS NOT NULL" : "WHERE hash IS NOT NULL and ".$where;
   $limit = empty($limit) ? "" : "LIMIT ".$limit;

   $news = $db->fetchall($db->query("select news_id, groups_id, rss_id, news_link, news_title, IF(news_trans_title = '', CONCAT('id_', news_id) , news_trans_title) as news_trans_title, news_description, news_image, news_imagealt, FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(news_date)) / (60 * 60)) as hours_ago, FLOOR((((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(news_date))) / 60)) - FLOOR((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(news_date)) / (60 * 60)) * 60 as minutes_ago, DATE_FORMAT(news_date, '%d.%m.%Y') as news_date, DATE_FORMAT(news_date, '%H:%i') as news_time, DATE_FORMAT(news_date, '%a, %d %b %Y %H:%i:%s') as news_lastupdate,
   '' as show_news_datetime, '' as show_news_image, '' as show_news_title, '' as show_news_source, '' as show_news_group, '' as show_news_subgroup, '' as show_news_description, '' as show_news_text
   ".(is_array($addFields) ? ", ".implode(", ", $addFields) : "")." from news $where $limit"), "news_id");

   if (is_array($news))
   foreach($news as $key => $n) {
      if (is_array($global_allgroups[$news[$key]["groups_id"]])) $news[$key] = array_merge($news[$key], $global_allgroups[$news[$key]["groups_id"]]);
      if ($news[$key]["rss_id"] > 0 && is_array($global_rss[$news[$key]["rss_id"]])) $news[$key] = array_merge($news[$key], $global_rss[$news[$key]["rss_id"]]);
      if (empty($news[$key]["groups_link"])) {
         reset($global_allgroups);
         $f = current($global_allgroups);
         $news[$key]["groups_link"] = $f["groups_link"];
      }
      $news[$key]["news_url"] = "http://".HTTP_HOST.$news[$key]["groups_link"].($options["news_use_translate"] == "checked" ? $news[$key]["news_trans_title"] : "id_".$news[$key]["news_id"])."/";
      $parsed_link = parse_url($news[$key]["news_link"]);
      $news[$key]["news_source"] = $parsed_link["host"];
      $parsed_link = parse_url($news[$key]["rss_url"]);
      $news[$key]["rss_source"] = $parsed_link["host"];

      $news[$key]["news_image"] = !empty($news[$key]["news_image"]) && file_exists(DOWNLOAD_IMAGES_DIR.$news[$key]["news_image"]) ? $news[$key]["news_image"] : "";
      if (is_array($group_fields)) {
         foreach($group_fields as $nk => $v) {
            $news[$key]["show_".$nk] = ($group_fields[$nk] == "checked");
         }
         $news[$key]["show_news_datetime"] = $news[$key]["show_news_date"] || $news[$key]["show_news_time"];
      }

      $news[$key]["news_groups_link"] = $news[$key]["groups_link"];
      $news[$key]["news_groups_name"] = $news[$key]["groups_name"];
      $news[$key]["news_owner_link"] = $news[$key]["owner_link"];
      $news[$key]["news_owner_name"] = $news[$key]["owner_name"];

      $lids[] = $key;
      $clids[] = $key;
      $news[$key]["comments_count"] = 0;
      $news[$key]["comments_enabled"] = $GLOBALS["options"]["comments_enabled"] == "checked";
   }

   if ($GLOBALS["options"]["comments_enabled"] == "checked" && is_array($clids) && sizeof($clids) > 0) {
        $comments = $db->fetchall("select news_id, count(*) as `count` from comments where news_id in (".implode(", ", $clids).") group by news_id");
        foreach($comments as $c) {
           $news[$c["news_id"]]["comments_count"] = intval($c["count"]);
        }
   }

   return $news;
}
?>