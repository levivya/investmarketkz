<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");

        // Connecting, selecting database
        $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

      // Months menu
		$months = array("months" =>
					array("Январь"=>"01",
						  "Февраль"=>"02",
						  "Март"=>"03",
						  "Апрель"=>"04",
						  "Май"=>"05",
						  "Июнь"=>"06",
						  "Июль"=>"07",
						  "Август"=>"08",
						  "Сентябрь"=>"09",
						  "Октябрь"=>"10",
						  "Ноябрь"=>"11",
						  "Декабрь"=>"12"));


		//Months menu
		$query = "SELECT
			 			MONTH(max(pfv.check_date)) last_month
				  FROM
						ism_pension_fund_value pfv
		         ";
		$vmonth = array();
		$rc = sql_stmt($query, 1, $vmonth, 2);

		$month_labels=array_keys($months['months']);

		if ($rc>0)
		{
			if(!isset($month))
			{
				$month = $vmonth['last_month'][0];
				if (strlen($month)<2)  $month='0'.$month;
			}

		   $MonthMenuStr = '<select name="month">'.menu_list(array_keys($months['months']), $month, array_values($months['months'])).'</select>';
		}

		$selected_month_label=$month_labels[$month-1];


		// Years menu
		$query = "SELECT
			 			DISTINCT YEAR(pfv.check_date) years
				  FROM
						ism_pension_fund_value pfv
				  ORDER BY
						YEAR(pfv.check_date) DESC";
		$years = array();
		$rc = sql_stmt($query, 1, $years, 2);
		if ($rc>0)
		{
			if(!isset($year))
			{
				$year = $years['years'][0];
			}

			$YearMenuStr = '<select name="year">'.menu_list($years['years'], $year, $years['years']).'</select>';
		}

		//format date
		if(isset($month) && isset($year))
		{
			$day = $year."-".$month."-01";
		}
		else
		{
			$day = $years['years'][0]."-".$months['months'][0]."-01";
			$year=$years['years'][0];
		}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Рейтинг пенсионных фондов</title>
<meta name="Description" content="Рейтинг пенсионных фондов (НПФ) Казахстана" >
<meta name="Keywords" content="рейтинг пенсионных фондов, пенсионные фонды казахстана рейтин, рейтинг нпф, нпф казахстана, пенсионные фонды, расчет пенсии, цена упе, активы, доходность">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
<script type="text/javascript">
	function vaccount(){window.location = "../vportfolio.php"}
	function consultant(){window.location = "../ask_question.php"}
	function income(){window.location = "../income_compare.php"}
	function gos_pens(){window.location = "./calculator2.php"}

    $(document).ready(function(){
     $("#Rating").tablesorter({
   	  widgets: ["zebra"],
	  sortList:[[7,1]],
      headers: {
            9: {
                // disable it by setting the property sorter to false
                sorter: false
               }
             }
                             });
     $("#Rating2").tablesorter({widgets: ["zebra"]});
                               });
</script>
</head>

<body>
<div id="container">
<!-- header -->
<?php
      $selected_menu='npf';
      include '../includes/header.php';
?>
<!-- main body -->
<div class="one-column-block">
<form method=get name="set_date">
<div class="search-block">
<div>Учетный месяц</div>
<span>
<?php echo $MonthMenuStr.'&nbsp;'.$YearMenuStr;?>
&nbsp;&nbsp;&nbsp;&nbsp;
<input value="Обновить"type="submit" title="Выбрать"/>
<input value="V-Счет" type="button" class="red" onclick="vaccount()" title="Открыть V-Счет"/>
<input value="Консультация" type="button" class="dblue" onclick="consultant()" title="вопрос консультанту"/>
<input value="Доходность" type="button" class="red" onclick="income()" title="доходность инструментов"/>
<input value="Гос. пенсия" type="button" class="dblue" onclick="gos_pens()" title="расчет государственной пенсии"/>
</span>
</div>
</form>

<div class="text">
<strong>Рейтинг пенсионных фондов</strong> Казахстана составлен на основе официальных данных, с целью оценки эффективности деятельности НПФ. Анализируя динамику изменения цены условной пенсионной единицы (УПЕ), мы можем сказать какую доходность имеет тот или иной пенсионный фонд. Таким образом НПФ Казахстана, показывающие стабильную доходность на протяжении длительного времени являются наиболее привлекательными. <a href="calculator1.php">Расчет пенсии</a>, с учетом среднегодовой доходности из рейтинга, дает представление о суммарных пенсионных накоплениях.
</div>
<div class="yashare-auto-init" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>

