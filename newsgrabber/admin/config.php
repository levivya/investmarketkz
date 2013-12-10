<?
header("Content-Type:text/html; charset=windows-1251");
if (!@file_exists(@realpath(".")."/../"."config.php") || !@require(@realpath(".")."/../"."config.php")) {
   die("Не найден файл настроек.");
}

if (!ini_get("session.auto_start")) session_start();


if ($_SESSION["logged"] != "try" && $_SESSION["logged_in"] !== true  && $_SERVER["PHP_SELF"] != $HTTP_ROOT."admin/index.php") {
        $_SESSION["logged"] = "try";
        $_SESSION["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
        header ("Location: ".$HTTP_ROOT."admin/");
        exit;
}


require(LIBDIR."lib.db.mysql.php");
require(LIBDIR."lib.tpl.php");
require(LIBDIR."lib.obj.php");
include_once(LIBDIR."lib.auth.php");

$db = new Db();
$tpl = new Template($DOCUMENT_ROOT."/admin/templates/");

$options = $db->fetchall($db->query("select options.* from options"));
foreach($options as $opt) {
  $temp[$opt["options_name"]] = $opt["options_value"];
}
$GLOBALS["options"] = $temp;
$GLOBALS["options"]["grab_text_imagesize_limit"] = explode(":", $GLOBALS["options"]["grab_text_imagesize_limit"]);
unset($temp);

$_SESSION["set_lang"] = empty($_SESSION["set_lang"]) || !file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/global.php") ? $GLOBALS["options"]["user_lang"] : $_SESSION["set_lang"];
if ($_GET["setlang"]) $_SESSION["set_lang"] = $_GET["setlang"];
if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/global.php")) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/global.php");

$admin = new user();
if (!$admin->CheckSession() && !(!empty($_COOKIE["users_login"]) && !empty($_COOKIE["users_password"]) && $admin->CheckLogin("", "", true)) && $admin->users_groups_id != 1 && $_SERVER["PHP_SELF"] != $HTTP_ROOT."admin/index.php") {
    header("Location: ".$HTTP_ROOT."admin/index.php");
    exit;
}



function check_updates() {
    if (!is_object($GLOBALS["db"])) $GLOBALS["db"] = new Db();
    $_SESSION["updates"] = unserialize($GLOBALS["db"]->data_decode($_SESSION["updates"]));
    $_SESSION["updates"]=false;
    if (!is_array($_SESSION["updates"])) {
        //$q[] = "clientID=".SITE_ID;
        $info["clientID"] = SITE_ID;
        $info["ver"] = VERSION;
        $info = serialize($info);
        $info = $GLOBALS["db"]->data_encode($info, get_site_key("by script call"));
        $q[] = "query=".urlencode(base64_encode($info));
        if (!function_exists("get_content")) use_functions("get_content");
        $data = get_content(UPDATES_URL."&".implode("&", $q));
        $data = $data["content"];
        $data = $GLOBALS["db"]->data_decode(base64_decode($data));
        $check_updates = unserialize($data);
        if (!$check_updates) {
            $_SESSION["updates"] = false;
            return false;
        }

        $_SESSION["updates"] = array();
        $_SESSION["updates"]["updates_exists"] = $check_updates["updates"];
        $_SESSION["updates"]["current_ver"] = date("ymd", strtotime($check_updates["current_ver"]));
        $_SESSION["updates"]["current_type"] = $check_updates["current_type"];
        $_SESSION["updates"]["last_ver"] = date("ymd", strtotime($check_updates["last_ver"]));
        $_SESSION["updates"]["update_description"] = $check_updates["update_description"];
	    $GLOBALS["_UPDATES"] = $_SESSION["updates"];
        $_SESSION["updates"] = $GLOBALS["db"]->data_encode(serialize($_SESSION["updates"]));
    } else {
	$GLOBALS["_UPDATES"] = $_SESSION["updates"];
	$_SESSION["updates"] = $GLOBALS["db"]->data_encode(serialize($_SESSION["updates"]));
    }
    return true;
}

//if (!check_updates() && DIE_ON_CHECK_UPDATE_ERROR === true) die("Updates check error.");

// nulling
$_SESSION["updates"] = false;
$check_updates["current_ver"] = VERSION;	// "2006-08-10"
$_SESSION["updates"]["current_ver"] = date("ymd", strtotime($check_updates[current_ver]));
$_SESSION["updates"]["current_type"] = "ST";	
$_SESSION["updates"]["update_description"] = "- none -";
$GLOBALS["_UPDATES"] = $_SESSION["updates"];
$_SESSION["updates"] = $GLOBALS["db"]->data_encode(serialize($_SESSION["updates"]));


$_config_loaded = true;
?>