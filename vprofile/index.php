<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Портфель инвестора</title>
<meta name="Description" content="Портфель инвестора" >
<meta name="Keywords" content="">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
</head>
<body>
<div id="container">
<?php
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
$selected_menu='main';
include '../includes/header.php';

if (!isset($type)) $type='';
$dinamic_block='';
$tab_name='ism_transactions';

/*+++++++++++++++ Buy Fund +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
//buy funds
if (isset($buy))
{

   $correct=true;

   //echo "virtual_account=".$virtual_account;

   //check we have enought money
   if ($volume>$virtual_account)
   {
         $correct=false;
         $err_mess=echoNLS('Сумма инвестирования превышает остаток на виртуальном счету!','');
   }

   //check is this the first buying time
   $query=" select
                  count(fund_id) number_buyings
            from  ism_transactions_virtual
            where user_id=".$user_id."
                  and fund_id=".$fund_id."
          ";
        $v=array();
        $rc=sql_stmt($query, 1, $v, 1);

    //if the first time than must be more limit_min_sum
    if ($v['number_buyings'][0] == 0 && $volume<$limit_min_sum && $correct)
    {
         $correct=false;
         $err_mess=echoNLS('При первой покупке, сумма инвестирования должна быть не меньше минимальной суммы (первый взнос)!','');
    }

    //if the second time than must be more next_min_sum
    if ($v['number_buyings'][0] > 0 && $volume<$next_min_sum && $correct)
    {
         $correct=false;
         $err_mess=echoNLS('При вторичной покупке, сумма инвестирования должна быть не меньше минимальной суммы (послед. взнос)!','');
    }

     if  ($correct)
     {
       $query="
               insert into ism_transactions_virtual(user_id,fund_id,total_sum,action,tstatus,request_date,complete_date)
               values (".$user_id.",".$fund_id.",".$volume.",".$ACTION_BUY.",".$TSTATUS_COMPLETED.",'".$last_check_date."','".$last_check_date."')";

        //echo $query;
        //die();
        $result=exec_query($query);

        $query="
               update ism_customers
               set virtual_account=".($virtual_account-$volume)."
               where user_id=".$user_id;

        //echo $query;
        //die();
        $result=exec_query($query);

        echo '<div class="info-message">'.echoNLS('Вы успешно осуществили виртуальную покупку!','').'</div>';
     }
     else
     {       echo '<div class="error-message">'.$err_mess.'</div>';
     }

}

/*+++++++ END Buy Fund ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

/*+++++++ Sell Fund ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
if (isset($sell))
{

   $correct=true;

   //check we have enought money
   if ($sell_pais>$pais)
   {
         $correct=false;
         $err_mess=echoNLS('Количество паев в портфеле должно быть не меньше количества продаваемых паев!','');
   }

   if  ($correct)
     {

       $volume=round($sell_pais*$last_value,10);

       $query="
               insert into ism_transactions_virtual(user_id,fund_id,total_sum,action,tstatus,request_date,complete_date)
               values (".$user_id.",".$fund_id.",".$volume.",".$ACTION_SELL.",".$TSTATUS_COMPLETED.",'".$last_check_date."','".$last_check_date."')";

        //echo $query;
        //die();
        $result=exec_query($query);

        $query="
               update ism_customers
               set virtual_account=virtual_account+".$volume."
               where user_id=".$user_id;

        //echo $query;
        //die();
        $result=exec_query($query);

        echo '<div class="info-message">'.echoNLS('Вы успешно продали виртуальные паи!','').'</div>';
     }
    else
     {
       echo '<div class="error-message">'.$err_mess.'</div>';
     }


}

/*+++++++ END Sell Fund ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/


if ($type == 'virtual')
{
  $tab_name='ism_transactions_virtual';
  $query="select virtual_account from ism_customers where user_id=".$user_id;
  $vaccount=array();
  $rc=sql_stmt($query, 1, $vaccount ,1);
  $dinamic_block.='<div class="title">Денежный счет (V-счёт)</div>';
  $dinamic_block.='<div class="grey-block"><ul class="list2"><li>Доступно:&nbsp;<strong>'.number_format($vaccount['virtual_account'][0], 2, ',', ' ').'</strong>&nbsp;тенге</li></ul></div>';
}




if (!isset($ondate))
{
//get last date
$query="
        select DATE_FORMAT(max(check_date),'%d.%m.%Y') last_date
        from   ism_fund_value
        where  fund_id in
                    (
                      select distinct(fund_id)
                      from ism_transactions_virtual
                      where user_id=".$user_id."
                            and  action=".$ACTION_BUY."
                    )
       ";
$last_date=array();
$rc=sql_stmt($query, 1, $last_date,2);
$ondate=$last_date['last_date'][0];
}

//format date
$day=substr($ondate,6,4)."-".substr($ondate,3,2)."-".substr($ondate,0,2);

$query="
          select
                   buy.fund_id
                  ,buy.user_id
                  ,f.name
                  ,buy.amount-sell.amount     amount
                  ,ifnull(l.value,-9999)    value
                  ,ifnull((buy.amount-sell.amount)*l.value,-9999)  amount_value
                  ,ifnull((buy.amount-sell.amount)*l.value-(buy.total_sum-sell.total_sum),-9999)  income
                  ,ifnull((buy.amount-sell.amount)*l.value-(buy.total_sum-sell.total_sum),0)  income_number
                  ,ifnull((buy.amount-sell.amount)*l.value,0)  amount_value_number
          from ism_funds f
               left join ism_fund_value l on l.fund_id=f.fund_id and l.check_date='".$day."'
               ,
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
                       and t.complete_date<='".$day."'
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
                       and t.complete_date<='".$day."'
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
                       and t.complete_date<='".$day."'
                 ) tt
                group by   tt.fund_id ,tt.user_id

               ) sell
          where buy.fund_id=f.fund_id
                and sell.fund_id=f.fund_id
                and round(buy.amount-sell.amount,10)!=0
       ";

//echo $query;
$vportfolio=array();
$rc1=sql_stmt($query, 9, $vportfolio ,2);

$funds_name=implode(",", $vportfolio['name']);
$funds_amount=implode(",", $vportfolio['amount_value']);

$fh = fopen('../amcharts/ampie/ampie_data.xml', 'w') or die("can't open file");
fwrite($fh, '<?xml version="1.0" encoding="UTF-8"?><pie>');

for ($i=0;$i<sizeof($vportfolio['name']);$i++)
		 {
    		 fwrite($fh, '<slice title="'.$vportfolio['name'][$i].'" pull_out="false">'.$vportfolio['amount_value'][$i].'</slice>');
    		 //echo   '<slice title="'.$vportfolio['name'][$i].'" pull_out="false">'.$vportfolio['amount_value'][$i].'</slice>';
		 }

fwrite($fh, '</pie>');
fclose($fh);


// set default tab_id
$tab_id=(isset($tab_id))?($tab_id):(0);

//edit personal data
//edit block
if (isset($edit))
{
$subscription=(isset($subscription) || $subscription=='on')?(1):(0);
$query="
        update  ism_users
             set  subscription=".$subscription."
        where user_id=".$user_id."
       ";
//echo $query;
$result=exec_query($query);
$planned_monthly_investment=($planned_monthly_investment=='')?(0):($planned_monthly_investment);
$query="
        update  ism_customers
             set  first_name='".$first_name."',last_name='".$last_name."',planned_monthly_investment=".$planned_monthly_investment."
        where user_id=".$user_id."
       ";
//echo $query;
$result=exec_query($query);
if ($result){echo '<div class="info-message">'.echoNLS('Данные изменены!','').'</div>';}
}


//change password +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if (isset($change))
{
  $correct=true;
  $err_mess="";

  //check both passowrds are simillar
  if ($passwd_new!=$passwd_new_conf)
        {
         $err_mess.=echoNLS('Потверждение пароля введено неверно!','');
         $correct=false;
        }
   //check length
  /*
  if (CheckPasswordStrength($passwd_new)==0 && $correct)
       {
         $err_mess.=echoNLS('Минимальная длинна пароля, должна быть не меньее 8 символов!','');
         $correct=false;
        }
   */

   //check for the old password
   $query = "select
                     password
         	from ism_users u
         	where user_name='".$user."'";

  //echo $query;
  $vars=array();
  $rc=sql_stmt($query, 1, $vars ,1);

  if (crypt($passwd, $vars['password'][0])!= $vars['password'][0])
        {
         $err_mess.=echoNLS('Неверный старый пароль!','');
         $correct=false;
        }


  // if everything correct - reset password and activate user
  if ($correct)
  {
     $crypt_password=crypt($passwd_new);
     $query = "
                update ism_users u
         	    set password='".$crypt_password."'
         	    where user_name='".$user."'";
     //echo $query;
     //die();
     $result=exec_query($query);

     if ($result) { echo '<div class="info-message">'.echoNLS('Пароль успешно сменен!','').'</div>';}
  }
  else
  {  	echo '<div class="error-message">'.$err_mess.'</div>';  }
}
//delete account +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if (isset($delete_account))
{
$query="
        delete from ism_users
        where user_id=".$user_id."
       ";
//echo $query;
$result=exec_query($query);

$query="
        delete from  ism_customers
        where user_id=".$user_id."
       ";
//echo $query;
$result=exec_query($query);

// unset priveous logined user
session_remove("user");
if (isset($user))unset($user);
session_remove("user_id");
if (isset($user_id))unset($user_id);
session_remove("grp");
if (isset($grp))unset($grp);
session_remove("comp_id");
if (isset($comp_id))unset($comp_id);


if ($result)
  {

  echo "<script language='javascript'>
               alert('Ваша учетная запись была удалена!');
               window.location.href = '../index.php';
        </script>";

  }

}


