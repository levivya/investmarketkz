<?php
//include lib and conf file
require("../main.cfg");
include("../lib/mysql.inc");


$myFile = "../rss.xml";
$fh = fopen($myFile, 'w') or die("can't open file");

$stringData = '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
<channel>
<title>Информационная лента Invest-Market.kz</title>
<link>'.$URL.'</link>
<description>Новости и аналитика от Invest-Market.kz</description>
<lastBuildDate>'.date('r').'</lastBuildDate>
<language>ru-en</language>';
fwrite($fh, $stringData);


// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);


$query="
            select
                       d.id
                      ,d.title
                      ,d.cdate
                      ,d.link
                      ,DATE_FORMAT(d.cdate,'%a, %e %b %Y %H:%i:%s') format_cdate
            from
            (select
                   t.analyt_id   id
                  ,t.title       title
                  ,t.analyt_date cdate
                  ,concat('article.php?type=analytic&id=',t.analyt_id) link
            from ism_analytics t
            union
            select
                   t.news_id   id
                  ,t.title       title
                  ,t.news_date cdate
                  ,concat('article.php?type=news&id=',t.news_id) link
            from ism_news t
            ) d
            order by d.cdate desc
            LIMIT 0,20
       ";

$vvalue=array();
$rc=sql_stmt($query, 5, $vvalue ,2);

for ($i=0;$i<sizeof($vvalue['id']);$i++)
{
$stringData ='
<item>
<title><![CDATA['.$vvalue['title'][$i].']]></title>
<link><![CDATA['.$URL.$vvalue['link'][$i].']]></link>
<pubDate>'.$vvalue['format_cdate'][$i].'</pubDate>
</item>
';
fwrite($fh, $stringData);

}

disconn($conn);






$stringData ='
<!-- put more items here -->
</channel>
</rss>';
fwrite($fh, $stringData);
fclose($fh);

echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><a href="index.php">На страницу Администратора</a>
          </td>
      </tr>
      </table><br>';

flush();

echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">RSS Feed обнавлен<br>Время:&nbsp;'.date('r').'</font>
          </td>
      </tr>
      </table><br>';


?>
