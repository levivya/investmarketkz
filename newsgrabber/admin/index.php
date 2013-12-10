<?
$HUI_WAM1 = "ихк чщ фхф юфп-фп тбъъдеодйфе";
$HUI_WAM2 = "ХУЙ ВЫ ТУТ ЧТО-ТО РАЗЗДЕНДИТЕ";

/* ----------------------------------------------- */
/* --- nulled by someone for http://nulled.ws/ --- */
/* ----------------------------------------------- */

$_config_loaded = false;
@include ("config.php");
if ($_config_loaded !== true) {
   die("config.php not found");
}

$_SESSION["logged"] = false;
$_SESSION["logged"] = "try";

if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__))) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__));
$page_title = "Авторизация";


///////////////////////////
if (!empty($_POST["login"]) && !empty($_POST["password"]) || (!empty($_COOKIE["users_login"]) && !empty($_COOKIE["users_password"]))) {
                if (empty($_POST["login"]) && empty($_POST["password"]) && !empty($_COOKIE["users_login"]) && !empty($_COOKIE["users_password"])) $by_cookie = true;
                if ($admin->CheckLogin($_POST["login"], $_POST["password"], $by_cookie)) {
                          if (!$admin->CheckAccess("admin", "dir")) {
                             $error="У вас нет доступа!";
                          } else if ($admin->users_active != 'checked') {
                             $error="Ваш логин деактивирован! Свяжитесь с администратором.";
                          } else {
                             if ($_POST["save_password"] != "" || $by_cookie) {
                                $_SESSION["by_cookie"] = true;
                                setcookie("users_login", $_SESSION["user"]->users_login, time()+3600 * 24 * 365);
                                setcookie("users_password", $_SESSION["user"]->users_password, time()+3600 * 24 * 365);
                             } else {
                                setcookie("users_login");
                                setcookie("users_password");
                             }
                             $url = (!empty($_SESSION["REQUEST_URI"]) && $_SESSION["REQUEST_URI"] != $_SERVER["REQUEST_URI"] ? $_SESSION["REQUEST_URI"] : "news.php");
                             $_SESSION["REQUEST_URI"] = "";
                             header ("Cache-Control: no-cache, must-revalidate");
                             header ("Location: ".$url);
                             exit;
                          }
                } else {
                          $error = "Указанный пользователь не найден.";
                }
}

///////////////////////////

$tpl->fid_load("main", "index.html", "page_title,error");

foreach($tpl->files as $key => $value) {
   $tpl->fid_array($key, $lang);
}

$tpl->fid_show("main");
?>