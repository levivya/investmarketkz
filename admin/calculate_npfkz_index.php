<?php
//
//   file:        calculate_npfkz_index.php
//   description: This script calculate index npfkz informatiom.
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");

// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

$NPFKZ_INDEX_FIRST_DAY='2001-01-01';
$NPFKZ_INDEX_FIRST_PINT=100;

set_time_limit ( 600 ) ;


echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><a href="index.php">'.echoNLS('На страницу Администратора','').'</a>
          </td>
      </tr>
      </table><br>';

flush();


//create backup
$query="delete from  ism_index_npfkz_backup";
$result=exec_query($query);
$query="insert into ism_index_npfkz_backup  select * from ism_index_npfkz";
$result=exec_query($query);



//fund's table
$query="delete from  ism_index_npfkz";

$result=exec_query($query);
if ($result)
  {
   echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('Таблица расчетов отчищена!<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';
  }



flush();


//++++++++++++++++++++ PREPARE TEMPORARY TABLE ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$query="drop table ism_pension_fund_value_tmp";
$result=exec_query($query);


$query="
        create table ism_pension_fund_value_tmp
        as
        select * from ism_pension_fund_value
        where fund_id in (
                            select fund_id
                            from  ism_pension_funds
                            where start_date<=DATE_ADD('".date('y-m-d')."',INTERVAL -1 YEAR)
                         )
        ";
//echo $query;
//die();

$result=exec_query($query);

$query="create unique index ism_pension_fund_value_tmp_idx on ism_pension_fund_value_tmp (fund_id,check_date)";
$result=exec_query($query);


//get max date
$query="select max(check_date) max_date from ism_pension_fund_value_tmp";
$max_date=array();
$rc=sql_stmt($query, 1, $max_date ,1);
$vmax_date=$max_date['max_date'][0];

//echo $vmax_date;
//die();

//get max values for each fund
$query="select
                t.fund_id
               ,t.check_date
               ,t.value
               ,t.asset_value
        from ism_pension_fund_value_tmp t
        where t.check_date=(select max(check_date) from ism_pension_fund_value_tmp where fund_id=t.fund_id)";

//echo $query;
//die();

$max_values=array();
$rc=sql_stmt($query, 4, $max_values ,2);
//continue last values till max date
for ($i=0;$i<sizeof($max_values['fund_id']);$i++)
 {
    if ($max_values['check_date'][$i]<$vmax_date)
    {
      $query="insert into ism_pension_fund_value_tmp(fund_id,check_date,value,asset_value) values(".$max_values['fund_id'][$i].",'".$vmax_date."',".$max_values['value'][$i].",".$max_values['asset_value'][$i].")";
      //echo $query;
      $result=exec_query($query);
    }
 }
//die();

//fill empty values
$query="
select
        t.fund_id
       ,min(t.check_date) min_date
       ,max(t.check_date) max_date
from ism_pension_fund_value_tmp t
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
                                from ( select adddate( '".$vdate['min_date'][$i]."',INTERVAL id MONTH) check_date from tab where adddate('".$vdate['min_date'][$i]."',INTERVAL id MONTH)<='".$vdate['max_date'][$i]."' ) tt
                                       left join ism_pension_fund_value_tmp t on t.fund_id=".$vdate['fund_id'][$i]." and tt.check_date=t.check_date
                                where t.check_date is null
                   ";
     //echo $query."<br>";
     $vdate2=array();
     $rc=sql_stmt($query, 1, $vdate2 ,2);

     for ($j=0;$j<sizeof($vdate2['check_date']);$j++)
     {
     $query="
                 insert into ism_pension_fund_value_tmp(fund_id,value,asset_value,check_date)
                 select  ".$vdate['fund_id'][$i]."
                         ,tt.value
                         ,tt.asset_value
                         ,'".$vdate2['check_date'][$j]."'
                 from ism_pension_fund_value_tmp tt
                 where tt.check_date=( select max(check_date) from ism_pension_fund_value_tmp where fund_id=".$vdate['fund_id'][$i]." and check_date< '".$vdate2['check_date'][$j]."' )
                       and tt.fund_id=".$vdate['fund_id'][$i]."
           ";
      $result=exec_query($query);
     //echo $query."<br>";

       }
 }


//++++++++++++++++++++ END PREPARE TEMPORARY TABLE ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++




//++++++++++++++++++++++++++DAILY INCOME PIFKZ (percent) ++++++++++++++++++++++++++++++++++++++++++++++++++++

$query="
insert into ism_index_npfkz(check_date,daily_income_percent,asset)
SELECT
          t.check_date  check_date
         ,round(avg(((t.value-tt.value)/tt.value)*100),2) prirost_npfkz_persent
         ,sum(t.asset_value) asset
FROM    ism_pension_fund_value_tmp  t
       ,ism_pension_fund_value_tmp tt
       ,ism_pension_funds f
WHERE t.check_date>=(select DATE_ADD(min(check_date),INTERVAL 1 MONTH) from ism_pension_fund_value_tmp where fund_id=t.fund_id  )
      and f.fund_id=t.fund_id
      and f.fund_id=tt.fund_id
      and tt.check_date=DATE_ADD(t.check_date,INTERVAL -1 MONTH)
      and t.fund_id=tt.fund_id
      and f.npfkz=1
GROUP BY t.check_date
      ";
