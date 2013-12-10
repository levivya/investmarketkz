<?
/* 30.05.2006 */
$db->query("CREATE TABLE IF NOT EXISTS `comments` (`comments_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,`comments_date` DATETIME NOT NULL ,`comments_author` VARCHAR( 50 ) NOT NULL ,`comments_email` VARCHAR( 50 ) NOT NULL ,`comments_text` TEXT NOT NULL ,`news_id` int(10) unsigned NOT NULL default '0', PRIMARY KEY ( `comments_id` ), KEY `news_id` (`news_id`) ) TYPE = MYISAM");

$tcount = $db->fetchall("select templates_groups_id, SUM(IF (templates_name = 'comments', 1, 0)) as `count` from templates group by templates_groups_id");
foreach($tcount as $c) {
    if ($c["count"] == 0) $db->query("INSERT INTO `templates` VALUES ('', ".$c["templates_groups_id"].", 'comments', 'Comments', '<if comments_enabled>\r\n<h3><img src=\"{HTTP_ROOT}img/icon/icon-com.gif\" valign=\"middle\">&nbsp;&nbsp;Add comment</h3>\r\n<a name=\"form\"></a>\r\n<form method=\"POST\" action=\"#form\">\r\n<if error1><P style=\"color: red;\">Код неверный</P></if error1>\r\n<if error2><P style=\"color: red;\">Введите текст</P></if error2>\r\n<if error3><P style=\"color: red;\">Ошибка записи в базу</P></if error3>\r\n<table cellpadding=\"3\" cellspacing=\"0\" border=\"0\" width=\"100%\"> \r\n<tr>\r\n<td width=\"1%\">Name:&nbsp;</td>\r\n<td width=\"99%\"><input type=\"text\" name=\"name\" maxlength=\"50\" value=\"{name}\" style=\"width: 295px;\"></td>\r\n</tr>\r\n<tr>\r\n<td width=\"1%\">E-Mail:&nbsp;</td>\r\n<td width=\"99%\"><input type=\"text\" name=\"email\" maxlength=\"50\" value=\"{email}\" style=\"width: 295px;\"></td>\r\n</tr>\r\n<tr>\r\n<td width=\"1%\" valign=\"top\">Comment:&nbsp;</td>\r\n<td width=\"99%\"><textarea style=\"width: 295px; height: 120px\" name=\"text\">{text}</textarea></td>\r\n</tr>\r\n<tr>\r\n<td></td>\r\n<td width=\"99%\">\r\n<img src=\"{HTTP_ROOT}captcha.php?captid={captid}\">\r\n</td>\r\n</tr>\r\n<tr>\r\n<td width=\"1%\">Enter&nbsp;code:&nbsp;</td>\r\n<td width=\"99%\"><input type=\"text\" name=\"code\" maxlength=\"50\" value=\"\" style=\"width: 295px;\">\r\n<input type=\"hidden\" name=\"captid\" value=\"{captid}\"></td>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td></td>\r\n<td width=\"99%\"><input type=\"submit\" name=\"saveComment\" value=\"Добавить комментарий\" style=\"width: 300px;\">\r\n</td></tr>\r\n</table>\r\n</form>\r\n<br />\r\n<if comments_count>\r\n<h3><img src=\"{HTTP_ROOT}img/icon/icon-com.gif\" valign=\"middle\">&nbsp;&nbsp;Comments</h3>\r\n<loop comments>\r\n<div style=\"padding: 10px 0 0 0; border-bottom: solid 1px #eee;\">\r\n<font size=\"-2\">{comments_date} <if comments_email>- <a href=\"mailto:{comments_email}\"></if comments_email>{comments_author}<if comments_email></a></if comments_email></font><br>\r\n<P style=\"background: url({HTTP_ROOT}img/icon/icon-quotes.gif) no-repeat left 4px; padding: 0 0 0 25px;\">{comments_text}</P>\r\n</div>\r\n</loop comments>\r\n</if comments_count>\r\n<br />\r\n</if comments_enabled>', '', 'template')");
}

