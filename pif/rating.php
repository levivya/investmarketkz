<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Рейтинг пифов (паевых фондов)</title>
<meta name="Description" content="Рейтинг Казахстанских ПИФов (доходность, активы, риск)" >
<meta name="Keywords" content="рейтинг пифов, паевые фонды, казахстан, доходность, пифы, риск, активы, пай, рассчитать доходность пифа">
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

        if (!isset($datepicker))
        {
        // get last funds value date
        $query="select DATE_FORMAT(max(check_date),'%m.%Y') last_date from ism_fund_value where fund_id in ( select fund_id from ism_funds where fund_type!=".$RISK_INVEST_OBJ.")";
        $last_date=array();
        $rc=sql_stmt($query, 1, $last_date ,1);
        $datepicker='01.'.$last_date["last_date"][0];
        }
?>

<!-- main body -->
<div class="one-column-block">

<script type="text/javascript">
$(function(){
  $("#datepicker").datepicker();
});
</script>

<form method=get>
<div class="search-block">
<div>Дата&nbsp;<input id="datepicker" name="datepicker" value="<?php echo $datepicker; ?>" type="text"></div>&nbsp;<span><input value="Обновить" class="button" type="submit" /></span>
</div>
</form>

<?php

//format date to mysql
$day=substr($datepicker,6,4)."-".substr($datepicker,3,2)."-".substr($datepicker,0,2);

$query="
   SELECT
           t.fund_id        id
           ,t.name          name
           ,c.name          company_name
           ,c.company_id
           ,CASE t.fund_type
            WHEN 17 THEN 'О'
            WHEN 18 THEN 'З'
            WHEN 19 THEN 'И'
            ELSE '-'
            END AS fund_type
           ,ifnull(value_today.value,-9999)                                                        today
           ,ifnull(value_today.asset_value,-9999)                                                  asset_value
           ,ifnull(round(((value_today.value-value_yesterday.value)/value_yesterday.value)*100,2),-9999)     yesterday_persent
           ,ifnull(round(((value_today.asset_value-value_yesterday.asset_value)/value_yesterday.asset_value)*100,2),-9999)     yesterday_asset_persent
           ,ifnull(round(((value_today.value-value_week.value)/value_week.value)*100,2),-9999)     week_persent
           ,ifnull(round(((value_today.asset_value-value_week.asset_value)/value_week.asset_value)*100,2),-9999)     week_asset_persent
           ,ifnull(round(((value_today.value-value_month.value)/value_month.value)*100,2),-9999)   month_persent
           ,ifnull(round(((value_today.asset_value-value_month.asset_value)/value_month.asset_value)*100,2),-9999)   month_asset_persent
           ,ifnull(round(((value_today.value-value_3month.value)/value_3month.value)*100,2),-9999) 3month_persent
           ,ifnull(round(((value_today.asset_value-value_3month.asset_value)/value_3month.asset_value)*100,2),-9999) 3month_asset_persent
           ,ifnull(round(((value_today.value-value_6month.value)/value_6month.value)*100,2),-9999) 6month_persent
           ,ifnull(round(((value_today.asset_value-value_6month.asset_value)/value_6month.asset_value)*100,2),-9999) 6month_asset_persent
           ,ifnull(round(((value_today.value-value_year.value)/value_year.value)*100,2),-9999)     year_persent
           ,ifnull(round(((value_today.asset_value-value_year.asset_value)/value_year.asset_value)*100,2),-9999)     year_asset_persent
           ,ifnull(round(((value_today.value-value_3year.value)/value_3year.value)*100,2),-9999)     3year_persent
           ,ifnull(round(((value_today.asset_value-value_3year.asset_value)/value_3year.asset_value)*100,2),-9999)     3year_asset_persent
   FROM    ism_funds t
           LEFT JOIN ism_fund_value value_today  ON t.fund_id=value_today.fund_id and value_today.check_date='".$day."' and value_today.fund_id!=20
           LEFT JOIN ism_fund_value value_yesterday  ON t.fund_id=value_yesterday.fund_id and value_yesterday.check_date=DATE_ADD(value_today.check_date,INTERVAL -1 DAY)
           LEFT JOIN ism_fund_value value_week  ON t.fund_id=value_week.fund_id and value_week.check_date=DATE_ADD(value_today.check_date,INTERVAL -7 DAY)
           LEFT JOIN ism_fund_value value_month  ON t.fund_id=value_month.fund_id and  value_month.check_date=DATE_ADD(value_today.check_date,INTERVAL -1 MONTH)
           LEFT JOIN ism_fund_value value_3month  ON t.fund_id=value_3month.fund_id and  value_3month.check_date=DATE_ADD(value_today.check_date,INTERVAL -3 MONTH)
           LEFT JOIN ism_fund_value value_6month  ON t.fund_id=value_6month.fund_id and   value_6month.check_date=DATE_ADD(value_today.check_date,INTERVAL -6 MONTH)
           LEFT JOIN ism_fund_value value_year  ON t.fund_id=value_year.fund_id and   value_year.check_date=DATE_ADD(value_today.check_date,INTERVAL -1 YEAR)
           LEFT JOIN ism_fund_value value_3year  ON t.fund_id=value_3year.fund_id and   value_3year.check_date=DATE_ADD(value_today.check_date,INTERVAL -3 YEAR)
           ,ism_companies c
   WHERE    t.company_id=c.company_id   and value_today.asset_value is not null
  ";
