<?php
/*
display vportfolio statistic
*/

//echo $type;

//get time frame
$query="
            select
                   DATE_FORMAT(min(t.complete_date),'%d.%m.%Y') sdate
                  ,DATE_FORMAT(current_date(),'%d.%m.%Y')    edate
            from ".$tab_name." t
            where  t.user_id=".$user_id."
                   and t.tstatus=".$TSTATUS_COMPLETED."
       ";
$vdate=array();
$rc=sql_stmt($query, 2, $vdate ,1);

if (!isset($sdate)) $sdate=$vdate['sdate'][0];
if (!isset($edate))  $edate=$vdate['edate'][0];
//database date format
$start_date=substr($sdate,6,4)."-".substr($sdate,3,2)."-".substr($sdate,0,2);
$end_date=substr($edate,6,4)."-".substr($edate,3,2)."-".substr($edate,0,2);

// get portfolio's funds ++++++++++++++++++++++++++++++++++++++++++++
$query="
          select
                    buy.fund_id
                   ,f.name  name
                   ,f.company_id
                   ,c.name company_name
          from ism_funds f
               , ism_companies c ,
               (
                 select
                        t.fund_id
                       ,t.user_id
                       ,sum(t.total_sum/(select ifnull(value,0) from ism_fund_value where fund_id=t.fund_id and check_date=t.complete_date)) amount
                       ,sum(t.total_sum) total_sum
                 from  ".$tab_name." t
                 where t.user_id=".$user_id."
                       and t.action=".$ACTION_BUY."
                       and t.tstatus=".$TSTATUS_COMPLETED."
                       and t.complete_date<='".$end_date."'
                group by   t.fund_id ,t.user_id
               ) buy,
               (
                 select
                        tt.fund_id
                       ,tt.user_id
                       ,sum(tt.amount)    amount
                       ,sum(tt.total_sum) total_sum
                from (
                 select
                        t.fund_id
                       ,t.user_id
                       ,sum(t.total_sum/(select ifnull(value,0) from ism_fund_value where fund_id=t.fund_id and check_date=t.complete_date)) amount
                       ,sum(t.total_sum) total_sum
                 from  ".$tab_name." t
                       , ism_fund_value v
                 where t.user_id=".$user_id."
                       and t.action=".$ACTION_SELL."
                       and t.tstatus=".$TSTATUS_COMPLETED."
                       and v.fund_id=t.fund_id
                       and v.check_date = t.complete_date
                       and t.complete_date<='".$end_date."'
                group by   t.fund_id ,t.user_id
                union
                select
                        t.fund_id
                       ,t.user_id
                       ,0
                       ,0
                 from  ".$tab_name." t
                 where t.user_id=".$user_id."
                       and t.action=".$ACTION_BUY."
                       and t.tstatus=".$TSTATUS_COMPLETED."
                       and t.complete_date<='".$end_date."'
                 ) tt
                group by   tt.fund_id ,tt.user_id

               ) sell
          where buy.fund_id=f.fund_id
                and sell.fund_id=f.fund_id
                and f.company_id=c.company_id
                and round(buy.amount-sell.amount,10)!=0
       ";
$vfunds=array();
$rc=sql_stmt($query, 4, $vfunds ,2);


?>
<script type="text/javascript">
$(function(){
$("#sdate").datepicker();
$("#edate").datepicker();
$('#data').dataTable(
				{	"bPaginate": true,
					"bLengthChange": true,
					"bFilter": false,
					"bSort": false,
					"bInfo": true,
					"iDisplayLength":25,
					"bAutoWidth": false }
					);
});
</script>
<form>
<input type="hidden" name="type" value="<?php echo $type; ?>">
<input type="hidden" name="tab_id" value="1">
<input type="hidden" name="ondate" value="<?php echo $ondate; ?>">

<div class="search-block grey-block">
<ul>
    <li>
        <div>Период</div>
        <input id="sdate" name="sdate" value="<?php echo $sdate;?>" />
        <input id="edate" name="edate" value="<?php echo $edate;?>" />
    </li>
</ul>
</div>

<div class="title">Структура портфеля</div>
<div class="scroll-block" style="height:<?php echo sizeof($vfunds['fund_id'])*30+30; ?>px;">
<table id="Funds" class="tab-table">
  <thead>
  <tr>
    <th></th>
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
			    <td><a href="../pif/pif.php?id='.$vfunds['fund_id'][$i].'">'.$vfunds['name'][$i].'</a></td>
       	        <td><a href="../pif/company.php?id='.$vfunds['company_id'][$i].'">'.$vfunds['company_name'][$i].'</a></td>
  			  </tr>
  			 ';
	  }
  }
  ?>
 </tbody>
</table>
</div>
(Консолидация/суммирование выполняется по выбранным фондам.)

<div class="search-block">
    <span><input type="submit" value="Выбрать" class="button"></span>
</div>

</form>

