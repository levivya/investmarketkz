<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Управляющие компании (УК) в Казахстане</title>
<meta name="Description" content="Управляющие компании Казахстана" >
<meta name="Keywords" content="паевой инвестиционный фонд, пиф, Казахстан, управляющие компании, УК, поиск, фонд, СЧА, минимальная сумма инвестирования, тип,Invest-Market.kz">
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
<div class="one-column-block">
<div class="title">Управляющие компании</div>
<?php
$query="
       select
			 c.company_id
			,c.name
			,c.full_name
			,c.address
			,DATE_FORMAT(c.licence_recived_date,'%d.%m.%Y') licence_recived_date
			,c.phone_fax
			,c.web_site
			,d.scha
		from ism_companies c
             left join (select
             			  		  c.company_id
					             ,sum(v.asset_value) scha
						from ism_companies c, ism_funds f, ism_fund_value v
						where c.company_id=f.company_id
					           and f.fund_id=v.fund_id
					           and v.check_date=(select max(check_date) from ism_fund_value where fund_id=f.fund_id)
						group by c.company_id
						) d on d.company_id=c.company_id
		order by d.scha desc
        ";
//echo $query;

$vcomps=array();
$rc=sql_stmt($query, 8, $vcomps ,2);
?>
<table class="tab-table top-border" id="Companies">
  <thead>
  <tr>
    <th>Управляющая компания</th>
    <th>Телефон/Факс</th>
    <th class="right">СЧА</th>
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
        if ($vcomps['scha'][$i]!=0) $vcomps['scha'][$i]=number_format($vcomps['scha'][$i], '', ',', ' ');
        else $vcomps['scha'][$i]='-';

        echo '
	          <tr class="'.$class.'">
			    <td><a href="company.php?id='.$vcomps['company_id'][$i].'">'.$vcomps['name'][$i].'</a></td>
    	        <td>'.$vcomps['phone_fax'][$i].'</td>
    		    <td class="right nowrap">'.$vcomps['scha'][$i].'</td>
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

</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>