<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");

if (!isset($lang)) $lang='ru';

$log_error=999;

if (isset($user))
{
 if (!isset($maingrp)) $maingrp=$grp;

 if (!isset($target_page)  || $target_page=='' )
 {
 switch ($maingrp) {
                        case 0:  header ("Location: vprofile/index.php");
                        break;
                        case 1:  header ("Location: company.php");
                        break;
                        case 2:  header ("Location: admin/index.php");
                        break;
                        case 3:  header ("Location: risk_funds.php");
                        break;
                        case 4:  header ("Location: vprofile/index.php?type=virtual");
                        break;
                    }
   }
   else
   {
     header ("Location: ".$target_page);
   }

return;
}
{
 if (!isset($passwd))  $log_error=0; //first time login
}

if ($log_error!=0)
{
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

// check user

$crypt_password=crypt($passwd);

$query = "select
                 u.user_id
                ,u.password
                ,ifnull(u.ulock,-9999) ulock
                ,u.ugroup
                ,u.company_id
                ,c.rnn
         from ism_users u
         LEFT JOIN ism_customers c  ON u.user_id=c.user_id
         where user_name='".addslashes($login)."'";

//echo $query;
$vars=array();
$rc=sql_stmt($query, 6, $vars ,1);

if ($rc < 1)
  {
    //$str=echoNLS("Îøèáêà èäåíòèôèêàöèè, ïîâòîğèòå åùå ğàç","Identification's Error, try to repeat");
    //echo '<div align=center class=black10><a target="_self" href="/log_test.php">'.$str.'</a></div>';
    header ("Location: log_test.php?log_error=1");
  }
  else
  {

    //new user - have to change password
    if ($vars["ulock"][0] == -9999)
    {
      header ("Location: change_password.php?login=".$login);
      return;
    }


    $correct=true;
    if (crypt($passwd, $vars["password"][0]) != $vars["password"][0])
    {
       header ("Location: log_test.php?log_error=2");
       $correct=false;
    }

    if  ($vars["ulock"][0]==0 && $correct)
    {
       header ("Location: log_test.php?log_error=3");
       $correct=false;
    }

    if  ($orig_random_number!=$random_number && $correct)
    {
       header ("Location: log_test.php?log_error=4");
       $correct=false;
    }

    if ($correct)
    {
        $user=$login;
        session_set("user");
        $user_id=$vars["user_id"][0];
        session_set("user_id");
        $grp=$vars["ugroup"][0];
        session_set("grp");
        $comp_id=$vars["company_id"][0];
        session_set("comp_id");
        $comp_id=$vars["company_id"][0];
        session_set("comp_id");
        $rnn=$vars["rnn"][0];
        session_set("rnn");

        $query="
                    update ism_users set last_login=CURRENT_TIMESTAMP(),visit=visit+1
                    where user_id=".$user_id;
        $result=exec_query($query);

        if (!isset($target_page) || $target_page=='')
        {
        switch ($grp) {
                        case 0:  header ("Location: vprofile/index.php");
                        break;
                        case 1:  header ("Location: company.php");
                        break;
                        case 2:  header ("Location: admin/index.php");
                        break;
                        case 3:  header ("Location: risk_funds.php");
                        break;
                        case 4:  header ("Location: vprofile/index.php?type=virtual");
                        break;
                    }
       }
       else
       {
          header ("Location: ".$target_page);
       }

    }
  }
}
else
{
header ("Location: log_test.php");
}
?>