//echo $query;

$vfunds=array();
$rc=sql_stmt($query, 21, $vfunds ,2);

$query="
  SELECT
           t.fund_id   id
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
        ";
//echo $query;

$vfunds2=array();
$rc2=sql_stmt($query, 5, $vfunds2 ,2);


?>
<div id="tabs">
      <ul>
        <li class="topic" title="рейтинг паевых фондов">Рейтинг паевых фондов (ПИФов)</li>
        <li class="first"><a href="#fragment-1">Доходность</a></li>
        <li><a href="#fragment-2">Активы</a></li>
        <li><a href="#fragment-3">Доходность-Риск</a></li>
      </ul>
<div id="fragment-1">
<table class="tab-table" id="Rating">
  <thead>
  <tr>
    <th>Паевой фонд</th>
    <!--
    <th>УК</th>
    <th>Тип*</th>
    -->
    <th class="right" title="ценая пая">Паи, тг.</th>
    <th class="right" title="доходность за день">1Д,%</th>
    <th class="right" title="доходность за месяц">1М,%</th>
    <th class="right" title="доходность за 6 месяцев">6М,%</th>
    <th class="right" title="доходность за год">12М,%</th>
    <th class="right" title="доходность за 3 года">36М,%</th>
    <th class="right">СЧА, тг.</th>
    <th></th>
  </tr>
  </thead>

  <tbody>
  <?php
  if ($rc>0)
  {
	  for ($i=0;$i<sizeof($vfunds['id']);$i++)
	  {
	    $class=(fmod(($i),2)==0)?('odd'):('even');

        if ($vfunds['today'][$i]!=-9999) {$vfunds['today'][$i]=number_format($vfunds['today'][$i], 2, ',', ' ');}
		else {$vfunds['today'][$i]='';}

		if ($vfunds['asset_value'][$i]!=-9999){$vfunds['asset_value'][$i]=number_format($vfunds['asset_value'][$i], 0, ',', ' ');}
		else{$vfunds['asset_value'][$i]='';}

		if ($vfunds['yesterday_persent'][$i]!=-9999)
		{
		        if ($vfunds['yesterday_persent'][$i]>0)  $vfunds['yesterday_persent'][$i]='<span class="arrow2 up">'.$vfunds['yesterday_persent'][$i].'</span>';
		        if ($vfunds['yesterday_persent'][$i]<0)  $vfunds['yesterday_persent'][$i]='<span class="arrow2 down">'.$vfunds['yesterday_persent'][$i].'</span>';;
		} else{ $vfunds['yesterday_persent'][$i]='';}

		if ($vfunds['month_persent'][$i]!=-9999)
		{
		        if ($vfunds['month_persent'][$i]>0)  $vfunds['month_persent'][$i]='<span class="arrow2 up">'.$vfunds['month_persent'][$i].'</span>';
		        if ($vfunds['month_persent'][$i]<0)  $vfunds['month_persent'][$i]='<span class="arrow2 down">'.$vfunds['month_persent'][$i].'</span>';;
		} else{ $vfunds['month_persent'][$i]='';}

		if ($vfunds['6month_persent'][$i]!=-9999)
		{
		        if ($vfunds['6month_persent'][$i]>0)  $vfunds['6month_persent'][$i]='<span class="arrow2 up">'.$vfunds['6month_persent'][$i].'</span>';
		        if ($vfunds['6month_persent'][$i]<0)  $vfunds['6month_persent'][$i]='<span class="arrow2 down">'.$vfunds['6month_persent'][$i].'</span>';;
		} else{ $vfunds['6month_persent'][$i]='';}

		if ($vfunds['year_persent'][$i]!=-9999)
		{
		        if ($vfunds['year_persent'][$i]>0)  $vfunds['year_persent'][$i]='<span class="arrow2 up">'.$vfunds['year_persent'][$i].'</span>';
		        if ($vfunds['year_persent'][$i]<0)  $vfunds['year_persent'][$i]='<span class="arrow2 down">'.$vfunds['year_persent'][$i].'</span>';;
		} else{ $vfunds['year_persent'][$i]='';}

   		if ($vfunds['3year_persent'][$i]!=-9999)
		{
		        if ($vfunds['3year_persent'][$i]>0)  $vfunds['3year_persent'][$i]='<span class="arrow2 up">'.$vfunds['3year_persent'][$i].'</span>';
		        if ($vfunds['3year_persent'][$i]<0)  $vfunds['3year_persent'][$i]='<span class="arrow2 down">'.$vfunds['3year_persent'][$i].'</span>';;
		} else{ $vfunds['3year_persent'][$i]='';}

        echo '
	          <tr class="'.$class.'">
			    <td><a href="pif.php?id='.$vfunds['id'][$i].'" title="'.$vfunds['name'][$i].'">'.$vfunds['name'][$i].'</a></td>
    	        <!--
    	        <td><a href="company.php?id='.$vfunds['company_id'][$i].'" title="'.$vfunds['company_name'][$i].'">'.$vfunds['company_name'][$i].'</a></td>
    	        <td class="center">'.$vfunds['fund_type'][$i].'</td>
    	        -->
    	        <td class="right nowrap">'.$vfunds['today'][$i].'</td>
       	        <td class="right">'.$vfunds['yesterday_persent'][$i].'</td>
  			    <td class="right">'.$vfunds['month_persent'][$i].'</td>
  			    <td class="right">'.$vfunds['6month_persent'][$i].'</td>
  			    <td class="right">'.$vfunds['year_persent'][$i].'</td>
  			    <td class="right">'.$vfunds['3year_persent'][$i].'</td>
  			    <td class="right nowrap">'.$vfunds['asset_value'][$i].'</td>
  			    <td>
  			    <a href="calculator.php?fund_'.$vfunds['id'][$i].'=true" title="рассчитать доходность '.$vfunds['name'][$i].'">
                     <img src="../media/images/calculator.png" height="18px" alt="рассчитать доходность" border="0">
                </a>
                </td>
  			  </tr>
  			 ';
	  }
  }
  ?>
  </tbody>
