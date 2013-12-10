<?php
/******************************************************************************
Power Banner Manager 1.5 !
(visit.php file)

Copyright Armin Kalajdzija, 2002.
E-mail: kalajdzija@hotmail.com
WebSite: http://www.ak85.tk
******************************************************************************/

include "config.inc.php";

if (isset($hostname) and isset($database) and isset($db_login) and isset($db_pass)) {
    $dbconn = mysql_connect($hostname, $db_login, $db_pass) or die("Could not connect");

    mysql_select_db($database) or die("Could not select database");

    $query = "SELECT url,visits FROM powerban WHERE id=$id";
    $result = mysql_query($query) or die("Query failed");
    
    $rows = mysql_fetch_row($result);
    
    $visits = $rows[1] + 1;
    $cdate = date("Y-m-d h:i:s");
    
    $query = "UPDATE powerban SET visits=$visits WHERE id=$id";
    $result = mysql_query($query) or die("Query failed");

    $query = "INSERT INTO powerban_stats_visits (id, address, agent, datetime, referer) VALUES ('$id', '$REMOTE_ADDR', '$HTTP_USER_AGENT', '$cdate', '$HTTP_REFERER')";
    $result = mysql_query($query) or die("Query failed");

    mysql_close($dbconn);
    header("Location:$rows[0]");
}

?>
