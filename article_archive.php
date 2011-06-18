<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

if (!isset($type)) $type='news'; //defaul content is news
$selected_menu='news';

switch ($type) {
   case 'news':
        $query="select
	                  t.news_id                           id
	                 ,DATE_FORMAT(t.news_date,'%d.%m.%Y') format_date
	                 ,t.title                             title
 	                 ,UNIX_TIMESTAMP(t.news_date) last_update_nm
	       		from ism_news t
	       		where t.ntype=".clean_int($subtype_id)."
	       		order by t.news_date desc
	       		";
        break;
  case 'analytic':
        $query="
				        select
			                  t.analyt_id                           id
			                 ,DATE_FORMAT(t.analyt_date,'%d.%m.%Y') format_date
			                 ,t.title                               title
         	                 ,UNIX_TIMESTAMP(t.analyt_date) last_update_nm
			       		from ism_analytics t
			       		where t.atype=".clean_int($subtype_id)."
			       		order by t.analyt_date desc
				       ";
		break;
  case 'analytic_doc':
        $query="
				        select
				                   t.id
               					  ,DATE_FORMAT(t.attached_date,'%d.%m.%Y') format_date
					              ,concat(concat(concat(t.name,' ('),(select name from ism_companies where company_id=t.company_id)),')')  title
         	                      ,UNIX_TIMESTAMP(t.attached_date) last_update_nm
				       from ism_documents t
       				   where t.company_id is not null
                       order by t.attached_date desc
				       ";
        break;
  case 'investor_school':
        $query="
		        select
	                  t.id                            id
	                 ,DATE_FORMAT(t.vdate,'%d.%m.%Y') format_date
	                 ,t.title                         title
 	                 ,UNIX_TIMESTAMP(t.vdate) last_update_nm
	       		from ism_investor_school t
	       		where t.cont_type=".clean_int($subtype_id)."
	       		order by t.vdate desc
		       ";
        $selected_menu='main';
        break;
}

$topics = array();
$rc = sql_stmt($query, 4, $topics, 2);
// Last-Modified
$LastModified_unix = filemtime(__FILE__);
$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
$LastDepositUpdate = max($topics['last_update_nm']);
if ($LastDepositUpdate >= $LastModified_unix)  $LastModified = gmdate('D, d M Y H:i:s', $LastDepositUpdate).' GMT';
header('Last-Modified: '. $LastModified);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $title;?></title>
<meta name="Description" content="<?php echo $title;?>" >
<meta name="Keywords" content="<?php echo $title;?>,новости, аналитика, школа инвестора,паевой инвестиционный фонд, пиф, управляющие компании, УК, инвестиции, сбережения, депозит, вклад, вопрос, кредит, ипотека, как выбрать банк, банк, получить кредит, процентные ставки">
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
    <!-- end sidebar2 -->
  </div>

<div class="mainContent">
<?php
echo '<div class="title">'.$title.'</div>';
if (!isset($page)) $page=1;
$page_amount= ceil(sizeof($topics['id'])/20);

$str="";
if ($page_amount>1)
{
 for ($j=1;$j<=$page_amount;$j++)
   {
      $str.='<a href="article_archive.php?page='.$j.'&type='.$type.'&subtype_id='.$subtype_id.'&title='.$title.'" >'.($k=($j==$page)?('<b>'.$j.'</b>'):($j)).'</a>&nbsp;';
   }
}

if ($rc>0)
{
echo '<div class="news-list"><ul>';

$to=($page*20);
$from=$to-20;
$to=($to>sizeof($topics['id']))?(sizeof($topics['id'])):($to);

for ($i=$from;$i<$to;$i++)

   {
     if (($type=='analytic_doc') && ($subtype_id==3))
		     {
		     	  echo '<li><div>'.$topics['format_date'][$i].'</div><a href="document.php?id='.$topics['id'][$i].'">'.$topics['title'][$i].'</a></li>';
		     }
			 else
			 {
               if ($type!='investor_school')
	            {
	            	echo '<li><div>'.$topics['format_date'][$i].'</div><a href="article.php?id='.$topics['id'][$i].'&type='.($str1=($type=='analytic')?('analytic'):('news')).'">'.$topics['title'][$i].'</a></li>';
	            }
	            else
	            {
	                echo '<li><div>'.$topics['format_date'][$i].'</div><a href="article.php?type=investor_school&id='.$topics['id'][$i].'">'.$topics['title'][$i].'</a></li>';
	            }
             }
    }
echo '<div class="pagination">'.$str.'</div>';
echo '</ul></div>';

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