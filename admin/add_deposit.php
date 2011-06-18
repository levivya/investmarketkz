<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Добавить депозит</title>
<meta name="Description" content="Панель администратора" >
<meta name="Keywords" content="">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
</head>


<body>
<div id="container">
<?php
        //Connecting, selecting database
        $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
  		$selected_menu='main';
		include '../includes/header.php';
?>

<div class="one-column-block">
<div class="title"><a class="more" href="index.php">Панель администратора</a>Добавить депозит</div>

<?php

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

if (isset($add)) //add deposit
{
 if ($name=="")
 {
   echo '<div class="error-message">'.echoNLS('Пропущенна обязательное поле "Название"!','').'</div>';
 }
else
 {

 function process_number($v)
 {
  if ($v=='')  {$v='null';}
  else {$v=str_replace(",", ".",$v);}
  return $v;

 }

 $min_sum=process_number($min_sum);
 $required_min_balance=process_number($required_min_balance);
 $min_period=process_number($min_period);
 $max_period=process_number($max_period);
 $rate_1m=process_number($rate_1m);
 $rate_2m=process_number($rate_2m);
 $rate_3m=process_number($rate_3m);
 $rate_4m=process_number($rate_4m);
 $rate_5m=process_number($rate_5m);
 $rate_6m=process_number($rate_6m);
 $rate_7m=process_number($rate_7m);
 $rate_8m=process_number($rate_8m);
 $rate_9m=process_number($rate_9m);
 $rate_10m=process_number($rate_10m);
 $rate_11m=process_number($rate_11m);
 $rate_12m=process_number($rate_12m);
 $rate_13m=process_number($rate_13m);
 $rate_18m=process_number($rate_18m);
 $rate_24m=process_number($rate_24m);
 $rate_25m=process_number($rate_25m);
 $rate_36m=process_number($rate_36m);
 $rate_37m=process_number($rate_37m);
 $rate_48m=process_number($rate_48m);
 $rate_more_60m=process_number($rate_more_60m);


 $query="
         insert  into ism_deposits(
                                   name
                                  ,bank_id
                                  ,min_sum
                                  ,min_period
                                  ,max_period
                                  ,currency
                                  ,required_min_balance
                                  ,additional_payment
                                  ,money_taking
                                  ,capitalization
                                  ,multicurrency
                                  ,free_card
                                  ,internet_access
                                  ,bonus
                                  ,rate_1m
                                  ,rate_2m
                                  ,rate_3m
                                  ,rate_4m
                                  ,rate_5m
                                  ,rate_6m
                                  ,rate_7m
                                  ,rate_8m
                                  ,rate_9m
                                  ,rate_10m
                                  ,rate_11m
                                  ,rate_12m
                                  ,rate_13m
                                  ,rate_18m
                                  ,rate_24m
                                  ,rate_25m
                                  ,rate_36m
                                  ,rate_37m
                                  ,rate_48m
                                  ,rate_more_60m
                                  ,comments
                                  ,contacts
                                  ,last_update
                                 )
         values('".$name."'
               ,".$bank_id."
               ,".$min_sum."
               ,".$min_period."
               ,".$max_period."
               ,".$currency."
               ,".$required_min_balance."
               ,".$additional_payment."
               ,".$money_taking."
               ,".$capitalization."
               ,".$multicurrency."
               ,".$free_card."
               ,".$internet_access."
               ,'".$bonus."'
               ,".$rate_1m."
               ,".$rate_2m."
               ,".$rate_3m."
               ,".$rate_4m."
               ,".$rate_5m."
               ,".$rate_6m."
               ,".$rate_7m."
               ,".$rate_8m."
               ,".$rate_9m."
               ,".$rate_10m."
               ,".$rate_11m."
               ,".$rate_12m."
               ,".$rate_13m."
               ,".$rate_18m."
               ,".$rate_24m."
               ,".$rate_25m."
               ,".$rate_36m."
               ,".$rate_37m."
               ,".$rate_48m."
               ,".$rate_more_60m."
               ,'".$comments."'
               ,'".$contacts."'
               ,current_date())";

 //echo $query;

 $result=exec_query($query);
 if ($result)
   {

     // insert type
     $query="
          select
                   deposit_id
          from ism_deposits
          where name='".$name."' and bank_id=".$bank_id."
       ";
         $vdep_id=array();
         $rc=sql_stmt($query,1,$vdep ,1);

     for ($j=0;$j<sizeof($dtype);$j++)
     {
        $query=" insert  into ism_deposit_types(deposit_id,type_id) values(".$vdep['deposit_id'][0].",".$dtype[$j].")";
        $result=exec_query($query);
     }

     //update statistics
     $stat_query="
         insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
         values('ism_deposits',".$vdep['deposit_id'][0].",0,current_date(),'".$user."','".$name."')";
      //echo $query;
      $result=exec_query($stat_query);

    //clear values
    $name="";
    $min_sum="";
 	$required_min_balance="";
 	$min_period="";
 	$max_period="";
 	$rate_1m="";
 	$rate_2m="";
 	$rate_3m="";
 	$rate_4m="";
 	$rate_5m="";
 	$rate_6m="";
 	$rate_7m="";
 	$rate_8m="";
 	$rate_10m="";
 	$rate_11m="";
 	$rate_12m="";
 	$rate_13m="";
 	$rate_18m="";
 	$rate_24m="";
 	$rate_25m="";
 	$rate_36m="";
 	$rate_37m="";
 	$rate_48m="";
 	$rate_more_60m="";
    $comments="";
    $contacts="";


    echo '<div class="info-message">'.echoNLS('Данные внесены!','').'</div>';
   }
 }
}