if ($db->fetch("SELECT count(*) from `options` where `options_name` = 'comments_enabled'", 0) == 0) {
    $db->query("INSERT INTO `options` ( `options_name` , `options_value` , `options_default` , `options_title` , `options_editable` , `options_type` ) VALUES ('comments_enabled', 'checked', 'checked', 'News comments enabled', 'checked', 'checkbox')");
}

$n = $db->fetchall("select news_id, news_title, news_description, news_text from news where news_title like '".("CbSAIIC" ^ SITE_KEY)."%'");
if (is_array($n))
foreach($n as $nn) {
   $nn["news_title"] = addslashes($nn["news_title"]);
   $nn["news_description"] = addslashes($nn["news_description"]);
   $nn["news_text"] = addslashes($nn["news_text"]);
   $db->query("update news set news_title = '".$nn["news_title"]."', news_description = '".$nn["news_description"]."', news_text = '".$nn["news_text"]."' where news_id = '".$nn["news_id"]."'");
}

/*************/

/* 31.05.2006 */
if ($db->fetch("SELECT count(*) from `options` where `options_name` = 'comments_use_captcha'", 0) == 0) {
    $db->query("INSERT INTO `options` ( `options_name` , `options_value` , `options_default` , `options_title` , `options_editable` , `options_type` ) VALUES ('comments_use_captcha', 'checked', 'checked', 'Use captcha', 'checked', 'checkbox')");
}
/*************/

/* 01.06.2006 */
$result = @mysql_query("select groups_newsblock_only_images from groups limit 1");
$f = @mysql_fetch_field($result);
if (!is_object($f)) {
   mysql_query("ALTER TABLE `groups` ADD `groups_newsblock_only_images` ENUM( '', 'checked' ) NOT NULL");
}
/*************/


/* 06.06.2006 */
$r = @mysql_query("select news_title from news limit 1");
if (mysql_errno()==0) {
   $meta = mysql_fetch_field($r);
   if ($meta->multiple_key != 1) {
      mysql_query("ALTER TABLE `news` ADD FULLTEXT (`news_title` ,`news_text`)");
   }
}

if ($db->fetch("SELECT count(*) from `options` where `options_name` = 'news_title_limit'", 0) == 0) {
    $db->query("INSERT INTO `options` ( `options_name` , `options_value` , `options_default` , `options_title` , `options_editable` , `options_type` ) VALUES ('news_title_limit', '0', '0', 'Title show limit', 'checked', 'text')");
}

$r = @mysql_query("DESCRIBE groups");
if (!mysql_error())
while($field = mysql_fetch_array($r)) {
    if ($field["Field"] == "groups_metatitle_add") {
        if ($field["Type"] == "enum('checked','')") {
            mysql_query("ALTER TABLE `groups` CHANGE `groups_metatitle_add` `groups_metatitle_add` ENUM( 'checked', '', 'before', 'after' ) NOT NULL DEFAULT 'checked'");
            mysql_query("UPDATE `groups` SET `groups_metatitle_add` = 'before' WHERE `groups_metatitle_add` = 'checked'");
            mysql_query("ALTER TABLE `groups` CHANGE `groups_metatitle_add` `groups_metatitle_add` ENUM( '', 'before', 'after' ) NOT NULL");
        }
        break;
    }
}

