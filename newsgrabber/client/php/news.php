<?
$HUI_WAM1 = "ихк чщ фхф юфп-фп тбъъдеодйфе";
$HUI_WAM2 = "ХУЙ ВЫ ТУТ ЧТО-ТО РАЗЗДЕНДИТЕ";

/* ----------------------------------------------- */
/* --- nulled by someone for http://nulled.ws/ --- */
/* ----------------------------------------------- */

if (!defined("SITE_ID") ||
    !defined("CLIENT_HOST") ||
    !defined("VERSION") ||
    !defined("CATALOG_URL") ||
    !defined("UPDATES_URL") ||
    !defined("TEMPLATES_EDIT") ||
    !defined("TEMPLATES_GROUPS_ADD") ||
    !defined("DIE_ON_CHECK_UPDATE_ERROR") ||
    !is_object($db) ||
    !is_object($tpl))
    die("key error");

include_once(LIBDIR."lib.calendar.php");
use_functions("to_translit");
$rss_template = '<?xml version="1.0" encoding="windows-1251" ?>';
if (file_exists(HOMEDIR."css/rss.xsl")) $rss_template .= '<?xml-stylesheet title="XSL_formatting" type="text/xsl" href="/css/rss.xsl"?>';
$rss_template .= '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
<channel>
        <title>{rss_title}</title>
        <link>http://{HTTP_HOST}</link>
        <description>{rss_description}</description>
        <language>{rss_lang}</language>
        <copyright>{rss_copyright}</copyright>
        <managingEditor>{rss_editormail}</managingEditor>
        <webMaster>{rss_editormail}</webMaster>
        <pubDate>{rss_lastupdate}</pubDate>
        <lastBuildDate>{rss_lastupdate}</lastBuildDate>
        <category>{rss_category}</category>
        <generator>{rss_generator}</generator>
        <docs>http://blogs.law.harvard.edu/tech/rss</docs>
        <cloud domain="rpc.sys.com" port="80" path="/RPC2" registerProcedure="pingMe" protocol="soap"/>
        <ttl>60</ttl>
        <image>
                <url>http://{HTTP_HOST}/img/{rss_imageurl}</url>
                <link>{rss_imagelink}</link>
                <title>{rss_imagetitle}</title>
        </image>
        <rating></rating>
        <loop rss_news><item>
            <title>{news_title}</title>
            <link>{news_url}</link>
            <description>{news_description}</description>
            <author>{rss_editormail}</author>
            <category><if owner_link>{owner_name}: </if owner_link>{groups_name}</category>
            <guid>{news_url}</guid>
            <pubDate>{news_rsslastupdate}</pubDate>
            <if news_image><media:content url="{news_image}" type="{image_type}" height="{image_height}" width="{image_width}" /></if news_image>
        </item>
        </loop rss_news>
</channel>
</rss>';


$lids = array(0);

$selectedDateInt = @strtotime($_GET["date"]);
if ($selectedDateInt > 0) {
   $selectedDate = $_GET["date"];
   $selectedYear = date("Y", $selectedDateInt);
   $selectedMonth = date("m", $selectedDateInt);
   $selectedDay = date("d", $selectedDateInt);
   $by_date = !empty($selectedDate) ? " and news_date BETWEEN '".$selectedYear."-".$selectedMonth."-".$selectedDay." 00:00:00' AND '".$selectedYear."-".$selectedMonth."-".$selectedDay." 23:59:59'" : "";
} else {
   $selectedYear = date("Y");
   $selectedMonth = date("m");
}

$c = new Calendar();
$tpl->fid_load("calendar", "calendar.html");

$c->setTemplate($tpl->files["calendar"]);
if (!empty($selectedDate)) $c->setDate($selectedDate);

