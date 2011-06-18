<?php
require("../main.cfg");
include("../lib/mysql.inc");

//connect to the db
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

//getting the list of funds
$query="
        select
                   t.index_id
                  ,t.name
                  ,t.investfunds_tick
                  ,tt.check_date last_date
         from   ism_indexes t
               ,ism_index_value tt
        where   t.index_id=tt.index_id
                and tt.check_date=(select max(check_date) from ism_index_value where index_id=t.index_id)
                and t.investfunds_tick  is not null
         order by  t.index_id
       ";

//echo $query;
$vfunds=array();
$rc=sql_stmt($query, 4, $funds ,2);

$log_file = "../logs/get_index_data_from_investfunds_".date("Ymd").".html";
$handle = fopen($log_file, 'w+');

if ($rc>0)
{
 for ($i=0;$i<sizeof($funds['index_id']);$i++)
   {
       $txt= '<b>Index: '.$funds['name'][$i].'</b> ('.$funds['last_date'][$i].')<br>';
       fwrite($handle, $txt);

       //get URL
       $url='http://investfunds.kz/markets/indicators/'.$funds['investfunds_tick'][$i].'/';
       //echo $url."<br>";

       $fp = fopen( $url, 'r' );
       $content = "";

        while( !feof( $fp ) )
         {

          $buffer = trim( fgets( $fp, 4096 ) );
          if(preg_match_all('/<td class="tblc-name">(.*?)<\/td><td>(.*?)<\/td><\/tr>/si',$buffer, $results, PREG_SET_ORDER))
          {
          foreach($results as $match)
           {
              $match[1]=str_replace("<strong>", "",$match[1]);
              $match[1]=str_replace("</strong>", "",$match[1]);
              $cdate=substr($match[1],6,4).'-'.substr($match[1],3,2).'-'.substr($match[1],0,2);
              //echo $cdate;
              $match[2]=str_replace("<strong>", "",$match[2]);
              $match[2]=str_replace("</strong>", "",$match[2]);
              $value=str_replace(",", ".",$match[2]);
 			  $value=str_replace(" ", "",$match[2]);
 			  //echo $value;

 			  if ($cdate>$funds['last_date'][$i])
              {
                 $query="insert into ism_index_value(index_id,check_date,value) values(".$funds['index_id'][$i].",'".$cdate."',".$value.")";
                 $result=exec_query($query);
                 //echo $query."<br>";
                 if ($result)
                 {
                   $txt = 'Данные внесены за '.$cdate.'<br>';
                   fwrite($handle, $txt);
                 }
               }
           }
          }
        }
   }
}

$txt = '<br><a href="../admin/get_list_of_logs.php" >All logs</a><br>';
fwrite($handle, $txt);

echo 'DONE. Check logs <a href="../logs/get_index_data_from_investfunds_'.date("Ymd").'.html" >here</a>.';

fclose($handle);
disconn($conn);
?>