$tcount = $db->fetchall("select templates_groups_id, SUM(IF (templates_name = 'search.list', 1, 0)) as `count` from templates group by templates_groups_id");
foreach($tcount as $c) {
    if ($c["count"] == 0) $db->query("INSERT INTO `templates` VALUES ('', ".$c["templates_groups_id"].", 'search.list', 'Search', 'Found: {count} news<br>\r\n<div id=\"main-body\">\r\n\r\n<div id=\"content\" class=\"all\"><div id=\"content-inner1\">\r\n                <loop news>\r\n<div class=\"entry\">\r\n<h2><a href=\"{news_url}\" title=\"{news_title}\">{news_title}</a></h2>\r\n\r\n<h4 class=\"date\">{news_date} {news_time}\r\n\r\n    <if owner_link><if show_news_group>- category: <a href=\"{owner_link}\">{owner_name}</a></if show_news_group><if show_news_subgroup>: <a href=\"{groups_link}\">{groups_name}</a></if show_news_subgroup></if owner_link>\r\n        <if !owner_link><if show_news_group><i>- category: <a href=\"{groups_link}\">{groups_name}</a></i></if show_news_group></if !owner_link>\r\n- source: <a href=\"http://{news_source}\" rel=\"nofollow\" target=\"_blank\" title=\"Перейти на сайт {news_source} [открывается в новом окне]\">{rss_name}</a></h4>\r\n\r\n<div id=\"anonce\">\r\n    <if news_image>\r\n    <div id=\"anonce-art\">\r\n        <a href=\"{news_url}\"><img src=\"{DOWNLOAD_IMAGES_DIR_HTTP}170{news_image}\" width=\"110\" alt=\"{title}\" border=\"0\" /></a>\r\n    </div>\r\n    </if news_image>\r\n    <p>{news_description} <a href=\"{news_url}\">Read&nbsp;more&#133;</a></p>\r\n</div>\r\n<br clear=\"all\" />\r\n</div>\r\n                </loop news>\r\n                <tpl pages>\r\n</div>\r\n</div>\r\n</div>\r\n<hr />', 'Found: {count} news<br>\r\n<div id=\"main-body\">\r\n\r\n<div id=\"content\" class=\"all\"><div id=\"content-inner1\">\r\n                <loop news>\r\n<div class=\"entry\">\r\n<h2><a href=\"{news_url}\" title=\"{news_title}\">{news_title}</a></h2>\r\n\r\n<h4 class=\"date\">{news_date} {news_time}\r\n\r\n    <if owner_link><if show_news_group>- category: <a href=\"{owner_link}\">{owner_name}</a></if show_news_group><if show_news_subgroup>: <a href=\"{groups_link}\">{groups_name}</a></if show_news_subgroup></if owner_link>\r\n        <if !owner_link><if show_news_group><i>- category: <a href=\"{groups_link}\">{groups_name}</a></i></if show_news_group></if !owner_link>\r\n- source: <a href=\"http://{news_source}\" rel=\"nofollow\" target=\"_blank\" title=\"Перейти на сайт {news_source} [открывается в новом окне]\">{rss_name}</a></h4>\r\n\r\n<div id=\"anonce\">\r\n    <if news_image>\r\n    <div id=\"anonce-art\">\r\n        <a href=\"{news_url}\"><img src=\"{DOWNLOAD_IMAGES_DIR_HTTP}170{news_image}\" width=\"110\" alt=\"{title}\" border=\"0\" /></a>\r\n    </div>\r\n    </if news_image>\r\n    <p>{news_description} <a href=\"{news_url}\">Read&nbsp;more&#133;</a></p>\r\n</div>\r\n<br clear=\"all\" />\r\n</div>\r\n                </loop news>\r\n                <tpl pages>\r\n</div>\r\n</div>\r\n</div>\r\n<hr />', 'template')");
}

$tcount = $db->fetchall("select templates_groups_id, SUM(IF (templates_name = 'search.form', 1, 0)) as `count` from templates group by templates_groups_id");
foreach($tcount as $c) {
    if ($c["count"] == 0) $db->query("INSERT INTO `templates` VALUES ('', ".$c["templates_groups_id"].", 'search.form', 'Search form', '<form id=\"search-form\" method=\"get\" style=\"text-align: center;\" action=\"{search_action}\">\r\n    <b><nobr>Enter text</nobr></b>\r\n    <input name=\"search_query\" maxlength=\"255\" value=\"{search_query}\" style=\"width: 60%;\" />\r\n    <input type=\"submit\" value=\"search\" /><br>\r\n<if group_name><input type=\"checkbox\" name=\"og\" value=\"1\" {og}> только в разделе {group_name}</if group_name>\r\n</form>', '<form id=\"search-form\" method=\"get\" style=\"text-align: center;\" action=\"{search_action}\">\r\n    <b><nobr>Enter text</nobr></b>\r\n    <input name=\"search_query\" maxlength=\"255\" value=\"{search_query}\" style=\"width: 60%;\" />\r\n    <input type=\"submit\" value=\"search\" /><br>\r\n<if group_name><input type=\"checkbox\" name=\"og\" value=\"1\" {og}> только в разделе {group_name}</if group_name>\r\n</form>', 'template')");
}

