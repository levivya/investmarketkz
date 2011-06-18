<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Invest-Market.kz - паевые фонды (пифы) в Казахстане. Рейтинг пифов, аналитика и инструменты анализа помогут Вам выбрать лучший паевой фонд</title>
<meta name="Description" content="Паевые фонды Казахстана. Все о инвестициях в паевые фонды (пифы), инструменты анализа, рейтиг доходности и многое другое." >
<meta name="Keywords" content="пифы казахстан, пифы, паевой инвестиционный фонд, пиф, управляющие компании, ук, паи, рейтинг пифов, новости, аналитика, виртуальный портфель, счет, индекс ПИФКЗ, цены паев, доходность пифов, инструменты анализа, школа инвестора, выбрать пиф, котировки пифов, фактор стабильности пиф, лучший пиф">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
</head>

<body>
<div id="container">
<!-- header -->
<?php
     // Connecting, selecting database
     $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
     $selected_menu='pif';
     include '../includes/header.php';
?>

<!-- main body -->
<noindex>
  <div class="sidebar2">

    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="../banner.php" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
                      Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
    <!-- end sidebar2 -->
  </div>
</noindex>

 <!-- PIF Rating -->
<?php

 $query="
   SELECT
            t.fund_id
           ,t.name
           ,ifnull(value_today.value,-9999)                                                        today
           ,ifnull(round(value_today.value-value_yesterday.value,2),-9999)                         yesterday
           ,ifnull(round(((value_today.value-value_yesterday.value)/value_yesterday.value)*100,2),-9999)     yesterday_persent
           ,ifnull(round(value_today.value-value_month.value,2),-9999)                             month
           ,ifnull(round(((value_today.value-value_month.value)/value_month.value)*100,2),-9999)   month_persent
           ,ifnull(round(value_today.value-value_3month.value,2),-9999)                            3month
           ,ifnull(round(((value_today.value-value_3month.value)/value_3month.value)*100,2),-9999) 3month_persent
           ,DATE_FORMAT(value_today.check_date,'%d.%m.%Y') last_date
   FROM    ism_funds t,
           ism_fund_value value_today,
           ism_fund_value value_yesterday,
           ism_fund_value value_month,
           ism_fund_value value_3month
   WHERE   t.fund_id=value_today.fund_id
           and value_today.check_date=(select max(check_date) from ism_fund_value where fund_type!=".$RISK_INVEST_OBJ.")
           and t.fund_id=value_yesterday.fund_id
           and value_yesterday.check_date=DATE_ADD(value_today.check_date,INTERVAL -1 DAY)
           and t.fund_id=value_month.fund_id
           and value_month.check_date=DATE_ADD(value_today.check_date,INTERVAL -1 MONTH)
           and t.fund_id=value_3month.fund_id
           and value_3month.check_date=DATE_ADD(value_today.check_date,INTERVAL -3 MONTH)
           and t.fund_type!=".$RISK_INVEST_OBJ."
       ";
//echo $query;

$vfunds=array();
$rc=sql_stmt($query, 10, $vfunds ,2);

?>
<div class="mainContent">