switch(true) {
  case !empty($_GET["search_query"]) || $textsearch:
        $template_file = "search.list.html";
        $ids = 0;
        //$groups_ids = $db->fetchccol($db->query("select groups_id from groups where groups_active = 'checked' and groups_id = ".($global_subgroups_id > 0 ? $global_subgroups_id : $global_groups_id)));
        //if (sizeof($groups_ids) > 0) $ids = implode(",", $groups_ids); else $ids = 0;
        $tpl->files["calendar"] = "";
        break;
  case $global_groups_id > 0 && $global_subgroups_id == 0 && $_GET["item"] < 1 && !$global_showall:
        $template_file = "news.groups.html";
        //$groups_ids = $db->fetchccol($db->query("select groups_id from groups where groups_active = 'checked' and (groups_id = $global_groups_id or groups_owner = ".$global_groups_id.")"));
        //if (sizeof($groups_ids) > 0) $ids = implode(",", $groups_ids); else $ids = 0;
        $gr = is_array($global_childs[$global_groups_id]) ? array_merge($global_childs[$global_groups_id], array($group["groups_id"])) : array($group["groups_id"]);
        $where = $where1 = array();
        foreach($gr as $gid) {
            if ($global_group["groups_newsblock_only_images"] != "checked") $where[] = "'0$gid'";
            $where[] = "'1$gid'";
            $where1[] = "'0$gid', '1$gid'";
        }
        (sizeof($where) > 0) ? $where = "hash IN (".implode(", ", $where).")" : $where = "hash IN ('0')";
        (sizeof($where1) > 0) ? $where1 = "hash IN (".implode(", ", $where1).")" : $where1 = "hash IN ('0')";
        $dates = $db->fetch($db->query("select DATE_FORMAT(MAX(news_date), '%Y-%m-%d') as max_date, DATE_FORMAT(MIN(news_date), '%Y-%m-%d') as min_date, DATE_FORMAT(MAX(news_date), '%a, %d %b %Y %H:%i:%s') as news_lastupdate from news where $where1"));
        $validDates = $db->fetchccol($db->query("select DISTINCT DATE_FORMAT(news_date, '%d') from news where $where1 and DATE_FORMAT(news_date, '%Y-%m') = '$selectedYear-$selectedMonth'"));
        $c->setFirstValidDate($dates["min_date"]);
        $c->setLastValidDate($dates["max_date"]);
        $c->setHref($HTTP_ROOT.$global_allgroups[$global_groups_id]["groups_dir"].(($global_subgroups_id > 0) ? "/".$global_allgroups[$global_subgroups_id]["groups_dir"]."/" : "/all/"));
        $tpl->files["calendar"] = $c->getCalc($validDates);
        $meta_tags["meta_title"] = empty($global_group["groups_metatitle"]) ? $global_group["groups_name"] : $global_group["groups_metatitle"];
        $meta_tags["meta_description"] = empty($global_group["groups_metatitle"]) ? "" : $global_group["groups_metadescription"];
        $meta_tags["meta_lasttime"] = $dates["news_lastupdate"];
        break;
  case $global_groups_id > 0 && $global_subgroups_id == 0 && $_GET["item"] < 1 && $global_showall:
        $template_file = "news.subgroups.html";
        //$ids = "0";
        $gr = !empty($selectedDate) && is_array($global_childs[$global_groups_id]) ? array_merge($global_childs[$global_groups_id], array($group["groups_id"])) : array($group["groups_id"]);
        $where = array();
        foreach($gr as $gid) {
            $where[] = "'0$gid', '1$gid'";
        }
        (sizeof($where) > 0) ? $where = "hash IN (".implode(", ", $where).")" : $where = "hash IN ('0')";                                                                                                                                              //news_active = 'checked' and groups_id = ".$global_group["groups_id"]."
        $dates = $db->fetch($db->query("select DATE_FORMAT(MAX(news_date), '%Y-%m-%d') as max_date, DATE_FORMAT(MIN(news_date), '%Y-%m-%d') as min_date, DATE_FORMAT(MAX(news_date), '%a, %d %b %Y %H:%i:%s') as news_lastupdate from news where ".$where));
        $validDates = $db->fetchccol($db->query("select DISTINCT DATE_FORMAT(news_date, '%d') from news where $where and DATE_FORMAT(news_date, '%Y-%m') = '$selectedYear-$selectedMonth'"));
        $c->setFirstValidDate($dates["min_date"]);
        $c->setLastValidDate($dates["max_date"]);
        $c->setHref($HTTP_ROOT.$global_allgroups[$global_groups_id]["groups_dir"].(($global_subgroups_id > 0) ? "/".$global_allgroups[$global_subgroups_id]["groups_dir"]."/" : "/all/"));
        $tpl->files["calendar"] = $c->getCalc($validDates);
        $meta_tags["meta_title"] = empty($global_group["groups_metatitle"]) ? $global_group["groups_name"].(($global_subgroups_id > 0) ? " - ".$global_subgroup["groups_name"] : "") : $global_group["groups_metatitle"];
        $meta_tags["meta_description"] = empty($global_group["groups_metatitle"]) ? "" : $global_group["groups_metadescription"];
        $meta_tags["meta_lasttime"] = $dates["news_lastupdate"];
        break;
  case $global_groups_id > 0 && $global_subgroups_id > 0 && $_GET["item"] < 1:
        $template_file = "news.subgroups.html";
        //$groups_ids = $db->fetchccol($db->query("select groups_id from groups where groups_active = 'checked' and groups_id = $global_subgroups_id"));
        //if (sizeof($groups_ids) > 0) $ids = implode(",", $groups_ids); else $ids = 0;
        $gr = !empty($selectedDate) && is_array($global_childs[$global_groups_id]) ? array_merge($global_childs[$global_groups_id], array($group["groups_id"])) : array($group["groups_id"]);
        $where = array();
        foreach($gr as $gid) {
            $where[] = "'0$gid', '1$gid'";
        }
        (sizeof($where) > 0) ? $where = "hash IN (".implode(", ", $where).")" : $where = "hash IN ('0')";                                                                                                                                              //news_active = 'checked' and groups_id = ".$global_subgroup["groups_id"].""
        $dates = $db->fetch($db->query("select DATE_FORMAT(MAX(news_date), '%Y-%m-%d') as max_date, DATE_FORMAT(MIN(news_date), '%Y-%m-%d') as min_date, DATE_FORMAT(MAX(news_date), '%a, %d %b %Y %H:%i:%s') as news_lastupdate from news where ".$where));
        $validDates = $db->fetchccol($db->query("select DISTINCT DATE_FORMAT(news_date, '%d') from news where $where and DATE_FORMAT(news_date, '%Y-%m') = '$selectedYear-$selectedMonth'"));
        $c->setFirstValidDate($dates["min_date"]);
        $c->setLastValidDate($dates["max_date"]);
        $c->setHref($HTTP_ROOT.$global_allgroups[$global_groups_id]["groups_dir"].(($global_subgroups_id > 0) ? "/".$global_allgroups[$global_subgroups_id]["groups_dir"]."/" : "/all/"));
        $tpl->files["calendar"] = $c->getCalc($validDates);
        $meta_tags["meta_title"] = empty($global_subgroup["groups_metatitle"]) ? $global_group["groups_name"].(($global_subgroups_id > 0) ? " - ".$global_subgroup["groups_name"] : "") : $global_subgroup["groups_metatitle"];
        $meta_tags["meta_description"] = empty($global_subgroup["groups_metatitle"]) ? "" : $global_subgroup["groups_metadescription"];
        $meta_tags["meta_lasttime"] = $dates["news_lastupdate"];
        break;
  case $_GET["item"] > 0:
        $template_file = "news.view.html";
        $ids = 0;
        //$groups_ids = $db->fetchccol($db->query("select groups_id from groups where groups_active = 'checked' and groups_id = ".($global_subgroups_id > 0 ? $global_subgroups_id : $global_groups_id)));
        //if (sizeof($groups_ids) > 0) $ids = implode(",", $groups_ids); else $ids = 0;
        $tpl->files["calendar"] = "";
        break;
  default:
        header("Location: /");
        exit;
        break;
}
//$tpl->files["calendar"] = "";


