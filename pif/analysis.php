<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Анализ тренда доходности ПИФов</title>
<meta name="Description" content="Выбрать паевой инвестиционный фонд (ПИФ). Анализ тренда доходности паевых фондов." >
<meta name="Keywords" content="пиф, паевой фонд, выбрать, доходность, риск, калькулятор, анализ">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>

<script type="text/javascript">
$(function(){
  $.datepicker.setDefaults(
        $.extend($.datepicker.regional["ru"])
  );
  $("#datepicker1").datepicker();
});

$(function(){
  $.datepicker.setDefaults(
        $.extend($.datepicker.regional["ru"])
  );
  $("#datepicker2").datepicker();
});

function calc_period()
 {
  var tmp1=document.forms['main'].elements['datepicker1'].value;
  var tmp2=document.forms['main'].elements['datepicker2'].value;
  var sdate=new Date(tmp1.substr(6,4), tmp1.substr(3,2)-1, tmp1.substr(0,2)); //Month is 0-11 in JavaScript
  var edate=new Date(tmp2.substr(6,4), tmp2.substr(3,2)-1, tmp2.substr(0,2)); //Month is 0-11 in JavaScript
  //Set 1 day in milliseconds
  var one_year=1000*60*60*24*365;
  var rnum = (edate.getTime()-sdate.getTime())/(one_year);
  //round number
  var rlength = 2; // The number of decimal places to round to
  if (rnum > 8191 && rnum < 10485) {
             rnum = rnum-5000;
             var newnumber = Math.round(rnum*Math.pow(10,rlength))/Math.pow(10,rlength);
             newnumber = newnumber+5000;
         } else {
             var newnumber = Math.round(rnum*Math.pow(10,rlength))/Math.pow(10,rlength);
         }

  document.forms['main'].elements['period'].value=newnumber;
  document.forms['main'].elements['calc'].focus();

 }

$(document).ready(function(){
    $("#slidingDiv").animate({"height": "hide"}, { duration: 100 });
   });
         //<![CDATA[
         function ShowHide(){
         $("#slidingDiv").animate({"height": "toggle"}, { duration: 100 });
          }
         //]]>

</script>


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

<?php
//set default dates
$datepicker1=(!isset($datepicker1))?("01.01.".date("Y")):($datepicker1);
$datepicker2=(!isset($datepicker2))?date("d.m.Y"):($datepicker2);
//format to mysql date
$sdate=substr($datepicker1,6,4)."-".substr($datepicker1,3,2)."-".substr($datepicker1,0,2);
$edate=substr($datepicker2,6,4)."-".substr($datepicker2,3,2)."-".substr($datepicker2,0,2);
//get period
$tmp1= mktime(0, 0, 0, substr($datepicker1,3,2), substr($datepicker1,0,2), substr($datepicker1,6,4));
$tmp2= mktime(0, 0, 0, substr($datepicker2,3,2), substr($datepicker2,0,2), substr($datepicker2,6,4));
$res = ($tmp2 - $tmp1)/ 86400;
$period = round(number_format($res, 0)/364,2);

//get funds
$query="
          select
                   f.fund_id
                  ,f.name name
                  ,c.name company_name
                  ,c.company_id
          from ism_funds f
               ,ism_companies c
               ,ism_fund_year_avg_income tt
          where f.company_id=c.company_id
                and f.fund_id=tt.fund_id
                and f.fund_type!=".$RISK_INVEST_OBJ."
                and tt.check_date=(select max(check_date) from ism_fund_year_avg_income where fund_id=f.fund_id and check_date between '".$sdate."' and '".$edate."')
                and tt.check_date>=DATE_ADD(NOW(), INTERVAL -1 MONTH)
       ";

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
{
 if (a.checked)
 {
 	<?php
 	for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
     {
 	   echo 'var v'.$i.'=document.getElementById("fund_'.$vfunds['fund_id'][$i].'"); v'.$i.'.checked = true;';
 	 }
 	?>
 }
 else
 {
  	<?php
 	for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
     {
 	   echo 'var v'.$i.'=document.getElementById("fund_'.$vfunds['fund_id'][$i].'"); v'.$i.'.checked = false;';
 	 }
 	?>

 }
}
</script>


<div class="mainContent">
<div class="title">Анализ тренда доходности ПИФов <font style="font-weight:normal;">(отметьте интересующие вас фонды)</font></div>

<form name="main" method="post">

<div class="scroll-block">
<table id="Funds" class="tab-table">
  <thead>
  <tr>
    <th><input type="checkbox" id="check_all" name="check_all" onclick="CheckAll(this);" <?php if (isset($check_all)) echo "checked"; ?>></th>
    <th>Фонд</th>
    <th>Управляющая компания</th>
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
	    if  (isset(${'fund_'.$vfunds['fund_id'][$i]}))
	    {
	    	$bgColor='bgcolor="'.$COLOR[$k].'"';
	    	$Checked='checked';
	    	$k++;
	    }
        echo '
	          <tr class="'.$class.'">
			    <td id="td_fund_'.$vfunds['fund_id'][$i].'" '.$bgColor.'><input type="checkbox" id="fund_'.$vfunds['fund_id'][$i].'" name="fund_'.$vfunds['fund_id'][$i].'" '.$Checked.'></td>
			    <td><a href="pif.php?id='.$vfunds['fund_id'][$i].'">'.$vfunds['name'][$i].'</a></td>
       	        <td><a href="company.php?id='.$vfunds['company_id'][$i].'">'.$vfunds['company_name'][$i].'</a></td>
  			  </tr>
  			 ';
	  }
  }
  ?>
 </tbody>
