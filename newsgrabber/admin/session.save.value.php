<?
$validNames = array("so" => "structure_opened", "go" => "groups_opened");

if(!empty($validNames[$_GET["g"]]) && is_integer(intval($_GET["i"])) && ($_GET["v"] == "o" || $_GET["v"] == "")) {
   if (!ini_get("session.auto_start")) session_start();	
   $_SESSION[$validNames[$_GET["g"]]][intval($_GET["i"])] = $_GET["v"] == "o";
}
                                     
?>