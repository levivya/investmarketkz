<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
//get last data update
$query = "select UNIX_TIMESTAMP(max(action_date))  last_update_nm
          from ism_data_statistics
          where table_name='ism_deposits'
             	and data_id=".clean_int($id);

$stat=array();
$rc=sql_stmt($query, 1, $stat ,1);
// Last-Modified
$LastModified_unix = filemtime(__FILE__);
$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
$LastDepositUpdate = $stat['last_update_nm'][0];
if ($LastDepositUpdate >= $LastModified_unix)  $LastModified = gmdate('D, d M Y H:i:s', $LastDepositUpdate).' GMT';
header('Last-Modified: '. $LastModified);



//edit block
if (isset($edit))
{
 function process_number($v)
 {
  if ($v=='')  {$v='null';}
  else {$v=str_replace(",", ".",$v);}
  return $v;

 }

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
         update  ism_deposits
         set  rate_1m=".$rate_1m."
              ,rate_2m=".$rate_2m."
              ,rate_3m=".$rate_3m."
              ,rate_4m=".$rate_4m."
              ,rate_5m=".$rate_5m."
              ,rate_6m=".$rate_6m."
              ,rate_7m=".$rate_7m."
              ,rate_8m=".$rate_8m."
              ,rate_9m=".$rate_9m."
              ,rate_10m=".$rate_10m."
              ,rate_11m=".$rate_11m."
              ,rate_12m=".$rate_12m."
              ,rate_13m=".$rate_13m."
              ,rate_18m=".$rate_18m."
              ,rate_24m=".$rate_24m."
              ,rate_25m=".$rate_25m."
              ,rate_36m=".$rate_36m."
              ,rate_37m=".$rate_37m."
              ,rate_48m=".$rate_48m."
              ,rate_more_60m=".$rate_more_60m."
              ,comments='".$comments."'
              ,contacts='".$contacts."'
              ,last_update=current_date()
         where deposit_id=".$id;


//echo $query;

$result=exec_query($query);
if ($result)
  {
   //update statistics
  $stat_query="
         insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
         values('ism_deposits',".$id.",1,current_date(),'".$user."','".$name."')";
  //echo $query;
  $result=exec_query($stat_query);
  echo '<div class="info-message">'.echoNLS('Данные изменены!','').'</div>';
  }

}


$query="
            select
                 d.deposit_id
                ,d.name
                ,(select name from ism_banks where bank_id=d.bank_id) bank_name
                ,d.bank_id
                ,d.min_sum
                ,d.min_period
                ,d.max_period
                ,d.currency
                ,d.required_min_balance
                ,d.additional_payment
                ,d.money_taking
                ,d.capitalization
                ,d.multicurrency
                ,d.free_card
                ,d.internet_access
                ,d.bonus
                ,d.rate_1m
                ,d.rate_2m
                ,d.rate_3m
                ,d.rate_4m
                ,d.rate_5m
                ,d.rate_6m
                ,d.rate_7m
                ,d.rate_8m
                ,d.rate_9m
                ,d.rate_10m
                ,d.rate_11m
                ,d.rate_12m
                ,d.rate_13m
                ,d.rate_18m
                ,d.rate_24m
                ,d.rate_25m
                ,d.rate_36m
                ,d.rate_37m
                ,d.rate_48m
                ,d.rate_more_60m
                ,d.comments
                ,d.contacts
                ,DATE_FORMAT(d.last_update,'%d.%m.%Y') last_update
         from ism_deposits d
         where d.deposit_id=".clean_int($id);


$vdep=array();
$rc=sql_stmt($query, 39, $vdep ,1);

        //no data exists
        if ($rc==0)
        {
          header('Location: /404.php');
          exit;
        }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Депозит <?php echo $vdep['name'][0]?></title>
<meta name="Description" content="Депозит <?php echo $vdep['name'][0]?>" >
<meta name="Keywords" content="депозит, банк, ставка депозита, срок, выгодные депозиты, <?php echo $vdep['name'][0]?>">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
</head>


<body>

<div id="container">
<!-- header -->
<?php
        $selected_menu='deposit';
        include '../includes/header.php';

        //check for edit rights
		$edit_form=false;
		if (isset($grp) && $grp==2) //if admin
		{
		$edit_form=true;
		}