<?php
//get selected funds
$select_in="";
for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
{
  if (isset(${'fund_'.$vfunds['fund_id'][$i]}))  $select_in.=$vfunds['fund_id'][$i].",";
}
$select_in=substr($select_in,0,strlen($select_in)-1);


$funds=$select_in;

// date sequence
$query="
         select
                  adddate( '".$start_date."',id) check_date
         from     tab
         where    adddate('".$start_date."',id)<='".$end_date."'
         order by check_date  desc
       ";



//echo $query;
$vvalue=array();
$rc=sql_stmt($query, 1, $vvalue ,2);

//if at least onr fund selected
if ($funds!='')
{
echo '
        <div class="title">Динамика доходности портфеля</div>
         <!--
         <div class="two-blocks">
           <div class="left-block">
           <img src="statistic_img.php"  alt="" border="0">
           </div>

           <div class="right-block">
           </div>
         </div>
         -->

        <table id="data" class="tab-table">
        <thead>
         <tr>
          <th>'.echoNLS('Дата','').'</th>
          <th>'.echoNLS('Активы, тенге','').'</th>
          <th>'.echoNLS('Инвестиции, тенге','').'</th>
          <th>'.echoNLS('Доход, тенге','').'</th>
        </tr>
        </thead>
        <tbody>
 ';
for ($i=0;$i<sizeof($vvalue['check_date']);$i++)
{
$query="
         select
                 DATE_FORMAT('".$vvalue['check_date'][$i]."','%d.%m.%y') format_check_date
                 ,ifnull((buy.amount-sell.amount)*v.value,-9999)    amount_value
                 ,ifnull((buy.total_sum-sell.total_sum),-9999)      invest
                 ,ifnull((buy.amount-sell.amount)*v.value-(buy.total_sum-sell.total_sum),-9999)  income
         from ism_funds f
              left join ism_fund_value v on v.fund_id=f.fund_id and v.check_date='".$vvalue['check_date'][$i]."',
               (
                 select
                        t.fund_id
                       ,ifnull(sum(t.total_sum/v.value),0) amount
                       ,sum(t.total_sum) total_sum
                 from  ".$tab_name." t
                       left join ism_fund_value v on v.fund_id=t.fund_id and v.check_date=t.complete_date
                 where t.user_id=".$user_id."
                       and t.action=".$ACTION_BUY."
                       and t.tstatus=".$TSTATUS_COMPLETED."
                       and t.complete_date<='".$vvalue['check_date'][$i]."'
                       and t.fund_id in (".$funds.")
                  group by   t.fund_id
               ) buy,
               (
                 select
                        tt.fund_id
                       ,sum(tt.amount)    amount
                       ,sum(tt.total_sum) total_sum
                from (
                 select
                        t.fund_id
                       ,ifnull(sum(t.total_sum/v.value),0)     amount
                       ,sum(t.total_sum) total_sum
                 from  ".$tab_name." t
                       left join ism_fund_value v on v.fund_id=t.fund_id and v.check_date=t.complete_date
                 where t.user_id=".$user_id."
                       and t.action=".$ACTION_SELL."
                       and t.tstatus=".$TSTATUS_COMPLETED."
                       and t.complete_date<='".$vvalue['check_date'][$i]."'
                       and t.fund_id in (".$funds.")
                 group by   t.fund_id
                union
                select
                        t.fund_id
                       ,0
                       ,0
                 from  ".$tab_name." t
                 where t.user_id=".$user_id."
                       and t.action=".$ACTION_BUY."
                       and t.tstatus=".$TSTATUS_COMPLETED."
                       and t.complete_date<='".$vvalue['check_date'][$i]."'
                       and t.fund_id in (".$funds.")
                 ) tt
                group by   tt.fund_id

               ) sell
          where  buy.fund_id=f.fund_id
                 and sell.fund_id=f.fund_id
                 and f.fund_id in (".$funds.")
       ";

//echo $query;
//die();

$vportfolio=array();
$rc=sql_stmt($query, 4, $vportfolio ,2);

if ($rc>0)
{
$str=(fmod(($i+1),2)==0)?("bgColor=#f3f3f3"):("");

if (!in_array(-9999, $vportfolio['amount_value'])) {$amount_value=number_format(array_sum($vportfolio['amount_value']), 2, ',', ' ');}
else {$amount_value='<font color="red">NA</font>';}
if (!in_array(-9999, $vportfolio['invest'])) {$invest=number_format(array_sum($vportfolio['invest']), 0, ',', ' ');}
else {$invest='<font color="red">NA</font>';}
if (!in_array(-9999, $vportfolio['income'])) {$income=number_format(array_sum($vportfolio['income']),2, ',', ' ');}
else {$income='<font color="red">NA</font>';}


echo '
     <tr>
          <td>'.$vportfolio['format_check_date'][0].'</td>
          <td>'.$amount_value.'</td>
          <td>'.$invest.'</td>
          <td>'.$income.'</td>
      </tr>
      ';
}

}

 echo '
        </tbody></table>   <br />
        (Символ "NA" означает, что цена пая/паев на выбранную дату еще недоступна.)
      ';
}
?>



