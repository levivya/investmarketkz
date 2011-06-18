<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Банки Казахстана</title>
<meta name="Description" content="Все банки Казахстана" >
<meta name="Keywords" content="банки казахстана, банк, банки алматы, банки астаны, депозиты банка, телефон банка">
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
     $selected_menu='deposit';
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

 <!-- BANKS -->
<?php

$query="
          select
                 b.bank_id
                ,b.name
                ,b.web_site
                ,b.phone
                ,(select count(deposit_id) from ism_deposits where bank_id=b.bank_id) deposits
         from ism_banks b
        ";
//echo $query;


$vbanks=array();
$rc=sql_stmt($query, 5, $vbanks ,2);

?>
<div class="mainContent">
<div class="text"><strong>Банки Алматы, Астаны</strong> и других регионов составляют основу финансовой стабильности Казахстана. Они предоставляют доступ к различным банковским продуктам, таким как <a href="/deposit/" title="">вклады</a> и кредиты, для граждан Республики. В данном разделе собрана статистика о всех <strong>банках Казахстана</strong>, которая должна помочь выбрать именно тот банк, который подходит именно вам.</div>

<h1 class="title"><a href="deposits.php" class="more">Депозиты банков</a>Банки Казахстана</h1>
<div class="block1 nopad">
 <table class="tab-table" id="Banks">
  <thead>
  <tr>
    <th>Банк</th>
    <th title="Депозиты банка">Депозиты</th>
    <th class="right" title="телефон банка">Телефон</th>
    <th class="right"></th>
    </tr>
  </thead>
  <tbody>
  <?php
  if ($rc>0)
  {
	  for ($i=0;$i<sizeof($vbanks['bank_id']);$i++)
	  {
	    $class=(fmod(($i),2)==0)?('odd'):('even');
        echo '
	          <tr class="'.$class.'">
    			<td><a href="bank.php?id='.$vbanks['bank_id'][$i].'" title="'.$vbanks['name'][$i].'">'.$vbanks['name'][$i].'</a></td>
                <td class="right"><a href="deposits.php?bank_id='.$vbanks['bank_id'][$i].'" title="депозиты банка '.$vbanks['name'][$i].'">'.$vbanks['deposits'][$i].'</a></td>
                <td class="right">'.$vbanks['phone'][$i].'</td>
                <td class="right">'.($str=($vbanks['web_site'][$i]!="")?('<noindex><a href="'.$vbanks['web_site'][$i].'" rel="nofollow"><img src=../media/images/url.gif></a><noindex>'):('')).'</td>
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
  $("#Banks").tablesorter({
	widgets: ["zebra"],
	sortList:[[0,0]]
  });
  // ---- tablesorter -----
});
</script>


</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>