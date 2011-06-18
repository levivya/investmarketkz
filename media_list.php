<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
// Get video
$query = "select
				 id
				,title
			  	,splashScreen
			  	,substr(description,1,500) description
			  	,tags
			  	,DATE_FORMAT(insert_date,'%d.%m.%Y')  insert_date
			  	,viewed
			  	,fileName
                ,UNIX_TIMESTAMP(insert_date) last_update_nm
		  from  ism_video
		  order by insert_date desc";

$videos = array();
$rc = sql_stmt($query, 9, $videos, 2);

// Last-Modified
$LastModified_unix = filemtime(__FILE__);
$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
$LastDepositUpdate = max($videos['last_update_nm']);
if ($LastDepositUpdate >= $LastModified_unix)  $LastModified = gmdate('D, d M Y H:i:s', $LastDepositUpdate).' GMT';
header('Last-Modified: '. $LastModified);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Видео интервью - iTV</title>
<meta name="Description" content="Видео интервью - iTV" >
<meta name="Keywords" content="видео, интервью, itv">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include 'includes/scripts.php';?>
</head>
<body>
<div id="container">
<!-- header -->
<?php
$selected_menu='main';
include 'includes/header.php';
?>
<!-- main body -->
  <div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="banner.php?zid=7624" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
                      Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
    <!-- end sidebar2 -->
  </div>
<div class="mainContent">
<div class="title">iTV - Онлайн Видео</div>
<?php
if (!isset($page)) $page=1;
$page_amount= ceil(sizeof($videos['id'])/10);

$str="";
if ($page_amount>1)
{
 for ($j=1;$j<=$page_amount;$j++)
   {
      $str.='<a href="media.php?page='.$j.'" >'.($k=($j==$page)?('<b>'.$j.'</b>'):($j)).'</a>&nbsp;';
   }
}

if ($rc>0)
{
$to=($page*10);
$from=$to-10;
$to=($to>sizeof($videos['id']))?(sizeof($videos['id'])):($to);

for ($i=$from;$i<$to;$i++)

   {
      echo '
            <div class="media-archive-block">
			<div class="miniature"><a href="media.php?id='.$videos['id'][$i].'" title="'.$videos['title'][$i].'"><img src="'.$videos['splashScreen'][$i].'" width="193" height="138" alt="'.$videos['title'][$i].'" /></a></div>
			<div class="topic">
			<span>'.$videos['insert_date'][$i].'  </span><a href="media.php?id='.$videos['id'][$i].'" title="'.$videos['title'][$i].'">'.$videos['title'][$i].'</a>
			</div>
			<div class="text">'.str_replace('<br><br>','&nbsp;',substr($videos['description'][$i],0,strrpos($videos['description'][$i], ".")+1)).'</div>
			<div class="options">Просмотров : '.$videos['viewed'][$i].'  /  <a href="'.$URL.substr($videos['fileName'][$i],1).'" title="скачать файл">Скачать, '.round((filesize($_SERVER["DOCUMENT_ROOT"].$videos['fileName'][$i])/1024)/1024,1).' Мб.</a></div>
			</div>
          ';
    }
echo '<div class="pagination">'.$str.'</div>';
}
?>
</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>