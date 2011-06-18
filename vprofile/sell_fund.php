<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<html>

<head>
  <title>Продать пай</title>
  <link type="text/css" href="../css/style.css" rel=stylesheet  />
  <meta name="Keywords" content="v-счет">
  <meta name="copyright" content="Invest-Market.kz">
  <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
  <meta HTTP-EQUIV="pragma" CONTENT="no-cache">

</head>

<body style="background:#fff;" marginright="2" marginheight="2" leftmargin="2" topmargin="2" marginwidth="2">


<?php
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

echo ' <script language="JavaScript">
function IsNumeric(obj)
{
   var ValidChars = "0123456789.";
   var IsNumber=true;
   var Char;
   var sText=obj.value;

   for (i = 0; i < sText.length && IsNumber == true; i++)
      {
       Char = sText.charAt(i);
       if (ValidChars.indexOf(Char) == -1)
          {
          alert("'.echoNLS('Неверное значение, оно должно быть числовым!','Wrong value, It should be numeric!').'");
          IsNumber = false;
          obj.focus();
          }
     }
   return IsNumber;
}
     </script>';


//get fund name
$query="  select
                   name
          from ism_funds
          where fund_id=".$fund_id;

$vfunds=array();
$rc=sql_stmt($query, 1, $vfunds ,1);


//get last value
$query="  select
                   value
                  ,check_date
                  ,DATE_FORMAT(check_date,'%d.%m.%Y') format_check_date
          from ism_fund_value
          where fund_id=".$fund_id."
          order by check_date desc
          LIMIT 0,1
       ";
$vvalue=array();
$rc=sql_stmt($query, 3, $vvalue ,1);
$day=$vvalue['check_date'][0];

$query="
          select
                    round(buy.amount-sell.amount,10)     amount
                   ,l.value
                   ,l.check_date
                   ,DATE_FORMAT(l.check_date,'%d.%m.%Y') format_check_date
          from
               ism_funds f
               left join ism_fund_value l on l.fund_id=f.fund_id and l.check_date='".$day."'
               ,
               (
                 select
                        t.fund_id
                        ,sum(t.total_sum/(select ifnull(value,0) from ism_fund_value where fund_id=t.fund_id and check_date=t.complete_date)) amount
                 from  ism_transactions_virtual t
                 where t.user_id=".$user_id."
                       and t.fund_id=".$fund_id."
                       and t.action=".$ACTION_BUY."
                       and t.tstatus=".$TSTATUS_COMPLETED."
                       and t.complete_date<='".$day."'
                 group by t.fund_id
               ) buy,
               (
                 select
                       tt.fund_id
                       ,sum(tt.amount)    amount
                 from (
                 select
                       t.fund_id
                       ,sum(t.total_sum/(select ifnull(value,0) from ism_fund_value where fund_id=t.fund_id and check_date=t.complete_date)) amount
                 from  ism_transactions_virtual t
                 where t.user_id=".$user_id."
                       and t.fund_id=".$fund_id."
                       and t.action=".$ACTION_SELL."
                       and t.tstatus=".$TSTATUS_COMPLETED."
                       and t.complete_date<='".$day."'
                  group by t.fund_id
                union
                select
                       t.fund_id
                       ,0
                 from  ism_transactions_virtual t
                 where t.user_id=".$user_id."
                       and t.fund_id=".$fund_id."
                       and t.action=".$ACTION_BUY."
                       and t.tstatus=".$TSTATUS_COMPLETED."
                       and t.complete_date<='".$day."'
                group by t.fund_id
                 ) tt
                 group by tt.fund_id
               ) sell
         where buy.fund_id=f.fund_id
                and sell.fund_id=f.fund_id
                and round(buy.amount-sell.amount,10)!=0
       ";



//echo $query;
//die();
$vportfolio=array();
$rc=sql_stmt($query, 4, $vportfolio ,2);

if ($rc>0)
{
$pais=$vportfolio['amount'][0];
echo '
       <div class="info-message">Внимание, погашение пая осуществляется на дату последнего обновления цены пая в системе.</div>

       <form name="sell_fund">
       <input type="hidden" name="type" value="virtual">
      <div class="search-block">
       <ul>
            <li><div>'.echoNLS('Фонд','').'</div>'.$vfunds['name'][0].'<input type="hidden" name="fund_id" value="'.$fund_id.'"></li>
            <li><div>'.echoNLS('Цена пая','').'</div>
                '.number_format($vportfolio['value'][0], 3, ',', ' ').'&nbsp;(тенге)
                <input type="hidden" name="last_value" value="'.$vportfolio['value'][0].'">
                <input type="hidden" name="last_check_date" value="'.$vportfolio['check_date'][0].'">
                <input type="hidden" name="selected_day" value="'.$selected_day.'">
           </li>
           <li><div>'.echoNLS('Дата','').'</div>'.$vportfolio['format_check_date'][0].'</li>
           <li><div>'.echoNLS('Паев в наличии','').'</div>
               '.$pais.'
               <input type="hidden" name="pais" value="'.$pais.'">
           </li>
           <li><div>'.echoNLS('Продать паев','').'</div><input name="sell_pais" value="'.$pais.'"></li>
           <li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;
           <span>
           <input type="submit" name="sell" value="'.echoNLS('Продать','').'">
           <input type="button" value="'.echoNLS('Отмена','').'" class="nyroModalClose" id="closeBut">
           </span></li>
        </ul>
        </form>
      ';
}
//disconnect from database
disconn($conn);

?>
</body>

</html>