?>

<script>
  $(document).ready(function(){
  	var $tabs = $('#tabs').tabs(); // first tab selected
  	$tabs.tabs('select', <?php echo $tab_id?>);
   });
</script>

  <div class="sidebar2">

    <div class="title">Структура портфеля</div>

    <!-- ampie script-->
	<script type="text/javascript" src="../amcharts/ampie/swfobject.js"></script>
		<div id="flashcontent_pie">
			<strong>Обновите ваш Flash Player</strong>
		</div>

		<script type="text/javascript">
			var so = new SWFObject("../amcharts/ampie/ampie.swf", "ampie", "250", "250", "8", "#FFFFFF");
			so.addVariable("path", "../amcharts/ampie/");
			so.addVariable("settings_file", encodeURIComponent("../amcharts/ampie/ampie_settings.xml"));
			so.addVariable("data_file", encodeURIComponent("../amcharts/ampie/ampie_data.xml"));
			so.addVariable("preloader_color", "#FFFFFF");
			so.addParam("wmode", "transparent");
            so.write("flashcontent_pie");
	 </script>
	<!-- end of ampie script -->

    <!--<img src="../lib/graph.php?type=pie&numbers=<?php echo $funds_amount;?>&labels=<?php echo $funds_name;?>" width="250" height="257" alt="img" />-->

    <div class="info"><font size=1>*При отсутствии стоимости пая по одному из фондов, стуктура может отражаться неверно.</font></div>
    <!--
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="../banner.php" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
                      Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
    -->
    <br /><br />
    <a href="../ask_question.php" title="задать вопрос конультанту"><img src="../media/images/ask_consultant2.png" alt="задать вопрос косультанту" border="0"></a>
    <br /><br />

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


