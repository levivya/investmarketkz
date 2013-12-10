<?
$HUI_WAM1 = "èõê ÷ù ôõô þôï-ôï òáúúäåîäéôå";
$HUI_WAM2 = "ÕÓÉ ÂÛ ÒÓÒ ×ÒÎ-ÒÎ ÐÀÇÇÄÅÍÄÈÒÅ";

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

$dates = $db->fetch($db->query("select DATE_FORMAT(MAX(news_date), '%Y-%m-%d') as max_date, DATE_FORMAT(MIN(news_date), '%Y-%m-%d') as min_date, DATE_FORMAT(MAX(news_date), '%a, %d %b %Y %H:%i:%s') as news_lastupdate from news where news_active = 'checked'"));
$validDates = $db->fetchccol($db->query("select DISTINCT DATE_FORMAT(news_date, '%d') from news where news_active = 'checked' and DATE_FORMAT(news_date, '%Y-%m') = '$selectedYear-$selectedMonth'"));
$c->setFirstValidDate($dates["min_date"]);
$c->setLastValidDate($dates["max_date"]);
$c->setHref(implode("/", $doc_dir)."/");
$tpl->files["calendar"] = $c->getCalc($validDates);


if ($_GET["rss"] == 1) {
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

        /*debug*/
        @ob_end_clean();
        /*debug*/

        
        $rss_news = get_news_list($parameters["where"], $options["rss_count"], array(), array("DATE_FORMAT(news_date, '%a, %d %b %Y %H:%i:%s ".date("O")."') as news_rsslastupdate"));
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
} else {

        $tpl->fid_load("content", "news.all.html");

        $by_date = !empty($selectedDate) ? " news_date BETWEEN '".$selectedYear."-".$selectedMonth."-".$selectedDay." 00:00:00' AND '".$selectedYear."-".$selectedMonth."-".$selectedDay." 23:59:59'".(!empty($parameters["where"]) ? " and " : "") : "";
        $count = $db->fetch($db->query("select count(news_id) from news where $by_date news_active = 'checked'"), 0);
        $limit = ($parameters["limit"]*(($_GET["page"] > 0) ? $_GET["page"]-1 : 0)).", ".$parameters["limit"];
        $nav["path"] = $HTTP_ROOT.substr(implode("/", $doc_dir), 1);

        $latest_news = get_news_list($by_date.$parameters["where"], $limit);
        $tpl->fid_loop("content", "news", $latest_news, true);


        include(PHPDIR."pages.php");
}
?>