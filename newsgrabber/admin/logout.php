<?
$HUI_WAM1 = "θυκ χω τυτ ώτο-το ςαϊϊδεξδιτε";
$HUI_WAM2 = "ΥΣΙ ΒΫ ÒΣÒ ΧÒΞ-ÒΞ ΠΐΗΗΔΕΝΔΘÒΕ";

/* ----------------------------------------------- */
/* --- nulled by someone for http://nulled.ws/ --- */
/* ----------------------------------------------- */

if (!ini_get("session.auto_start")) session_start();
$_COOKIE["users_login"] = "";
$_COOKIE["users_password"] = "";
setcookie("users_login");
setcookie("users_password");
$_SESSION["logged_in"] = false;
$_SESSION["logged"] = false;
$_SESSION["user"] = false;
session_write_close();

header("Location: index.php");
?>