<?php
//include lib and conf file
include("../lib/misc.inc");

// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

set_time_limit ( 600 ) ;


echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><a href="index.php">'.echoNLS('На страницу Администратора','').'</a>
          </td>
      </tr>
      </table><br>';

flush();

//create backup
$query="delete from  ism_fund_year_avg_income_backup";
$result=exec_query($query);
$query="insert into ism_fund_year_avg_income_backup  select * from ism_fund_year_avg_income";
$result=exec_query($query);



//fund's table
$query="delete from ism_fund_year_avg_income";

$result=exec_query($query);
if ($result)
  {
   echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('Данные по месячной доходности удалены!<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';
  }



flush();

$query="
insert into ism_fund_year_avg_income(fund_id,check_date,income)
SELECT
          t.fund_id
         ,t.check_date  check_date
         ,round(((t.value-tt.value)/tt.value)*100,2) year_income
FROM   ism_fund_value  t
       ,ism_fund_value tt
WHERE t.check_date>=(select DATE_ADD(min(check_date),INTERVAL 365 DAY) from ism_fund_value where fund_id=t.fund_id  )
      			and tt.check_date=DATE_ADD(t.check_date,INTERVAL -365 DAY)
      			and t.fund_id=tt.fund_id
      ";
flush();
//process data
$result=exec_query($query);
if ($result)
  {
   echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('Данные по месячной доходности внесены!<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';
  }


flush();

//++++++++++++++++++++++++++++++ UPDATE EMPTY VAUES +++++++++++++++++++++++++++++++++++++++++++++++++

echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('НАЧАТ расчет средней доходности на каждую дату...<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';

$query="
select
        t.fund_id
       ,min(t.check_date) min_date
       ,max(t.check_date) max_date
from ism_fund_year_avg_income t
group by t.fund_id
";

$vdate=array();
$rc=sql_stmt($query, 3, $vdate ,2);
flush();

for ($i=0;$i<sizeof($vdate['fund_id']);$i++)
 {
     $query="
                select
       					tt.check_date
				from ( select adddate( '".$vdate['min_date'][$i]."',id) check_date from tab where adddate('".$vdate['min_date'][$i]."',id)<='".$vdate['max_date'][$i]."' ) tt
      				 left join ism_fund_year_avg_income t on t.fund_id=".$vdate['fund_id'][$i]." and tt.check_date=t.check_date
				where t.check_date is null
           	";
     //echo $query."<br>";
     $vdate2=array();
     $rc=sql_stmt($query, 1, $vdate2 ,2);

     for ($j=0;$j<sizeof($vdate2['check_date']);$j++)
     {
     $query="
                 insert into ism_fund_year_avg_income(fund_id,income,check_date)
                 select  ".$vdate['fund_id'][$i]."
                         ,tt.income
                         ,'".$vdate2['check_date'][$j]."'
                 from ism_fund_year_avg_income tt
                 where tt.check_date=( select max(check_date) from ism_fund_month_avg_income where fund_id=".$vdate['fund_id'][$i]." and check_date< '".$vdate2['check_date'][$j]."' )
                       and tt.fund_id=".$vdate['fund_id'][$i]."
           ";
      $result=exec_query($query);
     //echo $query."<br>";

       }
 }

//++++++++++++++++++++++++++++++ END UPDATE EMPTY VAUES +++++++++++++++++++++++++++++++++++++++++++++



//++++++++++++++++++++++++++CALCULATE INCOME AVG ++++++++++++++++++++++++++++++++++++++++++++++++++++
echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('НАЧАТ расчет средней доходности на каждую дату...<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';

$query="
select
        t.fund_id
       ,t.check_date
       ,t.income
       ,(select round(avg(income),2) from ism_fund_year_avg_income where fund_id=t.fund_id and check_date between DATE_ADD(t.check_date,INTERVAL -365 DAY) and t.check_date) avg_income
from ism_fund_year_avg_income t
where  t.check_date>=(select min(check_date) from ism_fund_year_avg_income where fund_id=t.fund_id  )
order by t.fund_id,t.check_date
";

$vfunds=array();
$rc=sql_stmt($query, 4, $vfunds ,2);
flush();

for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
 {
     $query="
	             update  ism_fund_year_avg_income
	             set     avg_income=".round($vfunds['avg_income'][$i],2)."
	             where   fund_id=".$vfunds['fund_id'][$i]."
                         and check_date='".$vfunds['check_date'][$i]."'
           ";
      $result=exec_query($query);

 }

flush();
echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('ЗАКОНЧЕН расчет средней доходности на каждую дату!<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//+++++++++++++++++++++++++ CALCULATE VOLAT +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$query="
select
        t.fund_id
       ,avg_income last_avg_income
from ism_fund_year_avg_income t
where  t.check_date=(select max(check_date) from ism_fund_year_avg_income where fund_id=t.fund_id and avg_income is not null)
group by t.fund_id
";

$vfunds=array();
$rc=sql_stmt($query, 2, $vfunds ,2);
flush();

for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
 {

     $query="
        update ism_fund_year_avg_income
        set volat=round(((".$vfunds['last_avg_income'][$i]."-income)*(".$vfunds['last_avg_income'][$i]."-income))/100,3)
        where fund_id=".$vfunds['fund_id'][$i]."
       ";
     $result=exec_query($query);

 }


echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('Расчет "квадрат отклонения" закончен!<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//+++++++++++++++++++++++++ CALCULATE VOLAT AVG+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('НАЧАТ расчет волатильности на каждую дату...<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';

$query="
select
        t.fund_id
       ,t.check_date
       ,t.volat
       ,(select sqrt(sum(volat)/(count(volat)-1))*10 from ism_fund_year_avg_income where fund_id=t.fund_id and check_date<=t.check_date) avg_volat
from ism_fund_year_avg_income t
order by t.fund_id,t.check_date
";

$vfunds=array();
$rc=sql_stmt($query, 4, $vfunds ,2);
flush();

for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
 {
     $query="
	             update  ism_fund_year_avg_income
	             set     avg_volat=".round($vfunds['avg_volat'][$i],2)."
	             where   fund_id=".$vfunds['fund_id'][$i]."
                         and check_date='".$vfunds['check_date'][$i]."'
           ";
      $result=exec_query($query);

 }

flush();
echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('ЗАКОНЧЕН расчет волатильности на каждую дату!<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';
flush();

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//disconnect  from the database
disconn($conn);

?>