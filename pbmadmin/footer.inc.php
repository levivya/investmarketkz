<?php
/******************************************************************************
Power Banner Manager 1.5 !
(footer.php file)

Copyright Armin Kalajdzija, 2002.
E-mail: kalajdzija@hotmail.com
WebSite: http://www.ak85.tk
******************************************************************************/


print "<br><p ";
if (isset($chardir) and ($chardir <> "")) {
  print "dir='rtl' ";
}
print "align='center'><table width='404' border='0'><tr>";
print "<td background='images/footer.gif' height='25' width='405' >";
print "<div align='center'><font face='Verdana' size='1'><a href='mailto:kalajdzija@hotmail.com'>$footer_copyright_text Armin Kalajdzija, 2002.</a></div></td></tr></table>";

?>