?>


<!-- main body -->
  <div class="sidebar2">
<?php
 //get deposits
  $query="
           select
                 t.deposit_id
                 ,t.name
                 ,t.rate_12m
           from ism_deposits t
           order by  t.rate_12m desc
           LIMIT 0,5";
  //echo $query;
  $vdeps=array();
  $rc=sql_stmt($query, 3, $vdeps ,2);

  if ($rc>0)
  {
  	 echo '<div class="title" title="Выгодные депозиты">Выгодные депозиты</div><ul class="list">';
  	 for ($i=0;$i<sizeof($vdeps['deposit_id']);$i++)
     {
     	echo '<li><a href="deposit.php?id='.$vdeps['deposit_id'][$i].'" title="'.$vdeps['name'][$i].'">'.$vdeps['name'][$i].'</a> - '.$vdeps['rate_12m'][$i].'%</li>';
     }
  	 echo '</ul>';
  }

?>
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="../banner.php" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
     Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
    <!-- end sidebar2 -->
  </div>

<div class="mainContent">
<div class="title">Депозит <?php echo $vdep['name'][0]?><a href="deposits.php" class="more">Все депозиты банков</a></div>
<?php
if (!$edit_form)
 {
  switch ($vdep['currency'][0]) {
    case 0:
        $curr="KZT";
        break;
    case 1:
        $curr="USD";
        break;
    case 2:
        $curr="EUR";
        break;
        }

  $rate_str=($vdep['rate_12m'][0]!='')?('<span class="green">'.$vdep['rate_12m'][0].' %</span>'):('');

  echo '
         <div id="fund-container">
          <div class="left-block" style="width:500px">
               <div class="mid">'.$vdep['name'][0].'</div>
               <div class="small">Банк: <a href="bank.php?id='.$vdep['bank_id'][0].'" title="'.$vdep['bank_name'][0].'">'.$vdep['bank_name'][0].'</a></div>
               <div class="small">Дата обновления: '.$vdep['last_update'][0].'</div>
          </div>
          <div class="right-block" style="width:150px">
               <div class="mid">'.$rate_str.'<a class="nyroModal" rev="modal" href="calculator2.php?income='.$vdep['rate_12m'][0].'" title="расчет доходности вклада"><img src="../media/images/calculator.png" height="20px" alt="расчет доходности вклада" border="0"></a></div>
               <div class="small">Валюта: '.$curr.'</div>
               <div class="small">Мин. сумма: '.number_format($vdep['min_sum'][0], 0, ',', ' ').' '.$curr.'</div>
               <div class="small">Мин. срок: '.$vdep['min_period'][0].'М</div>
          </div>
         </div>
        ';

        $thead="<td><b>Срок</b></td>";
        $tdata="<td><b>Ставка депозита</b></td>";

        if ($vdep['rate_1m'][0]!="")
        {        	$thead.="<td>1M</td>";
        	$tdata.="<td>".$vdep['rate_1m'][0]."%</td>";        }
        if ($vdep['rate_2m'][0]!="")
        {
        	$thead.="<td>2M</td>";
        	$tdata.="<td>".$vdep['rate_2m'][0]."%</td>";
        }
        if ($vdep['rate_3m'][0]!="")
        {
        	$thead.="<td>3M</td>";
        	$tdata.="<td>".$vdep['rate_3m'][0]."%</td>";
        }
        if ($vdep['rate_4m'][0]!="")
        {
        	$thead.="<td>4M</td>";
        	$tdata.="<td>".$vdep['rate_4m'][0]."%</td>";
        }
        if ($vdep['rate_5m'][0]!="")
        {
        	$thead.="<td>5M</td>";
        	$tdata.="<td>".$vdep['rate_5m'][0]."%</td>";
        }
        if ($vdep['rate_6m'][0]!="")
        {
        	$thead.="<td>6M</td>";
        	$tdata.="<td>".$vdep['rate_6m'][0]."%</td>";
        }
        if ($vdep['rate_7m'][0]!="")
        {
        	$thead.="<td>7M</td>";
        	$tdata.="<td>".$vdep['rate_7m'][0]."%</td>";
        }
        if ($vdep['rate_8m'][0]!="")
        {
        	$thead.="<td>8M</td>";
        	$tdata.="<td>".$vdep['rate_8m'][0]."%</td>";
        }
        if ($vdep['rate_9m'][0]!="")
        {
        	$thead.="<td>9M</td>";
        	$tdata.="<td>".$vdep['rate_9m'][0]."%</td>";
        }
        if ($vdep['rate_10m'][0]!="")
        {
        	$thead.="<td>10M</td>";
        	$tdata.="<td>".$vdep['rate_10m'][0]."%</td>";
        }
        if ($vdep['rate_11m'][0]!="")
        {
        	$thead.="<td>11M</td>";
        	$tdata.="<td>".$vdep['rate_11m'][0]."%</td>";
        }
        if ($vdep['rate_12m'][0]!="")
        {
        	$thead.="<td>12M</td>";
        	$tdata.="<td>".$vdep['rate_12m'][0]."%</td>";
        }
        if ($vdep['rate_13m'][0]!="")
        {
        	$thead.="<td>13M</td>";
        	$tdata.="<td>".$vdep['rate_13m'][0]."%</td>";
        }
        if ($vdep['rate_18m'][0]!="")
        {
        	$thead.="<td>18M</td>";
        	$tdata.="<td>".$vdep['rate_18m'][0]."%</td>";
        }
        if ($vdep['rate_24m'][0]!="")
        {
        	$thead.="<td>24M</td>";
        	$tdata.="<td>".$vdep['rate_24m'][0]."%</td>";
        }
        if ($vdep['rate_25m'][0]!="")
        {
        	$thead.="<td>25M</td>";
        	$tdata.="<td>".$vdep['rate_25m'][0]."%</td>";
        }
        if ($vdep['rate_36m'][0]!="")
        {
        	$thead.="<td>36M</td>";
        	$tdata.="<td>".$vdep['rate_36m'][0]."%</td>";
        }
        if ($vdep['rate_37m'][0]!="")
        {
        	$thead.="<td>37M</td>";
        	$tdata.="<td>".$vdep['rate_37m'][0]."%</td>";
        }
        if ($vdep['rate_48m'][0]!="")
        {
        	$thead.="<td>48M</td>";
        	$tdata.="<td>".$vdep['rate_48m'][0]."%</td>";
        }
        if ($vdep['rate_more_60m'][0]!="")
        {
        	$thead.="<td>Более 60М</td>";
        	$tdata.="<td>".$vdep['rate_more_60m'][0]."%</td>";
        }

        echo '
              <div class="scroll-block-horiz">
              <table class="tab-table">
                <tr>'.$thead.'</tr>
                <tr>'.$tdata.'</tr>
              </table>
              </div>
             ';
       echo '
         <script type="text/javascript">
          //<![CDATA[
          function ShowHide(){
          $("#slidingDiv").animate({"height": "toggle"}, { duration: 100 });
           }
          //]]>
          </script>

       <div class="add_info"><a onclick="ShowHide(); return false;" href="#">Дополнительная информация</a></div>
       <div id="slidingDiv">

         <table class="tab-table top-border">
             <tr class="colored">
                           <td width="40%">'.echoNLS('Максимальный срок вклада','').'</td>
                           <td width="60%">'.$vdep['max_period'][0].'  месяцев</td>
             </tr>
             <tr>
                           <td >'.echoNLS('Неснижаемый остаток','').'</td>
                           <td >'.$vdep['required_min_balance'][0].' '.$curr.'</td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('Капитализация','').'</td>
                           <td >'.($str=($vdep['capitalization'][0]==true)?('Да'):('Нет')).'
                           </td>
             </tr>
             <tr>
                           <td >'.echoNLS('Дополнительные взносы','').'</td>
                           <td >'.($str=($vdep['additional_payment'][0]==true)?('Да'):('Нет')).'
                           </td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('Частичные изъятия','').'</td>
                           <td >'.($str=($vdep['money_taking'][0]==true)?('Да'):('Нет')).'
                           </td>
             </tr>
             <tr>
                           <td >'.echoNLS('Мультивалютность','').'</td>
                           <td >'.($str=($vdep['multicurrency'][0]==true)?('Да'):('Нет')).'
                           </td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('Бесплатная карточка','').'</td>
                           <td >'.($str=($vdep['free_card'][0]==true)?('Да'):('Нет')).'
                           </td>
             </tr>
             <tr>
                           <td >'.echoNLS('Доступ через интернет','').'</td>
                           <td >'.($str=($vdep['internet_access'][0]==true)?('Да'):('Нет')).'
                           </td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('Дополнительные бонусы','').'</td>
                           <td >'.$vdep['bonus'][0].'</td>
             </tr>
             <tr>
                           <td >'.echoNLS('Примечания','').'</td>
                           <td >'.$vdep['comments'][0].'</td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('Контакты','').'</td>
                           <td >'.$vdep['contacts'][0].'</td>
             </tr>
             </table>
          </div>
      ';

      echo '
      <script type="text/javascript"><!--
		google_ad_client = "pub-2712511792023009";
		/* 728x90, создано 24.09.10 */
		google_ad_slot = "7735537292";
		google_ad_width = 728;
		google_ad_height = 90;
		//-->
	  </script>

      <script type="text/javascript"
      src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
      </script>

           ';

  }
  else
  {
  echo '
        <form name="edit_form" method=post>
        <input type=hidden name=id value="'.$id.'">


         <table class="tab-table">
		  <tr class="colored">
		    <td>'.echoNLS('Название','').'</td>
		    <td>'.$vdep['name'][0].'</td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Банк','').'</td>
            <td><a href="bank.php?id='.$vdep['bank_id'][0].'">'.$vdep['bank_name'][0].'</a></td>
	      </tr>
          <tr>
                           <td >'.echoNLS('Валюта','').'</td>
                           <td >
                               <select name="currency" disabled>
                                  <option value="0" '.($str=($vdep['currency'][0]==0)?('SELECTED'):('')).'>KZT</option>
                                  <option value="1" '.($str=($vdep['currency'][0]==1)?('SELECTED'):('')).'>USD</option>
                                  <option value="2" '.($str=($vdep['currency'][0]==2)?('SELECTED'):('')).'>EUR</option>
                               </select>
                           </td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('Минимальная сумма вклада','').'</td>
                           <td >'.$vdep['min_sum'][0].'</td>
             </tr>
             <tr>
                           <td >'.echoNLS('Минимальный срок вклада (в месяцах)','').'</td>
                           <td >'.$vdep['min_period'][0].'</td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('Максимальный срок вклада (в месяцах)','').'</td>
                           <td >'.$vdep['max_period'][0].'</td>
             </tr>
             <tr>
                           <td >'.echoNLS('Неснижаемый остаток (в валюте вклада)','').'</td>
                           <td >'.$vdep['required_min_balance'][0].'</td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('Капитализация','').'</td>
                           <td >'.($str=($vdep['capitalization'][0]==true)?('Да'):('Нет')).'
                           </td>
             </tr>
             <tr>
                           <td >'.echoNLS('Дополнительные взносы','').'</td>
                           <td >'.($str=($vdep['additional_payment'][0]==true)?('Да'):('Нет')).'
                           </td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('Частичные изъятия','').'</td>
                           <td >'.($str=($vdep['money_taking'][0]==true)?('Да'):('Нет')).'
                           </td>
             </tr>
             <tr>
                           <td >'.echoNLS('Мультивалютность','').'</td>
                           <td >'.($str=($vdep['multicurrency'][0]==true)?('Да'):('Нет')).'
                           </td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('Бесплатная карточка','').'</td>
                           <td >'.($str=($vdep['free_card'][0]==true)?('Да'):('Нет')).'
                           </td>
             </tr>
             <tr>
                           <td >'.echoNLS('Доступ через интернет','').'</td>
                           <td >'.($str=($vdep['internet_access'][0]==true)?('Да'):('Нет')).'
                           </td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('Дополнительные бонусы','').'</td>
                           <td >'.$vdep['bonus'][0].'</td>
             </tr>
             <tr>
                           <td >'.echoNLS('Примечания','').'</td>
                           <td >'.$vdep['comments'][0].'</td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('Контакты','').'</td>
                           <td >'.$vdep['contacts'][0].'</td>
             </tr>
             </table>

            <br />
            <div class="title">'.echoNLS('Ставки','').'</div>

           <table class="tab-table">

             <tr class="colored">
                           <td >'.echoNLS('% на 1 мес','').'</td>
                           <td ><input type=text name=rate_1m value="'.$vdep['rate_1m'][0].'"></td>
             </tr>
            <tr>
                           <td >'.echoNLS('% на 2 мес','').'</td>
                           <td ><input type=text name=rate_2m value="'.$vdep['rate_2m'][0].'"></td>
             </tr>
            <tr class="colored">
                           <td >'.echoNLS('% на 3 мес','').'</td>
                           <td ><input type=text name=rate_3m value="'.$vdep['rate_3m'][0].'"></td>
             </tr>
           <tr>
                           <td >'.echoNLS('% на 4 мес','').'</td>
                           <td ><input type=text name=rate_4m value="'.$vdep['rate_4m'][0].'"></td>
             </tr>
           <tr class="colored">
                           <td >'.echoNLS('% на 5 мес','').'</td>
                           <td ><input type=text name=rate_5m value="'.$vdep['rate_5m'][0].'"></td>
             </tr>
           <tr>
                           <td >'.echoNLS('% на 6 мес','').'</td>
                           <td ><input type=text name=rate_6m value="'.$vdep['rate_6m'][0].'"></td>
             </tr>
           <tr class="colored">
                           <td >'.echoNLS('% на 7 мес','').'</td>
                           <td ><input type=text name=rate_7m value="'.$vdep['rate_7m'][0].'"></td>
             </tr>
           <tr>
                           <td >'.echoNLS('% на 8 мес','').'</td>
                           <td ><input type=text name=rate_8m value="'.$vdep['rate_8m'][0].'"></td>
             </tr>
           <tr class="colored">
                           <td >'.echoNLS('% на 9 мес','').'</td>
                           <td ><input type=text name=rate_9m value="'.$vdep['rate_9m'][0].'"></td>
             </tr>
           <tr>
                           <td >'.echoNLS('% на 10 мес','').'</td>
                           <td ><input type=text name=rate_10m value="'.$vdep['rate_10m'][0].'"></td>
             </tr>
           <tr class="colored">
                           <td >'.echoNLS('% на 11 мес','').'</td>
                           <td ><input type=text name=rate_11m value="'.$vdep['rate_11m'][0].'"></td>
             </tr>
           <tr>
                           <td >'.echoNLS('% на 12 мес','').'</td>
                           <td ><input type=text name=rate_12m value="'.$vdep['rate_12m'][0].'"></td>
             </tr>
           <tr class="colored">
                           <td >'.echoNLS('% на 13 мес','').'</td>
                           <td ><input type=text name=rate_13m value="'.$vdep['rate_13m'][0].'"></td>
             </tr>
            <tr>
                           <td >'.echoNLS('% на 18 мес','').'</td>
                           <td ><input type=text name=rate_18m value="'.$vdep['rate_18m'][0].'"></td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('% на 24 мес','').'</td>
                           <td ><input type=text name=rate_24m value="'.$vdep['rate_24m'][0].'"></td>
             </tr>
             <tr>
                           <td >'.echoNLS('% на 25 мес','').'</td>
                           <td ><input type=text name=rate_25m value="'.$vdep['rate_25m'][0].'"></td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('% на 36 мес','').'</td>
                           <td ><input type=text name=rate_36m value="'.$vdep['rate_36m'][0].'"></td>
             </tr>
              <tr>
                           <td >'.echoNLS('% на 37 мес','').'</td>
                           <td ><input type=text name=rate_37m value="'.$vdep['rate_37m'][0].'"></td>
             </tr>
             <tr class="colored">
                           <td >'.echoNLS('% на 48 мес','').'</td>
                           <td ><input type=text name=rate_48m value="'.$vdep['rate_48m'][0].'"></td>
             </tr>
             <tr>
                           <td >'.echoNLS('% на 60 и более мес','').'</td>
                           <td ><input type=text name=rate_60m value="'.$vdep['rate_60m'][0].'"></td>
             </tr>
              <tr class="colored">
                           <td >'.echoNLS('Дата обновления','').'</td>
                           <td >'.$vdep['last_update'][0].'</td>
             </tr>

	      </table>

     <div class="search-block">
     <span>
         <input type="submit"  name="edit" value="'.echoNLS('Изменить','').'">
         <input type="reset"   value="'.echoNLS('Отменить','').'">
     </span>
     </div>
     </form>

      ';
  }
?>


</div>
<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>