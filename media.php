<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

// Get video
$query = "select
				 id
				,title
			  	,fileName
			  	,splashScreen
			  	,description
			  	,tags
			  	,DATE_FORMAT(insert_date,'%d.%m.%Y')  insert_date
			  	,viewed
                ,UNIX_TIMESTAMP(insert_date) last_update_nm
		  from  ism_video
		  where id=".clean_int($id);

$video = array();
$rc = sql_stmt($query, 9, $video, 1);
// Last-Modified
$LastModified_unix = filemtime(__FILE__);
$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
$LastDepositUpdate = $video['last_update_nm'][0];
if ($LastDepositUpdate >= $LastModified_unix)  $LastModified = gmdate('D, d M Y H:i:s', $LastDepositUpdate).' GMT';
header('Last-Modified: '. $LastModified);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $video['title'][0];?></title>
<meta name="Description" content="<?php echo $video['title'][0];?>" >
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
  <div class="title">Популярное видео - ТОП-3</div>
  <?php
   //get top 3 popular videos
   $query = "select
			    	 id
				    ,title
			  	    ,splashScreen
		    from  ism_video
		    order by viewed desc
		    LIMIT 0,3
		  ";

  $videos = array();
  $rc = sql_stmt($query, 3, $videos, 2);


   for ($i=0;$i<sizeof($videos['id']);$i++)
    {    	echo '<div class="video-small">
				  <a href="media.php?id='.$videos['id'][$i].'" title="'.$videos['title'][$i].'">'.$videos['title'][$i].'</a>
  				  <img src="'.$videos['splashScreen'][$i].'" width="193" height="138" alt="'.$videos['title'][$i].'" />
  			  </div>';    }
?>

    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="banner.php?zid=7624" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
                      Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
    <!-- end sidebar2 -->
  </div>

  <div class="mainContent">
<div class="title"><?php echo $video['title'][0];?> / <?php echo $video['insert_date'][0];?></div>
<div class="media">
<div class="text"><?php echo $video['description'][0];?></div>
<div class="yashare-auto-init" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>
 <?php
		// Display video
	    fp_header();
		if(!empty($video["fileName"][0]))
		{
		  fp_render($video['id'][0], $video['fileName'][0], '706px', '413px', $video['splashScreen'][0]);
		}
 ?>
<div class="options"><div class="right"><?php echo $video['viewed'][0];?> просмотров</div>
 <!--5:09 мин, 5 Мб. /  <a href="#">Скачать, 18 Мб.</a>-->
 <a href="http://www.addthis.com/bookmark.php?v=250" onmouseover="return addthis_open(this, \'\', \'[url]\', \'[title]\')" onmouseout="addthis_close()" onclick="return addthis_sendto()"><img src="http://s7.addthis.com/static/btn/lg-share-en.gif" width="125" height="16" alt="bookmark and share" style="border:0"/></a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js?pub=xa-4a30c4b554241133"></script>
 <a href="phpBB2/index.php" title="Обсудить в форуме">Обсудить в форуме</a>
<br />
</div>
<?php
  discuss('ism_video_discuss', 'media.php', 'id',$id);
?>
</div>
</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>