<div class="mainContent">
<div id="tabs">
      <ul>
        <li class="topic">Портфель инвестора</li>
        <li class="first"><a href="#fragment-1">Портфель</a></li>
        <li class="first"><a href="#fragment-2">Статистика</a></li>
        <li><a href="#fragment-3">История транзакций</a></li>
        <li><a href="#fragment-4">Личные данные</a></li>
      </ul>
<div id="fragment-1">
<script type="text/javascript">
$(function(){
  $("#ondate").datepicker();
});
</script>

<div id="fund-container">
<div class="left-block">
      <div class="big">Инвестиционный доход</div>
      <div class="small">Наличные средства на виртуальном счете</div>
      <br>
      <div class="small search-block">
         <form method=get>
                Дата:
                <input name="ondate" id="ondate" value="<?php echo $ondate; ?>" style="background:#fff;border:1px solid #b5b8bc;vertical-align:middle;	 margin-right:2px; width:55px; padding:2px; 0; 0; 2px;">
    			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="submit" value="Обновить"></span>
                <input type="hidden" name="type" value="<?php echo $type; ?>">
		</form>
      </div>
</div>
<script type="text/javascript">
</script>

<div class="right-block">
      <div class="big black"><?php $str=(array_sum($vportfolio['income_number'])>=0)?("green"):("red"); $income_per=(array_sum($vportfolio['amount_value_number'])!=0)?(round((array_sum($vportfolio['income_number'])*100)/array_sum($vportfolio['amount_value_number']),2)):(0); echo number_format(array_sum($vportfolio['income_number']), 2, ',', ' ').' тг <span class="'.$str.' mid">('.$income_per.'%)</span>';?></div>
      <div class="small"><?php echo number_format($vaccount['virtual_account'][0], 2, ',', ' ');?> тенге <a href="#buy"><span style="background:white;">[ купить паи ПИФов ]</span></a> <a href="../media/images/vbuy1.png" class="nyroModal" title="Как приобрести паи ПИФов?"><img src="../media/images/help-icon.gif" style="vertical-align: bottom;"></a></div>
