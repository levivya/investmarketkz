<?php

 require("../main.cfg");
 include("../lib/mysql.inc");

 $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

 //getting the list of funds
 $query="
        select
                   t.index_id
                  ,t.name
                  ,t.investfunds_id
                  ,tt.check_date last_date
         from   ism_indexes t
               ,ism_index_value tt
        where   t.index_id=tt.index_id
                and tt.check_date=(select max(check_date) from ism_index_value where index_id=t.index_id)
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
       $url='http://investfunds.kz/indicators/'.$funds['investfunds_id'][$i].'/';
       //echo $url."<br>";


       $fp = fopen( $url, 'r' );
       $content = "";

        while( !feof( $fp ) )
         {
          $buffer = trim( fgets( $fp, 4096 ) );
          preg_match('/<td>(.*?)<\/td><td nowrap>(.*?)<\/td><\/tr>/si',$buffer, $results);

          if (isset($results[1]))
            {

              $cdate=substr($results[1],6,4).'-'.substr($results[1],3,2).'-'.substr($results[1],0,2);
              $value=str_replace(",", ".",$results[2]);
 			  $value=str_replace(" ", "",$results[2]);
 			  $asset_value=str_replace(",", ".",$results[3]);
 			  $asset_value=str_replace(" ", "",$results[3]);

              if ($cdate>$funds['last_date'][$i])
              {
                 $query="insert into ism_index_value(index_id,check_date,value) values(".$funds['index_id'][$i].",'".$cdate."',".$value.")";
                 $result=exec_query($query);

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

 $txt = '<br><a href="../admin/get_list_of_logs.php" >All logs</a><br>';
 fwrite($handle, $txt);

echo 'DONE. Check logs <a href="../logs/get_index_data_from_investfunds_'.date("Ymd").'.html" >here</a>.';

fclose($handle);
disconn($conn);
?>