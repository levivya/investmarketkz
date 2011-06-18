<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Паевые фонды (ПИФы) в Казахстане</title>
<meta name="Description" content="Все паевые фонды Казахстана" >
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
<?php
// filters
$query="

          select
                 -1  fund_id
                ,'все фонды' name
          union
          select
                   fund_id
                  ,name
          from ism_funds
          where fund_type!=".$RISK_INVEST_OBJ."

       ";
$vfunds=array();
$rc=sql_stmt($query, 2, $vfunds ,2);

if (!isset($fund_id))  $fund_id=-1;
$FundsMenuString = menu_list($vfunds['name'],$fund_id,$vfunds['fund_id']);
$FundsMenuString = '<select name="fund_id">'.$FundsMenuString.'</select>';

$query="
          select
                 -1  company_id
                ,'все компании' name
          union
          select
                   company_id
                  ,name
          from ism_companies
          order by name

       ";
$vcomps=array();
$rc=sql_stmt($query, 2, $vcomps ,2);

if (!isset($comp_id))  $comp_id=-1;
$CompsMenuString = menu_list($vcomps['name'],$comp_id,$vcomps['company_id']);
$CompsMenuString = '<select name="comp_id">'.$CompsMenuString.'</select>';

$query="
          select
                 -1   id
                ,'открытые+интервальные' name
          union
          select
                   id
                  ,desc_ru name
          from ism_dictionary
          where grp=".$GRP_TYPE."
                and id!=".$RISK_INVEST_OBJ."
          order by name

       ";
$vtype=array();
$rc=sql_stmt($query, 2, $vtype ,2);

if (!isset($type_id))  $type_id=-1;
$TypeMenuString = menu_list($vtype['name'],$type_id,$vtype['id']);
$TypeMenuString = '<select name="type_id">'.$TypeMenuString.'</select>';



?>
<div class="one-column-block">
<form action="funds.php" method=get>
<div class="search-block">
<ul>
<li>
    <div>Фонд</div>
    <input type="text" name="fund_name" />или
    <?php echo $FundsMenuString; ?>введите фрагмент названия  или выберите из списка
</li>
<li>
     <div>Управляющая компания</div>
     <input type="text" name="company_name" />или
     <?php echo $CompsMenuString; ?>введите фрагмент названия  или выберите из списка
</li>
<li>
      <div>Тип</div>
      <span><?php echo $TypeMenuString;?></span>
</li>
<li><div>Минимальная сумма до</div><input type="text" name="max_sum" />тг.
</li>
<li>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><input value="Найти фонд" class="button" type="submit" /></span>
</li>
</ul>
</div>
</form>
<?php

if (!isset($fund_name)) $fund_name="";
if (!isset($company_name)) $company_name="";
if (!isset($max_sum)) $max_sum=0;


$where="1=1 ";
if ($fund_name!="")      $where.=" and t.name like '%".$fund_name."%'";
if ($fund_id!=-1)        $where.=" and t.fund_id=".$fund_id;
if ($company_name!="")   $where.=" and c.name like '%".$company_name."%'";
if ($comp_id!=-1)        $where.=" and t.company_id=".$comp_id;
if ($type_id!=-1)        $where.=" and t.fund_type=".$type_id;
                    else $where.=" and t.fund_type!=".$RISK_INVEST_OBJ;
if ($max_sum!="")        $where.=" and t.limit_min_sum<=".$max_sum;


$query="
       select
                 t.fund_id
                 ,t.name
                 ,c.name company_name
                 ,t.company_id
                 ,d1.desc_ru status
                 ,d2.desc_ru fund_type
                 ,d3.desc_ru invest_object
                 ,v.check_date
                 ,v.value
                 ,if(v.asset_value=0,'',v.asset_value) asset_value
                 ,if(t.limit_min_sum=0,'',t.limit_min_sum) limit_min_sum
             from ism_funds t
             LEFT JOIN ism_dictionary d1  ON t.status=d1.id
             LEFT JOIN ism_dictionary d2  ON t.fund_type=d2.id
             LEFT JOIN ism_dictionary d3  ON t.invest_object=d3.id
             LEFT JOIN ism_fund_value v  ON t.fund_id=v.fund_id and v.check_date= (select max(check_date) from ism_fund_value where fund_id=t.fund_id )
             LEFT JOIN ism_companies c  ON t.company_id=c.company_id
        where
        ".$where." and t.status!=".$TSTATUS_DELETED;
//echo $query;

$vfunds=array();
$rc=sql_stmt($query, 11, $vfunds ,2);
?>
<div class="title">Действующие паевые фонды (ПИФы) Казахстана</div>
<table class="tab-table top-border" id="Funds">
  <thead>
  <tr>
    <th>Паевой фонд</th>
    <th>Управляющая компания</th>
    <th>Тип</th>
    <th class="right">Мин. сумма</th>
    <th class="right">СЧА</th>
  </tr>
  </thead>

  <tbody>
  <?php
  if ($rc>0)
  {
	  for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
	  {
	    $class=(fmod(($i),2)==0)?('odd'):('even');
        echo '
	          <tr class="'.$class.'">
    			<td><a href="pif.php?id='.$vfunds['fund_id'][$i].'" title="'.$vfunds['name'][$i].'">'.$vfunds['name'][$i].'</a></td>
			    <td><a href="company.php?id='.$vfunds['company_id'][$i].'" title="'.$vfunds['company_name'][$i].'">'.$vfunds['company_name'][$i].'</a></td>
    			<td>'.$vfunds['fund_type'][$i].'</td>
    			<td class="right">'.$str=(($vfunds['limit_min_sum'][$i]!='')?(number_format($vfunds['limit_min_sum'][$i], 2, ',', ' ')):('')).'</td>
    			<td class="right">'.number_format($vfunds['asset_value'][$i], '', ',', ' ').'</td>
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
  $("#Funds").tablesorter({
	widgets: ["zebra"],
	sortList:[[2,1]]
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