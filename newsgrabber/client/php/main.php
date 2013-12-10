<?
$HUI_WAM1 = "èõê ÷ù ôõô þôï-ôï òáúúäåîäéôå";
$HUI_WAM2 = "ÕÓÉ ÂÛ ÒÓÒ ×ÒÎ-ÒÎ ÐÀÇÇÄÅÍÄÈÒÅ";

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
$selectedDateInt = @strtotime($_GET["date"]);
if ($selectedDateInt > 0) {
   $selectedDate = $_GET["date"];
   $selectedYear = date("Y", $selectedDateInt);
   $selectedMonth = date("m", $selectedDateInt);
   $selectedDay = date("d", $selectedDateInt);
} else {
   $selectedYear = date("Y");
   $selectedMonth = date("m");
}

$c = new Calendar();
$tpl->fid_load("calendar", "calendar.html");

$c->setTemplate($tpl->files["calendar"]);
if (!empty($selectedDate)) $c->setDate($selectedDate);

$dates = $db->fetch($db->query("select DATE_FORMAT(MAX(news_date), '%Y-%m-%d') as max_date, DATE_FORMAT(MIN(news_date), '%Y-%m-%d') as min_date, DATE_FORMAT(MAX(news_date), '%a, %d %b %Y %H:%i:%s') as news_lastupdate from news where hash IS NOT NULL"));
$validDates = $db->fetchccol($db->query("select DISTINCT DATE_FORMAT(news_date, '%d') from news where hash IS NOT NULL and DATE_FORMAT(news_date, '%Y-%m') = '$selectedYear-$selectedMonth'"));
$c->setFirstValidDate($dates["min_date"]);
$c->setLastValidDate($dates["max_date"]);
$c->setHref($HTTP_ROOT."desc/");
$tpl->files["calendar"] = $c->getCalc($validDates);    
    
