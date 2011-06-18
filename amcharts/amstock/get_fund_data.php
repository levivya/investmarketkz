<?php

//include("../../lib/misc.inc");
// Connecting, selecting database
//$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);


// retrieves variables from a query string
$tab = $_GET["tab"];
$id = $_GET["id"];


// escape special characters in a string for use in a SQL statement (security)
$tab = mysql_real_escape_string($tab);
$id = mysql_real_escape_string($id);

// select entries from table with the same name as stock charts name ($stock), and the dates that comes after $start_date only
$query = "SELECT value,asset_value volum, check_date date FROM $tab WHERE fund_id = $id";
$res = mysql_query($query);

// echo data
while($obj = mysql_fetch_object($res)){
  $date = $obj->date;
  $value =  $obj->value;
  $volume =  $obj->volume;

  echo "$date;$value;$volume\n";
}

//disconnect  from the database
//disconn($conn);

?>