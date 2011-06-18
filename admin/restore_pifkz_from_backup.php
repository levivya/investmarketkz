<?php
//
//   file:        restore_pifkz_from_backup.php
//   description: This script restore index pifkz informatiom from backup table.


//include lib and conf file
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");

// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);




echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><a href="index.php">'.echoNLS('На страницу Администратора','').'</a>
          </td>
      </tr>
      </table><br>';

flush();


//create backup
$query="delete from  ism_index_pifkz";
$result=exec_query($query);
$query="insert into ism_index_pifkz  select * from ism_index_pifkz_backup";
$result=exec_query($query);

flush();
echo '<table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
      <tr bgcolor="white">
          <td width="100%" style=" font-size: 10pt;"><font color="red">'.echoNLS('ЗАКОНЧЕНО востановление данных!<br>Время:&nbsp;','').date('r').'</font>
          </td>
      </tr>
      </table><br>';

diconn($conn);

?>