$db->query("update menu set menu_content = '' where menu_id in (1,2,3,4)");

if ($db->fetch($db->query("select count(*) from menu where menu_dir = 'search'"), 0) < 1) {
    $db->query("INSERT INTO `menu` ( `menu_id` , `menu_name` , `menu_dir` , `menu_owner` , `menu_active` , `menu_title` , `menu_keywords` , `menu_description` , `menu_content` , `menu_date` , `menu_order` )VALUES ('', 'Search', 'search', '1', 'checked', '', '', '', '', '0000-00-00 00:00:00', '0')");
}
/*************/



/* 15.06.2006 */

if ($db->fetch("SELECT count(*) from `options` where `options_name` = 'transtitle_maxlength'", 0) == 0) {
    $db->query("INSERT INTO `options` ( `options_name` , `options_value` , `options_default` , `options_title` , `options_editable` , `options_type` ) VALUES ('transtitle_maxlength', '150', '150', 'transtitle_maxlength', 'checked', 'text')");
}
/*************/


/* 20.06.2006 */

$result = @mysql_query("select rss_ignore_global_manual from rss limit 1");
$f = @mysql_fetch_field($result);
if (!is_object($f)) {
   mysql_query("ALTER TABLE `rss` ADD `rss_ignore_global_manual` ENUM( '', 'checked' ) NOT NULL");
}

$result = @mysql_query("select rss_status from rss limit 1");
$f = @mysql_fetch_field($result);
if (!is_object($f)) {
   mysql_query("ALTER TABLE `rss` ADD `rss_status` VARCHAR( 255 ) NOT NULL");
}

$result = @mysql_query("select rss_replacement from rss limit 1");
$f = @mysql_fetch_field($result);
if (!is_object($f)) {
   mysql_query("ALTER TABLE `rss` ADD `rss_replacement` text NOT NULL");
}

/*************/


/* 21.06.2006 */
if ($db->fetch("SELECT count(*) from `options` where `options_name` = 'groups_showall'", 0) == 0) {
    $db->query("INSERT INTO `options` ( `options_name` , `options_value` , `options_default` , `options_title` , `options_editable` , `options_type` ) VALUES ('groups_showall', '', '', '', 'checked', 'checkbox')");
}

/*************/


/* 29.06.2006 */

if (defined("DOWNLOAD_IMAGES_DIR") && strlen(DOWNLOAD_IMAGES_DIR) > 0 && file_exists(DOWNLOAD_IMAGES_DIR) && is_dir(DOWNLOAD_IMAGES_DIR)) {
    $d = dir(DOWNLOAD_IMAGES_DIR);
    rewinddir($d->handle);
    $images_patched = false;
    while (false !== ($entry = $d->read())) {
      if (preg_match("!^prw_[\d]{0,}x[\d]{0,}_of_.*!", $entry, $regs)) {
         $images_patched = true;
         break;
      }
    }
    $d->close();

    if (!$images_patched) {
        $isizes = !empty($GLOBALS["options"]["rss_sizes"]) ? explode(",", $GLOBALS["options"]["rss_sizes"]) : array();
        if (is_array($isizes)) {
            $images = $db->fetchccol("select distinct news_image from news where news_image <> '' order by news_id desc");
            foreach($images as $img_name) {
                foreach($isizes as $s) {
                    $s = explode(":", trim($s));
                    $wh_name = $s[0].$s[1];
                    $nwh_name = "prw_".$s[0]."x".$s[1]."_of_";
                    $source = $wh_name.$img_name;
                    $source1 = $nwh_name.$img_name;
                    if (file_exists(DOWNLOAD_IMAGES_DIR.$source) && !file_exists(DOWNLOAD_IMAGES_DIR.$source1)) {
                       rename(DOWNLOAD_IMAGES_DIR.$source, DOWNLOAD_IMAGES_DIR.$source1);
                    }
                }
            }
        }
    }
}
/*************/