<div class="two-blocks">
<div class="left-block">
<div class="title">Доходность ПИФов <font size="1">(<?php echo $vfunds['last_date'][0]?>)</font></div>

 <table class="tab-table" id="Rating">
  <thead>
  <tr>
    <th title="паевой фонд" width="50%">Паевой фонд</th>
    <th class="right" title="доход фонда за день" width="25%">1Д, %</th>
    <!--<th class="right" title="доход фонда за месяц">За месяц, %</th>-->
    <th class="right" title="доход фонда за 3 месяца" width="25%">3М, %</th>
  </tr>
  </thead>
  <tbody>
  <?php
  if ($rc>0)
  {
	  for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
	  {
	    if ($vfunds['yesterday_persent'][$i]!=-9999)
		{
		  if ($vfunds['yesterday_persent'][$i]>0)  $vfunds['yesterday_persent'][$i]='<span class="arrow2 up">'.$vfunds['yesterday_persent'][$i].'</span>';
		  if ($vfunds['yesterday_persent'][$i]<0)  $vfunds['yesterday_persent'][$i]='<span class="arrow2 down">'.$vfunds['yesterday_persent'][$i].'</span>';;
		} else{ $vfunds['yesterday_persent'][$i]='-';}

	    if ($vfunds['3month_persent'][$i]!=-9999)
		{
		  if ($vfunds['3month_persent'][$i]>0)  $vfunds['3month_persent'][$i]='<span class="arrow2 up">'.$vfunds['3month_persent'][$i].'</span>';
		  if ($vfunds['3month_persent'][$i]<0)  $vfunds['3month_persent'][$i]='<span class="arrow2 down">'.$vfunds['3month_persent'][$i].'</span>';;
		} else{ $vfunds['3month_persent'][$i]='-';}

        echo '
	          <tr>
    			<td><a href="pif.php?id='.$vfunds['fund_id'][$i].'" title="'.$vfunds['name'][$i].'" target="_blank">'.$vfunds['name'][$i].'</a></td>
    			<td class="right">'.$vfunds['yesterday_persent'][$i].'</td>
    			<!--<td class="right">'.$vfunds['month_persent'][$i].'</td>-->
    			<td class="right">'.$vfunds['3month_persent'][$i].'</td>
  			  </tr>
  			 ';
	  }
  }
  ?>
  </tbody>
</table>

<script type="text/javascript">
$(document).ready(function(){
  // ---- tablesorter -----
  $("#Rating").tablesorter({
	widgets: ["zebra"],
	sortList:[[1,1]]
  });
  // ---- tablesorter -----
});
</script>

<!-- Top 10 -->
<?php
$query="
  SELECT
           t.fund_id
          ,t.name
          ,round(tt.avg_income,2)      avg_income
          ,round(tt.avg_income/tt.avg_volat,2)       factor
          ,tt.avg_volat volat
   FROM
         ism_funds t
        ,ism_fund_year_avg_income tt
   WHERE  t.fund_id=tt.fund_id
          AND tt.check_date=(select max(check_date) from ism_fund_year_avg_income where fund_id=t.fund_id)
          AND t.fund_type!=".$RISK_INVEST_OBJ."
          AND tt.check_date>=DATE_ADD(NOW(), INTERVAL -1 MONTH)
          AND DATE_ADD(t.start_date,INTERVAL 18 MONTH)<=current_date()
   ORDER BY tt.avg_income desc
   LIMIT 0,10
        ";
//echo $query;

$vfunds=array();
$rc=sql_stmt($query, 5, $vfunds ,2);
?>

<div class="title"><a class="more" href="map.php" title="карта доходность-риск пифов">Карта</a>Рейтинг ПИФов ТОП: Доходность-Риск</div>
<table class="tab-table" id="top10">
  <thead>
  <tr>
    <th title="паевой фонд">Паевой фонд</th>
    <th class="right" title="среднегодовая доходность, %">СГД</th>
    <th class="right" title="волатильность, %">Волат.</th>
  </tr>
  </thead>
  <tbody>
<?php
  if ($rc>0)
  {
	  for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
	  {
	    echo '
	          <tr>
    			<td><a href="pif.php?id='.$vfunds['fund_id'][$i].'" title="'.$vfunds['name'][$i].'" target="_blank">'.$vfunds['name'][$i].'</a></td>
    			<td class="right">'.$vfunds['avg_income'][$i].'</td>
    			<td class="right">'.$vfunds['volat'][$i].'</td>
    		  </tr>
  			 ';
	  }
  }
  ?>
</tbody></table>
<script type="text/javascript">
$(document).ready(function(){
  // ---- tablesorter -----
  $("#top10").tablesorter({
	widgets: ["zebra"],
	sortList:[[1,1]]
  });
  // ---- tablesorter -----
});
</script>

<!-- Investor School -->
  <?php
  $query="
   SELECT
           t.id
          ,t.title
   FROM
          ism_investor_school t
   WHERE  t.cont_type=0
   ORDER BY t.vdate desc
   LIMIT 0,8
        ";

  $vcont=array();
  $rc=sql_stmt($query, 2, $vcont ,2);

  ?>
  <div class="title"><a class="more" href="../articles.php?type=investor_school" title="Школа инвестора">Все материалы</a>Школа инвестора</div>
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