//banks list
$query="
          select
                   bank_id
                  ,name
          from ism_banks
          order by name

       ";
$vbanks=array();
$rc=sql_stmt($query, 2, $vbanks ,2);

if (!isset($bank_id))  {$bank_id=$vbanks['bank_id'][0] ;}
$BanksMenuString = menu_list($vbanks['name'],$bank_id,$vbanks['bank_id']);
$BanksMenuString = '<select name="bank_id">'.$BanksMenuString.'</select>';

$query="
          select
                   id
                  ,desc_ru
          from ism_dictionary
          where grp=".$GRP_DEPOSIT_TYPE."
       ";
$vdtype=array();
$rc=sql_stmt($query, 2, $vdtype ,2);

$DtypeMenuString = '<select name="dtype[]"  multiple>';
for ($i=0;$i<sizeof($vdtype['id']);$i++)
{
  $DtypeMenuString.='<option value="'.$vdtype['id'][$i].'">'.$vdtype['desc_ru'][$i].'</option>';
}
$DtypeMenuString.='</select>';



echo'
                 <form name="add_form" method=post>
                 <table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Название','').'</td>
                                  <td width="50%"><input type=text name=name value="'.$name.'"  style=" font-size: 8pt;">
                    </td>
                    </tr>
                   <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Банк','').'</td>
                                  <td width="50%">'.$BanksMenuString.'</td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Валюта','').'</td>
                                  <td width="50%">
                                      <select name="currency" class="fnt" cols="71">
                                         <option value="0">KZT</option>
                                         <option value="1">USD</option>
                                         <option value="2">EUR</option>
                                      </select>
                                  </td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Минимальная сумма вклада','').'</td>
                                  <td width="50%"><input type=text name=min_sum value="'.$min_sum.'"  style=" font-size: 8pt;"></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Минимальный срок вклада (в месяцах)','').'</td>
                                  <td width="50%"><input type=text name=min_period value="'.$min_period.'"  style=" font-size: 8pt;"></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Максимальный срок вклада (в месяцах)','').'</td>
                                  <td width="50%"><input type=text name=max_period value="'.$max_period.'"  style=" font-size: 8pt;"></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Неснижаемый остаток (в валюте вклада)','').'</td>
                                  <td width="50%"><input type=text name=required_min_balance value="'.$required_min_balance.'"  style=" font-size: 8pt;"></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Капитализация','').'</td>
                                  <td width="50%">
                                   <select name="capitalization" class="fnt" cols="71">
                                         <option value="true">Да</option>
                                         <option value="false">Нет</option>
                                   </select>
                                  </td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Дополнительные взносы','').'</td>
                                  <td width="50%">
                                   <select name="additional_payment" class="fnt" cols="71">
                                         <option value="true">Да</option>
                                         <option value="false">Нет</option>
                                   </select>
                                  </td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Частичные изъятия','').'</td>
                                  <td width="50%">
                                    <select name="money_taking" class="fnt" cols="71">
                                         <option value="true">Да</option>
                                         <option value="false">Нет</option>
                                    </select>
                                  </td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Мультивалютность','').'</td>
                                  <td width="50%">
                                    <select name="multicurrency" class="fnt" cols="71">
                                         <option value="true">Да</option>
                                         <option value="false">Нет</option>
                                    </select>
                                  </td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Бесплатная карточка','').'</td>
                                  <td width="50%">
                                    <select name="free_card" class="fnt" cols="71">
                                         <option value="true">Да</option>
                                         <option value="false">Нет</option>
                                    </select>
                                  </td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Доступ через интернет','').'</td>
                                  <td width="50%">
                                    <select name="internet_access" class="fnt" cols="71">
                                         <option value="true">Да</option>
                                         <option value="false">Нет</option>
                                    </select>
                                  </td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Дополнительные бонусы','').'</td>
                                  <td width="50%"><textarea name=bonus style=" font-size: 8pt;" rows=4 cols=32>'.$bonus.'</textarea></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Примечания','').'</td>
                                  <td width="50%"><textarea name=comments style=" font-size: 8pt;" rows=4 cols=32>'.$comments.'</textarea></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Контакты','').'</td>
                                  <td width="50%"><textarea name=contacts style=" font-size: 8pt;" rows=4 cols=32>'.$contacts.'</textarea></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 1 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_1m value="'.$rate_1m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                   <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 2 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_2m value="'.$rate_2m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                   <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 3 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_3m value="'.$rate_3m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                  <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 4 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_4m value="'.$rate_4m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                  <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 5 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_5m value="'.$rate_5m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                  <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 6 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_6m value="'.$rate_6m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                  <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 7 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_7m value="'.$rate_7m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                  <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 8 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_8m value="'.$rate_8m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                  <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 9 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_9m value="'.$rate_9m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                  <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 10 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_10m value="'.$rate_10m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                  <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 11 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_11m value="'.$rate_11m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                  <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 12 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_12m value="'.$rate_12m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                  <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 13 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_13m value="'.$rate_13m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                   <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 18 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_18m value="'.$rate_18m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 24 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_24m value="'.$rate_24m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 25 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_25m value="'.$rate_25m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 36 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_36m value="'.$rate_36m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                     <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 37 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_37m value="'.$rate_37m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 48 мес','').'</td>
                                  <td width="50%"><input type=text name=rate_48m value="'.$rate_48m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('% на 60 и более мес','').'</td>
                                  <td width="50%"><input type=text name=rate_more_60m value="'.$rate_more_60m.'"  style=" font-size: 8pt;"></td>
                    </tr>
                    <tr bgcolor="white">
                                  <td width="50%">'.echoNLS('Тип','').'</td>
                                  <td width="50%">'.  $DtypeMenuString   .'</td>
                    </tr>

                   <tr bgcolor="white">
                           <td width="50%"></td>
                           <td width="50%">
                           <input type="submit"  name="add" value="'.echoNLS('Добавить','').'"  style=" font-size: 8pt;">
                           <input type="reset"   value="'.echoNLS('Отменить','').'"  style=" font-size: 8pt;">
                           </td>
                       </tr>
                 </table>
                 </form>
                 ';