$tpl->fid_load("content", "main.html");

        $global_group = $db->fetch($db->query("select * from groups where groups_owner = -1"));

        $groups = $global_groups;

        $lids[] = 0;
        showMainNewsBlock();

        $ifs["all_last_news"] = false;
        showAllLastNewsBlock();

        $global_group["groups_newsblock_fields"] = unserialize($global_group["groups_newsblock_fields"]);
        if (is_array($groups))
        foreach($groups as $key => $group) {
            $group["groups_newsblock_lastnews_enabled"] = $groups[$key]["groups_newsblock_lastnews_enabled"] = $global_group["groups_newsblock_lastnews_enabled"];
            $group["groups_newsblock_lastnews_count"] = $groups[$key]["groups_newsblock_lastnews_count"] = $global_group["groups_newsblock_lastnews_count"];
            $group["groups_newsblock_lastnews_fields"] = $groups[$key]["groups_newsblock_lastnews_fields"] = $global_group["groups_newsblock_lastnews_fields"];
            if ($group["groups_show_in_owner"] == "checked" || ($global_subgroups_id == 0 && $group["groups_id"] == $global_groups_id) || ($global_subgroups_id != 0 && $group["groups_id"] == $global_subgroups_id)) $temp_groups[] = $group;
        }
        //$tpl->fid_loop("content", "groups", $temp_groups);

        if (is_array($temp_groups))
        foreach($temp_groups as $groups_key => $group) {
                $gr = is_array($global_childs[$group["groups_id"]]) ? array_merge($global_childs[$group["groups_id"]], array($group["groups_id"])) : array($group["groups_id"]);
                $where = $where1 = array();
                foreach($gr as $gid) {
                    if ($global_group["groups_newsblock_only_images"] != "checked") $where[] = "'0$gid'";
                    $where[] = "'1$gid'";
                    $where1[] = "'0$gid', '1$gid'";
                }
                (sizeof($where) > 0) ? $where = "hash IN (".implode(", ", $where).")" : $where = "hash IN ('0')";
                (sizeof($where1) > 0) ? $where1 = "hash IN (".implode(", ", $where1).")" : $where1 = "hash IN ('0')";

                if ($global_subgroups_id == 0 && !$global_showall) {                                                                                                                                                                              //and news_image <> ''
                        $main_news = get_news_list("news_id not in (".implode(",", $lids).") and $where order by news_id desc", $global_group["groups_newsblock_count"], $global_group["groups_newsblock_fields"]);
                        if (intval(sizeof($main_news)) < 1) {
                                $main_news = get_news_list("$where1 and news_id not in (".implode(",", $lids).") order by news_id desc", $global_group["groups_newsblock_count"], $global_group["groups_newsblock_fields"]);
                        }
                        $temp_groups[$groups_key]["main_news"] = $main_news;
                        //$tpl->fid_loop("content", "main_news".$group["groups_id"], $main_news, true);
                }

                if ($global_subgroups_id > 0 || $global_showall) {
                        $parameters["limit"] = $group["groups_list_count"];
                        $count = $db->fetch($db->query("select count(news_id) from news where $wnere and news_id <> '".$main_news[0]["news_id"]."' and news_id <> '".$last_news[0]["news_id"]."' and news_id not in (".implode(",", $lids).")"), 0);
                        $limit = ($parameters["limit"]*(($_GET["page"] > 0) ? $_GET["page"]-1 : 0)).", ".$parameters["limit"];
                        $nav["path"] = $GLOBALS["HTTP_ROOT"].$global_allgroups[$global_groups_id]["groups_dir"].(($global_subgroups_id > 0) ? $GLOBALS["HTTP_ROOT"].$global_allgroups[$global_subgroups_id]["groups_dir"] : "");
                        if ($global_showall) $nav["path"] .= "/all";
                } else {
                        $limit = $group["groups_newsblock_lastnews_count"];
                }

                $group["groups_newsblock_lastnews_fields"] = unserialize($group["groups_newsblock_lastnews_fields"]);
                $latest_news = get_news_list("news_id not in (".implode(",", $lids).") and $where order by news_id desc", $limit, $group["groups_newsblock_lastnews_fields"]);
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


function showAllLastNewsBlock() {
        global $global_group, $global_subgroup, $global_groups_id, $global_subgroups_id, $global_allgroups, $ids, $lids;
        global $tpl, $db, $ifs, $options;
        if (($global_group["groups_lastblock_enabled"] == "checked" && $global_subgroups_id == 0) || $global_subgroup["groups_mainblock_enabled"] == "checked" && $global_subgroups_id != 0) {
                $ifs["all_last_news"] = true;

                $global_group["groups_lastblock_fields"] = unserialize($global_group["groups_lastblock_fields"]);
                $latest_news = get_news_list("news_id not in (".implode(",", $lids).") order by news_id desc", $global_group["groups_lastblock_count"], $global_group["groups_lastblock_fields"]);
                $tpl->fid_loop("content", "all_last_news", $latest_news, true);
        }
}

function showMainNewsBlock() {
        global $global_group, $global_subgroup, $global_childs, $global_groups_id, $global_subgroups_id, $global_allgroups, $ids, $lids;
        global $tpl, $db, $ifs, $options;

        if (($global_group["groups_mainblock_enabled"] == "checked" && $global_subgroups_id == 0) || $global_subgroup["groups_mainblock_enabled"] == "checked" && $global_subgroups_id != 0) {
                $global_group["groups_mainblock_count"] = ($global_group["groups_mainblock_count"] < 1) ? 1 : $global_group["groups_mainblock_count"];
                $only_images = $global_group["groups_mainblock_only_images"] == "checked";
                $global_group["groups_mainblock_fields"] = unserialize($global_group["groups_mainblock_fields"]);

                $gr = is_array($global_childs["0"]) ? array_merge($global_childs["0"], array($global_group["groups_id"])) : array($global_group["groups_id"]);
                $where = $where1 = array();
                foreach($gr as $gid) {
                    if (!$only_images) $where[] = "'0$gid'";
                    $where[] = "'1$gid'";
                    $where1[] = "'0$gid', '1$gid'";
                }
                (sizeof($where) > 0) ? $where = "hash IN (".implode(", ", $where).")" : $where = "hash IN ('0')";
                (sizeof($where1) > 0) ? $where1 = "hash IN (".implode(", ", $where1).")" : $where1 = "hash IN ('0')";

                $last_news = get_news_list("news_id not in (".implode(",", $lids).") and $where order by news_id desc", $global_group["groups_mainblock_count"], $global_group["groups_mainblock_fields"]);
                if (intval(sizeof($last_news)) < 1) {
                    $last_news = get_news_list("news_id not in (".implode(",", $lids).") and ".$where1." order by news_id desc", $global_group["groups_mainblock_count"], $global_group["groups_mainblock_fields"]);
                }
                $tpl->fid_loop("content", "last_news", $last_news, true);
        }
}
?>