</table>
</div>

<div class="add_info"><strong>Анализируемый период:</strong> <?php echo $datepicker1; ?> - <?php echo $datepicker2; ?><a onclick="ShowHide(); return false;" href="#">Изменить параметры</a></div>

<div class="search-block grey-block" id="slidingDiv">
<ul>
<li>
    <div>Задать период</div>
    <input id="datepicker1" name="datepicker1" value="<?php echo $datepicker1; ?>" onChange="calc_period(this);" />
    <input id="datepicker2" name="datepicker2" value="<?php echo $datepicker2; ?>" onChange="calc_period(this);" />
    <input type="text" name="period" style="width: 15px" disabled value="<?php echo $period; ?>" />&nbsp;лет
</li>
</ul>
</div>


<div class="search-block"><span><input value="Обновить" name="calc" class="button" type="button" onclick="submit();" /></span></div>
</form>

<script type="text/javascript">
$(document).ready(function(){
  // ---- tablesorter -----
  $("#Funds").tablesorter({
	widgets: ["zebra"],
	sortList:[[1,0]],
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

<div class="title">Результаты вычислений</div>
<table class="tab-table" id="FundsResults">
  <thead>
  <tr>
    <th></th>
    <th>Фонд</th>
    <th class="right">СГД, %</th>
    <th class="right">Доход, %</th>
    <th class="right">Волатильность, %</th>
  </tr>
 </thead>
  <tbody>
<?php
//get avg values
$data=get_sgd_volat($tedate);
$avg_year_income=$data['avg_year_income'];
$volat=$data['volat'];

// Index data
$query="
         select
               t.income_year       income_year
              ,t.income_year_avg   income_year_avg
         from ism_index_pifkz t
         where t.check_date=(select max(check_date) from ism_index_pifkz where check_date between '".$sdate."' and '".$edate."'   )
         ";
//echo $query;
$vindex=array();
$rc=sql_stmt($query, 2, $vindex ,1);

echo  '
       	<tr>
    		<td><img src="../media/images/color_index.png" /></td>
    		<td><a href="../im_index.php?type=pifkz">ПИФКЗ</a></td>
    		<td class="right">'.number_format($vindex['income_year_avg'][0], 2, '.', ' ').'</td>
    		<td class="right">'.number_format($vindex['income_year'][0], 2, '.', ' ').'</td>
    		<td class="right">'.$volat.'</td>
  		</tr>
      ';

// selected funds
if ($select_in!="")
{
$query="
        select
               t.fund_id
              ,t.name
              ,t.fund_type
              ,tt.income           income
              ,tt.avg_income       avg_income
              ,tt.avg_volat        avg_volat
         from  ism_funds t
               left join ism_fund_year_avg_income tt on tt.fund_id=t.fund_id and tt.check_date=(select max(check_date) from ism_fund_year_avg_income where fund_id=t.fund_id and check_date between '".$sdate."' and '".$edate."')
         where t.fund_id in (".$select_in.")
         order by t.fund_id
         ";
//echo $query;
$vfunds=array();
$rc=sql_stmt($query, 6, $vfunds ,2);

if ($rc>0)
  {
	  for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
	  {
	    $class=(fmod(($i),2)==0)?('odd'):('even');

	    echo '
	          <tr class="'.$class.'">
			    <td><img src="../media/images/color'.$i.'.png" /></td>
			    <td><a href="pif.php?id='.$vfunds['fund_id'][$i].'">'.$vfunds['name'][$i].'</a></td>
			    <td class="right">'.number_format($vfunds['avg_income'][$i], 2, '.', ' ').'</td>
			    <td class="right">'.number_format($vfunds['income'][$i], 2, '.', ' ').'</td>
			    <td class="right">'.$vfunds['avg_volat'][$i].'</td>
  			  </tr>
  			 ';
	  }
  }


}
?>

  </tbody>
</table>
<script type="text/javascript">
$(document).ready(function(){
  // ---- tablesorter -----
  $("#FundsResults").tablesorter({
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

<img src="analysis_img.php?funds=<?php echo $select_in; ?>&sdate=<?php echo $sdate; ?>&edate=<?php echo $edate; ?>&invest_amount=<?php echo $invest_amount;?>" alt="доходность паевых фондов" />
<br />
<img src="analysis_img2.php?funds=<?php echo $select_in; ?>&sdate=<?php echo $sdate; ?>&edate=<?php echo $edate; ?>&invest_amount=<?php echo $invest_amount;?>" alt="среднегодовая доходность паевых фондов" />

<div class="text"><strong>Анализ тренда доходности ПИФов</strong> поможет Вам сравнить динамику доходности интересующего Вас паевого фонда с динамикой доходности других пифов и среднерыночной динамикой индекса ПИФКЗ. Вы также сможете увидеть тренд доходности паевого фонда и оценить его возможные значения в краткосрочной перспективе, а также качество управления активами фонда.</div>


</div>
<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>