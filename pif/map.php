<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Карта доходность-риск</title>
<meta name="Description" content="Выбрать паевой фонд (ПИФ) при помощи карты доходность-риск" >
<meta name="Keywords" content="пиф, паевой фонд, выбрать, доходность, риск, карта">
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
  <div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="../banner.php" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
     Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
    <!-- end sidebar2 -->
  </div>

<div class="mainContent">
<?php
//sorting
$order=(isset($refresh))?(""):("order by d.avg_income desc");

//get funds
$query="
 SELECT
           d.fund_id
          ,d.name
          ,d.period
          ,d.avg_income
          ,d.avg_volat
 FROM
  (
  SELECT
           t.fund_id
          ,t.name
          ,round(DATEDIFF(curdate(),t.start_date)/365,2)   period
          ,round(tt.avg_income,2)      avg_income
          ,round(tt.avg_volat,2)        avg_volat
   FROM
         ism_funds t
        ,ism_fund_year_avg_income tt
   WHERE  t.fund_id=tt.fund_id
          AND tt.check_date=(select max(check_date) from ism_fund_year_avg_income where fund_id=t.fund_id)
          AND t.fund_type!=".$RISK_INVEST_OBJ."
          AND tt.check_date>=DATE_ADD(NOW(), INTERVAL -1 MONTH)
          AND DATE_ADD(t.start_date,INTERVAL 18 MONTH)<=current_date()
   ) d
   ".$order;

$vfunds=array();
$rc=sql_stmt($query, 5, $vfunds ,2);

$select_in="";
for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
{
  if (isset(${'fund_'.$vfunds['fund_id'][$i]}))  $select_in.=$vfunds['fund_id'][$i].",";
}
$select_in=substr($select_in,0,strlen($select_in)-1);
?>

<script language="javascript">

function CheckAll(a)
{ if (a.checked)
 { 	<?php
 	for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
     {
 	   echo 'var v'.$i.'=document.getElementById("fund_'.$vfunds['fund_id'][$i].'"); v'.$i.'.checked = true;';
 	 }
 	?> }
 else
 {  	<?php
 	for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
     {
 	   echo 'var v'.$i.'=document.getElementById("fund_'.$vfunds['fund_id'][$i].'"); v'.$i.'.checked = false;';
 	 }
 	?>
 }
}
</script>

<?php
//get avg year income and volat for index pifkz
$data=get_sgd_volat();
$avg_year_income=$data['avg_year_income'];
$volat=$data['volat'];
?>


<div class="title">Карта доходность-риск <font style="font-weight:normal;">(отметьте интересующие вас фонды)</font></div>
<div class="scroll-block">
<form name="main" method="post">
<table id="Funds" class="tab-table">
  <thead>
  <tr>
    <th><input type="checkbox" id="check_all" name="check_all" onclick="CheckAll(this);" <?php if (isset($check_all)) echo "checked"; ?>></th>
    <th>Паевой фонд</th>
    <th class="right" title="фонд работает (лет)">Работает, лет</th>
    <th class="right" title="среднегодовая доходность">СГД,%</th>
    <th class="right">Волатильность,%</th>
  </tr>
 </thead>
  <tbody>
    <?php
  if ($rc>0)
  {
	  $k=0;
	  for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
	  {
	    $class=(fmod(($i),2)==0)?('odd'):('even');
	    $bgColor=' ';
	    $Checked=' ';
	    $Style=' ';
	    $Img=' ';

	    if  (isset(${'fund_'.$vfunds['fund_id'][$i]}))
	    {
	    	$bgColor='bgcolor="'.$COLOR[$k].'"';
	    	$Img='<img src="../media/images/color'.$k.'.png" />';
	    	$Checked='checked';
	    	$Style='style="background-color: '.$COLOR[$k].'; color: '.$COLOR[$k].';"';
	    	$k++;
	    }
        echo '
	          <tr class="'.$class.'">
			    <td id="td_fund_'.$vfunds['fund_id'][$i].'" ><input type="checkbox" id="fund_'.$vfunds['fund_id'][$i].'" name="fund_'.$vfunds['fund_id'][$i].'" '.$Checked.' '.$Style.'></td>
			    <td>'.$Img.'&nbsp;<a href="pif.php?id='.$vfunds['fund_id'][$i].'">'.$vfunds['name'][$i].'</a></td>
			    <td class="right">'.$vfunds['period'][$i].'</td>
			    <td class="right">'.$vfunds['avg_income'][$i].'</td>
			    <td class="right">'.$vfunds['avg_volat'][$i].'</td>
  			  </tr>
  			 ';
	  }
  }
  ?>
 </tbody>
</table>
</div>
<div class="search-block"><span><input value="Обновить" name="refresh" class="button" type="submit" /></span><strong>Индекс ПИФКЗ</strong> (CГД: <span class="blue"><?php echo $avg_year_income;?>%</span>; Волатильность: <span class="blue"><?php echo $volat;?>%</span>)</div>
</form>

<img src="map_img.php?funds=<?php echo $select_in;?>&volat=<?php echo $volat;?>&avg_year_income=<?php echo $avg_year_income;?>" alt="карта доходность-риск пифов" />

<div class="text">
<strong>Карта Доходность-Риск </strong> поможет Вам провести сравнительный анализ ПИФов в координатах Доходность-Риск, на базе сравнения со среднерыночным показателем индекса <a href="../im_index.php?type=pifkz"title="индекс ПИФКЗ">ПИФКЗ</a>. Вы сможете оценить в какой паевой фонд Вам следует вкладывать свои деньги и на сколько рискованны данные вложения.
</div>


<script type="text/javascript">
$(document).ready(function(){
  // ---- tablesorter -----
  $("#Funds").tablesorter({
	widgets: ["zebra"],
	sortList:[[3,1]],
    headers: {
            0: {
                // disable it by setting the property sorter to false
                sorter: false
               }
             }
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