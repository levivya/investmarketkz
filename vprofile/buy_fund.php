<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<html>

<head>
  <title>Купить пай</title>
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

//get min values
$query="  select
                   limit_min_sum
                  ,next_min_sum
          from ism_funds
          where fund_id=".$fund_id."
       ";
$vmin=array();
$rc=sql_stmt($query, 2, $vmin ,1);

//get available resource on a virtual account
$query="  select
                   virtual_account
          from ism_customers
          where user_id=".$user_id."
       ";
$vaccount=array();
$rc=sql_stmt($query, 1, $vaccount ,1);


echo '

       <form name="buy_fund">
       <input type="hidden" name="limit_min_sum" value="'.$vmin['limit_min_sum'][0].'">
       <input type="hidden" name="next_min_sum" value="'.$vmin['next_min_sum'][0].'">
       <input type="hidden" name="type" value="virtual">

       <div class="info-message">
          Внимание, приобретение пая осуществляется на дату последнего обновления цены пая.
       </div>

       <div class="search-block">
       <ul>
           <li><div>'.echoNLS('Фонд','').'</div>'.$vfunds['name'][0].'<input type="hidden" name="fund_id" value="'.$fund_id.'"></li>
           <li><div>'.echoNLS('Цена пая','').'</div>'.number_format($vvalue['value'][0], 3, ',', ' ').'&nbsp;(тенге)
                <input type="hidden" name="last_value" value="'.$vvalue['value'][0].'">
                <input type="hidden" name="last_check_date" value="'.$vvalue['check_date'][0].'">
           </li>
           <li><div>'.echoNLS('Дата','').'</div>'.$vvalue['format_check_date'][0].'</li>
           <li><div>'.echoNLS('Доступные средства','').'</div>'.number_format($vaccount['virtual_account'][0], 3, ',', ' ').'&nbsp;(тенге)
               <input type="hidden" name="virtual_account" value="'.$vaccount['virtual_account'][0].'">
           </li>
           <li><div>'.echoNLS('Сумма инвестирования','').'</div><input type="text" name="volume" onblur="IsNumeric(this)">&nbsp;(тенге)</li>
           <li>
           <div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;
                    <span><input type="submit" name="buy" value="'.echoNLS('Купить','').'">
                    <input type="button" value="'.echoNLS('Отмена','').'" class="nyroModalClose" id="closeBut">
                    </span>

           </li>
       </ul>

       </div>
        </form>
      ';


//disconnect from database
disconn($conn);

?>

</body>

</html>
