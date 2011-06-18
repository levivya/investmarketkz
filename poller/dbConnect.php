<?php
//dev
//$conn = mysql_connect("localhost","root","1234");
//mysql_select_db("investmarketkz",$conn);

//uat
//$conn = mysql_connect("db.invest04.mass.hc.ru","invest04_inves02","Qwerty123");
//mysql_select_db("wwwinvest_marketcom_invest_market_uat",$conn);

//prod
$conn = mysql_connect("db.invest04.mass.hc.ru","invest04","eicah3Hr");
mysql_select_db("wwwinvest_marketcom_investsm",$conn);

?>