//delete deposit
if (isset($delete))
{
 if (isset($deposit_id) && $deposit_id!="")
 {

  $deposit_query="
	                 select
	                           name
	                 from ism_deposits
	                 where deposit_id=".$deposit_id;

  $vdep=array();
  $rc=sql_stmt($deposit_query, 1, $vdep ,1);
  $name=$vdep['name'][0];


  $query="
           delete from ism_deposits
           where deposit_id=".$deposit_id."
         ";
  $result=exec_query($query);

  //update statistics
  $stat_query="
         insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
         values('ism_deposits',".$deposit_id.",2,current_date(),'".$user."','".$name."')";
  //echo $query;
  $result=exec_query($stat_query);
  echo '<div class="info-message">'.echoNLS('Депозит удален!','').'</div>';

 }

}

if (isset($grp) && $grp==2)
{

 $query="
          select
                   t.deposit_id
                  ,CONCAT(CONCAT(t.name,' - '),(select name from ism_banks where bank_id=t.bank_id)) name
          from ism_deposits t
          order by t.name

       ";
$vdeps=array();
$rc=sql_stmt($query,2,$vdeps ,2);
if ($rc>0)
{
if (!isset($deposit_id))  {$deposit_id=$vdeps['deposit_id'][0] ;}
$DepMenuString = menu_list($vdeps['name'],$deposit_id,$vdeps['deposit_id']);
$DepMenuString = '<select name="deposit_id">'.$DepMenuString.'</select>';

echo '<form name="delete_form">
      <div class="search-block grey-block">
      <ul>
          <li><div>'.echoNLS('Удалить депозит','').'</div>'.$DepMenuString.'&nbsp;&nbsp;<span><input type="submit"  name="delete" value="'.echoNLS('Удалить','').'"></span></li>
      </ul>
      </div>
      </form>
      ';
}

}
else
{
    echo '<div class="info-message">'.echoNLS('У Вас нет доступа к данной странице!','').'</div>';
}
?>