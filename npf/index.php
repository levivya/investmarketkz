<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

$query="
	 SELECT
	         t.fund_id
	    	,t.name
	    	,tod.value
	    	,tod.asset_value
	    	,DATE_FORMAT(dt.date_today, '%d.%m.%Y') date_today
	        ,ifnull(round(t1.value, 3), -9999) 1month
	        ,ifnull(round((tod.value-t1.value)/t1.value*100, 2), -9999) 1month_percent
	FROM
	        ism_pension_funds t
	        ,(SELECT max(check_date) date_today from ism_pension_fund_value) dt
	LEFT JOIN
	        ism_pension_fund_value tod
	        ON t.fund_id=tod.fund_id
	        AND tod.check_date=dt.date_today
	LEFT JOIN
	        ism_pension_fund_value t1
	        ON t.fund_id=t1.fund_id
	        AND t1.check_date=DATE_ADD(dt.date_today, INTERVAL -1 MONTH)
	WHERE 	t.status!=".$PFUND_CLOSED."
	order by  1month_percent desc
	";

$vfunds=array();
$rc=sql_stmt($query, 7, $vfunds ,2);
$funds_list=implode(",", $vfunds['name']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Пенсионные фонды Казахстана</title>
<meta name="Description" content="Полная информация о пенсионных фондах Казахстана. Накопительная и государственная пенсия в Казахстане. Рейтинг пенсионных фондов и калькулятор пенсионных накоплений." >
<meta name="Keywords" content="пенсионные фонды казахстана, пенсия в казахстане, пенсионный фонд, нпф, пенсия, расчет пенсии, накопительная пенсия, государственная пенсия, рейтинг пенсионных фондов(нпф),пенсионная система РК, индекс НПФКЗ, доходность пенсионных фондов, как выбрать пенсионный фонд?,<?php echo $funds_list;?>">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
<script>
  $(document).ready(function(){$("#slidingDiv").animate({"height": "hide"}, { duration: 100 });});
  function ShowHide(){$("#slidingDiv").animate({"height": "toggle"}, { duration: 100 });}
</script>
</head>
<body>
<div id="container">
<!-- header -->
<?php
$selected_menu='npf';
include '../includes/header.php';
?>
<noindex>
 <div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="../banner.php?zid=5270" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
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
</noindex>

<div class="mainContent">
<h1 class="title">Пенсионные фонды Казахстана</h1>
<div class="text">Негосударственные <strong>пенсионные фонды Казахстана</strong> являются основной функционирования пенсионной системы РК. Пенсионные фонды предназначены для управления пенсионными активами, отчисляемыми гражданами, а так же лиц амии постоянно проживающими на территории Казахстана. На данный момент  в Казахстане существуют 12 пенсионных фондов (<strong>НПФ</strong>). <a href="rating.php">Рейтинг пенсионных фондов</a> поможет выбрать тот фонд, который показывает стабильную доходность, и гарантирует стабильную пенсию.</div>
<div class="two-blocks">
<div class="left-block">
<div class="title">Доходность фондов  <font size="1">(<?php echo $vfunds['date_today'][0]?>)</font><a href="#" class="more" onclick="ShowHide(); return false;" title="показать все пенсионные фонды">Все НПФ</a></div>
<table class="tab-table" id="NPFs">
  <thead>
  <tr>
    <th>Пенсионный фонд</th>
    <th class="right" title="доходность за месяц">% в месяц</th>
  </tr>
  </thead>
  <tbody>
<?php
if ($rc>0)
{

for ($i=0;$i<8;$i++)
   {

	    $class=(fmod(($i),2)==0)?('odd'):('even');

	    if ($vfunds['1month_percent'][$i]!=-9999)
		{
		  if ($vfunds['1month_percent'][$i]>0)  $vfunds['1month_percent'][$i]='<span class="arrow2 up">'.$vfunds['1month_percent'][$i].'</span>';
		  if ($vfunds['1month_percent'][$i]<0)  $vfunds['1month_percent'][$i]='<span class="arrow2 down">'.$vfunds['1month_percent'][$i].'</span>';;
		} else{ $vfunds['1month_percent'][$i]='-';}

	    echo '
	          <tr class="'.$class.'">
	           <td><a href="npf.php?id='.$vfunds['fund_id'][$i].'" target="_blank">'.$vfunds['name'][$i].'</a></td>
	           <td class="right">'.$vfunds['1month_percent'][$i].'</td>
  	          </tr>';
     }
}
?>
</tbody>
</table>
<div id="slidingDiv">
  <table class="tab-table">
  <tbody>
<?php
if ($rc>0)
{

for ($i=8;$i<sizeof($vfunds['fund_id']);$i++)
   {

	    $class=(fmod(($i),2)==0)?('odd'):('even');

	    if ($vfunds['1month_percent'][$i]!=-9999)
		{
		  if ($vfunds['1month_percent'][$i]>0)  $vfunds['1month_percent'][$i]='<span class="arrow2 up">'.$vfunds['1month_percent'][$i].'</span>';
		  if ($vfunds['1month_percent'][$i]<0)  $vfunds['1month_percent'][$i]='<span class="arrow2 down">'.$vfunds['1month_percent'][$i].'</span>';;
		} else{ $vfunds['1month_percent'][$i]='-';}


	    echo '
	          <tr class="'.$class.'">
	          <td><a href="npf.php?id='.$vfunds['fund_id'][$i].'" target="_blank">'.$vfunds['name'][$i].'</a></td>
	          <td class="right">'.$vfunds['1month_percent'][$i].'</td>
	        </tr>';
     }
}
?>
</tbody>
</table>
</div>
<?php
 // INDEX ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  $index_query="
   SELECT
            round(index_today.npfkz_point,2)                               today
           ,round(index_today.npfkz_point-index_month.npfkz_point,2)       month
           ,round(((index_today.npfkz_point-index_month.npfkz_point)/index_month.npfkz_point)*100,2)       month_persent
           ,round(index_today.npfkz_point-index_6month.npfkz_point,2)      6month
           ,round(((index_today.npfkz_point-index_6month.npfkz_point)/index_6month.npfkz_point)*100,2)      6month_persent
           ,round(index_today.npfkz_point-index_12month.npfkz_point,2)      12month
           ,round(((index_today.npfkz_point-index_12month.npfkz_point)/index_12month.npfkz_point)*100,2)      12month_persent
   FROM
           ism_index_npfkz index_today,
           ism_index_npfkz index_month,
           ism_index_npfkz index_6month,
           ism_index_npfkz index_12month
  where    index_today.check_date=(select max(check_date) from ism_index_npfkz)
           and  index_month.check_date=DATE_ADD(index_today.check_date,INTERVAL -1 MONTH)
           and  index_6month.check_date=DATE_ADD(index_today.check_date,INTERVAL -6 MONTH)
           and  index_12month.check_date=DATE_ADD(index_today.check_date,INTERVAL -1 YEAR)
        ";
  //echo $query;
  $vindex=array();
  $rc=sql_stmt($index_query, 7, $vindex ,1);
  ?>
<noindex>
<div id="tabs">
      <ul>
      <li class="topic">Индекс НПФКЗ</li>
        <li class="first"><a href="#fragment-1" rel="nofollow">Значение</a></li>
        <li><a href="#fragment-2" rel="nofollow">Портфель</a></li>
      </ul>
<div id="fragment-1">
<table class="tab-table">
  <tr class="colored">
    <td>Индекс</td>
    <td class="right"><?php echo $vindex['today'][0];?></td>
  </tr>
   <tr>
     <td>Доходность за год</td>
   <?php
    if ($vindex['12month_persent'][0]>0)  echo '<td class="right"><span class="arrow2 up">'.$vindex['12month_persent'][0].'%</span></td>';
    if ($vindex['12month_persent'][0]<0)  echo '<td class="right"><span class="arrow2 down">'.$vindex['12month_persent'][0].'%</span></td>';
    if ($vindex['12month_persent'][0]==0) echo '<td class="right">'.$vindex['12month_persent'][0].'%</td>';
    ?>
  </tr>
  <tr>
   <td class="nopad" colspan="2">
    <a href="../im_index.php?type=npfkz" rel="nofollow"><img src="../lib/graph.php?tab=ism_index_npfkz&id_col=1&interval=9&id_val=1&val_col=npfkz_point&date_format=month"  alt="Индекс НПФКЗ" /></a></td>
  </tr></table>
</div>

<div id="fragment-2">
    <?php

  	$query='
  	         select
        		 (select desc_ru from ism_dictionary where id=i.item) item
       	        ,round(sum(ifnull(i.volume,0))/(select count(fund_id) from ism_funds where pifkz=1),1) volume
             from  ism_pension_fund_structure_item i
             where i.structure_id in (select structure_id from ism_pension_fund_structure where structure_date=(select max(structure_date) from ism_pension_fund_structure) and fund_id in (select fund_id from ism_pension_funds where npfkz=1))
             group by i.item
             order by i.item
           ';
     $vindex_stru_data=array();
	 $rc=sql_stmt($query, 2, $vfund_stru_data ,2);

     if ($rc>0)
	 {
         $fh = fopen('../amcharts/ampie/ampie_data.xml', 'w') or die("can't open file");
		 fwrite($fh, '<?xml version="1.0" encoding="UTF-8"?><pie>');

		 for ($i=0;$i<sizeof($vfund_stru_data['item']);$i++)
		 {
    		 fwrite($fh, '<slice title="'.$vfund_stru_data['item'][$i].'" pull_out="false">'.$vfund_stru_data['volume'][$i].'</slice>');
		 }

  		 fwrite($fh, '</pie>');
		 fclose($fh);

         echo '
               <!-- ampie script-->
				<script type="text/javascript" src="../amcharts/ampie/swfobject.js"></script>
				<div id="flashcontent_pie" align="center"><strong>Обновите ваш Flash Player</strong></div>
				<script type="text/javascript">
						var so = new SWFObject("../amcharts/ampie/ampie.swf", "ampie", "210", "210", "8", "#FFFFFF");
						so.addVariable("path", "../amcharts/ampie/");
						so.addVariable("settings_file", encodeURIComponent("../amcharts/ampie/ampie_settings.xml"));
						so.addVariable("data_file", encodeURIComponent("../amcharts/ampie/ampie_data.xml"));
						so.addVariable("preloader_color", "#FFFFFF");
						so.addParam("wmode", "transparent");
						so.write("flashcontent_pie");
				 </script>
				<!-- end of ampie script -->
               <div class="info"><font size=1>Для получения подробной информации наведите курсор на изображение.</font></div><br>
              ';
     }
  ?>
</div>
</div>
</noindex>

</div>

<div class="right-block">
                <!-- News ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                <?php
                $query="
							    SELECT
							           t.news_id  id
							          ,DATE_FORMAT(t.news_date,'%d.%m.%Y') vdate_format
							          ,t.title
							          ,t.special
							  FROM
							          ism_news t
							  WHERE ntype=9
							  ORDER BY t.news_date desc
							  LIMIT 0,6
                            ";

				$vnews=array();
				$rc=sql_stmt($query, 4, $vnews ,2);
                ?>
                <!-- News ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->

<h1 class="title"><a class="more" href="../articles.php?type=news-npf">Пенсия в Казахстане</a>Новости НПФ</h1>
<ul class="list">
<?php
    for ($i=0;$i<sizeof($vnews['id']);$i++) {echo '<li><a href="../article.php?id='.$vnews['id'][$i].'&type=news" title="'.$vnews['title'][$i].'">'.$vnews['title'][$i].'</a></li>';     }
?>
</ul>

<!-- Investor School -->
<?php
$query="
   SELECT
           t.id
          ,t.title
   FROM
          ism_investor_school t
   WHERE  t.cont_type=3
   ORDER BY t.vdate desc
   LIMIT 0,5
        ";

$vcont=array();
$rc=sql_stmt($query, 2, $vcont ,2);
?>
<h1 class="title"><a class="more" href="../article_archive.php?type=investor_school&subtype_id=3&title=Моя пенсия - советы будущему пенсионеру" title="Моя пенсия - советы будущему пенсионеру">Моя пенсия</a>Выбрать пенсионный фонд</h1>
<ul class="list dark">
<?php
 if ($rc>0)
  {
     for ($i=0;$i<sizeof($vcont['id']);$i++)
	  {
         echo ' <li><a href="../article.php?type=investor_school&id='.$vcont['id'][$i].'" title="'.$vcont['title'][$i].'">'.$vcont['title'][$i].'</a></li>';
      }
  }
?>
</ul>
<noindex>
<div align="center"><a href="../ask_question.php" rel="nofollow"><img src="../media/images/ask_consultant.png" alt="задать вопрос консультанту"></a></div>
</noindex>
</div>
</div>
<h1 class="title">Пенсионный фонд</h1>
<div class="text">
<strong>Пенсионный фонд</strong>  это  совокупность активов, внесённых будущими пенсионерами, на основании договора, предназначенные для получения прибыли, и последующего распределения инвестиционного дохода  между вкладчиками.
<br><br>
Для управления активами, <strong>пенсионный фонд</strong> может привлекать специальные компании (КУПА), либо делать это самостоятельно. В любом случае, для этого требуется лицензия на инвестиционное управление пенсионными активами. Результатом деятельности фонда становится инвестиционный доход, который увеличивает сумму первоначальных вложений. Таким образом, при выходе на <strong>пенсию</strong>, вкладчик получает кроме своих накоплений, деньги от дохода фонда. Вы можете рассчитать сумму накоплений, при помощи сервиса <a href="calculator1.php">расчет пенсии</a>.
</div>
</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>