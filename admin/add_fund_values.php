<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Добавить значения по фондам(ПИФ\НПФ)</title>
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
<div class="title"><a class="more" href="index.php">Панель администратора</a>Добавить значения по фондам(ПИФ\НПФ)</div>

<?php
if (!isset($type)) $type='pif';

if ($type=='pif')
{	$main_tab='ism_funds';
	$value_tab='ism_fund_value';}
else
{	$main_tab='ism_pension_funds';
	$value_tab='ism_pension_fund_value';
}

//get list of funds
 $query="
            select
                   t.fund_id
                  ,t.name
             from ".$main_tab." t
             order by t.name
        ";

 $vfund=array();
 $rc=sql_stmt($query, 2, $vfund ,2);

 if (!isset($fund)) $fund=$vfund['fund_id'][0];

 $FundMenuString = menu_list($vfund['name'],$fund,$vfund['fund_id']);
 $FundMenuString = '<select name="fund" onchange="submit()">'.$FundMenuString.'</select>';

 echo   '<form>
              <div class="search-block grey-block"><ul><li><div>Выбрать фонд</div>'.$FundMenuString.'</li></ul></div>
              <input type="hidden" name="type" value="'.$type.'">
        </form>';

 $query="
                select
                        t.start_date
                       ,t.name
               from ".$main_tab." t
               where fund_id=".$fund;

 $vsdate=array();
 $rc=sql_stmt($query, 2, $vsdate ,1);

 $query="
       select
               tt.check_date
              ,DATE_FORMAT(tt.check_date,'%d.%m.%Y') check_date_format
              ,DATE_FORMAT(tt.check_date,'%d-%m-%Y') check_date_format2
       from ( select adddate( '".$vsdate['start_date'][0]."',id) check_date from tab where adddate('".$vsdate['start_date'][0]."',id)<='".date('Y-m-d')."' ) tt
            left join ".$value_tab." t on t.fund_id=".$fund." and tt.check_date=t.check_date
       where t.check_date is null  ".($str=($type=='npf')?('and DAYOFMONTH(tt.check_date) = 1'):(''))."
       order by check_date desc
       ";

 //echo $query;
 //die();

 $vdate=array();
 $rc=sql_stmt($query, 3, $vdate ,2);

 if ($rc>0)
 {
   echo '
         <table id="data" class="tab-table">
		  <thead>
		  <tr>
		    <th>Дата</th>
		    <th></th>
		  </tr>
		 </thead>
    	 <tbody>
        ';

   for ($j=0;$j<sizeof($vdate['check_date']);$j++)
     {
         echo '<tr>
                  <td>'.$vdate['check_date_format'][$j].'</td>
                  <td class="right">
                    <div class="ui-datepicker-header ui-widget-header ui-corner-all" style="width:17px">
                    <a onclick="window.open(\'add_fund_value.php?tab='.$value_tab.'&fund_name='.$vsdate['name'][0].'&check_date='.$vdate['check_date_format'][$j].'&fund_id='.$fund.'\',\'newWin\',\'width=600,height=300\')" title="Внести данные">
                    <span class="ui-icon ui-icon-plus"></span>
                    </a>
                    </div>
                  </td>
               </tr>';
     }

   echo '
         </tbody>
         </table>
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