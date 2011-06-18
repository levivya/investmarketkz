<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Last-Modified
//$LastModified_unix = gmmktime(date('H'), 0, 0, date('m'), date('d'), date('Y'));
//$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
//header('Last-Modified: '. $LastModified);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- Google Website Optimizer Control Script -->
<script>
function utmx_section(){}function utmx(){}
(function(){var k='3020233403',d=document,l=d.location,c=d.cookie;function f(n){
if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.indexOf(';',i);return c.substring(i+n.
length+1,j<0?c.length:j)}}}var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;
d.write('<sc'+'ript src="'+
'http'+(l.protocol=='https:'?'s://ssl':'://www')+'.google-analytics.com'
+'/siteopt.js?v=1&utmxkey='+k+'&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='
+new Date().valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+
'" type="text/javascript" charset="utf-8"></sc'+'ript>')})();
</script>
<!-- End of Google Website Optimizer Control Script -->
<title>Invest-Market.kz: Инвестиции, банковские продукты (депозиты, кредиты), пенсионные фонды, страхование. Все о личных финансах в Казахстане</title>
<meta name="Description" content="Полная информация о финасовых продуктах в Казахстане (пифы, депозиты, кредиты, пенсионные, страховки и т.д.). Мы поможем выбрать выгодный депозит (вклад), высокодоходный пиф и надежный пенсионный фонд.." >
<meta name="Keywords" content="пиф, казахстан, паевые фонды, нпф, пенсионные фонды, рейтинг фондов, i-tv, заработать деньги, сохранить вложения, капитал, банки, депозиты, вклады, доходность, финансовая консультация, школа инвестора, пенсионные накопления, калькулятор, прибыль, форум, аналитика">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include 'includes/scripts.php';?>
</head>
<body>
<div id="container">
<!-- header -->
<?php
        // Connecting, selecting database
        $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
        $selected_menu='main';
        include 'includes/header.php';
