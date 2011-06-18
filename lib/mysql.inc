<?php

/*******************************************************************/
/* File:        mysql.inc                                          */
/* Description: Library ofmysql's functions                        */
/* Date:        17-MAY-2006                                        */
/* Author:      Levin Vyacheslav                                   */
/*******************************************************************/


//============================================================================
// cConnect to the database
function conn ($user, $pass, $db, $host)
{
   global $lang;
   //connect
   $conn = mysql_connect($host, $user, $pass)   or die('Could not connect: ' . mysql_error());
   //select database
   mysql_select_db($db) or die('Could not select database');

   exec_query("SET NAMES utf8");

   return $conn;
}
//==============================================================================================
//Disconnect from the database
function disconn($conn)
{
 // Closing connection
 if ($conn) { mysql_close($conn);}
 else {echo "Cann't disconnect because not connected!";}
}
//==============================================================================================
//execute sql statment
function exec_query($query)
{
 global $DEBUG_MODE;

 $result = mysql_query($query);
 if (!$result)
 {
   if (!($DEBUG_MODE)) header ("Location: ../404.php");
   echo "Could not successfully run query ($query) from DB: " . mysql_error();
   return false;
 }
 else { return $result;}
}
//==============================================================================================
//function return data from database
// $query -base query string
// $ncols - number of asked columns
// &$vars - data array
// $close_cur :  if =2 - return all records
//               if =1 - return first record
function sql_stmt($query, $ncols, &$vars ,$close_cur)
{
  global $DEBUG_MODE;

  $rc=0;
  $result = mysql_query($query);
  if (!$result)
     {
       if (!($DEBUG_MODE)) header ("Location: ../404.php");
       echo "Could not successfully run query ($query) from DB: " . mysql_error();
       return $rc;
     }
  else
     {
        if ($ncols == 0) $ncols_limit=100;
        else             $ncols_limit=$ncols;
        //get list of column
        for ($i=0;$i<$ncols_limit;$i++)
         {
          $col_name = mysql_field_name($result, $i);
          if ($col_name =="NULL" || $col_name =="") { $ncols=$i; break; }
          $vars[$col_name]=array();
          $vars["_NAME"][$i]=$col_name;
         }
        $vars["_NCOLS"] = $ncols;

        //take data ++++++++++++++++++++++++++++++++++++++++++++++
        //return all data
        if ($close_cur == 2)
         {
           $nr=0;
           while ($row = mysql_fetch_array($result))
            {
              //set that data exixts
              $rc = 1;
              for ($i=0;$i<$ncols;$i++)
               {
                 $col_name=$vars["_NAME"][$i];
                 $vars[$col_name] [$nr]= $row[$i];
                 //support column address as number of column
                 $vars["$i"][$nr]=$vars[$col_name] [$nr];
               }
             $nr++;
           }
        }
         //return first row
        if ($close_cur == 1)
         {
           $nr=0;
           while (($row = mysql_fetch_array($result))&& $nr<1)
            {
              //set that data exixts
              $rc = 1;
              for ($i=0;$i<$ncols;$i++)
               {
                 $col_name=$vars["_NAME"][$i];
                 $vars[$col_name] [$nr]= $row[$i];
                 //support column address as number of column
                 $vars["$i"][$nr]=$vars[$col_name] [$nr];
               }
             $nr++;
           }
        }

     }
    //free all memory
    mysql_free_result($result);
    return $rc;
}


?>