</div>


<div class="right-block">

 <!-- Analytics -->
  <?php
  $query="
   SELECT
          t.analyt_id   id
          ,t.title       title
   FROM
          ism_analytics t
   WHERE  t.atype=2
   ORDER BY t.analyt_date  desc
   LIMIT 0,5
        ";
  $vcont=array();
  $rc=sql_stmt($query, 2, $vcont ,2);
  ?>

  <div class="title"><a class="more" href="../articles.php?type=analytic" title="аналитика">Вся аналитика</a>Аналитика от Invest-Market.kz</div>
  <ul class="list">
 <?php
 if ($rc>0)
  {
     for ($i=0;$i<sizeof($vcont['id']);$i++)
	  {
         echo ' <li><a href="../article.php?type=analytic&id='.$vcont['id'][$i].'" title="'.$vcont['title'][$i].'">'.$vcont['title'][$i].'</a></li>';
      }
  }
 ?>
  </ul>




<?php
 // INDEX ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  $index_query="
   SELECT
            index_today.pifkz_point                               today
           ,round(index_today.pifkz_point-index_yesterday.pifkz_point,2)   yesterday
           ,round(index_today.pifkz_point-index_week.pifkz_point,2)        week
           ,round(((index_today.pifkz_point-index_week.pifkz_point)/index_week.pifkz_point)*100,2) week_persent
           ,round(index_today.pifkz_point-index_month.pifkz_point,2)       month
           ,round(((index_today.pifkz_point-index_month.pifkz_point)/index_month.pifkz_point)*100,2)       month_persent
           ,round(index_today.pifkz_point-index_6month.pifkz_point,2)      6month
           ,round(((index_today.pifkz_point-index_6month.pifkz_point)/index_6month.pifkz_point)*100,2)      6month_persent
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

<div id="tabs">
      <ul>
      <li class="topic">Индекс ПИФКЗ</li>
        <li class="first"><a href="#fragment-1">Значение</a></li>
        <li><a href="#fragment-2">Портфель</a></li>
      </ul>
<div id="fragment-1">

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
    <td class="nopad" colspan="2">
    <a href="../im_index.php?type=pifkz" title="Индекс ПИФКЗ"><img src="../lib/graph.php?tab=ism_index_pifkz&id_col=1&interval=9&id_val=1&val_col=pifkz_point&date_format=month" alt="Индекс ПИФКЗ"/></a></td>
  </tr></table>
</div>

<div id="fragment-2">
   <?php

  	$query='
  	         select
        		 (select desc_ru from ism_dictionary where id=i.item) item
       	        ,round(sum(ifnull(i.volume,0))/(select count(fund_id) from ism_funds where pifkz=1),2) volume
             from  ism_fund_structure_item i
             where i.structure_id in (select structure_id from ism_fund_structure where structure_date=(select max(structure_date) from ism_fund_structure) and fund_id in (select fund_id from ism_funds where pifkz=1))
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
					<div id="flashcontent_pie" align="center">
						<strong>Обновите ваш Flash Player</strong>
					</div>

					<script type="text/javascript">
						var so = new SWFObject("../amcharts/ampie/ampie.swf", "ampie", "250", "250", "8", "#FFFFFF");
						so.addVariable("path", "../amcharts/ampie/");
						so.addVariable("settings_file", encodeURIComponent("../amcharts/ampie/ampie_settings.xml"));
						so.addVariable("data_file", encodeURIComponent("../amcharts/ampie/ampie_data.xml"));
						so.addVariable("preloader_color", "#FFFFFF");
						so.write("flashcontent_pie");
				 </script>
				<!-- end of ampie script -->


              ';
         echo '<div class="info"><font size=1>Для получения подробной информации наведите курсор на изображение (<a href="../im_index.php?type=pifkz" title="индекс пифкз">Индекс ПИФКЗ</a>). </font></div>';

     }


  ?>
</div>
</div>

</div>
</div>

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


</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>