<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Депозиты</title>
<link href="/css/style.min.css" rel="stylesheet" type="text/css" />
<link href="/css/base/jquery.ui.all.css" rel="stylesheet"  type="text/css" />
<script type="text/javascript" src="/scripts/jquery.nyroModal-1.6.2.min.js"></script>
</head>
<body>
<?php
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

$where="";
if ($kzt=="false") $where.=" and d.currency!=0";
if ($usd=="false") $where.=" and d.currency!=1";
if ($eur=="false") $where.=" and d.currency!=2";
$where.=" and d.min_sum <= ".($min_sum*1000)." and d.min_period <= ".$min_period;
if ($banks!="") $where.=" and d.bank_id in (".$banks.")"; else $where.=" and 1=2";

$query="
          select
                 d.deposit_id
                ,d.name
                ,(select name from ism_banks where bank_id=d.bank_id) bank_name
                ,CASE currency WHEN 0 THEN 'KZT' WHEN 1 THEN 'USD' ELSE 'EUR' END currency
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
         from ism_deposits d, ism_deposit_types t
         where d.deposit_id=t.deposit_id
       ".$where."
       order by d.rate_12m desc, d.min_period";

$vdeps=array();
$rc=sql_stmt($query, 15, $vdeps ,2);
?>
<table class="tab-table"  style="font-size:.8em;">
<tr class="colored"><td colspan="3">Всего записей: <?php echo sizeof($vdeps['deposit_id']);?></td></tr>
<?php
for ($i=0;$i<sizeof($vdeps['deposit_id']);$i++)
{
    $class=(fmod(($i),2)==0)?('class="colored"'):('');
	echo '<tr '.$class.'><td style="vertical-align: middle; width:60px; text-align: center;"><span style="font-size: 1.8em; font-weight:bold; color:#00377B;">'.$vdeps['rate_12m'][$i].' %</span><br>ставка 12М<br><a class="nyroModal" rev="modal" href="calculator2.php?income='.$vdeps['rate_12m'][$i].'"><img src="../media/images/calculator.png" height="20px"></a></td><td><a href="deposit.php?id='.$vdeps['deposit_id'][$i].'" target="_blank" style="font-size:1.6em">'.$vdeps['name'][$i].'</a><br><a href="bank.php?id='.$vdeps['bank_id'][$i].'" target="_blank">'.$vdeps['bank_name'][$i].'</a><br>Минимальная сумма: '.$vdeps['min_sum'][$i].' | Минимальный срок: '.$vdeps['min_period'][$i].'М</td><td style="width:120px; vertical-align:middle; text-align:center;"><span style="font-size: 1.6em; color:#00377B;">'.$vdeps['currency'][$i].' </span><br>Обновлено <br>'.$vdeps['last_update'][$i].'</td></tr>';
}
?>

</table>
<?php
//disconnect  from the database
disconn($conn);
?>
</body>