$tpl->fid_load("content", $template_file);

$groups = $global_owngroups;

if ($_GET["rss"] == 1) {
        
        $gr = $global_subgroups_id > 0 ? array($global_subgroups_id) : (is_array($global_childs[$global_groups_id]) ? array_merge($global_childs[$global_groups_id], array($global_groups_id)) : array($global_groups_id));
        $where = array();
        foreach($gr as $gid) {
            $where[] = "'0$gid'";
            $where[] = "'1$gid'";
        }
        (sizeof($where) > 0) ? $where = "hash IN (".implode(", ", $where).")" : $where = "hash IN ('0')";

        $rss_news = get_news_list("$where order by news_id desc", $options["rss_count"], array(), array("DATE_FORMAT(news_date, '%a, %d %b %Y %H:%i:%s ".date("O")."') as news_rsslastupdate"));
        foreach($rss_news as $key => $news) {
            $rss_news[$key]["news_title"] = htmlspecialchars(html_entity_decode(stripslashes($rss_news[$key]["news_title"])));
            $rss_news[$key]["news_description"] = htmlspecialchars(html_entity_decode(stripslashes($rss_news[$key]["news_description"])));
            $options["rss_image_show"] = "checked";
            if ($options["rss_image_show"] == "checked" && !empty($rss_news[$key]["news_image"]) && file_exists(DOWNLOAD_IMAGES_DIR.$rss_news[$key]["news_image"])) {
               $type = getimagesize(DOWNLOAD_IMAGES_DIR.$rss_news[$key]["news_image"]);
               switch($type[2]) {
                  case 1:
                       $t = "image/gif";
                       break;
                  case 2:
                       $t = "image/jpeg";
                       break;
                  case 3:
                       $t = "image/png";
                       break;
                  case 4:
                       $t = "application/x-shockwave-flash";
                       break;
               }
               $rss_news[$key]["news_image"] = "http://".CLIENT_HOST.DOWNLOAD_IMAGES_DIR_HTTP.$rss_news[$key]["news_image"];
               $rss_news[$key]["image_type"] = $t;
               $rss_news[$key]["image_width"] = $type[0];
               $rss_news[$key]["image_height"] = $type[1];
            } else {
               $rss_news[$key]["news_image"] = false;
            }
        }
        $c = current($rss_news);
        $options["rss_lastupdate"] = $c["news_rsslastupdate"];
        $tpl->files["content"] = $rss_template;
        $tpl->fid_loop("content", "rss_news", $rss_news, true);
        $tpl->fid_array("content", $options);
        $tpl->fid_array("content", $cur_date);
        $tpl->fid_array("content", get_defined_constants());
        
        header("Content-Type: application/xml");
        $tpl->fid_show("content");
        exit;
} elseif (!empty($_GET["search_query"]) || $textsearch) {

        $parameters["limit"] = $group["groups_list_count"] > 0 ? $group["groups_list_count"] : 20;

        $_GET["search_query"] = html_entity_decode($_GET["search_query"]);
        $_GET["search_query"] = preg_replace("![\s]{2,}!", " ", $_GET["search_query"]);
        $_GET["search_query"] = trim($_GET["search_query"]);
        if (!get_magic_quotes_gpc()) $_GET["search_query"] = addslashes($_GET["search_query"]);

        $hash = array();
        if ($_GET["og"] == "1" && ($global_groups_id > 0 || $global_subgroups_id > 0)) {
            $g = $global_subgroups_id > 0 ? $global_subgroups_id : $global_groups_id;
            $gr = array($g);
            foreach($gr as $gid) {
                $hash[] = "'0$gid', '1$gid'";
            }
        }
        $where = array((sizeof($hash) > 0) ? "hash IN (".implode(", ", $hash).")" : "hash IS NOT NULL", "MATCH (news_title, news_text) AGAINST ('".$_GET["search_query"]."' IN BOOLEAN MODE)");
        if (!empty($selectedDate) > 0) $where[] = "and DATE_FORMAT(news_date, '%Y-%m-%d') = '".$selectedDate."'";

        $where = implode(" and ", $where);
        $count = $db->fetch($db->query("select count(news_id) from news where $where"), 0);
        $limit = ($parameters["limit"]*(($_GET["page"] > 0) ? $_GET["page"]-1 : 0)).", ".$parameters["limit"];
        $nav["path"] = $HTTP_ROOT.($global_groups_id > 0 ? $global_allgroups[$global_groups_id]["groups_dir"].(($global_subgroups_id > 0) ? "/".$global_allgroups[$global_subgroups_id]["groups_dir"] : "") : "news");
        $group["groups_newsblock_lastnews_fields"] = unserialize($group["groups_newsblock_lastnews_fields"]);
        $latest_news = get_news_list("".$where." order by news_id desc", $limit, $group["groups_newsblock_lastnews_fields"]);
        $tpl->fid_loop("content", "news", $latest_news, true);

        $s_obj = array("count" => ($count > 0 ? $count : 0));
        $tpl->fid_array("content", $s_obj);
        include(PHPDIR."pages.php");

} elseif ($_GET["item"] > 0) {
        $_GET["item"] = intval($_GET["item"]);
        if ($options["news_use_translate"] == "checked" && !empty($trans_title)) {
            $where = "news_trans_title = '".$trans_title."'";
        } else {
            $where = "news_id = ".$_GET["item"];
        }

        if (!is_array($global_item)) {
           $global_item = get_news_list($where, 1, $options["news_fields"], array("news_text"));
           $global_item = $global_item[0];
        }
        $news = $global_item;
        $lids[] = $global_item["news_id"];
        $meta_tags["meta_title"] = stripslashes($news["news_title"]);
        $meta_tags["meta_description"] = strip_tags(stripslashes($news["news_description"]));
        $meta_tags["meta_lasttime"] = $news["news_lastupdate"];

        if ($options["news_title_limit"] > 0 && strlen($meta_tags["meta_title"]) > $options["news_title_limit"]) {
            if (strpos($meta_tags["meta_title"], " ") < $options["news_title_limit"]) {
                $meta_tags["meta_title"] = substr($meta_tags["meta_title"], 0, $options["news_title_limit"]);
                $asp = strrchr($meta_tags["meta_title"], " ");
                if (!empty($asp) && strpos($meta_tags["meta_title"], $asp) !== false) {
                    $meta_tags["meta_title"] = substr($meta_tags["meta_title"], 0, strpos($meta_tags["meta_title"], $asp));
                }
            } else {
                if (strpos($meta_tags["meta_title"], " ") !== false) $meta_tags["meta_title"] = substr($news["news_title"], 0, strpos($meta_tags["meta_title"], " "));
            }
        }

        $news["news_text"] = str_replace("\\'", "'", $news["news_text"]);
        $news["news_text"] = str_replace("{DOWNLOAD_IMAGES_DIR_HTTP}", DOWNLOAD_IMAGES_DIR_HTTP, $news["news_text"]);
        $news["news_text"] = str_replace("{HTTP_ROOT}", HTTP_ROOT, $news["news_text"]);
        $news["news_text"] = preg_replace("!(<[ph0-9]{1,}>[\s]{0,}</[ph0-9]{1,}>)|(<[ph0-9]{1,}>&nbsp;</[ph0-9]{1,}>)!Umsi", "", $news["news_text"]);

        $parsed_link = parse_url($news["news_link"]);
        $Host = "http://".$parsed_link["host"]."/";
        if ($options["grab_links_encode"] == "checked" || $options["grab_links_open_in_blank"] == "checked" || $options["grab_links_nofollow"] == "checked") $news["news_text"] = preg_replace_callback("!<a (.*)>(.*)</a>!Ui", "conv", $news["news_text"]);

        //$news["show_news_datetime"] = $news["show_news_date"] || $news["show_news_time"];
        //$news["news_image"] = !empty($news["news_image"]) && file_exists(DOWNLOAD_IMAGES_DIR.$news["news_image"]) ? $news["news_image"] : "";
        //$news["news_source"] = $parsed_link["host"];

        if (preg_match("!<img !", $news["news_text"])) {
                //$news["news_text"] = str_replace("<img ", "<img align=\"left\" ", $news["news_text"]);
                if (!$news["show_news_image"]) $news["news_image"] = "";
        }

        if ($global_subgroups_id > 0) {
                if (!empty($global_subgroup["groups_metatitle"]) && $global_subgroup["groups_metatitle_add"] <> "") $meta_tags["meta_title"] = $global_subgroup["groups_metatitle_add"] == "before" ? $global_subgroup["groups_metatitle"]." - ".$meta_tags["meta_title"] : $meta_tags["meta_title"]." - ".$global_subgroup["groups_metatitle"];
                $global_subgroup["groups_mainblock_enabled"] = $options["news_mainblock_enabled"];
                $global_subgroup["groups_lastblock_enabled"] = $options["news_lastblock_enabled"];
        } else {
                if (!empty($global_group["groups_metatitle"]) && $global_group["groups_metatitle_add"] <> "") $meta_tags["meta_title"] = $global_group["groups_metatitle_add"] == "before" ? $global_group["groups_metatitle"]." - ".$meta_tags["meta_title"] : $meta_tags["meta_title"]." - ".$global_group["groups_metatitle"];
                $global_group["groups_mainblock_enabled"] = $options["news_mainblock_enabled"];
                $global_group["groups_lastblock_enabled"] = $options["news_lastblock_enabled"];
        }

        showMainNewsBlock();
        showAllLastNewsBlock();

        $news["groups_mainblock_enabled"] = $options["news_mainblock_enabled"];
        $news["groups_lastblock_enabled"] = $options["news_lastblock_enabled"];

        if ($options["comments_enabled"] == "checked") {
            $tpl->fid_load("comments", "comments.html");

            $news["comments_enabled"] = $options["comments_enabled"];
            $news["comments_use_captcha"] = $options["comments_use_captcha"];
            if (!empty($_POST["saveComment"])) {
                //$_POST["text"] = trim(substr(strip_tags($_POST["text"], "<a>"), 0, 500));
                $_POST["text"] = trim(strip_tags($_POST["text"], "<a>"));
                switch(true) {
                    case $options["comments_use_captcha"] == "checked" && (empty($_POST["code"]) || strtolower($_POST["code"]) != strtolower($_SESSION["captches"][$_POST["captid"]])):
                         $formData["error1"] = true;
                         break;
                    case empty($_POST["text"]):
                         $formData["error2"] = true;
                         break;
                }

                if (!$formData["error1"] && !$formData["error2"]) {
                    $db->query("insert into comments (comments_date, comments_author, comments_email, comments_text, news_id )
                                              values (NOW(), '{name}', '{email}', '{text}', ".$news["news_id"].")", $_POST);
                    if (mysql_errno()) {
                        $formData["error3"] = true;
                    } else {
                        header("Location: ".$_SERVER["HTTP_REFERER"]);
                        exit();
                    }
                } else {
                }

                if ($formData["error1"] || $formData["error2"] || $formData["error3"]) {
                    if (ini_get("magic_quotes_gpc")) {
                        $_POST["text"] = stripslashes($_POST["text"]);
                    }
                    $formData["text"] = htmlspecialchars($_POST["text"], ENT_QUOTES);
                }

                if (ini_get("magic_quotes_gpc")) {
                    $_POST["name"] = stripslashes($_POST["name"]);
                    $_POST["email"] = stripslashes($_POST["email"]);
                }
                $formData["name"] = htmlspecialchars($_POST["name"], ENT_QUOTES);
                $formData["email"] = htmlspecialchars($_POST["email"], ENT_QUOTES);
                setcookie("cname", $formData["name"], time()+3600*24*365);
                setcookie("cmail", $formData["email"], time()+3600*24*365);

            } else {
                if (!empty($_COOKIE["cname"])) $formData["name"] = $_COOKIE["cname"];
                if (!empty($_COOKIE["cmail"])) $formData["email"] = $_COOKIE["cmail"];
            }

            $comments = $db->fetchall("select *,
                                          DATE_FORMAT(comments_date, '%d') as comments_date_day,
                                          DATE_FORMAT(comments_date, '%m') as comments_date_month,
                                          DATE_FORMAT(comments_date, '%Y') as comments_date_year,
                                          DATE_FORMAT(comments_date, '%H') as comments_date_hour,
                                          DATE_FORMAT(comments_date, '%i') as comments_date_minute,
                                          DATE_FORMAT(comments_date, '%d.%m.%Y %H:%i') as comments_date
                                   from comments where news_id = ".$news["news_id"]." order by comments_id asc");

            $temp["grab_links_encode"] = $options["grab_links_encode"];
            $temp["grab_links_open_in_blank"] = $options["grab_links_open_in_blank"];
            $temp["grab_links_nofollow"] = $options["grab_links_nofollow"];
            $options["grab_links_encode"] = "";
            $options["grab_links_open_in_blank"] = "checked";
            $options["grab_links_nofollow"] = "checked";
            $nochange_url = true;
            foreach($comments as $k => $c) {
                $comments[$k]["comments_email"] = htmlspecialchars($comments[$k]["comments_email"], ENT_QUOTES);
                $comments[$k]["comments_name"] = htmlspecialchars($comments[$k]["comments_name"], ENT_QUOTES);
                $comments[$k]["comments_text"] = nl2br($comments[$k]["comments_text"]);

                $comments[$k]["comments_text"] = preg_replace_callback("!<a (.*)>(.*)</a>!Ui", "conv", $comments[$k]["comments_text"]);
            }
            $options["grab_links_encode"] = $temp["grab_links_encode"];
            $options["grab_links_open_in_blank"] = $temp["grab_links_open_in_blank"];
            $options["grab_links_nofollow"] = $temp["grab_links_nofollow"];
            $tpl->fid_loop("comments", "comments", $comments);

            if ($options["comments_use_captcha"] == "checked") {
                $formData["captid"] = md5(date("Ymdhis"));
                $chars = array();
                for($i = 48; $i < 123; $i++) if ($i < 58 || ($i > 64 && $i < 91) || $i > 96) $chars[] = chr($i);
                $_SESSION["captches"][$formData["captid"]] = strtoupper($chars[rand(0, sizeof($chars)-1)].$chars[rand(0, sizeof($chars)-1)].$chars[rand(0, sizeof($chars)-1)].$chars[rand(0, sizeof($chars)-1)].$chars[rand(0, sizeof($chars)-1)].$chars[rand(0, sizeof($chars)-1)]);
                $formData["ccc"] = $_SESSION["captches"][$formData["captid"]];
            }
            $tpl->fid_array("comments", $formData, true);
            $tpl->fid_array("comments", $news, true);
        }
        $tpl->fid_array("content", $news, true);
} else {
        $global_subgroup["title_linked"] = false;
        if ($global_subgroups_id == 0 && !$global_showall) {
                $global_group["owner_dir"] = "";
                array_unshift($groups, $global_group);
        } else {
                if ($global_showall) {
                        $groups = Array($global_group);
                } else {
                        $groups = Array($global_subgroup);
                }
                //$global_groups_id = $global_subgroups_id;
        }

        showMainNewsBlock();

        $ifs["all_last_news"] = false;
        if ($global_subgroups_id == 0 && !$global_showall) showAllLastNewsBlock();

        $global_group["groups_newsblock_fields"] = unserialize($global_group["groups_newsblock_fields"]);
        foreach($groups as $key => $group) {
            $group["groups_newsblock_lastnews_enabled"] = $groups[$key]["groups_newsblock_lastnews_enabled"] = $global_group["groups_newsblock_lastnews_enabled"];
            $group["groups_newsblock_lastnews_count"] = $groups[$key]["groups_newsblock_lastnews_count"] = $global_group["groups_newsblock_lastnews_count"];
            $group["groups_newsblock_lastnews_fields"] = $groups[$key]["groups_newsblock_lastnews_fields"] = $global_group["groups_newsblock_lastnews_fields"];
            if ($group["groups_show_in_owner"] == "checked" || ($global_subgroups_id == 0 && $group["groups_id"] == $global_groups_id) || ($global_subgroups_id != 0 && $group["groups_id"] == $global_subgroups_id)) $temp_groups[] = $group;
        }
        //$tpl->fid_loop("content", "groups", $temp_groups);

        foreach($temp_groups as $groups_key => $group) {
                if ($global_showall && !empty($by_date)) {
                   $gr = is_array($global_childs[$group["groups_id"]]) ? array_merge($global_childs[$group["groups_id"]], array($group["groups_id"])) : array($group["groups_id"]);
                } else {
                   $gr = array($group["groups_id"]);
                }
                $where = $where1 = array();
                foreach($gr as $gid) {
                    if ($global_group["groups_newsblock_only_images"] != "checked") $where[] = "'0$gid'";
                    $where[] = "'1$gid'";
                    $where1[] = "'0$gid', '1$gid'";
                }
                (sizeof($where) > 0) ? $where = "hash IN (".implode(", ", $where).")" : $where = "hash IN ('0')";
                (sizeof($where1) > 0) ? $where1 = "hash IN (".implode(", ", $where1).")" : $where1 = "hash IN ('0')";

                if ($global_subgroups_id == 0 && !$global_showall) {                                                                                                                                                                              //and news_image <> ''
                        $main_news = get_news_list("$where and news_id not in (".implode(",", $lids).") $by_date order by news_id desc", $global_group["groups_newsblock_count"], $global_group["groups_newsblock_fields"]);
                        if (intval(sizeof($main_news)) < 1) {
                                $main_news = get_news_list("$where1 and news_id not in (".implode(",", $lids).") $by_date order by news_id desc", $global_group["groups_newsblock_count"], $global_group["groups_newsblock_fields"]);
                        }
                        $temp_groups[$groups_key]["main_news"] = $main_news;
                        //$tpl->fid_loop("content", "main_news".$group["groups_id"], $main_news, true);
                }

                if ($global_subgroups_id > 0 || $global_showall) {
                        $parameters["limit"] = $group["groups_list_count"];
                        //$by_date = !empty($selectedDate) > 0 ? "and DATE_FORMAT(news_date, '%Y-%m-%d') = '".$selectedDate."'" : "";
                        $count = $db->fetch($db->query("select count(news_id) from news where $where and news_id not in (".implode(",", $lids).") $by_date"), 0);
                        $limit = ($parameters["limit"]*(($_GET["page"] > 0) ? $_GET["page"]-1 : 0)).", ".$parameters["limit"];
                        $nav["path"] = $HTTP_ROOT.$global_allgroups[$global_groups_id]["groups_dir"].(($global_subgroups_id > 0) ? "/".$global_allgroups[$global_subgroups_id]["groups_dir"] : "");
                        if ($global_showall) $nav["path"] .= "/all";

                } else {
                        $limit = $group["groups_newsblock_lastnews_count"];
                }

                $group["groups_newsblock_lastnews_fields"] = unserialize($group["groups_newsblock_lastnews_fields"]);
                $latest_news = get_news_list("news_id not in (".implode(",", $lids).") and ".$where." $by_date order by news_id desc", $limit, $group["groups_newsblock_lastnews_fields"]);
                if (sizeof($main_news) == 0 && sizeof($latest_news) == 0) {
                  unset($temp_groups[$groups_key]);
                } else {
                  $temp_groups[$groups_key]["latest_news"] = $latest_news;
                  //$tpl->fid_loop("content", "latest_news".$group["groups_id"], $latest_news, true);
                }
                
        }

        $tpl->fid_loop("content", "groups", $temp_groups);
        foreach($temp_groups as $groups_key => $group) {
           $tpl->fid_loop("content", "main_news".$group["groups_id"], $group["main_news"], true);
           $tpl->fid_loop("content", "latest_news".$group["groups_id"], $group["latest_news"], true); 
        }
        
        
        $tpl->fid_if("content", "latest_news", $ifs);
        $tpl->fid_array("content", $global_group, true);

        include(PHPDIR."pages.php");
}


function showAllLastNewsBlock() {
        global $global_group, $global_subgroup, $global_childs, $global_groups_id, $global_subgroups_id, $global_allgroups, $ids, $lids;
        global $tpl, $db, $ifs, $options;
        if (($global_group["groups_lastblock_enabled"] == "checked" && $global_subgroups_id == 0) || $global_subgroup["groups_lastblock_enabled"] == "checked" && $global_subgroups_id != 0) {
                $ifs["all_last_news"] = true;

                if ($global_subgroups_id == 0) {
                    $ggg = $global_group;
                } else {
                    $ggg = $global_subgroup;
                }

                $gr = is_array($global_childs[$ggg["groups_id"]]) ? array_merge($global_childs[$ggg["groups_id"]], array($ggg["groups_id"])) : array($ggg["groups_id"]);
                $where = array();
                foreach($gr as $gid) {
                    $where[] = "'0$gid'";
                    $where[] = "'1$gid'";
                }
                (sizeof($where) > 0) ? $where = "hash IN (".implode(", ", $where).")" : $where = "hash IN ('0')";
                $global_group["groups_lastblock_fields"] = unserialize($global_group["groups_lastblock_fields"]);
                $latest_news = get_news_list("news_id not in (".implode(",", $lids).") and ".$where." order by news_id desc", $global_group["groups_lastblock_count"], $global_group["groups_lastblock_fields"]);
                $tpl->fid_loop("content", "all_last_news", $latest_news, true);
        }
}

function showMainNewsBlock() {
        global $global_group, $global_subgroup, $global_childs, $global_groups_id, $global_subgroups_id, $global_allgroups, $ids, $lids;
        global $tpl, $db, $ifs, $options;

        if (($global_group["groups_mainblock_enabled"] == "checked" && $global_subgroups_id == 0) || $global_subgroup["groups_mainblock_enabled"] == "checked" && $global_subgroups_id != 0) {
                if ($global_subgroups_id == 0) {
                    $ggg = $global_group;
                } else {
                    $ggg = $global_subgroup;
                }

                $ggg["groups_mainblock_count"] = ($ggg["groups_mainblock_count"] < 1) ? 1 : $ggg["groups_mainblock_count"];
                $only_images = $ggg["groups_mainblock_only_images"] == "checked" ? "and news_image <> ''" : "";
                $ggg["groups_mainblock_fields"] = unserialize($ggg["groups_mainblock_fields"]);

                $gr = is_array($global_childs[$ggg["groups_id"]]) ? array_merge($global_childs[$ggg["groups_id"]], array($ggg["groups_id"])) : array($ggg["groups_id"]);
                $where = $where1 = array();
                foreach($gr as $gid) {
                    if (!$only_images) $where[] = "'0$gid'";
                    $where[] = "'1$gid'";
                    $where1[] = "'0$gid', '1$gid'";
                }
                (sizeof($where) > 0) ? $where = "hash IN (".implode(", ", $where).")" : $where = "hash IN ('0')";
                (sizeof($where1) > 0) ? $where1 = "hash IN (".implode(", ", $where1).")" : $where1 = "hash IN ('0')";

                $last_news = get_news_list("news_id not in (".implode(",", $lids).") and ".$where." order by news_id desc", $ggg["groups_mainblock_count"], $ggg["groups_mainblock_fields"]);
                if (intval(sizeof($last_news)) < 1) {
                   $last_news = get_news_list("news_id not in (".implode(",", $lids).") and ".$where1." order by news_id desc", $ggg["groups_mainblock_count"], $ggg["groups_mainblock_fields"]);
                }
                $tpl->fid_loop("content", "last_news", $last_news, true);
        }
}


function conv($matches) {
                global $options, $Host, $nochange_url;

                if (preg_match("!href=[\"']{0,1}([^ >'\"]+)!", $matches[1], $rrr)) {
                   $old_rrr = $rrr[1];
                   if ($nochange_url == true) {

                   } else {
                        if (substr($rrr[1], 0, 4) != "http" && substr($rrr[1], 0, 7) != "mailto:") {
                                if (substr($rrr[1], 0, 1) == "/") $rrr[1] = substr($rrr[1], 1);
                                $matches[1] = str_replace($old_rrr, $Host.$rrr[1], $matches[1]);

                        }
                   }
                }
                $matches[1] .= "target=\"new\"";
                $matches[1] = preg_replace("!target=[^ ]+!", "", $matches[1]);
                $matches[1] = "<a ".$matches[1].($options["grab_links_open_in_blank"] == "checked" ? " target=\"_blank\"" : "").(($options["grab_links_nofollow"] == "checked") ? " rel=\"nofollow\"" : "").">";
                if ($options["grab_links_encode"] == "checked") {
                    for($i=0; $i<strlen($matches[1]);$i++) {
                        $str[] = ord($matches[1]{$i});
                    }
                    $matches[1] = "<script>document.write(String.fromCharCode(".implode(",", $str).")+ '".addslashes($matches[2])."</a>"."');</script>";
                } else {
                    $matches[1] .= $matches[2]."</a>";
                }
                return $matches[1];
}
?>