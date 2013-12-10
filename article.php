<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

if (!isset($type)) $type='news'; //defaul content is news
$selected_menu='news';
if (!isset($tags)) $tags='';
if (!isset($body)) $body='';

switch ($type) {
    case 'news':
        $query="
            select
                  t.news_id   id
                 ,t.title     title
                 ,DATE_FORMAT(t.news_date,'%d.%m.%Y') date
                 ,t.body                              text
                 ,t.ntype                             subtype
                 ,t.tags                              tags
 	             ,UNIX_TIMESTAMP(ADDDATE(t.news_date,1)) last_update_nm
             from ism_news t
             where  t.news_id=".clean_int($id);
        $tab="ism_news_discuss";
        $delete_query="delete from ism_news where news_id=".clean_int($id);
        $update_query="update ism_news set tags='".$tags."',body='".$body."',npf_id=".$npf_id." where news_id=".clean_int($id);
        break;
    case 'analytic':
        $query="
            select
                  t.analyt_id     id
                 ,t.title         title
                 ,DATE_FORMAT(t.analyt_date,'%d.%m.%Y') date
                 ,t.body                                text
                 ,t.atype                               subtype
                 ,t.tags                              tags
         	     ,UNIX_TIMESTAMP(ADDDATE(t.analyt_date,1)) last_update_nm
             from ism_analytics t
             where  t.analyt_id=".clean_int($id);

        $tab="ism_analyt_discuss";
        $delete_query="delete from ism_analytics where analyt_id=".clean_int($id);
        $update_query="update ism_analytics set tags='".$tags."',body='".$body."' where analyt_id=".clean_int($id);
        break;
    case 'question':
         $query="
            select
                  t.id     id
                 ,t.subject         title
                 ,DATE_FORMAT(t.post_date,'%d.%m.%Y') date
                 ,concat(concat(concat('<b>Вопрос:</b>&nbsp;',t.question),'<br><br><b>Ответ:</b>&nbsp;'),t.comments)                  text
                 ,1                               subtype
                 ,t.tags                              tags
         	     ,UNIX_TIMESTAMP(ADDDATE(t.post_date,1)) last_update_nm
             from ism_questions t
             where  t.id=".clean_int($id);
         $tab="ism_questions_discuss";
         $delete_query="delete from ism_questions where id=".clean_int($id);
         $update_query="update ism_questions set tags='".$tags."' where id=".clean_int($id);
         break;
    case 'investor_school':
         $query="
            select
                  t.id     id
                 ,t.title         title
                 ,DATE_FORMAT(t.vdate,'%d.%m.%Y') date
                 ,t.body                          text
                 ,t.cont_type                     subtype
                 ,t.tags                              tags
                 ,UNIX_TIMESTAMP(ADDDATE(t.vdate,1)) last_update_nm
             from ism_investor_school t
             where  t.id=".clean_int($id);
         $selected_menu='main';
         $delete_query="delete from ism_investor_school where id=".clean_int($id);
         $update_query="update ism_investor_school set tags='".$tags."',body='".$body."' where id=".clean_int($id);
         break;
}

//get info
$article=array();
$rc=sql_stmt($query, 7, $article ,1);

//list of npfs
$query="
          select -999 fund_id, ' ' name
          union
          select
                   fund_id
                  ,name
          from ism_pension_funds
       ";
$vnpfs=array();
$rc=sql_stmt($query, 2, $vnpfs ,2);

if (!isset($npf_id))  {$npf_id=$vnpfs['fund_id'][0] ;}
$NPFsMenuString = menu_list($vnpfs['name'],$npf_id,$vnpfs['fund_id']);
$NPFsMenuString = '<select name="npf_id">'.$NPFsMenuString.'</select>';


if (isset($delete))
{
  //echo $delete_query;
  $result=exec_query($delete_query);
  echo '<div class="info-message">'.echoNLS('Удалено!','').'</div>';
}

if (isset($update))
{
  //echo $update_query;
  $result=exec_query($update_query);
  echo '<div class="info-message">'.echoNLS('Сохранено.','').'</div>';
}


//no data exists
if ($rc==0)
        {
          header('Location: /404.php');
          exit;
        }
// Last-Modified
$LastModified = gmdate('D, d M Y H:i:s', $article['last_update_nm'][0]).' GMT';
//echo  $LastModified;
header('Last-Modified: '. $LastModified);


$default_kw=($article['tags'][0]=="")?("новости, аналитика, школа инвестора,паевой инвестиционный фонд, пиф, управляющие компании, УК, инвестиции, сбережения, депозит, вклад, вопрос, кредит, ипотека, как выбрать банк, банк, получить кредит, процентные ставки"):($article['tags'][0]);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $article['title'][0];?></title>
<meta name="Description" content="<?php echo $article['title'][0];?>" >
<meta name="Keywords" content="<?php echo $default_kw; ?>">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include 'includes/scripts.php';?>
</head>



<body>
<div id="container">
<!-- header -->
<?php
        include 'includes/header.php';
?>

<!-- main body -->

  <div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="banner.php?zid=7624" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
                      Ваш браузер не поддерживает плавающие фреймы!</iframe>
    <!--<a href="#"><img src="media/images/banner2.gif" width="240" height="399" alt="img" /></a> -->
    </div>

    <!-- Links -->
    <div class="publicity">
    <?php
    $query="           select
			                  t.id
			                 ,t.link
			       		from ism_ext_links t
			       		where t.page='article.php'
			       		      and id=".clean_int($id)."
			       		      and type='investor_school'
				       ";
	$rc = sql_stmt($query, 2, $links, 2);


    for ($i=0;$i<sizeof($links['id']);$i++)
	{		echo $links['link'][$i]."<br>";	}

    ?>
    </div>
    <!-- end sidebar2 -->
  </div>

<div class="mainContent">
 <div class="title"><?php echo $article['title'][0]; ?></div>
<?php
if (isset($grp) && $grp==2)
{
?>
 <div class="search-block"><form method="post"><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="hidden" name="type" value="<?php echo $type; ?>"><span><input type="submit" value="Удалить" name="delete"></span>|&nbsp;&nbsp;<input type="text" name="tags" style="width:470px;" value="<?php echo $article['tags'][0]; ?>"> &nbsp;<span><input type="submit" value="Обновить" name="update"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea name="body" rows="8" cols="88"><?php echo $article['text'][0]; ?></textarea><?php echo $NPFsMenuString;?></form></div>
<?php
}
else
{
?>
<div class="article_header">
Дата: <?php echo $article['date'][0]; ?> <div class="yashare-auto-init" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>
</div>
<?php
}
?>
 <div class="media">
  <div class="text"><?php echo $article['text'][0]; ?></div>
 <div class="options"><div class="right"> <a href="phpBB2/index.php">Обсудить в форуме</a> </div>
  <a href="http://www.addthis.com/bookmark.php?v=250" onmouseover="return addthis_open(this, \'\', \'[url]\', \'[title]\')" onmouseout="addthis_close()" onclick="return addthis_sendto()"><img src="http://s7.addthis.com/static/btn/lg-share-en.gif" width="125" height="16" alt="bookmark and share" style="border:0"/></a><script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js?pub=xa-4a30c4b554241133"></script>
  </div>
 <br />
 <script type="text/javascript"><!--
google_ad_client = "pub-2712511792023009";
/* 728x90, создано 24.09.10 */
google_ad_slot = "7735537292";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>

 <br />
<?php
  if ($type!='investor_school') discuss($tab, 'article.php?type='.$type, 'id',$id);
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