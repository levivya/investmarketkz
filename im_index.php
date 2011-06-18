<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

//define default type
if (!isset($type)) $type='pifkz';

switch ($type) {
    case 'pifkz': $query_main = "SELECT round(pifkz_point,2) value,round(asset,0) volume, check_date date FROM ism_index_pifkz order by check_date desc";
                $page_title='Индекс ПИФКЗ';
                $page_kaywords='пиф, паевой фонд, индекс ПИФКЗ, активы';
                $query2="select fund_id,name from ism_funds where pifkz=1 order by name";
                $fund_link='../pif/pif.php';
                $index_text='Индекс ПИФКЗ рассчитывается на основе статистической информации входящих в него паевых фондов, и отображает среднерыночную тенденцию рынка коллективных инвестиций в Республике Казахстан.';
		        $selected_menu='pif';
    			break;
    case 'npfkz': $query_main = "SELECT round(npfkz_point,2) value,round(asset,0) volume, check_date date FROM ism_index_npfkz order by check_date desc";
                $page_title='Индекс НПФКЗ';
                $tab='ism_index_npfkz';
                $page_kaywords='нпф, пенсионный фонд, индекс НПФКЗ, активы';
                $query2="select fund_id,name from ism_pension_funds where npfkz=1 order by name";
                $fund_link='../npf/npf.php';
                $index_text='Индекс НПФКЗ рассчитывается на основе статистической информации входящих в него пенсионных фондов, и отображает среднерыночную тенденцию рынка негосударственных пенсионных фондов в Республике Казахстан.';
		        $selected_menu='npf';
    			break;

    break;
               }
if (!isset($tab_id)) $tab_id=0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $page_title;?></title>
<meta name="Description" content="<?php echo $page_title;?>" >
<meta name="Keywords" content="<?php echo $page_keywords;?>">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include 'includes/scripts.php';?>
<script>
  $(document).ready(function(){
  	var $tabs = $('#tabs').tabs(); // first tab selected
  	$tabs.tabs('select', <?php echo $tab_id?>);
   });
</script>
</head>
<body>
<div id="container">
<!-- header -->
<?php include 'includes/header.php';?>
<!-- main body -->
<div class="sidebar2">
 <?php

  if ($type=='pifkz')
  {  	$query='
  	    select
           		 (select desc_ru from ism_dictionary where id=d.id) item
                 ,round(sum(ifnull(i.volume,0))/count(s.fund_id),2) volume
        from ism_fund_structure s, ism_dictionary d left join ism_fund_structure_item i on i.item=d.id  and i.structure_id=s.structure_id
        where s.structure_date = (select max(structure_date) from ism_fund_structure)
              and s.fund_id in (select fund_id from ism_funds where pifkz=1)
        group by d.id
        order by d.id
           ';

   //echo $query;

   $caption='ПИФКЗ';

   }
   else
   {   	$query='
  	    select
           		 (select desc_ru from ism_dictionary where id=d.id) item
                 ,round(sum(ifnull(i.volume,0))/count(s.fund_id),2) volume
        from ism_pension_fund_structure s, ism_dictionary d left join ism_pension_fund_structure_item i on i.item=d.id  and i.structure_id=s.structure_id
        where s.structure_date = (select max(structure_date) from ism_pension_fund_structure)
              and s.fund_id in (select fund_id from ism_pension_funds where npfkz=1)
        group by d.id
        order by d.id
           ';
    $caption='НПФКЗ';
   }
     $vindex_stru_data=array();
	 $rc=sql_stmt($query, 2, $vfund_stru_data ,2);

     if ($rc>0)
	 {
         $fh = fopen('amcharts/ampie/ampie_data.xml', 'w') or die("can't open file");
		 fwrite($fh, '<?xml version="1.0" encoding="UTF-8"?><pie>');

		 for ($i=0;$i<sizeof($vfund_stru_data['item']);$i++)
		 {
    		 fwrite($fh, '<slice title="'.$vfund_stru_data['item'][$i].'" pull_out="false">'.$vfund_stru_data['volume'][$i].'</slice>');
		 }

  		 fwrite($fh, '</pie>');
		 fclose($fh);

  	     echo '<div class="title">Структура портфеля '.$caption.'</div>';

         echo '
               <!-- ampie script-->
				<script type="text/javascript" src="amcharts/ampie/swfobject.js"></script>
					<div id="flashcontent_pie">
						<strong>Обнавите ваш Flash Player</strong>
					</div>

					<script type="text/javascript">
						var so = new SWFObject("amcharts/ampie/ampie.swf", "ampie", "250", "250", "8", "#FFFFFF");
						so.addVariable("path", "amcharts/ampie/");
						so.addVariable("settings_file", encodeURIComponent("amcharts/ampie/ampie_settings.xml"));
						so.addVariable("data_file", encodeURIComponent("amcharts/ampie/ampie_data.xml"));
						so.addVariable("preloader_color", "#FFFFFF");
						so.write("flashcontent_pie");
				 </script>
				<!-- end of ampie script -->


              ';
         echo '<div class="info"><font size=1>Для получения подробной информации наведите курсор на изображение.</font></div><br>';

     }


  ?>
<noindex>
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="../banner.php" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
     Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
    <!-- end sidebar2 -->
  </div>
</noindex>
<div class="mainContent">
 <div id="tabs">
      <ul>
        <li class="topic"><?php echo $page_title;?></li>
        <li class="first"><a href="#fragment-1">Динамика активов</a></li>
        <li><a href="#fragment-2">Структура портфеля</a></li>
      </ul>
  <div id="fragment-1">
<?php
// write to graph file
$fh = fopen('./amcharts/amstock/data.csv', 'w') or die("can't open file");
//echo $query_main;
$res = mysql_query($query_main);
while($obj = mysql_fetch_object($res)){
  $date = $obj->date;
  $value =  $obj->value;
  $volume =  $obj->volume;
  fwrite($fh, "$date,$volume,$value\n");
}
fclose($fh);
?>
<!-- amstock script-->
<noindex>
  <script type="text/javascript" src="../amcharts/amstock/swfobject.js"></script>
	<div id="flashcontent" class="search-block grey-block">
		<strong>Вам необходимо обновить Flash Player</strong>
	</div>

	<script type="text/javascript">
		var so = new SWFObject("./amcharts/amstock/amstock.swf", "amstock", "680", "500", "8", "#FFFFFF");
		so.addVariable("path", "./amcharts/amstock/");
		so.addVariable("settings_file", encodeURIComponent("./amcharts/amstock/amstock_settings.xml"));
		so.addVariable("preloader_color", "#184789");
		so.write("flashcontent");
	</script>
<!-- end of amstock script -->
</noindex>
<div class="text"><?php echo $index_text; ?></div>
</div>
<div id="fragment-2">
<?php
       $type=($type=='pifkz')?('pif'):('npf');
       $subtype='index';
       include('./includes/fund_structure.php');
?>
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
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>