<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");

// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

//deposit types list
$query="select -1  id,'Все' desc_ru union select id,desc_ru from ism_dictionary where grp=".$GRP_DEPOSIT_TYPE;
$vdtypes=array();
$rc=sql_stmt($query, 2, $vdtypes ,2);
if (!isset($dtype))  {$dtype=$vdtypes['id'][1] ;}
$DTypesMenuString = menu_list($vdtypes['desc_ru'],$dtype,$vdtypes['id']);
$DTypesMenuString = '<select name="dtype" class="fnt" cols="71" onchange=submit() >'.$DTypesMenuString.'</select>';

//get deposits
$where_str=($dtype!=-1)?('and t.type_id='.clean_int($dtype)):('');
if (isset($bank_id)) $where_str.=' and d.bank_id='.clean_int($bank_id);
$query="
          select
                 d.deposit_id
                ,d.name
                ,(select name from ism_banks where bank_id=d.bank_id) bank_name
                ,d.bank_id
                ,d.min_period
                ,d.max_period
                ,d.min_sum
                ,d.rate_3m
                ,d.rate_6m
                ,d.rate_12m
                ,d.rate_18m
                ,d.rate_24m
                ,d.rate_more_60m
                ,DATE_FORMAT(d.last_update,'%d.%m.%Y') last_update
                ,UNIX_TIMESTAMP(d.last_update) last_update_nm
         from ism_deposits d, ism_deposit_types t
         where d.deposit_id=t.deposit_id
               ".$where_str."
         ";
$vdeps=array();
$rc=sql_stmt($query, 15, $vdeps ,2);

// Last-Modified
$LastModified_unix = filemtime(__FILE__);
$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
$LastDepositUpdate = max($vdeps['last_update_nm']);
if ($LastDepositUpdate >= $LastModified_unix)  $LastModified = gmdate('D, d M Y H:i:s', $LastDepositUpdate).' GMT';
header('Last-Modified: '. $LastModified);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Депозиты банков</title>
<meta name="Description" content="Депозиты банков Казахстана, депозиты в тенге, депозиты в долларах, депозиты в евро. Рейтинг депозитов по доходности." >
<meta name="Keywords" content="депозиты банков, депозиты банков казахстана, депозиты, вклады, ставка депозита, тип депозита, банки казахстана, минимальный срок, минимальная сумма, доходность">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<?php include '../includes/scripts.php';?>
<script type="text/javascript">
$(document).ready(function(){$("#Deposits").tablesorter({widgets: ["zebra"],sortList:[[6,1]]});});
</script>
</head>
<body>
<div id="container">
<!-- header -->
<?php
    $selected_menu='deposit';
    include '../includes/header.php';
?>
<div class="one-column-block">
<form><div class="search-block"><ul><li><div>Тип депозита</div><?php echo $DTypesMenuString; ?></li></ul></div></form>
<div class="text">В данном разделе собрана актуальная информация о <strong>депозитах</strong>, которые <a href="banks.php" title="банки Казахстана">банки Казахстана</a> предоставляют своим вкладчикам. Вы можете выбрать тип депозита, например, задав валюту вклада и получив результат выборки, отсортировать ее по интересующему параметру. Так выбрав интересующие <strong>депозиты банков Казахстана</strong>, вы можете найти максимальную ставку, на период планируемого вложения. К тому же, минимальная сумма вклада и его минимальный срок, помогут понять сможете ли воспользоваться этим вкладом исходя из ваших условий.</div>
<div class="yashare-auto-init" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>
<h1 class="title">Депозиты банков</h1>
<table class="tab-table" id="Deposits">
  <thead>
  <tr>
    <th title="название вклада">Депозит</th>
    <th title="банк">Банк</th>
    <th class="right" title="минимальная сумма">Сумма</th>
    <th class="right" title="Минимальный срок депозита в мецяцах">Срок</th>
    <th class="right" title="ставка за 3 месяца">3M</th>
    <th class="right" title="ставка за пол года">6M</th>
    <th class="right" title="ставка за год">12M</th>
    <th class="right" title="ставка за 2 года">24M</th>
    <th class="right" title="дата обновления">Дата</th>
  </tr>
  </thead>
  <tbody>
<?php
if ($rc>0)
{for ($i=0;$i<sizeof($vdeps['deposit_id']);$i++)
   {
	    $class=(fmod(($i),2)==0)?('odd'):('even');
        echo '
	          <tr class="'.$class.'">
	          <td width="300px"><a href="deposit.php?id='.$vdeps['deposit_id'][$i].'" target="_blank" title="Депозит '.$vdeps['name'][$i].'">'.$vdeps['name'][$i].'</a></td>
	          <td><a href="bank.php?id='.$vdeps['bank_id'][$i].'" target="_blank" title="'.$vdeps['bank_name'][$i].'">'.$vdeps['bank_name'][$i].'</a></td>
	          <td class="right">'.$str=(($vdeps['min_sum'][$i]!='')?(number_format($vdeps['min_sum'][$i], 0, ',', ' ')):('')).'</td>
	          <td class="right">'.$vdeps['min_period'][$i].'</td>
	          <td class="right">'.($srt=($vdeps['rate_3m'][$i]!='')?($vdeps['rate_3m'][$i]):('')).'</td>
	          <td class="right">'.($srt=($vdeps['rate_6m'][$i]!='')?($vdeps['rate_6m'][$i]):('')).'</td>
	          <td class="right">'.($srt=($vdeps['rate_12m'][$i]!='')?($vdeps['rate_12m'][$i]):('')).'</td>
	          <td class="right">'.($srt=($vdeps['rate_24m'][$i]!='')?($vdeps['rate_24m'][$i]):('')).'</td>
  	          <td class="right">'.$vdeps['last_update'][$i].'</td>
  	          </tr>';
     }
}
?>
 </tbody>
</table>
</div>
<!-- end of main body -->
<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>