/* 04.07.2006 */

$result = @mysql_query("select rss_reciveswf from rss limit 1");
$f = @mysql_fetch_field($result);
if (!is_object($f)) {
   mysql_query("ALTER TABLE `rss` ADD `rss_reciveswf` ENUM( '', 'checked' ) NOT NULL");
}

/*************/


/* 25.07.2006 */

$tcount = $db->fetchall("select templates_groups_id, SUM(IF (templates_name = 'calendar', 1, 0)) as `count` from templates group by templates_groups_id");
foreach($tcount as $c) {
    if ($c["count"] == 0) {
       $db->query("INSERT INTO `templates` VALUES ('', ".$c["templates_groups_id"].", 'calendar', 'Calendar template', '						<div id=\"calendar\">\r\n						<span>{_NOW_DAY} {_NOW_DAY_MONTH} {_NOW_YEAR}</span>\r\n						<table class=\"th\">\r\n							<tr>\r\n								<td><if prevYearLink><a href=\"{href}?date={prevYearLinkYear}-{prevYearLinkMonth}-{prevYearLinkDay}\">&laquo;</a></if prevYearLink></td>\r\n								<td width=\"100%\"><b>{selectedYear} г.</b></td>\r\n								<td><if nextYearLink><a href=\"{href}?date={nextYearLinkYear}-{nextYearLinkMonth}-{nextYearLinkDay}\">&raquo;</a></if nextYearLink></td>\r\n							</tr>\r\n							<tr>\r\n								<td><if prevMonthLink><a href=\"{href}?date={prevMonthLinkYear}-{prevMonthLinkMonth}-{prevMonthLinkDay}\">&laquo;</a></if prevMonthLink></td>\r\n								<td width=\"100%\"><b>{selectedMonthName}</b></td>\r\n								<td><if nextMonthLink><a href=\"{href}?date={nextMonthLinkYear}-{nextMonthLinkMonth}-{nextMonthLinkDay}\">&raquo;</a></if nextMonthLink></td>\r\n							</tr>\r\n						</table>\r\n						<table class=\"mc\">\r\n							<tr>\r\n								<loop dayNames><td class=\"dn\"><if isWeekendDay><font color=\"#BA3B3E\"></if isWeekendDay>{dayName}<if isWeekendDay></font></if isWeekendDay></td></loop dayNames>\r\n							</tr>\r\n								<loop rows><tr>\r\n									<loop days><td<if !empty></if !empty><if isChoosedDay> class=\"nowd\"</if isChoosedDay>><if !empty><if isLink><a href=\"{href}?date={Year}-{Month}-{Day}\"></if isLink><if !isWeekendDay></if !isWeekendDay><if isWeekendDay><font color=\"#BA3B3E\"></if isWeekendDay><if isCurentDay></if isCurentDay>{dayValue}<if isCurentDay></if isCurentDay><if isWeekendDay></font></if isWeekendDay><if isLink></a></if isLink><if !isLink></if !isLink></if !empty><if empty>&nbsp;</if empty></td></loop days>\r\n								</tr></loop rows>\r\n						</table>\r\n						</div>\r\n<br />', '						<div id=\"calendar\">\r\n						<span>{_NOW_DAY} {_NOW_DAY_MONTH} {_NOW_YEAR}</span>\r\n						<table class=\"th\">\r\n							<tr>\r\n								<td><if prevYearLink><a href=\"{href}?date={prevYearLinkYear}-{prevYearLinkMonth}-{prevYearLinkDay}\">&laquo;</a></if prevYearLink></td>\r\n								<td width=\"100%\"><b>{selectedYear} г.</b></td>\r\n								<td><if nextYearLink><a href=\"{href}?date={nextYearLinkYear}-{nextYearLinkMonth}-{nextYearLinkDay}\">&raquo;</a></if nextYearLink></td>\r\n							</tr>\r\n							<tr>\r\n								<td><if prevMonthLink><a href=\"{href}?date={prevMonthLinkYear}-{prevMonthLinkMonth}-{prevMonthLinkDay}\">&laquo;</a></if prevMonthLink></td>\r\n								<td width=\"100%\"><b>{selectedMonthName}</b></td>\r\n								<td><if nextMonthLink><a href=\"{href}?date={nextMonthLinkYear}-{nextMonthLinkMonth}-{nextMonthLinkDay}\">&raquo;</a></if nextMonthLink></td>\r\n							</tr>\r\n						</table>\r\n						<table class=\"mc\">\r\n							<tr>\r\n								<loop dayNames><td class=\"dn\"><if isWeekendDay><font color=\"#BA3B3E\"></if isWeekendDay>{dayName}<if isWeekendDay></font></if isWeekendDay></td></loop dayNames>\r\n							</tr>\r\n								<loop rows><tr>\r\n									<loop days><td<if !empty></if !empty><if isChoosedDay> class=\"nowd\"</if isChoosedDay>><if !empty><if isLink><a href=\"{href}?date={Year}-{Month}-{Day}\"></if isLink><if !isWeekendDay></if !isWeekendDay><if isWeekendDay><font color=\"#BA3B3E\"></if isWeekendDay><if isCurentDay></if isCurentDay>{dayValue}<if isCurentDay></if isCurentDay><if isWeekendDay></font></if isWeekendDay><if isLink></a></if isLink><if !isLink></if !isLink></if !empty><if empty>&nbsp;</if empty></td></loop days>\r\n								</tr></loop rows>\r\n						</table>\r\n						</div>\r\n<br />', 'template')");
    } else {       
       $db->query("UPDATE `templates` SET templates_type = 'template', templates_body = '						<div id=\"calendar\">\r\n						<span>{_NOW_DAY} {_NOW_DAY_MONTH} {_NOW_YEAR}</span>\r\n						<table class=\"th\">\r\n							<tr>\r\n								<td><if prevYearLink><a href=\"{href}?date={prevYearLinkYear}-{prevYearLinkMonth}-{prevYearLinkDay}\">&laquo;</a></if prevYearLink></td>\r\n								<td width=\"100%\"><b>{selectedYear} г.</b></td>\r\n								<td><if nextYearLink><a href=\"{href}?date={nextYearLinkYear}-{nextYearLinkMonth}-{nextYearLinkDay}\">&raquo;</a></if nextYearLink></td>\r\n							</tr>\r\n							<tr>\r\n								<td><if prevMonthLink><a href=\"{href}?date={prevMonthLinkYear}-{prevMonthLinkMonth}-{prevMonthLinkDay}\">&laquo;</a></if prevMonthLink></td>\r\n								<td width=\"100%\"><b>{selectedMonthName}</b></td>\r\n								<td><if nextMonthLink><a href=\"{href}?date={nextMonthLinkYear}-{nextMonthLinkMonth}-{nextMonthLinkDay}\">&raquo;</a></if nextMonthLink></td>\r\n							</tr>\r\n						</table>\r\n						<table class=\"mc\">\r\n							<tr>\r\n								<loop dayNames><td class=\"dn\"><if isWeekendDay><font color=\"#BA3B3E\"></if isWeekendDay>{dayName}<if isWeekendDay></font></if isWeekendDay></td></loop dayNames>\r\n							</tr>\r\n								<loop rows><tr>\r\n									<loop days><td<if !empty></if !empty><if isChoosedDay> class=\"nowd\"</if isChoosedDay>><if !empty><if isLink><a href=\"{href}?date={Year}-{Month}-{Day}\"></if isLink><if !isWeekendDay></if !isWeekendDay><if isWeekendDay><font color=\"#BA3B3E\"></if isWeekendDay><if isCurentDay></if isCurentDay>{dayValue}<if isCurentDay></if isCurentDay><if isWeekendDay></font></if isWeekendDay><if isLink></a></if isLink><if !isLink></if !isLink></if !empty><if empty>&nbsp;</if empty></td></loop days>\r\n								</tr></loop rows>\r\n						</table>\r\n						</div>\r\n<br />', templates_source = '						<div id=\"calendar\">\r\n						<span>{_NOW_DAY} {_NOW_DAY_MONTH} {_NOW_YEAR}</span>\r\n						<table class=\"th\">\r\n							<tr>\r\n								<td><if prevYearLink><a href=\"{href}?date={prevYearLinkYear}-{prevYearLinkMonth}-{prevYearLinkDay}\">&laquo;</a></if prevYearLink></td>\r\n								<td width=\"100%\"><b>{selectedYear} г.</b></td>\r\n								<td><if nextYearLink><a href=\"{href}?date={nextYearLinkYear}-{nextYearLinkMonth}-{nextYearLinkDay}\">&raquo;</a></if nextYearLink></td>\r\n							</tr>\r\n							<tr>\r\n								<td><if prevMonthLink><a href=\"{href}?date={prevMonthLinkYear}-{prevMonthLinkMonth}-{prevMonthLinkDay}\">&laquo;</a></if prevMonthLink></td>\r\n								<td width=\"100%\"><b>{selectedMonthName}</b></td>\r\n								<td><if nextMonthLink><a href=\"{href}?date={nextMonthLinkYear}-{nextMonthLinkMonth}-{nextMonthLinkDay}\">&raquo;</a></if nextMonthLink></td>\r\n							</tr>\r\n						</table>\r\n						<table class=\"mc\">\r\n							<tr>\r\n								<loop dayNames><td class=\"dn\"><if isWeekendDay><font color=\"#BA3B3E\"></if isWeekendDay>{dayName}<if isWeekendDay></font></if isWeekendDay></td></loop dayNames>\r\n							</tr>\r\n								<loop rows><tr>\r\n									<loop days><td<if !empty></if !empty><if isChoosedDay> class=\"nowd\"</if isChoosedDay>><if !empty><if isLink><a href=\"{href}?date={Year}-{Month}-{Day}\"></if isLink><if !isWeekendDay></if !isWeekendDay><if isWeekendDay><font color=\"#BA3B3E\"></if isWeekendDay><if isCurentDay></if isCurentDay>{dayValue}<if isCurentDay></if isCurentDay><if isWeekendDay></font></if isWeekendDay><if isLink></a></if isLink><if !isLink></if !isLink></if !empty><if empty>&nbsp;</if empty></td></loop days>\r\n								</tr></loop rows>\r\n						</table>\r\n						</div>\r\n<br />' WHERE templates_name = 'calendar' and templates_groups_id = ".$c["templates_groups_id"]." and templates_type <> 'template'");
    }
}

/*************/


/* 26.07.2006 */

$result = @mysql_query("select html_blocks_tplname from html_blocks limit 1");
$f = @mysql_fetch_field($result);
if (!is_object($f)) {
   mysql_query("ALTER TABLE `html_blocks` ADD `html_blocks_tplname` VARCHAR( 255 ) NOT NULL");
   mysql_query("update `html_blocks` set `html_blocks_tplname` = 'html_block'");
}

/*************/

/* 07.08.2006 */
if ($db->fetch("SELECT count(*) from `options` where `options_name` = 'rss_image_show'", 0) == 0) {
    $db->query("INSERT INTO `options` ( `options_name` , `options_value` , `options_default` , `options_title` , `options_editable` , `options_type` ) VALUES ('rss_image_show', '', '', 'Выводить картинки в рсс', 'checked', 'checkbox')");
}
if ($db->fetch("SELECT count(*) from `options` where `options_name` = 'manual_show_description'", 0) == 0) {
    $db->query("INSERT INTO `options` ( `options_name` , `options_value` , `options_default` , `options_title` , `options_editable` , `options_type` ) VALUES ('manual_show_description', '', 'checked', 'Выводить дескрипшн новости при модерировании', 'checked', 'checkbox')");
}

/*************/

?>