</div>
</div>

<?php if ($rc1>0) {?>
<br />
<div class="title">Структура портфеля</div>
        <table class="tab-table top-border">
          <thead>
			  <tr>
			    <th></th>
			    <th>Фонд</th>
			    <th class="right">Цена пая, тг.</th>
			    <th class="right">Кол-во паев, штук</th>
			    <th class="right">Активы, тг.</th>
			    <th class="right">Доход, тг.</th>
			    <?php $sell_str=($type == 'virtual')?('<th class="right"></th>'):(''); echo $sell_str; ?>
			  </tr>
		  </thead>

		  <tbody>
<?php
for ($i=0;$i<sizeof($vportfolio['fund_id']);$i++)
   {
   $class=(fmod(($i),2)==0)?('odd'):('even');

   echo '
     <tr class='.$class.'>
          <td><img src="../media/images/color'.$i.'.png"></td>
          <td><a href="../pif/pif.php?id='.$vportfolio['fund_id'][$i].'" target="_blank">'.$vportfolio['name'][$i].'</a></td>
          <td class="right">'.($str=($vportfolio['value'][$i]==-9999)?('<font color="red">NA</font>'):(number_format($vportfolio['value'][$i], 2, ',', ' '))).'</td>
          <td class="right">'.number_format($vportfolio['amount'][$i], 10, ',', ' ').'</td>
          <td class="right">'.($str=($vportfolio['amount_value'][$i]==-9999)?('<font color="red">NA</font>'):(number_format($vportfolio['amount_value'][$i], 2, ',', ' '))).'</td>
          <td class="right">'.($str=($vportfolio['income'][$i]==-9999)?('<font color="red">NA</font>'):(number_format($vportfolio['income'][$i], 2, ',', ' '))).'</td>
          '.($sell_str=($type == 'virtual')?('<td class="right"><div class="ui-datepicker-header ui-widget-header ui-corner-all" style="width:17px"><a class="nyroModal" rev="modal" href="sell_fund.php?fund_id='.$vportfolio['fund_id'][$i].'" title="Продать"><span class="ui-icon ui-icon-trash"></span></a></div></td>'):('')).'
      </tr>
      ';
  }


  echo ' <tr>
          <td></td>
          <td><b>'.echoNLS('Итого','').'</b></td>
          <td></td>
          <td class="right"><b>'.number_format(array_sum($vportfolio['amount']), 10, ',', ' ').'</b></td>
          <td class="right">'.($str=(array_sum($vportfolio['amount_value_number'])==0)?('<font color="red">NA</font>'):('<b>'.number_format(array_sum($vportfolio['amount_value_number']), 2, ',', ' ').'</b>')).'</td>
          <td class="right">'.($str=(array_sum($vportfolio['income_number'])==0)?('<font color="red">NA</font>'):('<b>'.number_format(array_sum($vportfolio['income_number']), 2, ',', ' ').'</b>')).'</td>
          '.($sell_str=($type == 'virtual')?('<td></td>'):('')).'
         </tr>
       ';

?>

		  </tbody>

        </table>
        <font size=1>(Символ "NA" означает, что цена пая на выбранную дату в системе еще недоступна. Задайте другую более раннюю дату.)</font>
<br />
<?php }?>

<br />

<div class="title"><a name="buy">Супермаркет ПИФов</a></div>

<table class="tab-table top-border">
<thead>
 <tr>
   <th>Фонд</th>
   <th class="right">СГД, %</th>
   <th class="right">Мин сумма, тг.</th>
   <th class="right">Цена пая, тг.</th>
   <th class="right"></th>
 </tr>
</thead>

