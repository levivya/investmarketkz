<?php
//
//   file:        update_virtual_account.php
//   description: This script updates virtual accounts every month via cron.

//include lib and conf file
include("../lib/misc.inc");

// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

//fund's table
$query="
          update ism_customers
          set virtual_account=virtual_account+planned_monthly_investment
          where user_id in (select user_id from ism_users where ugroup in (0,4))
       ";

$result=exec_query($query);
if ($result)
  {
   echo 'virtual accounts have been updated -'.date('d.m.Y');
  }


disconn($conn);


?>