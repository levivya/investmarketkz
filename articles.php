<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
if (!isset($type)) $type='news'; //defaul content is news
$selected_menu='news';

switch ($type) {
   case 'news':
        $tab="ism_news_discuss";
        $ntype['id'] = array (2,0,1,4);
        $ntype['caption'] = array ('Казахстан','Мировые рынки','События','Новости компаний и M&A');
        $title='Новости';
        $last_date_query = "select UNIX_TIMESTAMP(max(news_date)) last_update_nm from ism_news where ntype in (2,0,1,4)";
        break;
   case 'news-bank':
        $tab="ism_news_discuss";
        $ntype['id'] = array (5,6);
        $ntype['caption'] = array ('Новости банков и регуляторов','Статьи и интервью');
        $title='Новости банков';
        $last_date_query = "select UNIX_TIMESTAMP(max(news_date)) last_update_nm from ism_news where ntype in (5,6)";
        break;
   case 'news-npf':
        $tab="ism_news_discuss";
        $ntype['id'] = array (9);
        $ntype['caption'] = array ('Пенсия в Казахстане');
        $title='Пенсия в Казахстане';
        $last_date_query = "select UNIX_TIMESTAMP(max(news_date)) last_update_nm from ism_news where ntype=9";
        break;
  case 'news-site':
        $tab="ism_news_discuss";
        $ntype['id'] = array (8);
        $ntype['caption'] = array ('Новости сайта');
        $title='Новости сайта';
        $last_date_query = "select UNIX_TIMESTAMP(max(news_date)) last_update_nm from ism_news where ntype=8";
        break;
  case 'analytic':
        $tab="ism_analyt_discuss";
        $ntype['id'] = array (2,1,0,3);
        $ntype['caption'] = array ('Аналитика от Invest-Market.kz','Казахстан','Мировые рынки','Аналитика от УК');
        $title='Аналитика';
        $last_date_query = "select UNIX_TIMESTAMP(max(analyt_date)) last_update_nm from ism_analytics";
        break;
  case 'investor_school':
        $tab="ism_investor_school";
        $ntype['id'] = array (0,1,2,3);
        $ntype['caption'] = array ('Инвестиции в ПИФы','Все о вкладах','Советы заемщику','Моя пенсия');
        $selected_menu='main';
        $title='Школа инвестора';
        $last_date_query = "select UNIX_TIMESTAMP(max(vdate)) last_update_nm from ism_investor_school";
        break;
}

// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

$ldate = array();
$rc = sql_stmt($last_date_query, 1, $ldate, 1);

// Last-Modified
$LastModified_unix = filemtime(__FILE__);
$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
$LastArticleUpdate = $ldate['last_update_nm'][0];
if ($LastArticleUpdate >= $LastModified_unix)  $LastModified = gmdate('D, d M Y H:i:s', $LastArticleUpdate).' GMT';
header('Last-Modified: '. $LastModified);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $title; ?></title>
<meta name="Description" content="<?php echo $title; ?>" >
<meta name="Keywords" content="новости, аналитика, школа инвестора,паевой инвестиционный фонд, пиф, управляющие компании, УК, инвестиции, сбережения, депозит, вклад, вопрос, кредит, ипотека, как выбрать банк, банк, получить кредит, процентные ставки">
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
    </div>
    <br />
     <div class="title">Реклама от партнеров</div>
    <script type="text/javascript"><!--
	google_ad_client = "pub-2712511792023009";
	/* 250x250, создано 24.09.10 */
	google_ad_slot = "2344662444";
	google_ad_width = 250;
	google_ad_height = 250;
	//-->
	</script>
	<script type="text/javascript"
	src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
    <!-- end sidebar2 -->
  </div>
<div class="mainContent">
<?php
for ($i=0;$i<sizeof($ntype['id']);$i++)
{	echo '<div class="title">'.$ntype['caption'][$i].'</div>';

   	if ($type!='analytic')
	{
	  if ($type!='investor_school')
	  {
		  $query="
		        select
	                  t.news_id                           id
	                 ,DATE_FORMAT(t.news_date,'%d.%m.%Y') format_date
	                 ,t.title                             title
	       		from ism_news t
	       		where t.ntype=".$ntype['id'][$i]."
	       		order by t.news_date desc
	       		LIMIT 0,8
		       ";
	   }
	   else
	   {
	   	 $query="
		        select
	                  t.id                            id
	                 ,DATE_FORMAT(t.vdate,'%d.%m.%Y') format_date
	                 ,t.title                         title
	       		from ism_investor_school t
	       		where t.cont_type=".$ntype['id'][$i]."
	       		order by t.vdate desc
	       		LIMIT 0,8
		       ";

	   }
     }
     else
     {
		     if ($ntype['id'][$i]!=3)
		     {
			     $query="
				        select
			                  t.analyt_id                           id
			                 ,DATE_FORMAT(t.analyt_date,'%d.%m.%Y') format_date
			                 ,t.title                             title
			       		from ism_analytics t
			       		where t.atype=".$ntype['id'][$i]."
			       		order by t.analyt_date desc
			       		LIMIT 0,8
				       ";
			 }
			 else
			 {
			     $query="
				        select
				                   t.id
               					  ,DATE_FORMAT(t.attached_date,'%d.%m.%Y') format_date
					              ,concat(concat(concat(t.name,' ('),(select name from ism_companies where company_id=t.company_id)),')')  title
				       from ism_documents t
       				   where t.company_id is not null
                       order by t.attached_date desc
			       	   LIMIT 0,8
				       ";
			 }
     }

    $topics = array();
    $rc = sql_stmt($query, 3, $topics, 2);

    if ($rc>0)
    {
		echo '<div class="news-list"><ul>';

		for ($j=0;$j<sizeof($topics['id']);$j++)
	     {
             if (($type=='analytic') && ($ntype['id'][$i]==3))
		     {		     	  echo '<li><div>'.$topics['format_date'][$j].'</div><a href="document.php?id='.$topics['id'][$j].'" title="'.$topics['title'][$j].'">'.$topics['title'][$j].'</a></li>';
		     	  $new_type='analytic_doc';
             }
			 else
			 {
               if ($type!='investor_school')
	            {	            	echo '<li><div>'.$topics['format_date'][$j].'</div><a href="article.php?id='.$topics['id'][$j].'&type='.($str=($type=='analytic')?('analytic'):('news')).'" title="'.$topics['title'][$j].'">'.$topics['title'][$j].'</a></li>';
	            	$new_type=($type=='analytic')?('analytic'):('news');
	            }
	            else
	            {	                echo '<li><div>'.$topics['format_date'][$j].'</div><a href="article.php?type=investor_school&id='.$topics['id'][$j].'" title="'.$topics['title'][$j].'" title="'.$topics['title'][$j].'">'.$topics['title'][$j].'</a></li>';
	                $new_type='investor_school';
	            }
             }
	     }
	    echo '</ul><a class="more" href="article_archive.php?type='.$new_type.'&subtype_id='.$ntype['id'][$i].'&title='.$ntype['caption'][$i].'" title="Архив">Архив</a></div>';
	}
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