?>
<!-- main body -->
  <noindex>
  <div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity"><iframe src="banner.php?zid=7624" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px" target="_parent">Ваш браузер не поддерживает плавающие фреймы!</iframe></div>
    <div class="title">Реклама от партнеров</div>
    <script type="text/javascript"><!--
	google_ad_client = "pub-2712511792023009";
	/* 250x250, создано 24.09.10 */
	google_ad_slot = "2344662444";
	google_ad_width = 250;
	google_ad_height = 250;
	//-->
	</script>
	<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
    <br /><br />
    <div class="title"><a class="more" href="votes.php" title="Архив голосований">Архив голосований</a>Мнение</div>
    <iframe src="poller/ajax-poller.php" name="poller" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="300px">Ваш браузер не поддерживает плавающие фреймы!</iframe>
    <!-- end sidebar2 -->
  </div>
  </noindex>

  <div class="sidebar1">
    <div id="tabs">
      <ul>
        <li class="topic">Лидеры ТОП 5</li>
        <li  class="first"><a href="#fragment-1" title="депозиты (вклады)">Депозиты</a></li>
        <li><a href="#fragment-2" title="пенсионные фонды (нпф)">НПФ</a></li>
        <li><a href="#fragment-3" title="паевые фонды (пиф)">ПИФы</a></li>
      </ul>
      <!-- PIF Rating +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
      <?php
					      $funds_query="
      										  SELECT
           												t.fund_id   id
          												,t.name name
          												,round(tt.avg_income,2)      avg_income
											   FROM
											         ism_funds t
											        ,ism_fund_year_avg_income tt
											   WHERE  t.fund_id=tt.fund_id
											          AND tt.check_date=(select max(check_date) from ism_fund_year_avg_income where fund_id=t.fund_id)
											          AND t.fund_type!=".$RISK_INVEST_OBJ."
											          AND DATE_ADD(t.start_date,INTERVAL 18 MONTH)<=current_date()
											          AND tt.check_date>=DATE_ADD(NOW(), INTERVAL -1 MONTH)
											          ORDER BY tt.avg_income desc
											   LIMIT 0,5
											           ";
                           $vfunds=array();
                           $rc=sql_stmt($funds_query, 3, $vfunds ,2);
		 ?>
  <div id="fragment-3">
  <table class="tab-table">
   <?php
    for ($i=0;$i<sizeof($vfunds['id']);$i++)
     {
      $class=(fmod(($i),2)==0)?('class="colored"'):('');
     echo '
            <tr '.$class.'>
             <td><a href="pif/pif.php?id='.$vfunds['id'][$i].'" title="'.$vfunds['name'][$i].'">'.$vfunds['name'][$i].'</a></td>
             <td class="right">'.$vfunds['avg_income'][$i].'%</td>
           </tr>
          ';
     }
   ?>
 <tr><td class="info" colspan="2">* Лидеры ТОП 5 формируется по  средней годовой доходности <a href="/pif/" target="_blank">паевых фондов</a> представленных на рынке Казахстана.</td></tr>
  </table>
  </div>
  <!-- Deposit Rating +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
  <?php
						$deposit_query="
											   SELECT
											           t.deposit_id  id
											          ,t.name name
											          ,rate_12m
											   FROM
											         ism_deposits t
											        ,ism_deposit_types tt
											   WHERE  t.deposit_id=tt.deposit_id
											          and tt.type_id=".$DEP_KZT."
											          and t.rate_12m is not null
											   ORDER BY t.rate_12m desc
											   LIMIT 0,5
									        ";

						$vdeps=array();
						$rc=sql_stmt($deposit_query, 3, $vdeps ,2);

  ?>
  <div id="fragment-1">
  <table class="tab-table">
   <?php
    for ($i=0;$i<sizeof($vdeps['id']);$i++)
     {
      $class=(fmod(($i),2)==0)?('class="colored"'):('');
     echo '
            <tr '.$class.'>
             <td><a href="deposit/deposit.php?id='.$vdeps['id'][$i].'" title="'.$vdeps['name'][$i].'">'.$vdeps['name'][$i].'</a></td>
             <td class="right">'.$vdeps['rate_12m'][$i].'%</td>
           </tr>
          ';
     }
   ?>
  <tr><td class="info" colspan="2">* 5 лучших <a href="/deposit/deposits.php">депозитов банков Казахстана</a> в тенге на 12 месяцев.</td></tr>
  </table>
  </div>
  <!-- NPF Rating +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
  <?php
                     $npf_query="
								   SELECT
								           t.fund_id   id
								          ,t.name      name
								          ,round(((1today.value-1year.value)/1year.value)*100,2)      1year_persent
								   FROM
								          ism_pension_funds t
								         ,ism_pension_fund_value 1today
								         ,ism_pension_fund_value 1year
								   WHERE   t.fund_id=1today.fund_id
								           and t.fund_id=1year.fund_id
								           and 1today.check_date=(select max(check_date) from ism_pension_fund_value)
								           and  1year.check_date=DATE_ADD(1today.check_date,INTERVAL -1 YEAR)
								   ORDER BY 1year_persent  desc
								   LIMIT 0,5
								        ";

					$vnpfs=array();
					$rc=sql_stmt($npf_query, 3 , $vnpfs ,2);
  ?>
  <div id="fragment-2">
  <table class="tab-table">
  <?php
    for ($i=0;$i<sizeof($vnpfs['id']);$i++)
     {
      $class=(fmod(($i),2)==0)?('class="colored"'):('');
     echo '
            <tr '.$class.'>
             <td><a href="npf/npf.php?id='.$vnpfs['id'][$i].'" title="'.$vnpfs['name'][$i].'">'.$vnpfs['name'][$i].'</a></td>
             <td class="right">'.$vnpfs['1year_persent'][$i].'%</td>
           </tr>
          ';
     }
   ?>
  <tr>
    <td class="info" colspan="2">* Лидеры ТОП-5 формируется по  годовой доходности <a href="/npf/"> пенсионных фондов Казахстана</a>.</td>
  </tr></table> </div>
    </div>

    <script>utmx_section("consultant")</script>
    <div class="consultant"><noindex><a class="topic" href="ask_question.php" rel="nofollow">Задать вопрос консультанту</a></noindex>
		<ul>
			<li><a href="article_archive.php?type=investor_school&subtype_id=0&title=Инвестиции в ПИФы" title="Инвестиции в ПИФы">Как выбрать ПИФы?</a></li>
			<li><a href="article_archive.php?type=investor_school&subtype_id=2&title=Советы заемщику" title="Советы заемщику">Стоит ли брать кредит?</a></li>
			<li><a href="article_archive.php?type=investor_school&subtype_id=1&title=Все о вкладах" title="Все о вкладах">Какой депозит лучше?</a></li>
		</ul>
    </div>
    </noscript>

    <div class="title"><noindex><a class="more" href="articles.php?type=investor_school" rel="nofollow">Все материалы</a></noindex>Школа инвестора</div>
    <ul class="list dark">
	<!-- Investor school ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
	<?php
	$query="
			select
   	  				t.id  id
	 				,t.title
			from
	 				ism_investor_school t
		    order by t.vdate desc
	        limit 0,5
	            ";

	$vis=array();
	$rc=sql_stmt($query, 2, $vis ,2);
	for ($i=0;$i<sizeof($vis['id']);$i++)
     {     	echo '<li><a href="article.php?type=investor_school&id='.$vis['id'][$i].'" title="'.$vis['title'][$i].'">'.$vis['title'][$i].'</a></li>';
     }
	?>
	<!-- Investor school ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
    </ul>

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
							  ORDER BY t.news_date desc
							  LIMIT 0,12
                            ";

				$vnews=array();
				$rc=sql_stmt($query, 4, $vnews ,2);
                ?>
                <!-- News ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->

    <div class="title"><noindex><a class="more" href="articles.php" rel="nofollow">Все новости</a><noindex>Анонс новостей</div>
    <ul class="list">
  <?php
    for ($i=0;$i<sizeof($vnews['id']);$i++)
     {
       echo '
            <li><a href="article.php?id='.$vnews['id'][$i].'&type=news" title="'.$vnews['title'][$i].'">'.$vnews['title'][$i].'</a></li>
          ';
     }
   ?>



    </ul>
    <!-- end sidebar1 -->
  </div>

 <div class="sidebar1">
              <?php
             		// Get last video
					$query = "SELECT
								 id
								,title
							  	,fileName
							  	,splashScreen
							  	,description
							  	,tags
							  	,DATE_FORMAT(insert_date,'%d.%m.%Y')  insert_date
							  	,viewed
							  FROM
							  	 ism_video
							  ORDER BY id DESC";
					$video = array();
					$rc = sql_stmt($query, 8, $video, 1);
            	?>

 <div class="title"><noindex><a class="more" href="media_list.php" rel="nofollow">Все видео</a></noindex>iTV - Онлайн Видео</div>
 <div class="block1">
 <?php
	 echo '<a href="media.php?id='.$video["id"][0].'" title="'.$video["title"][0].'">'.$video["title"][0].'</a>';
 ?>
 <div class="online-video">
 <?php
		// Display video
	    fp_header();
		if(!empty($video["fileName"][0]))
		{
		  fp_render($video['id'][0], $video['fileName'][0], '318px', '229px', $video['splashScreen'][0]);
		}
 ?>
 </div>
 </div>

 <div id="tabs2">
      <ul>
      <li class="topic">Индексы</li>
        <li class="first"><a href="#fragment-4" title="ПИФКЗ">ПИФКЗ</a></li>
        <li><a href="#fragment-5" title="НПФКЗ">НПФКЗ</a></li>
      </ul>
  <?php

  // INDEX ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  $index_query="
   SELECT
            index_today.pifkz_point                               today
           ,round(index_today.pifkz_point-index_yesterday.pifkz_point,3)   yesterday
           ,round(index_today.pifkz_point-index_week.pifkz_point,3)        week
           ,round(((index_today.pifkz_point-index_week.pifkz_point)/index_week.pifkz_point)*100,3) week_persent
           ,round(index_today.pifkz_point-index_month.pifkz_point,3)       month
           ,round(((index_today.pifkz_point-index_month.pifkz_point)/index_month.pifkz_point)*100,3)       month_persent
           ,round(index_today.pifkz_point-index_6month.pifkz_point,3)      6month
           ,round(((index_today.pifkz_point-index_6month.pifkz_point)/index_6month.pifkz_point)*100,3)      6month_persent
           ,index_today.income_year                                        today_income_year
   FROM
           ism_index_pifkz index_today,
           ism_index_pifkz index_yesterday,
           ism_index_pifkz index_week,
           ism_index_pifkz index_month,
           ism_index_pifkz index_6month
  where    index_today.check_date=(select max(check_date) from ism_index_pifkz)
           and  index_yesterday.check_date=DATE_ADD(index_today.check_date,INTERVAL -1 DAY)
           and  index_week.check_date=DATE_ADD(index_today.check_date,INTERVAL -7 DAY)
           and  index_month.check_date=DATE_ADD(index_today.check_date,INTERVAL -1 MONTH)
           and  index_6month.check_date=DATE_ADD(index_today.check_date,INTERVAL -6 MONTH)
        ";

  $vindex=array();
  $rc=sql_stmt($index_query, 9, $vindex ,1);


  ?>

  <div id="fragment-4">
  <table class="tab-table">
   <tr class="colored">
    <td>Значение индекса</td>
    <td class="right"><?php echo $vindex['today'][0];?></td>
  </tr>
  <tr>
    <td>За неделю</td>
    <?php
    if ($vindex['week'][0]>0)  echo '<td class="right"><span class="arrow2 up">'.$vindex['week'][0].'%</span></td>';
    if ($vindex['week'][0]<0)  echo '<td class="right"><span class="arrow2 down">'.$vindex['week'][0].'%</span></td>';
    if ($vindex['week'][0]==0) echo '<td class="right">'.$vindex['week'][0].'%</td>';
    ?>
  </tr>
  <tr class="colored">
    <td>За месяц</td>
    <?php
    if ($vindex['month_persent'][0]>0)  echo '<td class="right"><span class="arrow2 up">'.$vindex['month_persent'][0].'%</span></td>';
    if ($vindex['month_persent'][0]<0)  echo '<td class="right"><span class="arrow2 down">'.$vindex['month_persent'][0].'%</span></td>';
    if ($vindex['month_persent'][0]==0) echo '<td class="right">'.$vindex['month_persent'][0].'%</td>';
    ?>
  </tr>
  <tr>
    <td>За 6 месяцев</td>
    <?php
    if ($vindex['6month_persent'][0]>0)  echo '<td class="right"><span class="arrow2 up">'.$vindex['6month_persent'][0].'%</span></td>';
    if ($vindex['6month_persent'][0]<0)  echo '<td class="right"><span class="arrow2 down">'.$vindex['6month_persent'][0].'%</span></td>';
    if ($vindex['6month_persent'][0]==0) echo '<td class="right">'.$vindex['6month_persent'][0].'%</td>';
    ?>
  </tr>
  <tr>
    <td class="nopad" colspan="2"><a href="im_index.php?type=pifkz" title="Индекс ПИФКЗ"><img src="lib/graph.php?tab=ism_index_pifkz&id_col=1&interval=9&id_val=1&val_col=pifkz_point&date_format=month" alt="индекс ПИФКЗ"/></a></td>
  </tr>
  </table>

  </div>


  <?php
  // INDEX ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$index_query="
   SELECT
            index_today.npfkz_point                               today
           ,round(index_today.npfkz_point-index_month.npfkz_point,3)       month
           ,round(((index_today.npfkz_point-index_month.npfkz_point)/index_month.npfkz_point)*100,3)       month_persent
           ,round(index_today.npfkz_point-index_6month.npfkz_point,3)      6month
           ,round(((index_today.npfkz_point-index_6month.npfkz_point)/index_6month.npfkz_point)*100,3)      6month_persent
           ,round(index_today.npfkz_point-index_year.npfkz_point,3)      1year
           ,round(((index_today.npfkz_point-index_year.npfkz_point)/index_year.npfkz_point)*100,3)      1year_persent
           ,index_today.income_year                                        today_income_year
   FROM
           ism_index_npfkz index_today,
           ism_index_npfkz index_month,
           ism_index_npfkz index_6month,
           ism_index_npfkz index_year
  where    index_today.check_date=(select max(check_date) from ism_index_npfkz)
           and  index_month.check_date=DATE_ADD(index_today.check_date,INTERVAL -1 MONTH)
           and  index_6month.check_date=DATE_ADD(index_today.check_date,INTERVAL -6 MONTH)
           and  index_year.check_date=DATE_ADD(index_today.check_date,INTERVAL -1 YEAR)
        ";