</table>
</div>
<div id="fragment-2">
<table class="tab-table" id="Rating2">
  <thead>
  <tr>
    <th>Паевой фонд</th>
    <th class="right nowrap" title="текущие активы" width="15%">СЧА, тг.</th>
    <th class="right" title="прирост активов за день">1Д,%</th>
    <th class="right" title="прирост активов за месяц">1М,%</th>
    <th class="right" title="прирост активов за 6 месяцев">6М,%</th>
    <th class="right" title="прирост активов за год">12М,%</th>
    <th class="right" title="прирост активов за 3 года">36М,%</th>
  </tr>
  </thead>

  <tbody>
  <?php
  if ($rc>0)
  {
	  for ($i=0;$i<sizeof($vfunds['id']);$i++)
	  {
	    $class=(fmod(($i),2)==0)?('odd'):('even');

        /*
		if ($vfunds['asset_value'][$i]!=-9999){$vfunds['asset_value'][$i]=number_format($vfunds['asset_value'][$i], 0, ',', ' ');}
		else{$vfunds['asset_value'][$i]='';}
        */

		if ($vfunds['yesterday_asset_persent'][$i]!=-9999)
		{
		        if ($vfunds['yesterday_asset_persent'][$i]>0)  $vfunds['yesterday_asset_persent'][$i]='<span class="arrow2 up">'.$vfunds['yesterday_asset_persent'][$i].'</span>';
		        if ($vfunds['yesterday_asset_persent'][$i]<0)  $vfunds['yesterday_asset_persent'][$i]='<span class="arrow2 down">'.$vfunds['yesterday_asset_persent'][$i].'</span>';;
		} else{ $vfunds['yesterday_asset_persent'][$i]='';}

		if ($vfunds['month_asset_persent'][$i]!=-9999)
		{
		        if ($vfunds['month_asset_persent'][$i]>0)  $vfunds['month_asset_persent'][$i]='<span class="arrow2 up">'.$vfunds['month_asset_persent'][$i].'</span>';
		        if ($vfunds['month_asset_persent'][$i]<0)  $vfunds['month_asset_persent'][$i]='<span class="arrow2 down">'.$vfunds['month_asset_persent'][$i].'</span>';;
		} else{ $vfunds['month_asset_persent'][$i]='';}

		if ($vfunds['6month_asset_persent'][$i]!=-9999)
		{
		        if ($vfunds['6month_asset_persent'][$i]>0)  $vfunds['6month_asset_persent'][$i]='<span class="arrow2 up">'.$vfunds['6month_asset_persent'][$i].'</span>';
		        if ($vfunds['6month_asset_persent'][$i]<0)  $vfunds['6month_asset_persent'][$i]='<span class="arrow2 down">'.$vfunds['6month_asset_persent'][$i].'</span>';;
		} else{ $vfunds['6month_asset_persent'][$i]='';}

		if ($vfunds['year_asset_persent'][$i]!=-9999)
		{
		        if ($vfunds['year_asset_persent'][$i]>0)  $vfunds['year_asset_persent'][$i]='<span class="arrow2 up">'.$vfunds['year_asset_persent'][$i].'</span>';
		        if ($vfunds['year_asset_persent'][$i]<0)  $vfunds['year_asset_persent'][$i]='<span class="arrow2 down">'.$vfunds['year_asset_persent'][$i].'</span>';;
		} else{ $vfunds['year_asset_persent'][$i]='';}

		if ($vfunds['3year_asset_persent'][$i]!=-9999)
		{
		        if ($vfunds['3year_asset_persent'][$i]>0)  $vfunds['3year_asset_persent'][$i]='<span class="arrow2 up">'.$vfunds['3year_asset_persent'][$i].'</span>';
		        if ($vfunds['3year_asset_persent'][$i]<0)  $vfunds['3year_asset_persent'][$i]='<span class="arrow2 down">'.$vfunds['3year_asset_persent'][$i].'</span>';;
		} else{ $vfunds['3year_asset_persent'][$i]='';}

        echo '
	          <tr class="'.$class.'">
			    <td><a href="pif.php?id='.$vfunds['id'][$i].'" title="'.$vfunds['name'][$i].'">'.$vfunds['name'][$i].'</a></td>
   			    <td class="right nowrap" width="15%">'.$vfunds['asset_value'][$i].'</td>
       	        <td class="right">'.$vfunds['yesterday_asset_persent'][$i].'</td>
  			    <td class="right">'.$vfunds['month_asset_persent'][$i].'</td>
  			    <td class="right">'.$vfunds['6month_asset_persent'][$i].'</td>
  			    <td class="right">'.$vfunds['year_asset_persent'][$i].'</td>
  			    <td class="right">'.$vfunds['3year_asset_persent'][$i].'</td>
  			  </tr>
  			 ';
	  }
  }
  ?>
  </tbody>
