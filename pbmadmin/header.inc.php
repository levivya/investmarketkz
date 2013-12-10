<?php if (isset($chardir) and ($chardir <> "")) {
    print "<p dir='$chardir'>";
}
?>
<font face="Trebuchet MS" size="6"><?php echo $header_panel_main_text; ?></font><br>
<?php
/******************************************************************************
Power Banner Manager 1.5 !
(header.php file)

Copyright Armin Kalajdzija, 2002.
E-Mail: kalajdzija@hotmail.com
Web Site: http://www.ak85.tk
******************************************************************************/
print "<font face='Trebuchet MS' size='2'>$header_mysql_ver_text ";
      print mysql_get_server_info($dbconn);
      print " (".mysql_get_host_info($dbconn).")";
      print " | $header_user_logged_text $user_login";
      print "</font><br><br>";
?>
<table width="100%" border="1" bordercolor="#32587F">
  <tr bordercolor="#FFFFFF" valign="bottom">
    <td height="2">
      <div align="center"><font face="Trebuchet MS" size="2"><a href="admin.php"><img src="images/moreinfo.gif" width="29" height="25" border="0"><br>
        <?php echo $menu_banner_list_text; ?></a></font></div>
    </td>
    <td height="2">
      <div align="center"><font face="Trebuchet MS" size="2"><a href="admin.php?action=addban"><img src="images/modify.gif" width="29" height="29" border="0"><br>
        <?php echo $menu_add_banner_text; ?></a></font></div>
    </td>
    <td height="2">
      <div align="center"><font face="Trebuchet MS" size="2"><a href="admin.php?action=zones"><img src="images/addzone.gif" width="32" height="30" border="0"><br>
        <?php echo $menu_zone_manage_text; ?></a></font></div>
    </td>
    <td height="2">
      <div align="center"><font face="Trebuchet MS" size="2"><a href="admin.php?action=search"><img src="images/search.gif" width="25" height="30" border="0"><br>
        <?php echo $menu_banner_search_text; ?></a></font></div>
    </td>
    <?php if ($permit == 1) {  ?>
    <td height="2">
      <div align="center"><font face="Trebuchet MS" size="2"><a href="admin.php?action=users"><img src="images/visitinfo.gif" width="32" height="30" border="0"><br>
        <?php echo $menu_user_manage_text; ?></a></font></div>
    </td>
    <?php }else{ ?>
    <td height="2">
      <div align="center"><font face="Trebuchet MS" size="2"><a href="admin.php?action=chpass"><img src="images/chpass.gif" border="0"><br>
        <?php echo $menu_change_password_text; ?></a></font></div>
    </td>
    <?php } ?>
    <td height="2">
      <div align="center"><font face="Trebuchet MS" size="2"><a href="admin.php?action=setup"><img src="images/about.gif" width="31" height="30" border="0"><br>
        <?php echo $menu_pbm_options_text; ?></a></font></div>
    </td>
    <td height="2">
      <div align="center"><font face="Trebuchet MS" size="2"><a href="admin.php?action=logout"><img src="images/logout.gif" width="32" height="32" border="0"><br>
        <?php echo $menu_logout_text; ?></a></font></div>
    </td>
  </tr>
</table>
<br>