<?php

$query = "SELECT
			 t.fund_id  id
		    ,t.name
		    ,c.company_id
		    ,c.name company_name
		    ,CASE t.fund_type
	             WHEN 131 THEN 'О'
	             WHEN 130 THEN 'К'
             	 ELSE '-'
             END AS fund_type
		    ,ifnull(round(tod.value, 2), 'N/A')                           today
		    ,ifnull(round(tod.asset_value, 2), 'N/A')                     asset_value
			,ifnull(round((tod.value-t1.value)/t1.value*100, 2), 'N/A')   1month_percent
			,ifnull(round((tod.value-t6.value)/t6.value*100, 2), 'N/A')   6month_percent
			,ifnull(round((tod.value-t12.value)/t12.value*100, 2), 'N/A') 12month_percent
			,ifnull(round((tod.value-t36.value)/t36.value*100, 2), 'N/A') 36month_percent
			,ifnull(round((tod.value-t60.value)/t60.value*100, 2), 'N/A') 60month_percent
			,ifnull(round((tod.asset_value-t1.asset_value)/t1.asset_value*100, 2), 'N/A')   1month_percent_asset
			,ifnull(round((tod.asset_value-t6.asset_value)/t6.asset_value*100, 2), 'N/A')   6month_percent_asset
			,ifnull(round((tod.asset_value-t12.asset_value)/t12.asset_value*100, 2), 'N/A') 12month_percent_asset
			,ifnull(round((tod.asset_value-t36.asset_value)/t36.asset_value*100, 2), 'N/A') 36month_percent_asset
			,ifnull(round((tod.asset_value-t60.asset_value)/t60.asset_value*100, 2), 'N/A') 60month_percent_asset
		FROM
			ism_pension_funds t
			,(SELECT '".$day."' date_today) dt
			,ism_pension_companies c
		LEFT JOIN
			ism_pension_fund_value tod
			ON t.fund_id=tod.fund_id
			   AND tod.check_date=dt.date_today
		LEFT JOIN
			ism_pension_fund_value t1
			ON t.fund_id=t1.fund_id
			   AND t1.check_date=DATE_ADD(dt.date_today, INTERVAL -1 MONTH)
		LEFT JOIN
			ism_pension_fund_value t6
			ON t.fund_id=t6.fund_id
			   AND t6.check_date=DATE_ADD(dt.date_today, INTERVAL -6 MONTH)
		LEFT JOIN
			ism_pension_fund_value t12
			ON t.fund_id=t12.fund_id
			   AND t12.check_date=DATE_ADD(dt.date_today, INTERVAL -12 MONTH)
	   	LEFT JOIN
			ism_pension_fund_value t36
			ON t.fund_id=t36.fund_id
			   AND t36.check_date=DATE_ADD(dt.date_today, INTERVAL -36 MONTH)
	   	LEFT JOIN
			ism_pension_fund_value t60
			ON t.fund_id=t60.fund_id
			   AND t60.check_date=DATE_ADD(dt.date_today, INTERVAL -60 MONTH)
	WHERE
			t.company_id = c.company_id
			and t.status!=".$PFUND_CLOSED;
//echo($query);
$vfunds=array();
$rc=sql_stmt($query, 17, $vfunds ,2);

?>
<div id="tabs">
      <ul>
        <li class="topic">Пенсионные фонды Казахстана  рейтинг</li>
        <li class="first"><a href="#fragment-1" title="рейтинг по цене УПЕ">Цена УПЕ</a></li>
        <li><a href="#fragment-2" title="рейтинг по активам">Активы</a></li>
      </ul>