<tbody>
<?php
$query="
      select
                 t.fund_id
                 ,t.name
                 ,DATE_FORMAT(v.check_date,'%d.%m.%Y') format_check_date
                 ,v.check_date
                 ,v.value
                 ,if(t.limit_min_sum=0,'',t.limit_min_sum) limit_min_sum
                 ,if(t.next_min_sum=0,'',t.next_min_sum)   next_min_sum
                 ,round(tt.avg_income,2)      avg_income
             from ism_funds t
                  LEFT JOIN ism_fund_year_avg_income tt ON  t.fund_id=tt.fund_id    and tt.check_date=(select max(check_date) from ism_fund_year_avg_income where fund_id=t.fund_id)
		          LEFT JOIN ism_fund_value v  ON t.fund_id=v.fund_id
             where v.check_date= (select max(check_date) from ism_fund_value where fund_id=t.fund_id )
                   and v.check_date >= DATE_ADD(NOW(), INTERVAL -2 MONTH)
    	order by t.name
       ";
//echo $query;

$vfunds=array();
$rc=sql_stmt($query, 8, $vfunds ,2);

if ($rc>0)
{for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
   {
$str=(fmod(($i+1),2)==0)?("bgColor=#f3f3f3"):("");

echo '<tr '.$str.'>
          <td><a href="../pif/pif.php?id='.$vfunds['fund_id'][$i].'" target="_blank">'.$vfunds['name'][$i].'</a></td>
          <td class="right">'.$vfunds['avg_income'][$i].'</td>
          <td class="right">'.$str=(($vfunds['limit_min_sum'][$i]!='')?(number_format($vfunds['limit_min_sum'][$i], 2, ',', ' ')):('')).'</td>
          <td class="right nowrap">'.number_format($vfunds['value'][$i], 2, ',', ' ').' <font size="1">('.$vfunds['format_check_date'][$i].')</font></td>
          <td class="right">
                 <div class="ui-datepicker-header ui-widget-header ui-corner-all" style="width:17px">
                 <a class="nyroModal" rev="modal" href="buy_fund.php?fund_id='.$vfunds['fund_id'][$i].'" title="Купить">
                 <span class="ui-icon ui-icon-cart"></span>
                 </a>
                 </div>
          </td>
        </tr>';
     }

}

?>
</tbody>
</table>
</div>

<div id="fragment-2">
<?php
include('statistic.php');
?>
</div>

<div id="fragment-3">
<?php
$query="
         select  t.fund_id
                ,t.transaction_id
                ,t.total_sum
                ,ifnull(t.total_sum/v.value,0) amount
                ,DATE_FORMAT(t.complete_date,'%d.%m.%y') comp_date
                ,ifnull(v.value,0) price_per_share
                ,d1.desc_".echoNLS('ru','')." action
                ,t.action action_id
                ,d2.desc_".echoNLS('ru','')." tstatus
                ,t.tstatus tstatus_id
                ,f.name fund_name
                ,(select name from ism_companies where company_id=f.company_id) comp_name
         from ".$tab_name." t
              left join ism_fund_value v on v.fund_id=t.fund_id and v.check_date=t.complete_date
             ,ism_dictionary d1, ism_funds f,ism_dictionary d2
         where t.user_id=".$user_id."
               and t.action=d1.id
               and t.fund_id=f.fund_id
               and t.tstatus=d2.id
         order by complete_date  desc
       ";
//echo $query;

$vportfolio=array();
$rc=sql_stmt($query, 12, $vportfolio ,2);

