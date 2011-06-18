<?php
require("./lib/misc.inc");

// unset priveous logined user
session_remove("user");
if (isset($user))unset($user);
session_remove("user_id");
if (isset($user_id))unset($user_id);
session_remove("grp");
if (isset($grp))unset($grp);
session_remove("comp_id");
if (isset($comp_id))unset($comp_id);



header ("Location: index.php");

?>