$vindex=array();
$rc=sql_stmt($index_query, 8, $vindex ,1);


  ?>
  <div id="fragment-5"> <table class="tab-table">
  <tr class="colored">
    <td>Значение индекса</td>
    <td class="right"><?php echo $vindex['today'][0];?></td>
  </tr>
  <tr>
    <td>За месяц</td>
 <?php
    if ($vindex['month_persent'][0]>0)  echo '<td class="right"><span class="arrow2 up">'.$vindex['month_persent'][0].'%</span></td>';
    if ($vindex['month_persent'][0]<0)  echo '<td class="right"><span class="arrow2 down">'.$vindex['month_persent'][0].'%</span></td>';
    if ($vindex['month_persent'][0]==0) echo '<td class="right">'.$vindex['month_persent'][0].'%</td>';
    ?>
  </tr>
  <tr class="colored">
    <td>За 6 месяцев</td>
    <?php
    if ($vindex['6month_persent'][0]>0)  echo '<td class="right"><span class="arrow2 up">'.$vindex['6month_persent'][0].'%</span></td>';
    if ($vindex['6month_persent'][0]<0)  echo '<td class="right"><span class="arrow2 down">'.$vindex['6month_persent'][0].'%</span></td>';
    if ($vindex['6month_persent'][0]==0) echo '<td class="right">'.$vindex['6month_persent'][0].'%</td>';
    ?>
  </tr>
  <tr>
    <td>За год</td>
    <?php
    if ($vindex['1year_persent'][0]>0)  echo '<td class="right"><span class="arrow2 up">'.$vindex['1year_persent'][0].'%</span></td>';
    if ($vindex['1year_persent'][0]<0)  echo '<td class="right"><span class="arrow2 down">'.$vindex['1year_persent'][0].'%</span></td>';
    if ($vindex['1year_persent'][0]==0) echo '<td class="right">'.$vindex['1year_persent'][0].'%</td>';
    ?>
  </tr>
  <tr>
    <td class="nopad" colspan="2"><a href="im_index.php?type=npfkz" title="Индекс НПФКЗ"><img src="lib/graph.php?tab=ism_index_npfkz&id_col=1&interval=9&id_val=1&val_col=npfkz_point&date_format=month"  alt="Индекс НПФКЗ" /></a></td>
  </tr></table> </div>

    </div>


    <div class="title"><noindex><a class="more" href="registration.php" rel="nofollow">Открыть V-счет</a><noindex>Лидеры инвестирования</div>
    <div class="block1 nopad">
    <table class="tab-table">
      <tr class="colored">
       <th>Фонд</th>
       <th class="right">Виртуальные активы клиентов (тг.)</th>
     </tr>
   </table>

  				<!-- Invest leaders +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
				<?php
				$query="
				         select
				           f.fund_id id
				          ,f.name
				          ,buy.total-sell.total total
					     from
					     (
					     select fund_id,sum(total_sum)  total
					     from ism_transactions_virtual
					     where action=".$ACTION_BUY."
					     group by fund_id
					     ) buy ,
					     (
					     select fund_id,sum(total_sum)  total
					     from ism_transactions_virtual
					     where action=".$ACTION_SELL."
					     group by fund_id
					     ) sell ,
					     ism_funds f
					     where f.fund_id=buy.fund_id  and f.fund_id=sell.fund_id
					     order by total desc
				         LIMIT 0,5
				       ";
				//echo $query;


				$vfunds=array();
				$rc=sql_stmt($query, 3, $vfunds ,2);


				?>

  <table class="tab-table">
   <?php
    for ($i=0;$i<sizeof($vfunds['id']);$i++)
     {
      $class=(fmod(($i+1),2)==0)?('class="colored"'):('');
     echo '
            <tr '.$class.'>
             <td><a href="pif/pif.php?id='.$vfunds['id'][$i].'" title="'.$vfunds['name'][$i].'">'.$vfunds['name'][$i].'</a></td>
             <td class="right">'.number_format($vfunds['total'][$i], 2, ',', ' ').'</td>
           </tr>
          ';
     }
   ?>

  <tr>
    <td class="info" colspan="2">* V-счёт - это "Инвестиционный Симулятор", с помощью которого Вы можете осуществлять виртуальные инвестиции в казахстанские ПИФы, не рискуя собственными деньгами.</td>
  </tr>
  </table>
  </div>
  <!-- FORUM ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                <?php
                 $query="
					         select
					           f.topic_id id
					          ,f.topic_title title
					          ,f.topic_time
						     from  phpbb_topics f
					         order by  f.topic_time desc
					         LIMIT 0,5
					       ";
					//echo $query;


					$vtopics=array();
					$rc=sql_stmt($query, 3, $vtopics ,2);

                ?>

  <div class="title"><noindex><a class="more" href="phpBB2/index.php" title="Форум" rel="nofollow">Форум</a></noindex>Горячие темы форума</div>
  <ul class="list">
<?php
 for ($i=0;$i<sizeof($vtopics['id']);$i++)
                              {
                			 	echo '<li><a href="phpBB2/viewtopic.php?t='.$vtopics['id'][$i].'" title="'.$vtopics['title'][$i].'">'.$vtopics['title'][$i].'</a></li>';
                			  }
?>
   </ul>
    <!-- end sidebar1 -->
  </div>


<!-- end of main body -->

<!-- Google Website Optimizer Tracking Script -->
<script type="text/javascript">
if(typeof(_gat)!='object')document.write('<sc'+'ript src="http'+
(document.location.protocol=='https:'?'s://ssl':'://www')+
'.google-analytics.com/ga.js"></sc'+'ript>')</script>
<script type="text/javascript">
try {
var gwoTracker=_gat._getTracker("UA-11894353-2");
gwoTracker._trackPageview("/3020233403/test");
}catch(err){}</script>
<!-- End of Google Website Optimizer Tracking Script -->



<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>