<div id="fragment-1">
<table class="tab-table" id="Rating">
  <thead>
  <tr>
    <th title="пенсионный фонд">Пенсионный фонд</th>
    <th class="right" title="стоимость УПЕ (тенге)">УПЕ, тг.</th>
    <th class="right" title="доходность пенсионного фонда за месяц">1М,%</th>
    <th class="right" title="доходность пенсионного фонда за 6 месяцев">6M,%</th>
    <th class="right" title="доходность пенсионного фонда за год">12М,%</th>
    <th class="right" title="доходность пенсионного фонда за 3 года">36М,%</th>
    <th class="right" title="доходность пенсионного фонда за 5 лет">5 лет,%</th>
    <th class="right" title="среднегодовая доходность">СГД.,%</th>
    <th class="right" title="активы пенсионного фонда">Активы, тг.</th>
    <th></th>
  </tr>
  </thead>

  <tbody>
  <?php
  if ($rc>0)
  {
	  for ($i=0;$i<sizeof($vfunds['id']);$i++)
	  {

		if ($vfunds['1month_percent'][$i]!='N/A')
		{
		        if ($vfunds['1month_percent'][$i]>0)  $vfunds['1month_percent'][$i]='<span class="arrow2 up">'.$vfunds['1month_percent'][$i].'</span>';
		        if ($vfunds['1month_percent'][$i]<0)  $vfunds['1month_percent'][$i]='<span class="arrow2 down">'.$vfunds['1month_percent'][$i].'</span>';;
		} else{ $vfunds['1month_percent'][$i]='-';}


		if ($vfunds['6month_percent'][$i]!='N/A')
		{
		        if ($vfunds['6month_percent'][$i]>0)  $vfunds['6month_percent'][$i]='<span class="arrow2 up">'.$vfunds['6month_percent'][$i].'</span>';
		        if ($vfunds['6month_percent'][$i]<0)  $vfunds['6month_percent'][$i]='<span class="arrow2 down">'.$vfunds['6month_percent'][$i].'</span>';;
		} else{ $vfunds['6month_percent'][$i]='-';}

	    $year_income=$vfunds['12month_percent'][$i];

		if ($vfunds['12month_percent'][$i]!='N/A')
		{
		        if ($vfunds['12month_percent'][$i]>0)  $vfunds['12month_percent'][$i]='<span class="arrow2 up">'.$vfunds['12month_percent'][$i].'</span>';
		        if ($vfunds['12month_percent'][$i]<0)  $vfunds['12month_percent'][$i]='<span class="arrow2 down">'.$vfunds['12month_percent'][$i].'</span>';;
		} else{ $vfunds['12month_percent'][$i]='-';}

   		if ($vfunds['36month_percent'][$i]!='N/A')
		{
		        $avg3=round($vfunds['36month_percent'][$i]/3,2);
		        if ($vfunds['36month_percent'][$i]>0)  $vfunds['36month_percent'][$i]='<span class="arrow2 up">'.$vfunds['36month_percent'][$i].'</span>';
		        if ($vfunds['36month_percent'][$i]<0)  $vfunds['36month_percent'][$i]='<span class="arrow2 down">'.$vfunds['36month_percent'][$i].'</span>';;
		} else{ $vfunds['36month_percent'][$i]='-';}

       if ($vfunds['60month_percent'][$i]!='N/A')
		{
   		        $avg=round($vfunds['60month_percent'][$i]/5,2);
		        if ($vfunds['60month_percent'][$i]>0)  $vfunds['60month_percent'][$i]='<span class="arrow2 up">'.$vfunds['60month_percent'][$i].'</span>';
		        if ($vfunds['60month_percent'][$i]<0)  $vfunds['60month_percent'][$i]='<span class="arrow2 down">'.$vfunds['60month_percent'][$i].'</span>';
		} else{   $avg=$avg3; $vfunds['60month_percent'][$i]='-';}

        echo '
	          <tr>
			    <td><a href="npf.php?id='.$vfunds['id'][$i].'" title="пенсионный фонд '.$vfunds['name'][$i].'">'.$vfunds['name'][$i].'</a></td>
    	        <td class="right">'.$vfunds['today'][$i].'</td>
  			    <td class="right">'.$vfunds['1month_percent'][$i].'</td>
  			    <td class="right">'.$vfunds['6month_percent'][$i].'</td>
  			    <td class="right">'.$vfunds['12month_percent'][$i].'</td>
  			    <td class="right">'.$vfunds['36month_percent'][$i].'</td>
  			    <td class="right">'.$vfunds['60month_percent'][$i].'</td>
  			    <td class="right">'.$avg.'</td>
  			    <td class="right nowrap">'.number_format($vfunds['asset_value'][$i], 0, ',', ' ').'</td>
  		        <td>
                     <a class="nyroModal" rev="modal" href="calculator.php?income='.$avg.'" title="расчет пенсии">
                     <img src="../media/images/calculator.png" height="18px" alt="расчет пенсии" border="0">
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
    <th title="пенсионный фонд">Пенсионный фонд</th>
    <th class="right" title="активы пенсионного фонда на учетный период">Активы, тг.</th>
    <th class="right">1М,%</th>
    <th class="right">6M,%</th>
    <th class="right">12М,%</th>
    <th class="right">36М,%</th>
    <th class="right">5 лет,%</th>
  </tr>
  </thead>

  <tbody>
  <?php
  if ($rc>0)
  {
	  for ($i=0;$i<sizeof($vfunds['id']);$i++)
	  {

		if ($vfunds['1month_percent_asset'][$i]!='N/A')
		{
		        if ($vfunds['1month_percent_asset'][$i]>0)  $vfunds['1month_percent_asset'][$i]='<span class="arrow2 up">'.$vfunds['1month_percent_asset'][$i].'</span>';
		        if ($vfunds['1month_percent_asset'][$i]<0)  $vfunds['1month_percent_asset'][$i]='<span class="arrow2 down">'.$vfunds['1month_percent_asset'][$i].'</span>';;
		} else{ $vfunds['1month_percent_asset'][$i]='-';}


		if ($vfunds['6month_percent_asset'][$i]!='N/A')
		{
		        if ($vfunds['6month_percent_asset'][$i]>0)  $vfunds['6month_percent_asset'][$i]='<span class="arrow2 up">'.$vfunds['6month_percent_asset'][$i].'</span>';
		        if ($vfunds['6month_percent_asset'][$i]<0)  $vfunds['6month_percent_asset'][$i]='<span class="arrow2 down">'.$vfunds['6month_percent_asset'][$i].'</span>';;
		} else{ $vfunds['6month_percent_asset'][$i]='-';}

		if ($vfunds['12month_percent_asset'][$i]!='N/A')
		{
		        if ($vfunds['12month_percent_asset'][$i]>0)  $vfunds['12month_percent_asset'][$i]='<span class="arrow2 up">'.$vfunds['12month_percent_asset'][$i].'</span>';
		        if ($vfunds['12month_percent_asset'][$i]<0)  $vfunds['12month_percent_asset'][$i]='<span class="arrow2 down">'.$vfunds['12month_percent_asset'][$i].'</span>';;
		} else{ $vfunds['12month_percent_asset'][$i]='-';}

   		if ($vfunds['36month_percent_asset'][$i]!='N/A')
		{
		        if ($vfunds['36month_percent_asset'][$i]>0)  $vfunds['36month_percent_asset'][$i]='<span class="arrow2 up">'.$vfunds['36month_percent_asset'][$i].'</span>';
		        if ($vfunds['36month_percent_asset'][$i]<0)  $vfunds['36month_percent_asset'][$i]='<span class="arrow2 down">'.$vfunds['36month_percent_asset'][$i].'</span>';;
		} else{ $vfunds['36month_percent_asset'][$i]='-';}

       if ($vfunds['60month_percent_asset'][$i]!='N/A')
		{
		        if ($vfunds['60month_percent_asset'][$i]>0)  $vfunds['60month_percent_asset'][$i]='<span class="arrow2 up">'.$vfunds['60month_percent_asset'][$i].'</span>';
		        if ($vfunds['60month_percent_asset'][$i]<0)  $vfunds['60month_percent_asset'][$i]='<span class="arrow2 down">'.$vfunds['60month_percent_asset'][$i].'</span>';;
		} else{ $vfunds['60month_percent_asset'][$i]='-';}

         $class=(fmod(($i),2)==0)?('odd'):('even');

          echo '
	           <tr class="'.$class.'">
		        <td><a href="npf.php?id='.$vfunds['id'][$i].'" title="пенсионный фонд '.$vfunds['name'][$i].'">'.$vfunds['name'][$i].'</a></td>
  			    <td class="right nowrap">'.number_format($vfunds['asset_value'][$i], 0, ',', ' ').'</td>
                <td class="right">'.$vfunds['1month_percent_asset'][$i].'</td>
  			    <td class="right">'.$vfunds['6month_percent_asset'][$i].'</td>
  			    <td class="right">'.$vfunds['12month_percent_asset'][$i].'</td>
  			    <td class="right">'.$vfunds['36month_percent_asset'][$i].'</td>
  			    <td class="right">'.$vfunds['60month_percent_asset'][$i].'</td>
  			  </tr>
  			 ';
	  }
  }
  ?>
  </tbody>
</table>
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