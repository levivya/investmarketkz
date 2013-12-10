<?php
/******************************************************************************
  Power Banner Manager 1.5 !
  (admin.php file)

  Copyright Armin Kalajdzija, 2002.
  E-mail: kalajdzija@hotmail.com
  WebSite: http://www.ak85.tk
******************************************************************************/

session_start();

include "config.inc.php";

$program_version = "1.5";
$varcount = 0;
$bancount = 0;
$auth = false;

print "<style>";
print "A:VISITED, A:ACTIVEA:LINK {  ";
print "	color : $link_color;}";
print "A:HOVER {   ";
print "	color : $link_over_color;}   ";
print "</style>   "; ?>

<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function MM_jumpMenu(targ,selObj,restore,action,id) {
targ = "parent";
if (action == 1) {
  if (selObj.options[selObj.selectedIndex].value == 1) eval(targ+".location='admin.php?action=addban'");
  if (selObj.options[selObj.selectedIndex].value == 2) eval(targ+".location='admin.php?action=addban&type=2'");
  if (restore) selObj.selectedIndex=0;
}else if(action == 2) {
  eval(targ+".location='admin.php?action=view&part=stats_view&id="+id+"&month="+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
}
//  End -->
</script>

<?php
if (!session_is_registered('user_login')) {

   if (isset($user_login) and ($user_login <> "") and isset($user_pass) and ($user_pass <> "")) {
    if (isset($hostname) and isset($database) and isset($db_login) and isset($db_pass)) {
    $dbconn = mysql_connect($hostname, $db_login, $db_pass) or die("Could not connect");

    mysql_select_db($database) or die("Could not select database");

    $query = "SELECT login,password,permit,uid,language FROM powerban_auth WHERE login='$user_login'";
    $result = mysql_query($query) or die("Query failed");

    $line = mysql_fetch_array($result);

    if (strtolower($line[0]) == strtolower($user_login)) {
        if ($line[1] == (crypt($user_pass,$line[1]))) {
            $auth = true;
            $permit = $line[2];
            $uid = $line[3];
            $language = $line[4];
            session_register('user_login');
            session_register('user_pass');
            session_register('permit');
            session_register('uid');
            session_register('language');
            $date = date("Y-m-d h:i:s");
            $query = "UPDATE powerban_auth SET ip='$REMOTE_ADDR', date='$date' WHERE uid='$uid'";
            $result = mysql_query($query) or die("Query failed");
            // Selecting language from users settings
            include "languages/$language";
        }else{
            print "Wrong Password !";
        }
    }else{
        print "Wrong Login !";
    }
}
}
}else{
    $auth = true;
    include "languages/$language";
}

if (!$auth) {
print "<title>Power Banner Manager $program_version</title>";
print "<br><br><br><form name='forma' method='post' action='admin.php'>";
print "<table width='463' border='1' bordercolor='#32587F' align='center'>";
print "<tr bordercolor='#FFFFFF'><td colspan='2'>";
print "<div align='center'><font face='Trebuchet MS' size='2'><b><font size='3'>POWER BANNER ADMINISTRATION PANEL</font></b></font></div>";
print "</td></tr><tr valign='top' bordercolor='#FFFFFF'><td colspan='2' height='34'>";
print "<div align='center'><font face='Trebuchet MS' size='2'>PLEASE ENTER YOUR USER NAME AND PASSWORD</font></div>";
print "</td></tr><tr bordercolor='#FFFFFF'><td width='181'><div align='right'><font face='Trebuchet MS' size='2'>User Name:</font></div>";
print "</td><td width='272'><font face='Trebuchet MS' size='2'><input type='text' name='user_login'></font></td></tr>";
print "<tr bordercolor='#FFFFFF'><td width='181'><div align='right'><font face='Trebuchet MS' size='2'>Password:</font></div></td>";
print "<td width='272'><font face='Trebuchet MS' size='2'><input type='password' name='user_pass'></font></td></tr><tr bordercolor='#FFFFFF'>";
print "<td colspan='2' height='57'><div align='center'><font face='Trebuchet MS' size='2'><input type='submit' name='Submit' value='Enter Panel'></font></div>";
print "</td></tr></table></form>";

}

if ($auth) {
    $dbconn = mysql_connect($hostname, $db_login, $db_pass) or die("Could not connect");

    mysql_select_db($database) or die("Could not select database");

      if (isset($action) and ($action == "logout")) {
          session_destroy();
          print "<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=".$charset."'>";
          print "<p align='center'><table width='300' border='0'><tr>";
          print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/done.gif'></font></td>";
          print "<td width='433'><p align='center'><font face='Trebuchet MS' size='2'>$logout_main_text<br><a href='admin.php'>$logout_login_again_text</a></font></td></tr></table>";
          die;
      }
      include "header.inc.php";
      print "<title>$header_page_title_text $program_version</title>";
      print "<META HTTP-EQUIV='Content-Type' CONTENT='text/html; charset=".$charset."'>";
    if (isset($action)) {
         if ($action == "view") {

           $query = "SELECT * FROM powerban WHERE id='$id'";
           $result = mysql_query($query) or die("Query failed");

           $line = mysql_fetch_array($result);

                if (($permit > 1) and ($line[10] <> $uid)) {
                     print "<table width='495' border='0'><tr>";
                     print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
                     print "<td width='433'><font face='Trebuchet MS' size='2'>$list_no_permition_text</font></td></tr></table>";
                     die;
                }
                print "<table width='100%' border='0'><tr background='images/hpic2.gif'><td colspan='2'>";
                print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
                print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
                print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'>$line[0]</font></td>";
                print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table></td></tr>";
                print "<tr><td height='89'>";
                if ($line[6] == 1) {
                   print "<div align='center'><img src='$line[1]' width='468' height='60' alt='$line[2]'></div></td></tr>";
                }else if ($line[6] == 2) {
                   $swfdims = split('[x]',$line[3]);
                   print "<div align='center'><object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0' width='$swfdims[0]' height='$swfdims[1]'>";
                   print "<param name=movie value='$line[1]'>";
                   print "<param name=quality value=high>";
                   print "<embed src='$line[1]' quality=high pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash' width='$swfdims[0]' height='$swfdims[1]'>";
                   print "</embed></object></div>";
                }
                print "<tr><td height='89'><div align='center'><table width='75%' border='0'><tr>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$modify_banner_id_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>$id</td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_format_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                if ($line[6] == 1) {
                    print "$add_banner_picture_format_text";
                }else if ($line[6] == 2) {
                    print "$add_banner_flash_format_text";
                }
                print "</td></tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_name_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>$line[0]</td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_source_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>$line[1]</td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_alt_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>$line[2]</td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'>"; if ($line[6] == 1) { print "<b>$add_banner_url_text"; }else if ($line[6] == 2) { print "<b>$add_banner_flash_dim_text"; } print ":</td><td colspan='2'><font face='Trebuchet MS' size='2'>$line[3]</td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_zone_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                  if (($line[13] == "") or ($line[13] == 0)) {
                      print "$add_banner_zone_unsorted_text";
                  }else{
                      $query = "SELECT zname FROM powerban_zones WHERE zid='$line[13]'";
                      $result2 = mysql_query($query) or die("Query failed");
                      $line2 = mysql_fetch_array($result2);
                      print $line2[0];
                  }
                print "</td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_diplay_type_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                list($dtype,$location) = split('[|]',$line[12]);
                if ($dtype == 1) {
                    print "$add_banner_type_standard_text";
                }else if ($dtype == 2) {
                    print "$add_banner_type_popup_text";
                }else if ($dtype == 3) {
                    print "$add_banner_type_watermark_text";
                }
                print "</td>";
                if ($dtype == 3) {
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_wm_location_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                if ($location == 1) {
                    print "$add_banner_wn_top_left_text";
                }else if ($location == 2) {
                    print "$add_banner_wn_top_right_text";
                }else if ($location == 3) {
                    print "$add_banner_wn_bottom_left_text";
                }else if ($location == 4) {
                    print "$add_banner_wn_bottom_right_text";
                }
                print "</td>";
                }
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_target_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                if ($line[11] == "_self") {
                    print "$add_banner_target_current_text";
                }else if ($line[11] == "_blank") {
                    print "$add_banner_target_new_text";
                }
                print "</td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$info_banner_total_dis_text / $info_banner_of_text / $info_banner_procent_text:</td><td colspan='2'><font face='Trebuchet MS' size='2'>$line[8] / ";
                if ($line[7] == 0) {
                    print "$add_banner_times_unlimited_text / ";
                }else{
                    print $line[7]." / ";
                }
                $query = "SELECT SUM(dised_times) FROM powerban";
                $result = mysql_query($query) or die("Query failed");
                $line2 =  mysql_fetch_array($result);

                if ($line[8] <> 0) {
                   $procdistime = ($line[8] * 100) / $line2[0];
                   print round($procdistime,2)."% ($info_banner_of_all_text)";
                }else{
                   print "0%";
                }
                if ($line[6] <> 2) {
                print "</td></tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$info_banner_clicks_text / $info_banner_procent_text:</td><td colspan='2'><font face='Trebuchet MS' size='2'>$line[4] / ";
                if ($line[4] <> 0) {
                   $procclicktime = ($line[4] * 100) / $line[8];
                   print round($procclicktime,2)."% ($info_banner_of_dis_text)";
                }else{
                   print "0%";
                }
                }
                print "</tr><tr height=50 valign=bottom><td colspan='2'><font face='Trebuchet MS' size='2'><b>$info_banner_month_dis_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                print "<select name='monthly_dis' onpropertychange='MM_jumpMenu(0,this,0,2,$id,this)'>";
                print "<option value='0'>$info_banner_select_month_text</option>";
                $query2 = "SELECT date FROM powerban_stats_views WHERE id='$id' ORDER BY date DESC";
                $result2 = mysql_query($query2) or die("Query failed");
                $cdate = "";
                while ($views = mysql_fetch_array($result2, MYSQL_ASSOC)) {
                   if ($cdate <> $views['date']) {
                     $cdate = $views['date'];
                     print "<option value='$cdate'>";
                     $cyear = substr($cdate,0,4);
                     if (substr($cdate,5,2) == "01") {
                         print $cyear." - $info_banner_month_january";
                     }else if (substr($cdate,5,2) == "02") {
                         print $cyear." - $info_banner_month_february";
                     }else if (substr($cdate,5,2) == "03") {
                         print $cyear." - $info_banner_month_march";
                     }else if (substr($cdate,5,2) == "04") {
                         print $cyear." - $info_banner_month_april";
                     }else if (substr($cdate,5,2) == "05") {
                         print $cyear." - $info_banner_month_may";
                     }else if (substr($cdate,5,2) == "06") {
                         print $cyear." - $info_banner_month_june";
                     }else if (substr($cdate,5,2) == "07") {
                         print $cyear." - $info_banner_month_july";
                     }else if (substr($cdate,5,2) == "08") {
                         print $cyear." - $info_banner_month_august";
                     }else if (substr($cdate,5,2) == "09") {
                         print $cyear." - $info_banner_month_september";
                     }else if (substr($cdate,5,2) == "10") {
                         print $cyear." - $info_banner_month_october";
                     }else if (substr($cdate,5,2) == "11") {
                         print $cyear." - $info_banner_month_november";
                     }else if (substr($cdate,5,2) == "12") {
                         print $cyear." - $info_banner_month_december";
                     }
                     print "</option>";
                   }
                }
                print "</select>";
                print "</td>";
                if (isset($part) and ($part == "stats_view") and isset($id) and isset($month)) {
                   $montht = substr($month,0,7);
                   print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$info_banner_dis_on_month ".$montht."</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                   $query2 = "SELECT COUNT(date) FROM powerban_stats_views WHERE id=$id AND date LIKE '%$montht%'";
                   $result2 = mysql_query($query2) or die("Query failed");
                   $views = mysql_fetch_array($result2);
                   print "$views[0] / ";
                   if ($line[7] == 0) {
                    print "$add_banner_times_unlimited_text / ";
                    }else{
                    print $line[7]." / ";
                    }
                    if ($views[0] <> 0) {
                       $procdistime = ($views[0] * 100) / $line['dised_times'];
                       print round($procdistime,2)."% ($info_banner_of_total_dis_text)";
                    }else{
                       print "0%";
                    }
                   print "</td>";
                   }
                print "</td></tr></table></div></td></tr></table><br><br>";

                print "<p align='center'><table width='400' border='0'><tr><td width='128'>";
                print "<div align='center'><font face='Trebuchet MS' size='1'><a href='admin.php?action=edit&id=$id'><img src='images/modify.gif' width='29' height='29' border=0></a><br>$list_button_modify_text</font></div>";
                if ($line[6] == 1) {
                   print "</td><td width='121'><div align='center'><font face='Trebuchet MS' size='1'><a href='admin.php?action=stats&id=$id'><img src='images/visitinfo.gif' border=0></a><br>$list_button_visitor_text</font></div></td>";
                   print "<td width='121'><div align='center'><font face='Trebuchet MS' size='1'><a href='$line[3]' target='_blank'><img src='images/gotosite.gif' border=0></a><br>$list_button_go_to_site_text</font></div></td>";
                }
                print "<td width='121'><div align='center'><font face='Trebuchet MS' size='1'><a href='admin.php?action=del&id=$id'><img src='images/delete.gif' width='25' height='31' border=0></a><br>$list_button_delete_text</font></div></td></tr></table><br><br>";


         }else if (($action == "del") and ((!isset($sure)) or ($sure <> 1))) {
                $query = "SELECT uid FROM powerban WHERE id='$id'";
                $result = mysql_query($query) or die("Query failed");
                $line = mysql_fetch_array($result);

                if (($permit > 1) and ($line[0] <> $uid)) {
                     print "<table width='495' border='0'><tr>";
                     print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
                     print "<td width='433'><font face='Trebuchet MS' size='2'>$delete_banner_no_permition</font></td></tr></table>";
                     die;
                }
             print "<table width='495' border='0'><tr>";
             print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/sure.gif'></font></td>";
             print "<td width='433'><font face='Trebuchet MS' size='2'>$delete_banner_sure_text $id ? <a href='admin.php?action=del&id=$id&sure=1'>[YES]</a></font></td></tr></table>";

         }else if (($action == "del") and (isset($sure)) and ($sure == 1)) {
                $query = "SELECT uid FROM powerban WHERE id='$id'";
                $result = mysql_query($query) or die("Query failed");
                $line = mysql_fetch_array($result);

                if (($permit > 1) and ($line[0] <> $uid)) {
                     print "<table width='495' border='0'><tr>";
                     print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
                     print "<td width='433'><font face='Trebuchet MS' size='2'>$delete_banner_no_permition</font></td></tr></table>";
                     die;
                }
             $query = "DELETE FROM powerban WHERE id='$id'";
             $result = mysql_query($query) or die("Query failed");

             print "<table width='495' border='0'><tr>";
             print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/done.gif'></font></td>";
             print "<td width='433'><font face='Trebuchet MS' size='2'>$delete_banner_done_text $id !</font></td></tr></table>";

         }else if (($action == "deluser") and ((!isset($sure)) or ($sure <> 1))) {

             if ($permit > 1) {
                     print "<table width='495' border='0'><tr>";
                     print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
                     print "<td width='433'><font face='Trebuchet MS' size='2'>$users_delete_no_permition_text</font></td></tr></table>";
                     die;
                }
             print "<table width='495' border='0'><tr>";
             print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/sure.gif'></font></td>";
             print "<td width='433'><font face='Trebuchet MS' size='2'>$users_delete_sure_text $del_uid ?<br><a href='admin.php?action=deluser&del_uid=$del_uid&sure=1&deletebans=on'>$users_delete_dont_leave_banners_text</a><br><a href='admin.php?action=deluser&del_uid=$del_uid&sure=1'>$users_delete_leave_banners_text</a><br></font></td></tr></table>";

         }else if (($action == "deluser") and (isset($sure)) and ($sure == 1) and ($permit == 1)) {

             $query = "DELETE FROM powerban_auth WHERE uid='$del_uid'";
             $result = mysql_query($query) or die("Query failed");

             if (isset($deletebans) and ($deletebans == "on")) {
                $query = "DELETE FROM powerban WHERE uid='$del_uid'";
                $result = mysql_query($query) or die("Query failed");
             }else{
                $query = "UPDATE powerban SET uid= '1' WHERE uid='$del_uid'";
                $result = mysql_query($query) or die("Query failed");
             }

             print "<table width='495' border='0'><tr>";
             print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/done.gif'></font></td>";
             print "<td width='433'><font face='Trebuchet MS' size='2'>$del_uid $users_delete_done_text</font></td></tr></table>";

         }else if (($action == "edit") and (!isset($change))) {

             $query = "SELECT * FROM powerban WHERE id='$id'";
             $result = mysql_query($query) or die("Query failed");
             $line = mysql_fetch_array($result);

                if (($permit > 1) and ($line[10] <> $uid)) {
                     print "<table width='495' border='0'><tr>";
                     print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
                     print "<td width='433'><font face='Trebuchet MS' size='2'>$modify_banner_no_permition</font></td></tr></table>";
                     die;
                }
                print "<form name='change' method='post' action='admin.php'><table width='100%' border='0'><tr background='images/hpic2.gif'><td colspan='2'>";
                print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
                print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
                print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'>$line[0]</font></td>";
                print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table></td></tr>";
                print "<tr><td height='89'>";
                if ($line[6] == 1) {
                   print "<div align='center'><img src='$line[1]' width='468' height='60' alt='$line[2]'></div></td></tr>";
                }else if ($line[6] == 2) {
                   $swfdims = split('[x]',$line[3]);
                   print "<div align='center'><object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0' width='$swfdims[0]' height='$swfdims[1]'>";
                   print "<param name=movie value='$line[1]'>";
                   print "<param name=quality value=high>";
                   print "<embed src='$line[1]' quality=high pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash' width='$swfdims[0]' height='$swfdims[1]'>";
                   print "</embed></object></div>";
                }
                print "<tr><td height='89'><div align='center'><table width='75%' border='0'><tr>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'>$modify_banner_id_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>$id</td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_format_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                if ($line[6] == 1) {
                   print "$add_banner_picture_format_text";
                }else if ($line[6] == 2) {
                   print "$add_banner_flash_format_text";
                }
                print "</td></tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_name_text</td><td colspan='2'><font face='Trebuchet MS' size='2'><input type='text' name='new_name' size='60' value='$line[0]'></td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_source_text</td><td colspan='2'><font face='Trebuchet MS' size='2'><input type='text' name='new_src' size='60' value='$line[1]'></td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'>$add_banner_alt_text</td><td colspan='2'><font face='Trebuchet MS' size='2'><input type='text' name='new_alt' size='60' value='$line[2]'></td>";
                if ($line[6] == 2) {
                    $swfdims = split('[x]',$line[3]);
                }
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>"; if ($line[6] == 1) { print "URL"; }else if ($line[6] == 2) { print "Width x Height"; }
                if ($line[6] == 2) {
                   print ":</td><td colspan='2'><font face='Trebuchet MS' size='2'><input type='text' name='new_url1' size='20' value='$swfdims[0]'> x <input type='text' name='new_url2' size='20' value='$swfdims[1]'></td>";
                }else{
                   print ":</td><td colspan='2'><font face='Trebuchet MS' size='2'><input type='text' name='new_url' size='60' value='$line[3]'>";
                }
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'>$add_banner_zone_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                print "<select name='new_ban_zone'>";
                  print "<option value='0'"; if (($line[13] == 0) or ($line[13] == "")) { print "selected"; } print ">$add_banner_zone_unsorted_text</option>";
                  $query = "SELECT zname,zid,uid FROM powerban_zones";
                  $result2 = mysql_query($query) or die("Query failed");
                  while ($line2 = mysql_fetch_array($result2)) {
                    if ($line2[2] == $uid) {
                       print "<option value='$line2[1]'"; if ($line2[1] == $line[13]) { print "selected"; } print ">$line2[0]</option>";
                    }
                  }
                print "</select> ($add_banner_zone_select_text)";
                print "</td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'>$add_banner_times_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                print "<select name='new_dis_times'>";
                print "<option value='0'"; if ($line[7] == 0) { print "selected"; } print ">$add_banner_times_unlimited_text</option>";
                print "<option value='EV' selected>$add_banner_enter_value_combo_text</option>";
                print "<option value='100'"; if ($line[7] == 100) { print "selected"; } print ">100</option>";
                print "<option value='200'"; if ($line[7] == 200) { print "selected"; } print ">200</option>";
                print "<option value='300'"; if ($line[7] == 300) { print "selected"; } print ">300</option>";
                print "<option value='400'"; if ($line[7] == 400) { print "selected"; } print ">400</option>";
                print "<option value='500'"; if ($line[7] == 500) { print "selected"; } print ">500</option>";
                print "<option value='600'"; if ($line[7] == 600) { print "selected"; } print ">600</option>";
                print "<option value='700'"; if ($line[7] == 700) { print "selected"; } print ">700</option>";
                print "<option value='800'"; if ($line[7] == 800) { print "selected"; } print ">800</option>";
                print "<option value='900'"; if ($line[7] == 900) { print "selected"; } print ">900</option>";
                print "<option value='1000'";
                if ($line[7] == 1000) {
                    print " selected";
                    print ">1000</option></select>";
                    print " $add_banner_enter_value_text <input type='text' name='new_dis_times_ev' size='29' value=$line[7]>";
                }else{
                    print ">1000</option></select>";
                    print " $add_banner_enter_value_text <input type='text' name='new_dis_times_ev' size='29' value=$line[7]>";
                }
                print "</tr><tr><td colspan='2' height=40 valign=bottom><font face='Trebuchet MS' size='2'>$add_banner_diplay_type_text</td><td colspan='2' valign=bottom>";
                list($dtype,$location) = split('[|]',$line[12]);
                print "<select name='new_dis_type'>";
                print "<option value='1'"; if ($dtype == 1) { print "selected"; } print ">$add_banner_type_standard_text</option>";
                print "<option value='2'"; if ($dtype == 2) { print "selected"; } print ">$add_banner_type_popup_text</option>";
                print "<option value='3'"; if ($dtype == 3) { print "selected"; } print ">$add_banner_type_watermark_text</option>";
                print "</select>";
                print "<font face='Trebuchet MS' size='2'> $add_banner_wm_location_text ";
                print "<select name='new_dis_type_loc'>";
                print "<option value='1'"; if ($location == 1) { print "selected"; } print ">$add_banner_wn_top_left_text</option>";
                print "<option value='2'"; if ($location == 2) { print "selected"; } print ">$add_banner_wn_top_right_text</option>";
                print "<option value='3'"; if ($location == 3) { print "selected"; } print ">$add_banner_wn_bottom_left_text</option>";
                print "<option value='3'"; if ($location == 4) { print "selected"; } print ">$add_banner_wn_bottom_right_text</option>";
                print "</select>";
                print "</td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'>$add_banner_target_text</td><td colspan='2' valign=bottom>";
                print "<select name='new_target'>";
                print "<option value='_self'"; if ($line[11] == "_self") { print "selected"; } print ">$add_banner_target_current_text</option>";
                print "<option value='_blank'"; if ($line[11] == "_blank") { print "selected"; } print ">$add_banner_target_new_text</option>";
                print "</select></td>";
                print "</td></tr></table></div></td></tr></table><br>";
                print "<input type='hidden' name='id' value=$id>";
                print "<input type='hidden' name='action' value='edit'>";
                print "<input type='hidden' name='change' value=1>";
                print "<p align='center'><input type='submit' name='subchange' value='$modify_banner_button_text'></form><br>";

         }else if (($action == "adduser") and ($permit == 1) and (!isset($doadd))) {
                print "<form name='adduser' method='post' action='admin.php'><table width='100%' border='0'><tr background='images/hpic2.gif'><td colspan='2'>";
                print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
                print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
                print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'>$users_add_title_text</font></td>";
                print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table></td></tr>";
                print "<tr><td height='89'><div align='center'><table width='75%' border='0'><tr>";
                print "</td></tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$users_add_login_text</td><td colspan='2'><font face='Trebuchet MS' size='2'><input type='text' name='new_login' size='40'></td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$users_add_password_text</td><td colspan='2'><font face='Trebuchet MS' size='2'><input type='password' name='new_pass' size='40'></td>";
                print "</tr><tr><td colspan='2'><b><font face='Trebuchet MS' size='2'>$users_add_permition_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                print "<select name='new_permit'>";
                print "<option value='2' selected>$users_normal_user_text</option>";
                print "<option value='1'>$users_admin_text</option>";
                print "</select>";
                print "</td></tr></table></div></td></tr></table><br>";
                print "<input type='hidden' name='action' value='adduser'>";
                print "<input type='hidden' name='doadd' value=1>";
                print "<p align='center'><input type='submit' name='subadd' value='$users_add_button_text'></form><br>";

         }else if (($action == "addban") and (!isset($doadd))) {

                print "<form name='change' method='post' action='admin.php'><table width='100%' border='0'><tr background='images/hpic2.gif'><td colspan='2'>";
                print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
                print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
                print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'>$add_banner_text</font></td>";
                print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table></td></tr>";
                print "<tr><td height='89'><div align='center'><table width='75%' border='0'><tr>";
                print "</tr><tr><td colspan='2'><b><font face='Trebuchet MS' size='2'>$add_banner_format_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                print "<select name='new_type'  onChange='MM_jumpMenu(0,this,0,1)'>";
                if (!isset($type)) {
                   print "<option value='1' selected>$add_banner_picture_format_text</option>";
                   print "<option value='2'>$add_banner_flash_format_text</option>";
                }else{
                   print "<option value='1'>$add_banner_picture_format_text</option>";
                   print "<option value='2' selected>$add_banner_flash_format_text</option>";
                }
                print "</select>";
                print "</td></tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_name_text</td><td colspan='2'><font face='Trebuchet MS' size='2'><input type='text' name='new_name' size='60'></td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>$add_banner_source_text</td><td colspan='2'><font face='Trebuchet MS' size='2'><input type='text' name='new_src' size='60' value='http://'></td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'>$add_banner_alt_text</td><td colspan='2'><font face='Trebuchet MS' size='2'><input type='text' name='new_alt' size='60'></td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'><b>"; if (!isset($type)) { print $add_banner_url_text; }else if ($type == 2) { print $add_banner_flash_dim_text; }
                if (isset($type) and ($type == 2)) {
                   print ":</td><td colspan='2'><font face='Trebuchet MS' size='2'><input type='text' name='new_url1' size='20'> x <input type='text' name='new_url2' size='20'></td>";
                }else{
                   print ":</td><td colspan='2'><font face='Trebuchet MS' size='2'><input type='text' name='new_url' size='60' value='http://'>";
                }
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'>$add_banner_zone_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                print "<select name='new_ban_zone'>";
                print "<option value='0' selected>$add_banner_zone_unsorted_text</option>";
                $query = "SELECT zname,zid,uid FROM powerban_zones";
                $result2 = mysql_query($query) or die("Query failed");
                while ($line2 = mysql_fetch_array($result2)) {
                  if ($line2[2] == $uid) {
                     print "<option value='$line2[1]'>$line2[0]</option>";
                  }
                }
                print "</select> ($add_banner_zone_select_text, <a href='admin.php?action=zones'>$add_banner_zone_add_zone_text</a>)";
                print "</td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'>$add_banner_times_text</td><td colspan='2'><font face='Trebuchet MS' size='2'>";
                print "<select name='new_dis_times'>";
                print "<option value='0'>$add_banner_times_unlimited_text</option>";
                print "<option value='EV'>$add_banner_enter_value_combo_text</option>";
                print "<option value='100'>100</option>";
                print "<option value='200'>200</option>";
                print "<option value='300'>300</option>";
                print "<option value='400'>400</option>";
                print "<option value='500'>500</option>";
                print "<option value='600'>600</option>";
                print "<option value='700'>700</option>";
                print "<option value='800'>800</option>";
                print "<option value='900'>900</option>";
                print "<option value='1000'>1000</option>";
                print "</select> $add_banner_enter_value_text <input type='text' name='new_dis_times_ev' size='29' value=''>";
                print "</tr><tr><td colspan='2' height=40 valign=bottom><font face='Trebuchet MS' size='2'>$add_banner_diplay_type_text</td><td colspan='2' valign=bottom>";
                print "<select name='new_dis_type' onChange='MM_jumpMenu(0,this,0,3)'>";
                print "<option value='1'>$add_banner_type_standard_text</option>";
                print "<option value='2'>$add_banner_type_popup_text</option>";
                print "<option value='3'>$add_banner_type_watermark_text</option>";
                print "</select>";
                print "<font face='Trebuchet MS' size='2'> $add_banner_wm_location_text ";
                print "<select name='new_dis_type_loc'>";
                print "<option value='1'>$add_banner_wn_top_left_text</option>";
                print "<option value='2'>$add_banner_wn_top_right_text</option>";
                print "<option value='3'>$add_banner_wn_bottom_left_text</option>";
                print "<option value='4'>$add_banner_wn_bottom_right_text</option>";
                print "</select>";
                print "</td>";
                print "</tr><tr><td colspan='2'><font face='Trebuchet MS' size='2'>$add_banner_target_text</td><td colspan='2' valign=bottom>";
                print "<select name='new_target'>";
                print "<option value='_self'>$add_banner_target_current_text</option>";
                print "<option value='_blank' selected>$add_banner_target_new_text</option>";
                print "</select></td>";
                print "</td></tr></table></div></td></tr></table><br>";
                print "<input type='hidden' name='action' value='add'>";
                print "<input type='hidden' name='doadd' value=1>";
                print "<p align='center'><input type='submit' name='subadd' value='$add_banner_button_text'></form><br>";

         }else if (isset($change) and ($change == 1)) {

                $query = "SELECT uid FROM powerban WHERE id='$id'";
                $result = mysql_query($query) or die("Query failed");
                $line = mysql_fetch_array($result);

                if (($permit > 1) and ($line[0] <> $uid)) {
                     print "<table width='495' border='0'><tr>";
                     print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
                     print "<td width='433'><font face='Trebuchet MS' size='2'>$modify_banner_no_permition</font></td></tr></table>";
                     die;
                }
                  if (($new_name <> "") and ($new_src <> "") and ((isset($new_url) and ($new_url <> "")) or (isset($new_url1) and ($new_url1 <> "") and isset($new_url2) and ($new_url2 <> "")))) {

                     if (isset($new_url1)) {
                         $new_url = $new_url1."x".$new_url2;
                     }
                     if (isset($new_dis_times) and ($new_dis_times == "EV")) {
                        $new_dis_times = $new_dis_times_ev;
                     }
                     if (($new_target == '_self') and ($new_dis_type == 2)) {
                         $new_target = '_blank';
                     }
                     if ($new_dis_type == 3) {
                        $new_dis_type = $new_dis_type."|".$new_dis_type_loc;
                     }else{
                        $new_dis_type = $new_dis_type."|0";
                     }

                     $query = "UPDATE powerban SET src='$new_src' , alt='$new_alt', url='$new_url', name='$new_name', dis_times='$new_dis_times', target='$new_target', dtype='$new_dis_type', zone='$new_ban_zone' WHERE id='$id'";
                     $result = mysql_query($query) or die("Query failed");

                     print "<table width='495' border='0'><tr>";
                     print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/done.gif'></font></td>";
                     print "<td width='433'><font face='Trebuchet MS' size='2'>$modify_banner_done_text $id !</few_type',ont></td></tr></table>";
                  }else{
                     print "<table width='495' border='0'><tr>";
                     print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
                     print "<td width='433'><font face='Trebuchet MS' size='2'>$add_banner_missing_fields_text</font></td></tr></table>";
                  }

         }else if (isset($doadd) and ($doadd == 1) and ($action == "add")) {     // do add action .. goes after add :)
          if (($new_name <> "") and ($new_src <> "") and ((isset($new_url) and ($new_url <> "")) or (isset($new_url1) and ($new_url1 <> "") and isset($new_url2) and ($new_url2 <> ""))) and ($new_type <> "")) {
          if (isset($new_url1)) {
             $new_url = $new_url1."x".$new_url2;
          }
          $id = rand(1,9999);

          $query = "SELECT url FROM powerban WHERE id='$id'";
          $result = mysql_query($query) or die("Query failed");

          $line = mysql_fetch_array($result);

          while ($line[0] <> "") {
          $id = rand(1,9999);

          $query = "SELECT url FROM powerban WHERE id='$id'";
          $result = mysql_query($query) or die("Query failed");

          $line = mysql_fetch_array($result);
          }
          $query = "SELECT MAX(added) FROM powerban";
          $result = mysql_query($query) or die("Query failed");

          $line = mysql_fetch_array($result);
          $new_added = $line[0] + 1;

          if (isset($new_dis_times) and ($new_dis_times == "EV")) {
              $new_dis_times = $new_dis_times_ev;
          }
          if ($new_dis_type == 3) {
            $new_dis_type = $new_dis_type."|".$new_dis_type_loc;
          }else{
            $new_dis_type = $new_dis_type."|0";
          }

          $query = "INSERT INTO powerban (src, alt, url, id, name, type, dis_times, added, uid, dtype, target, zone) VALUES ('$new_src','$new_alt','$new_url','$id','$new_name','$new_type','$new_dis_times', '$new_added', '$uid', '$new_dis_type', '$new_target', '$new_ban_zone')";
          $result = mysql_query($query) or die("Query failed");

          print "<table width='495' border='0'><tr>";
          print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/done.gif'></font></td>";
          print "<td width='433'><font face='Trebuchet MS' size='2'>$add_banner_done_text (ID: $id) !</font></td></tr></table>";
          }else{
             print "<table width='495' border='0'><tr>";
             print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
             print "<td width='433'><font face='Trebuchet MS' size='2'>$add_banner_missing_fields_text</font></td></tr></table>";
          }

          }else if (($action == "adduser") and (isset($doadd)) and ($permit == 1)) {
             if (($new_login <> "") and ($new_pass <> "") and ($new_permit <> "")) {
             $new_pass = crypt($new_pass);
             $new_uid = rand(1,999);

             $query = "SELECT permit FROM powerban_auth WHERE uid='$new_uid'";
             $result = mysql_query($query) or die("Query failed");

             $line = mysql_fetch_array($result);

             while ($line[0] <> "") {
             $id = rand(1,999);

             $query = "SELECT permit FROM powerban_auth WHERE uid='$new_uid'";
             $result = mysql_query($query) or die("Query failed");

             $line = mysql_fetch_array($result);
             }
             $query = "INSERT INTO powerban_auth (login, password, permit, uid, language) VALUES ('$new_login', '$new_pass', '$new_permit', '$new_uid', 'english.inc.php')";
             $result = mysql_query($query) or die("Query failed");

             print "<table width='495' border='0'><tr>";
             print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/done.gif'></font></td>";
             print "<td width='433'><font face='Trebuchet MS' size='2'>$users_add_done_text ($users_id_text $new_uid) !</font></td></tr></table>";
             }else{
               print "<table width='495' border='0'><tr>";
               print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
               print "<td width='433'><font face='Trebuchet MS' size='2'>$users_add_fill_all_text</font></td></tr></table>";

             }

          }else if (($action == "chpass") and (!isset($chpass))) {
          print "<form name='chpass' method='post' action='admin.php'>";
          print "<table width='100%' border='0'><tr background='images/hpic2.gif'><td colspan='2'>";
          print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
          print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
          print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'>$users_change_pass_title_text</font></td>";
          print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table></td></tr>";
          print "<tr><td height='89'>";
          if (isset($chpass_uid) and ($chpass_uid <> "") and ($permit == 1)) {
             print "<font face='Verdana' size='2'>$users_change_pass_enter_text ($users_id_text $chpass_uid): <input type='password' name='new_user_pass'><br>";
          }else{
             print "<font face='Verdana' size='2'>$users_change_pass_enter_text: <input type='password' name='new_user_pass'><br>";
          }
          print "$users_change_pass_enter_again_text: </font><input type='password' name='new_user_pass2'><br>";
          print "<input type='hidden' name='action' value='chpass'>";
          print "<input type='hidden' name='chpass' value='1'><br>";
          if (isset($chpass_uid)) {
             print "<input type='hidden' name='chpass_uid' value='$chpass_uid'><br>";
          }
          print "<input type='submit' name='chpassw' value='$users_change_pass_button_text'>";

          }else if ((isset($chpass) and ($chpass == 1) and ($action == "chpass"))) {
             if ($new_user_pass == $new_user_pass2) {
                $new_user_pass3 = crypt($new_user_pass);

                $query = "UPDATE powerban_auth SET password='$new_user_pass3' WHERE";
                if (isset($chpass_uid) and ($chpass_uid <> "") and ($permit == 1)) {
                    $query = $query." uid='$chpass_uid'";
                }else{
                    $query = $query." login='$user_login'";
                }
                $result = mysql_query($query) or die("Query failed");
                print "<table width='495' border='0'><tr>";
                print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/done.gif'></font></td>";
                print "<td width='433'><font face='Trebuchet MS' size='2'>$users_change_pass_done_text</font></td></tr></table>";
             }else{
                print "<table width='495' border='0'><tr>";
                print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
                print "<td width='433'><font face='Trebuchet MS' size='2'>$users_change_pass_dont_match_text</font></td></tr></table>";

             }
          }else if ($action == "stats") {
          $query = "SELECT address,agent,datetime,referer FROM powerban_stats_visits where id='$id'";
          $result = mysql_query($query) or die("Query failed");
          print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
          print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
          print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'>$visitor_title_text</font></td>";
          print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table></td></tr>";

          print "<table width='100%' border='1' bordercolor='#32587F' cellpadding='0' cellspacing='0'>\n";
          print "<tr>";
          print "<td width='150' height='20' bordercolor='#FFFFFF'><font face='Verdana' size='2'>$visitor_address_text</font></td>";
          print "<td width='350' bordercolor='#FFFFFF'><font face='Verdana' size='2'>$visitor_browser_text</font></td>";
          print "<td width='120' bordercolor='#FFFFFF'><font face='Verdana' size='2'>$visitor_date_time_text</font></td>";
          print "<td width='250' bordercolor='#FFFFFF'><font face='Verdana' size='2'>$visitor_ref_by_text</font></td>";
          print "</tr>";

          while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
          print "\t<tr>\n";
             foreach ($line as $col_value) {
                print "\t\t<td height='20' bordercolor='#FFFFFF'><font face='Verdana' size='2'>$col_value</font></td>\n";
             }
          print "\t</tr>\n";
          }
          print "</table>\n";
          }else if ($action == "search") {
              print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
              print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
              print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'>$search_title_text</font></td>";
              print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table>";
              print "<form name='search' method='post' action='admin.php'>";
              print "<p ";
              if (isset($chardir) and ($chardir <> "")) {
                 print "dir='rtl' ";
              }
              print "align='center'><table width='700' border='0'><tr>";
              print "<td width='8%'><font face='Trebuchet MS' size='2'>$search_by_text</font></td>";
              print "<td width='12%'><font face='Trebuchet MS' size='2'>";
              print "<select name='search_type'>";
              print "<option value='1' selected>$search_by_banner_id_text</option>";
              print "<option value='2'>$search_by_banner_name_text</option>";
              print "<option value='3'>$search_by_banner_url_text</option>";
              print "</select></font></td>";
              print "<td width='4%'><font face='Trebuchet MS' size='2'>$search_something_like_text</font></td>";
              print "<td width='32%'><font face='Trebuchet MS' size='2'>";
              print "<input type='text' name='search_text' size='50'></font></td> ";
              print "<td width='9%'><font face='Trebuchet MS' size='2'>";
              print "<input type='submit' name='Submit' value='$search_button_text'></font></td> ";
              print "<td width='10%'><font face='Trebuchet MS' size='2'>  ";
              print "<input type='reset' name='Submit2' value='$search_reset_text'></font></td></tr></table><input type='hidden' name='search' value='1'></form>";
          }else if ($action == "setup") {
              if (!isset($part)) {
              print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
              print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
              print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'>$options_title_text</font></td>";
              print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table>";
              print "<table width='100%' border='0'><tr><td width='65%'>";
              include "readme.inc.php";
              print "</td><td width='35%' valign='top'><br>";
              print "<p ";
              if (isset($chardir) and ($chardir <> "")) {
                 print "dir='rtl' ";
              }
              print "align=center><font face='Trebuchet MS' size='2'>$options_chose_language_text";
              print "<form name='change_language' method='post' action='admin.php'><select name='new_language' size='10'>";
              $opendir = $scriptdir."languages";
              if ($dir = @opendir($opendir)) {
                 while (($file = readdir($dir)) !== false) {
                   if (($file <> ".") and ($file <> "..")) {
                      echo "<option value='$file'>$file</option>";
                   }
                 }
                 closedir($dir);
              }
              print "<input type='hidden' name='action' value='setup'>";
              print "<input type='hidden' name='part' value='change_lan'>";
              print "</select><br><br><input type='submit' name='change_language_button' value='$options_button_change_text'></form>";
              print $author_comments;
              print "</td></tr></table>";
              }else if (isset($part) and ($part == "change_lan")) {
                 if ($new_language <> "") {
                 $query = "UPDATE powerban_auth SET language='$new_language' WHERE uid='$uid'";
                 $result = mysql_query($query) or die("Query failed");

                 print "<table width='495' border='0'><tr>";
                 print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/done.gif'></font></td>";
                 print "<td width='433'><font face='Trebuchet MS' size='2'>$options_language_changed_text</font></td></tr></table>";
              }
              }
          }else if (($action == "zones") and (!isset($zadd)) and !isset($part)) {
              print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
              print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
              print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'>$zones_add_title_text</font></td>";
              print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table>";
              print "<form name='frm_new_zone' method='post' action='admin.php'>";
              print "<p ";
              if (isset($chardir) and ($chardir <> "")) {
                 print "dir='rtl' ";
              }
              print "align=center><table width='376' border='0'><tr>";
              print "<td width='131' height='43'><font face='Trebuchet MS' size='2'>$zones_add_new_zone_name_text</font></td>";
              print "<td width='235' height='43'><font face='Trebuchet MS' size='2'>";
              print "<input type='text' name='new_zone_name' size='40'></font></td></tr><tr>";
              print "<td colspan='2'><div align='center'><font face='Trebuchet MS' size='2'>";
              print "<input type='hidden' name='action' value='zones'>";
              print "<input type='hidden' name='zadd' value='1'>";
              print "<input type='submit' name='btn_add_zone' value='$zones_add_zone_add_button_text'></font></div>";
              print "</td></tr></table></form>";
                if ($permit <> 1) {
                   $query = "SELECT zname, uid, zid FROM powerban_zones WHERE uid='$uid'";
                }else{
                   $query = "SELECT zname, uid, zid FROM powerban_zones";
                }
                $result = mysql_query($query) or die("Query failed");
                $lcount = 0;
                print "<table width='100%' border='0'><tr background='images/hpic2.gif'><td colspan='2'>";
                print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
                print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
                print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'>$zones_list_zones_text</font></td>";
                print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table></td></tr>";
                print "<tr><td height='89'><div align='center'><table width='100%' border='0'><tr>";
                print "<td width='15%'><font face='Trebuchet MS' size='2'><b>$zones_list_zone_name_text</font></td>";
                print "<td width='15%'><font face='Trebuchet MS' size='2'><b>$zones_list_user_text</font></td>";
                print "<td width='9%'><font face='Trebuchet MS' size='2'><b>$zones_list_zone_id_text</font></td>";
                print "<td width='9%'><font face='Trebuchet MS' size='2'><b>$zones_list_modify_text:</font></td>";
                print "<td width='9%'><font face='Trebuchet MS' size='2'><b>$zones_list_delete_text:</font></td>";
                while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                      print "<tr>";
                      foreach ($line as $line2[$lcount]) {
                        if ($lcount == 0) {
                           print "<td width='35%'><font face='Trebuchet MS' size='2'>$line2[0]</font></td>";
                        }else if ($lcount == 1) {
                           $query3 = "SELECT login FROM powerban_auth WHERE uid='$line2[1]'";
                           $result3 = mysql_query($query3) or die("Query failed");

                           $line3 = mysql_fetch_array($result3);
                           print "<td width='15%'><font face='Trebuchet MS' size='2'>$line3[0]</font></td>";
                        }else if ($lcount == 2) {
                           print "<td width='10%'><font face='Trebuchet MS' size='2'>$line2[2]</font></td>";
                        }
                        $lcount = $lcount + 1;
                      }
                      print "<td width='5%'><font face='Trebuchet MS' size='2'><a href='admin.php?action=zones&part=modify&zid=$line2[2]'>$zones_list_modify_text</a></font></td>";
                      print "<td width='5%'><font face='Trebuchet MS' size='2'><a href='admin.php?action=zones&part=delete&zid=$line2[2]'>$zones_list_delete_text</a></font></td>";
                      print "</tr>";
                      $lcount = 0;
                }
                print "</table>";
          }else if (($action == "zones") and (isset($zadd))) {
              if (isset($new_zone_name) and ($new_zone_name <> "")) {
                 $zid = rand(1,9999);

                 $query = "SELECT uid FROM powerban_zones WHERE zid='$zid'";
                 $result = mysql_query($query) or die("Query failed");

                 $line = mysql_fetch_array($result);

                 while ($line[0] <> "") {
                 $zid = rand(1,9999);

                 $query = "SELECT uid FROM powerban_zones WHERE zid='$zid'";
                 $result = mysql_query($query) or die("Query failed");

                 $line = mysql_fetch_array($result);
                 }

                 $query = "INSERT INTO powerban_zones (zid, zname, uid) VALUES ('$zid', '$new_zone_name', '$uid')";
                 $result = mysql_query($query) or die("Query failed");

                 print "<table width='495' border='0'><tr>";
                 print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/done.gif'></font></td>";
                 print "<td width='433'><font face='Trebuchet MS' size='2'>$zones_new_added_text (ID: $zid) !</font></td></tr></table>";

              }else{
                print "<table width='495' border='0'><tr>";
                print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
                print "<td width='433'><font face='Trebuchet MS' size='2'>$add_banner_missing_fields_text</font></td></tr></table>";
              }
          }else if (($action == "zones") and (isset($part))) {
                if (($part == "delete") and !isset($sure)) {
                   $query = "SELECT uid FROM powerban_zones WHERE zid='$zid'";
                   $result = mysql_query($query) or die("Query failed");
                   $line = mysql_fetch_array($result);

                if (($permit > 1) and ($line[0] <> $uid)) {
                     print "<table width='495' border='0'><tr>";
                     print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
                     print "<td width='433'><font face='Trebuchet MS' size='2'>$zone_delete_no_permition</font></td></tr></table>";
                     die;
                }
                print "<table width='495' border='0'><tr>";
                print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/sure.gif'></font></td>";
                print "<td width='433'><font face='Trebuchet MS' size='2'>$zone_delete_sure_text $zid ? <a href='admin.php?action=zones&zid=$zid&sure=1&part=delete'>[YES]</a></font></td></tr></table>";

                }else if (($part == "delete") and isset($sure)) {
                   $query = "DELETE FROM powerban_zones WHERE zid='$zid'";
                   $result = mysql_query($query) or die("Query failed");

                   $query = "UPDATE powerban SET zone='0' WHERE zone='$zid'";
                   $result = mysql_query($query) or die("Query failed");

                   print "<table width='495' border='0'><tr>";
                   print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/done.gif'></font></td>";
                   print "<td width='433'><font face='Trebuchet MS' size='2'>$zone_delete_done_text $zid !</font></td></tr></table>";

                }else if (($part == "modify") and !isset($zmodify)) {
                   $query = "SELECT uid FROM powerban_zones WHERE zid='$zid'";
                   $result = mysql_query($query) or die("Query failed");
                   $line = mysql_fetch_array($result);

                if (($permit > 1) and ($line[0] <> $uid)) {
                     print "<table width='495' border='0'><tr>";
                     print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
                     print "<td width='433'><font face='Trebuchet MS' size='2'>$zone_modify_no_permition_text</font></td></tr></table>";
                     die;
                }
                   $query = "SELECT zname FROM powerban_zones WHERE zid='$zid'";
                   $result = mysql_query($query) or die("Query failed");
                   $line = mysql_fetch_array($result);

                   print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
                   print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
                   print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'>$zone_modify_title_text</font></td>";
                   print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table>";
                   print "<form name='frm_modify_zone' method='post' action='admin.php'>";
                   print "<p align=center><table width='376' border='0'><tr>";
                   print "<td width='161' height='43'><font face='Trebuchet MS' size='2'>$zone_modify_zone_name_text</font></td>";
                   print "<td width='205' height='43'><font face='Trebuchet MS' size='2'>";
                   print "<input type='text' name='new_zone_name' size='40' value='$line[0]'></font></td></tr><tr>";
                   print "<td colspan='2'><div align='center'><font face='Trebuchet MS' size='2'>";
                   print "<input type='hidden' name='action' value='zones'>";
                   print "<input type='hidden' name='part' value='modify'>";
                   print "<input type='hidden' name='zid' value='$zid'>";
                   print "<input type='hidden' name='zmodify' value='1'>";
                   print "<input type='submit' name='btn_add_zone' value='$zone_modify_button_text'></font></div>";
                   print "</td></tr></table></form>";

                }else if (($part == "modify") and isset($zmodify)) {
                   $query = "SELECT uid FROM powerban_zones WHERE zid='$zid'";
                   $result = mysql_query($query) or die("Query failed");
                   $line = mysql_fetch_array($result);

                if (($permit > 1) and ($line[0] <> $uid)) {
                     print "<table width='495' border='0'><tr>";
                     print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/error.gif'></font></td>";
                     print "<td width='433'><font face='Trebuchet MS' size='2'>$zone_modify_no_permition_text</font></td></tr></table>";
                     die;
                }

                   $query = "UPDATE powerban_zones SET zname='$new_zone_name' WHERE zid='$zid'";
                   $result = mysql_query($query) or die("Query failed");

                   print "<table width='495' border='0'><tr>";
                   print "<td width='52'><font face='Trebuchet MS' size='2'><img src='images/done.gif'></font></td>";
                   print "<td width='433'><font face='Trebuchet MS' size='2'>$zone_modify_done_text $zid !</font></td></tr></table>";
                }

          }else if ($action == "users") {
                $query = "SELECT * FROM powerban_auth";
                $result = mysql_query($query) or die("Query failed");
                print "<table width='100%' border='0'><tr background='images/hpic2.gif'><td colspan='2'>";
                print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
                print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
                print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'>$users_title_text</font></td>";
                print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table></td></tr>";
                print "<tr><td height='89'><div align='center'><table width='100%' border='0'><tr>";
                print "<td width='15%'><font face='Trebuchet MS' size='2'><b>$users_login_text</font></td>";
                print "<td width='12%'><font face='Trebuchet MS' size='2'><b>$users_permition_text</font></td>";
                print "<td width='10%'><font face='Trebuchet MS' size='2'><b>$users_id_text</font></td>";
                print "<td width='22%'><font face='Trebuchet MS' size='2'><b>$users_last_ip_text</font></td>";
                print "<td width='20%'><font face='Trebuchet MS' size='2'><b>$users_last_time_text</font></td>";
                print "<td width='13%'><font face='Trebuchet MS' size='2'><b>$users_change_pass_text:</font></td>";
                print "<td width='13%'><font face='Trebuchet MS' size='2'><b>$users_delete_text:</font></td></tr>";
                while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                      foreach ($line as $col_value[$varcount]) {
                      if ($varcount == 5) {
                          print "<tr><td width='15%'><font face='Trebuchet MS' size='2'>$col_value[0]</font></td>";
                          print "<td width='12%'><font face='Trebuchet MS' size='2'>";
                          if ($col_value[4] > 1) {
                              print "$users_normal_user_text";
                          }else if ($col_value[4] = 1) {
                              print "$users_admin_text";
                          }
                          print "</font></td><td width='10%'><font face='Trebuchet MS' size='2'>$col_value[5]</font></td>";
                          print "<td width='22%'><font face='Trebuchet MS' size='2'>$col_value[2]</font></td>";
                          print "<td width='20%'><font face='Trebuchet MS' size='2'>$col_value[3]</font></td>";
                          print "<td width='13%'><font face='Trebuchet MS' size='2'><a href='admin.php?action=chpass&chpass_uid=$col_value[5]'>$users_change_pass_text</a></font></td>";
                          if ($col_value[5] <> 1) {
                             print "<td width='13%'><font face='Trebuchet MS' size='2'><a href='admin.php?action=deluser&del_uid=$col_value[5]'>$users_delete_text</a></font></td></tr>";
                          }
                      }
                      $varcount = $varcount + 1;
                      }
                      $varcount = 0;
                }
                print "</table></div></td></tr><td width='50%'><a href='admin.php?action=adduser'><img src='images/adduser.gif' width='32' height='30' border='0'><font face='Trebuchet MS' size='2'><b>$users_create_new_user_text</a></font></td></tr></table>";
          }
    }else{

    if (!isset($next) and (!isset($search))) {
       if ($permit > 1) {
           $query = "SELECT zone,name,type,src,alt,uid,url,id,added FROM powerban WHERE uid=$uid ORDER BY added DESC";
       }else{
           $query = "SELECT zone,name,type,src,alt,uid,url,id,added FROM powerban ORDER BY added DESC";
       }
    }else if (isset($next)) {
       if ($permit > 1) {
           $query = "SELECT zone,name,type,src,alt,uid,url,id,added FROM powerban WHERE added < $next AND uid=$uid ORDER BY added DESC";
       }else{
           $query = "SELECT zone,name,type,src,alt,uid,url,id,added FROM powerban WHERE added < $next ORDER BY added DESC";
       }
    }else if (isset($search)) {
       if ($permit > 1) {
           if ($search_type == 1) {
              $query = "SELECT zone,name,type,src,alt,uid,url,id,added FROM powerban WHERE id LIKE '%$search_text%' AND uid='$uid'";
           }else if ($search_type == 2) {
              $query = "SELECT zone,name,type,src,alt,uid,url,id,added FROM powerban WHERE name LIKE '%$search_text%' AND uid='$uid'";
           }else if ($search_type == 3) {
              $query = "SELECT zone,name,type,src,alt,uid,url,id,added FROM powerban WHERE url LIKE '%$search_text%' AND uid='$uid'";
           }
       }else{
           if ($search_type == 1) {
              $query = "SELECT zone,name,type,src,alt,uid,url,id,added FROM powerban WHERE id LIKE '%$search_text%'";
           }else if ($search_type == 2) {
              $query = "SELECT zone,name,type,src,alt,uid,url,id,added FROM powerban WHERE name LIKE '%$search_text%'";
           }else if ($search_type == 3) {
              $query = "SELECT zone,name,type,src,alt,uid,url,id,added FROM powerban WHERE url LIKE '%$search_text%'";
           }
       }
    }
    $result = mysql_query($query) or die("Query failed");
    if (mysql_affected_rows($dbconn) == 0) {
        print "<font face='Trebuchet MS' size='2'><p align=center>No banner found in database that match your search criteria !</p>";
        mysql_close($dbconn);
        include "footer.inc.php";
        die;
    }

    while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
         $bancount = $bancount + 1;
         if (isset($chardir) and ($chardir <> "")) {
             print "<p dir='rtl'>";
         }
         if ($bancount <= $maxdisplay ) {
         foreach ($line as $col_value[$varcount]) {
             if ($varcount == 1) {
                print "<table width='100%' border='0'><tr background='images/hpic2.gif'><td colspan='2'>";
                print "<table width='100%' border='0' cellpadding='0' cellspacing='0' background='images/hpic2.gif'>";
                print "<tr><td valign='baseline' width='12' background='images/hpic1.gif'>&nbsp;</td>";
                print "<td valign='top' width='736'><font face='Trebuchet MS' size='2' color='#FFFFFF'><b>| $list_banner_name_text</b> $col_value[1] <b> | $list_banner_zone_text</b>";
                if (($col_value[0] == "") or ($col_value[0] == 0)) {
                    print " $list_if_banner_in_unsorted_zone_text";
                }else{
                    $query = "SELECT zname FROM powerban_zones WHERE zid='$col_value[0]'";
                    $result2 = mysql_query($query) or die("Query failed");
                    $line2 = mysql_fetch_array($result2);
                    print " ".$line2[0];
                }
                print "</font></td>";
                print "<td width='10'><div align='right'><img src='images/hpic3.gif' width='2' height='20'></div></td></tr></table></td></tr>";
             }else if ($varcount == 7) {
                print "<tr><td width='840' height='120'>";
                if ($col_value[2] == 1) {
                   print "<div align='center'><img src='$col_value[3]' width='468' height='60' alt='$col_value[4]'></div>";
                }else if ($col_value[2] == 2) {
                   $swfdims = split('[x]',$col_value[6]);
                   print "<p div='center'><object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0' width='$swfdims[0]' height='$swfdims[1]'>";
                   print "<param name=movie value='$col_value[3]'>";
                   print "<param name=quality value=high>";
                   print "<embed src='$col_value[3]' quality=high pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash' width='$swfdims[0]' height='$swfdims[1]'>";
                   print "</embed></object></div>";
                }
                   $query = "SELECT login FROM powerban_auth WHERE uid='$col_value[5]'";
                   $result2 = mysql_query($query) or die("Query failed");
                   $banlogin = mysql_fetch_array($result2);
                   print "<p align='center'><table idth='455' border='0'><tr>";
                   print "<td width='45%'><font face='Trebuchet MS' size='2'>$list_banner_user_name_text $banlogin[0]</font></td>";
                   print "<td width='30%'><font face='Trebuchet MS' size='2'>$list_banner_user_id_text $col_value[5]</font></td>";
                   print "<td width='45%'><font face='Trebuchet MS' size='2'>$list_banner_format_text";
                   if ($col_value[2] == 1) {
                       print " $list_banner_format_picture_text";
                   }else if ($col_value[2] == 2) {
                       print " $list_banner_format_flash_text";
                   }
                   print "</font></td></tr></table></p>";

             }else if ($varcount == 8) {
                print "</td><td width='300'><table width='100%' border='0' align='center'><tr><td width='25%'>";
                print "<div align='center'><a href='admin.php?action=view&id=$col_value[7]'><img src='images/moreinfo.gif' width='29' height='25' border='0'></a></div></td>";
                if ($col_value[2] == 1) {
                   print "<td width='26%'><div align='center'><a href='$col_value[6]' target='_blank'>";
                   print "<img src='images/gotosite.gif' width='31' height='29' border='0'></a></div>";
                }
                print "</td><td width='22%'><div align='center'><a href='admin.php?action=edit&id=$col_value[7]'><img src='images/modify.gif' width='29' height='29' border='0'></a></div>";
                print "</td><td width='27%'><div align='center'><a href='admin.php?action=del&id=$col_value[7]'><img src='images/delete.gif' width='25' height='31' border='0'></a></div>";
                print "</td></tr><tr><td width='25%' height='2'><div align='center'><font face='Trebuchet MS' size='1'>$list_button_more_info_text</font></div>";
                if ($col_value[2] == 1) {
                   print "</td><td width='25%' height='2'><div align='center'><font face='Trebuchet MS' size='1'>$list_button_go_to_site_text</font></div>";
                }
                print "</td><td width='25%' height='2'><div align='center'><font face='Trebuchet MS' size='1'>$list_button_modify_text</font></div>";
                print "</td><td width='25%' height='2'><div align='center'><font face='Trebuchet MS' size='1'>$list_button_delete_text</font>";
                print "</div></td></tr></table></td></tr></table>";
             }
             $varcount = $varcount + 1;
         }
         $varcount = 0;


         }else{
             print "<a href='admin.php?next=$col_value[8]'><font face='Trebuchet MS' size='2'>$list_see_next_page_text</font></a>";
             mysql_close($dbconn);
             include "footer.inc.php";
             die;
         }
         print "<hr size='1'>";
    }
    mysql_close($dbconn);
    }
    include "footer.inc.php";

}
?>
