<?php

/* 	This is the SQL that creates the default tables used in the poller script from www.dhtmlgoodies.com	*/

//SQL that creates the tables needed in this demo

//dev
//$conn = mysql_connect("localhost","root","1234");
//mysql_select_db("investmarketkz",$conn);

//uat
//$conn = mysql_connect("db.invest04.mass.hc.ru","invest04_inves02","Qwerty123");
//mysql_select_db("wwwinvest_marketcom_invest_market_uat",$conn);

//prod
$conn = mysql_connect("db.invest04.mass.hc.ru","invest04","eicah3Hr");
mysql_select_db("wwwinvest_marketcom_investsm",$conn);


mysql_query("drop table poller");
mysql_query("drop table poller_vote");
mysql_query("drop table poller_option");

mysql_query("create table poller(ID int auto_increment not null primary key,
pollerTitle varchar(255))") or die(mysql_error());

mysql_query("create table poller_vote(
ID int auto_increment not null primary key,
optionID int(11),
ipAddress varchar(255))") or die(mysql_error());

mysql_query("create table poller_option(
ID int auto_increment not null primary key,
pollerID int(11),
optionText varchar(255),
pollerOrder int,
defaultChecked char(1) default 0)") or die(mysql_error());


mysql_query("insert into poller(ID,pollerTitle)values('1','How would you rate this script?')");

mysql_query("insert into poller_option(pollerID,optionText,pollerOrder,defaultChecked)values('1','Excellent','1','1')");
mysql_query("insert into poller_option(pollerID,optionText,pollerOrder)values('1','Very good','2')");
mysql_query("insert into poller_option(pollerID,optionText,pollerOrder)values('1','Good','3')");
mysql_query("insert into poller_option(pollerID,optionText,pollerOrder)values('1','Fair','3')");
mysql_query("insert into poller_option(pollerID,optionText,pollerOrder)values('1','Poor','4')");