flush();
//process data
$result=exec_query($query);
if ($result)
  {
   echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('Данные по дневному приросту НПФКЗ в процентах внесены!<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';
  }


flush();

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//die();

//++++++++++++++++++++++++++DAILY INCOME PIFKZ (point) ++++++++++++++++++++++++++++++++++++++++++++++++++++
echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('НАЧАТ расчет НПФКЗ...<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';


//update the first point in pifkz index
$query="   update ism_index_npfkz
           set npfkz_point= ".$NPFKZ_INDEX_FIRST_PINT."
           where check_date='".$NPFKZ_INDEX_FIRST_DAY."'
      ";
$result=exec_query($query);

//die();

$query="
SELECT
        t.check_date  check_date
FROM    ism_index_npfkz  t
WHERE   t.check_date>=DATE_ADD('".$NPFKZ_INDEX_FIRST_DAY."',INTERVAL 1 MONTH)
";

$vdate=array();
$rc=sql_stmt($query, 1, $vdate ,2);
flush();

for ($i=0;$i<sizeof($vdate['check_date']);$i++)
 {
     $query="
                                SELECT
                                        t.check_date  check_date
                        ,(tt.npfkz_point*t.daily_income_percent)/100                   daily_income_point
                        ,tt.npfkz_point+((tt.npfkz_point*t.daily_income_percent)/100) npfkz_point
                                FROM    ism_index_npfkz  t,
                        ism_index_npfkz  tt
                                WHERE   t.check_date='".$vdate['check_date'][$i]."'
                        and tt.check_date=DATE_ADD('".$vdate['check_date'][$i]."',INTERVAL -1 MONTH)
                        ";

     //echo $query."<br>";
     //die();

     $vincome=array();
     $rc=sql_stmt($query, 3, $vincome ,1);
     flush();


     $query="
                 update  ism_index_npfkz
                 set     daily_income_point=".round($vincome['daily_income_point'][0],5).",
                         npfkz_point=".round($vincome['npfkz_point'][0],3)."
                 where   check_date='".$vdate['check_date'][$i]."'
           ";
      $result=exec_query($query);


 }

flush();
echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('ЗАКОНЧЕН расчет НПФКЗ!<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//++++++++++++++++++++++++++YEAR INCOME ++++++++++++++++++++++++++++++++++++++++++++++++++++
echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('НАЧАТ расчет Индекса годовой доходности...<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';
flush();

$query="
SELECT
          t.check_date  check_date
         ,round(((t.npfkz_point-tt.npfkz_point)/tt.npfkz_point)*100,2)  income_year
FROM    ism_index_npfkz  t
       ,ism_index_npfkz tt
WHERE t.check_date>=DATE_ADD('".$NPFKZ_INDEX_FIRST_DAY."',INTERVAL 1 YEAR)
      and tt.check_date=DATE_ADD(t.check_date,INTERVAL -1 YEAR)
      and (t.npfkz_point-tt.npfkz_point)/tt.npfkz_point is not null
      ";

$vincome=array();
$rc=sql_stmt($query, 2, $vincome ,2);
flush();

for ($i=0;$i<sizeof($vincome['check_date']);$i++)
 {

     $query="
                 update  ism_index_npfkz
                 set     income_year=".$vincome['income_year'][$i]."
                 where   check_date='".$vincome['check_date'][$i]."'
           ";
      $result=exec_query($query);
 }


flush();
echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('ЗАКОНЧЕН расчет Индекса годовой доходности!<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';

//++++++++++++++++++++++++++END YEAR INCOME +++++++++++++++++++++++++++++++++++++++++++++++++


//++++++++++++++++++++++++++YEAR AVG INCOME ++++++++++++++++++++++++++++++++++++++++++++++++++++
echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('НАЧАТ расчет Индекса среднее годовой доходности...<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';
flush();

$query="
SELECT
          t.check_date  check_date
         ,(select avg(income_year) from ism_index_npfkz where check_date between DATE_ADD(t.check_date,INTERVAL -1 YEAR) and t.check_date and income_year is not null) income_year_avg
FROM     ism_index_npfkz  t
WHERE t.check_date>=DATE_ADD('".$NPFKZ_INDEX_FIRST_DAY."',INTERVAL 1 YEAR)
      ";

$vincome=array();
$rc=sql_stmt($query, 2, $vincome ,2);
flush();

for ($i=0;$i<sizeof($vincome['check_date']);$i++)
 {

     $query="
                 update  ism_index_npfkz
                 set     income_year_avg=round(".$vincome['income_year_avg'][$i].",2)
                 where   check_date='".$vincome['check_date'][$i]."'
           ";
      $result=exec_query($query);
 }


flush();
echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('ЗАКОНЧЕН расчет Индекса среднее годовой доходности!<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';

//++++++++++++++++++++++++++END YEAR AVG INCOME +++++++++++++++++++++++++++++++++++++++++++++++++


// CHECK DATA ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
/*
$query="
         select pifkz_point from ism_index_pifkz where check_date = (select max(check_date) from ism_index_pifkz)
       ";

$vindex=array();
$rc=sql_stmt($query, 1, $vindex ,1);
flush();

if ($vindex['pifkz_point'][0] < 120 || $vindex['pifkz_point'][0]> 180)
{
    echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
          <tr bgcolor="white">
            <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('Последнее значение выходит за пределы допустимых значений (120 - 180), начинаеться востановление данных из backup!<br>Время:&nbsp;','').date('r').'</font>
            </td>
          </tr>
          </table><br>';

    //restore from backup
        $query="delete from  ism_index_pifkz";
        $result=exec_query($query);
        $query="insert into ism_index_pifkz  select * from ism_index_pifkz_backup";
        $result=exec_query($query);

     echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
          <tr bgcolor="white">
            <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('ЗАКОНЧЕНО востановление данных!<br>Время:&nbsp;','').date('r').'</font>
            </td>
          </tr>
          </table><br>';

}
*/


//disconnect  from the database
disconn($conn);

?>