if ($rc>0)
{
echo '
        <table class="tab-table">
        <thead>
         <tr>
          <th>'.echoNLS('Фонд','').'</th>
          <th>'.echoNLS('Транзакция','').'</th>
          <th>'.echoNLS('Дата','').'</th>
          <!--<th>'.echoNLS('Статус','').'</th>-->
          <th>'.echoNLS('Сумма','').'</th>
          <th>'.echoNLS('Цена пая','').'</th>
          <th>'.echoNLS('Кол-во паев','').'</th>
        </tr>
        </thead>
        <tbody>
 ';

$tsum=0;
$tamount=0;

for ($i=0;$i<sizeof($vportfolio['transaction_id']);$i++)
   {

   if ($vportfolio['action_id'][$i]==$ACTION_BUY )
   {
     $tsum=$tsum+$vportfolio['total_sum'][$i];
     $tamount=$tamount+$vportfolio['amount'][$i];
   }
   else
   {
     $tsum=$tsum-$vportfolio['total_sum'][$i];
     $tamount=$tamount-$vportfolio['amount'][$i];
   }

   $class=(fmod(($i),2)==0)?('odd'):('even');

   echo '
     <tr class="'.$class.'">
          <td><a href="../pif/pif.php?id='.$vportfolio['fund_id'][$i].'" target="_blank">'.$vportfolio['fund_name'][$i].'</a></td>
          <td>'.$vportfolio['action'][$i].'</td>
          <td>'.$vportfolio['comp_date'][$i].'</td>
          <!--<td>'.$vportfolio['tstatus'][$i].'</td>-->
          <td class="right">'.number_format($vportfolio['total_sum'][$i], 2, ',', ' ').'</td>
          <td class="right">'.($str=($vportfolio['price_per_share'][$i]==0)?('NA'):(number_format($vportfolio['price_per_share'][$i], 2, ',', ' '))).'</td>
          <td class="right">'.($str=($vportfolio['amount'][$i]==0)?('NA'):(number_format($vportfolio['amount'][$i], 10, ',', ' '))).'</td>
      </tr>
      ';
  }

 echo '
       <tr>
          <td><b>'.echoNLS('Итого','').'</b></td>
          <td></td>
          <td></td>
          <!--<td></td>-->
          <td class="right"><b>'.number_format($tsum, 2, ',', ' ').'</b></td>
          <td></td>
          <td class="right"><b>'.($str=($tamount==0)?('NA'):(number_format($tamount, 10, ',', ' '))).'</b></td>
      </tr>
 </tbody></table>';
}
else
{
 echo echoNLS('Нет данных.','');
}


?>

</div>


<div id="fragment-4">
<?php
$query="
        select
                  u.user_id
                 ,c.first_name
                 ,c.last_name
                 ,c.planned_monthly_investment
                 ,u.subscription
        from ism_users u, ism_customers c
        where u.user_id=".$user_id."  and u.user_id=c.user_id
       ";

$vuser=array();
$rc=sql_stmt($query, 5, $vuser ,1);

if ($rc>0)
{

   echo '


 <form name="edit_form" method="post">
 <input type="hidden" name="tab_id" value="3">
 <div class="grey-block">
 <div class="search-block">
   <ul>
       <li><div>'.echoNLS('Счет','').'</div>'.$vuser['user_id'][0].'</li>
       <li><div>'.echoNLS('Имя','').'</div><input type=text name=first_name value="'.$vuser['first_name'][0].'"  size=20></li>
       <li><div>'.echoNLS('Фамилия','').'</div><input type=text name=last_name value="'.$vuser['last_name'][0].'"  size=20></li>
       <li><div>'.echoNLS('Планируемые инвестиции','').'</div><input type=text name=planned_monthly_investment value="'.$vuser['planned_monthly_investment'][0].'"  size=20 onblur="IsNumeric(this)">тенге/ планируемые месячные инвестиции <a class="nyroModal" href="../planned_mothly_investment.html">('.echoNLS('подробнее','').')</a></li>
       <li>'.echoNLS('Получать рассылку от Invest-Market.kz','').'<input type=checkbox name="subscription" '.($str1=($vuser['subscription'][0]==1)?("checked"):("")).'></li>
       <li><div>&nbsp;</div><span>
        <input type="submit"  class="button" name="edit" value="'.echoNLS('Изменить','').'">
        <input type="reset"   class="button" value="'.echoNLS('Отменить','').'">
       </span>
       </li>
   </ul>
 </div>
 </div>
<br />


 <div class="title">Сменить пароль</div>
 <div class="grey-block">
 <div class="search-block">

         <ul>
            <li><div>'.echoNLS('Старый пароль','').'</div><input type="password" name="passwd" size="20"></li>
            <li><div>'.echoNLS('Новый пароль','').'</div><input type="password" name="passwd_new" size="20"></li>
            <li><div>'.echoNLS('Подтверждение пароля','').'</div><input type="password" name="passwd_new_conf" size="20"></li>
            <li><div>&nbsp;</div>
                 <span>
                  <input type="submit"  class="button" name="change" value="'.echoNLS('Изменить','').'">
                  <input type="reset"   class="button" value="'.echoNLS('Отменить','').'">
                </span>
            </li>
         </ul>
    </div>
 </div>

 <br />
 <div class="title">Удаление учетной записи</div>
 <div class="grey-block">
 <div class="search-block">
       <ul><li><div>Удалить запись</div><span><input type="submit"  name="delete_account" value="'.echoNLS('Удалить','').'"></span></li></ul>
 </div>
 </div>


 </form>
 ';

}

?>

</div>
</div>

</div>

<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>