<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Компании управляющие пенсионными активами (КУПА)</title>
<meta name="Description" content="Компании управляющие пенсионными активами (КУПА)" >
<meta name="Keywords" content="нпф, Казахстан, управляющие компании, КУПА, пенсионные активы">
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
        $selected_menu='npf';
        include '../includes/header.php';

?>

<!-- main body -->
<div class="one-column-block">
<div class="title">Компании управляющие пенсионными активами (КУПА)
<a href="rating.php" class="more" alt="рейтинг пенсионных фондов (нпф)">Рейтинг пенсионных фондов</a>
</div>
<?php
	$query = "select
	          	c.company_id
	            ,c.name
	            ,c.director
	            ,c.address
	            ,c.phone
	            ,c.fax
	            ,c.email
	            ,c.web_site
	            ,c.general_info
	            ,if(d.spn=0,'',d.spn) spn
	          from
	          	ism_pension_companies c
	            ,(
	            	select
				    	sum(f.asset_value) spn
	                	,tt.company_id
	              	from
	              		ism_pension_funds tt,
	               		(
	                 		select
	                        	t.fund_id
	                            ,t.asset_value
	                 		from
	                 			ism_pension_fund_value t
	                 		where
	                 			check_date=(select max(check_date) from ism_pension_fund_value where fund_id=t.fund_id)
	                 		union
	                 		select
	                        	t.fund_id
	                            ,0 asset_value
	                 		from
	                 			ism_pension_funds t
	                 		where
	                 			t.fund_id not in (select distinct(fund_id) from ism_pension_fund_value)
	               		) f
	               where
	               		tt.fund_id = f.fund_id
	               group by
	               		tt.company_id
	               union
	               select
				   		0 spn
	                    ,tt.company_id
	               from
	               		ism_pension_companies tt
	               where
	               		tt.company_id not in (select distinct(company_id) from ism_pension_funds)
	             ) d
	         where
	         	d.company_id = c.company_id
          ";

	$vcomps=array();
	$rc=sql_stmt($query, 10, $vcomps, 2);

?>
<table class="tab-table top-border" id="Companies">
  <thead>
  <tr>
    <th>Наименование КУПА</th>
    <th>Телефон</th>
    <th>Факс</th>
    <th class="right">Накопления</th>
    <th class="right">Сайт</th>
  </tr>
  </thead>

  <tbody>
  <?php
  if ($rc>0)
  {
	  for ($i=0;$i<sizeof($vcomps['company_id']);$i++)
	  {
	    $class=(fmod(($i),2)==0)?('odd'):('even');
        if ($vcomps['spn'][$i]!=0) $vcomps['spn'][$i]=number_format($vcomps['spn'][$i], '', ',', ' ');
        else $vcomps['spn'][$i]='-';

        echo '
	          <tr class="'.$class.'">
			    <td><a href="company.php?id='.$vcomps['company_id'][$i].'">'.$vcomps['name'][$i].'</a></td>
    	        <td>'.$vcomps['phone'][$i].'</td>
    	        <td>'.$vcomps['fax'][$i].'</td>
    		    <td class="right nowrap">'.$vcomps['spn'][$i].'</td>
    		    <td class="right">'.($str=($vcomps['web_site'][$i]!="")?('<a href="'.$vcomps['web_site'][$i].'"><img src=../media/images/url.gif></a>'):('')).'</td>
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
  $("#Companies").tablesorter({
	widgets: ["zebra"],
	sortList:[[0,0]]
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