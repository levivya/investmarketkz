<?php

if (!isset($edate))
{
	$query="select max(check_date) mdate from ism_fund_value where  fund_id=".$id;
	$vmaxdate=array();
	$rc=sql_stmt($query, 1, $vmaxdate , 1);
	$edate=$vmaxdate['mdate'][0];
}

$query_stat="
			  SELECT
					 t.fund_id   id
					,t.name name
					,today.value        value
					,today.asset_value  asset_value
                    ,DATE_FORMAT(today.check_date, '%d.%m.%Y') last_date
                    ,round(((today.value-month.value)/month.value)*100,3) month_value
                    ,round(((today.asset_value-month.asset_value)/month.asset_value)*100,3) month_asset_value
                    ,round(((today.value-6month.value)/6month.value)*100,3) 6month_value
                    ,round(((today.asset_value-6month.asset_value)/6month.asset_value)*100,3) 6month_asset_value
                    ,round(((today.value-year.value)/year.value)*100,3) year_value
                    ,round(((today.asset_value-year.asset_value)/year.asset_value)*100,3) year_asset_value
  		   FROM
			         ism_funds t
			        ,ism_fund_value today
			        ,ism_fund_value month
			        ,ism_fund_value 6month
			        ,ism_fund_value year
			   WHERE  t.fund_id=".$id."
			          AND t.fund_id=today.fund_id
			          AND today.check_date='".$edate."'
			          AND t.fund_id=month.fund_id
			          AND t.fund_id=6month.fund_id
   			          AND t.fund_id=year.fund_id
   			          AND month.check_date=DATE_ADD('".$edate."',INTERVAL -1 MONTH)
   			          AND 6month.check_date=DATE_ADD('".$edate."',INTERVAL -6 MONTH)
   			          AND year.check_date=DATE_ADD('".$edate."',INTERVAL -1 YEAR)
         ";
//echo $query;

$vfund_stat=array();
$rc2=sql_stmt($query_stat, 11, $vfund_stat ,1);
?>
<div class="block1 nopad"><table class="tab-table">
  <tr>
    <th>&nbsp;</th>
    <th><?php echo $vfund_stat['last_date'][0];?></th>
    <th class="right">1 мес.</th>
    <th class="right">6 мес.</th>
    <th class="right">1 год</th>
  </tr>
  <tr>
    <td>Пай</td>
    <td><?php echo number_format($vfund_stat['value'][0], 2, ',', ' ');?></td>
    <td class="right"><?php echo $vfund_stat['month_value'][0];?>%</td>
    <td class="right"><?php echo $vfund_stat['6month_value'][0];?>%</td>
    <td class="right"><?php echo $vfund_stat['year_value'][0];?>%</td>
  </tr>
  <tr class="colored">
    <td>СЧА</td>
    <td><?php echo number_format($vfund_stat['asset_value'][0], 0, ',', ' ');?></td>
    <td class="right"><?php echo $vfund_stat['month_asset_value'][0];?>%</td>
    <td class="right"><?php echo $vfund_stat['6month_asset_value'][0];?>%</td>
    <td class="right"><?php echo $vfund_stat['year_asset_value'][0];?>%</td>
  </tr>
</table></div>