</table>
</div>
<div id="fragment-3">
<table class="tab-table" id="Rating3">
  <thead>
  <tr>
    <th>Паевой фонд</th>
    <th class="right">Среднегодовая доходность, %</th>
    <th class="right">Волатильность,%</th>
    <th></th>
  </tr>
  </thead>

  <tbody>
  <?php
  if ($rc2>0)
  {
	  for ($i=0;$i<sizeof($vfunds2['id']);$i++)
	  {
	    $class=(fmod(($i),2)==0)?('odd'):('even');


        echo '
	          <tr class="'.$class.'">
			    <td><a href="pif.php?id='.$vfunds2['id'][$i].'" title="'.$vfunds2['name'][$i].'">'.$vfunds2['name'][$i].'</a></td>
  			    <td class="right">'.$vfunds2['avg_income'][$i].'</td>
  			    <td class="right">'.$vfunds2['volat'][$i].'</td>
  			    <td>
  			    <a href="map.php?fund_'.$vfunds['id'][$i].'=true" title="показать на карте '.$vfunds['name'][$i].'">
                     <img src="../media/images/invest_trend.png" height="18px" alt="показать на карте" border="0">
                </a>
                </td>
  			  </tr>
  			 ';
	  }
  }
  ?>
  </tbody>
</table>
</div>

<script type="text/javascript">
$(document).ready(function(){
  // ---- tablesorter -----
  $("#Rating").tablesorter({
	widgets: ["zebra"],
	sortList:[[4,1]],
    headers: {
            8: {
                // disable it by setting the property sorter to false
                sorter: false
               }
             }
  });
  $("#Rating2").tablesorter({
	widgets: ["zebra"]
  });

  $("#Rating3").tablesorter({
	widgets: ["zebra"],
    headers: {
            3: {
                // disable it by setting the property sorter to false
                sorter: false
               }
             }
  });

  // ---- tablesorter -----
});
</script>

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