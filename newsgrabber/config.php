<?
//error_reporting(0);
//ini_set("display_errors", "0");

// turn on debug in case of special request variable
//if (isset($_REQUEST["show_errors"])) {error_reporting(E_ALL ^ E_NOTICE); ini_set("display_errors", "1"); }

error_reporting(E_ALL ^ E_NOTICE); ini_set("display_errors", "1");

if (!function_exists("get_site_key")) {
    function get_site_key() {
        if (func_get_arg(0) == "by script call") {
	    return SITE_KEY ;
        }
    }
}

define("SITE_ID", "999");
define("SITE_KEY", "123456789012345678901234567890");
		// site-key - 64 bytes ������ �� �������� 0-9a-f
$host=preg_replace("!^www.!", "", strtolower(getenv("HTTP_HOST")));
define("CLIENT_HOST", $host);
define("VERSION", "2006-08-10");
define("CATALOG_URL", "http://www.newsgrabber.inF0/get_cat.php");
define("UPDATES_URL", "http://www.newsgrabber.inF0/updates.php?clientID=999&ver=2006-08-10");
define("TEMPLATES_EDIT", true);
define("TEMPLATES_GROUPS_ADD", true);
define("DIE_ON_CHECK_UPDATE_ERROR", false);

$_globalLimits["site_structure_levels"] = 2;		// -1 unlim, ���������� ��������� ������� � ��������� �����
$_globalLimits["site_structure_level_items"] = -1;	// -1 unlim, ���������� ��������� ������� �� ������� � ��������� �����
$_globalLimits["rss_source_items"] = -1;		// -1 unlim, ���������� ��������� RSS-����
$_globalLimits["rss_groups_items"] = -1;		// -1 unlim, ���������� ��������� ����� ��� RSS-����

$keyIsLoaded = true;




//db info
$db_host = 'db.invest04.mass.hc.ru';
$db_name = 'wwwinvest_marketcom_investsm';
$db_login = 'invest04_inves01';
$db_password = 'invest4db$';

//dir info
define("HOMEDIR", "/www/invest04/www/htdocs/newsgrabber/");

$DOCUMENT_ROOT = substr(HOMEDIR, 0, -1);
$HTTP_ROOT = "/newsgrabber/";

define("PHPDIR", $DOCUMENT_ROOT."/client/php/");
define("TEMPLATESDIR", $DOCUMENT_ROOT."/client/templates/");
define("LIBDIR", $DOCUMENT_ROOT."/lib/");
define("FUNCTIONSDIR", $DOCUMENT_ROOT."/functions/");
define("DOWNLOAD_IMAGES_DIR", $DOCUMENT_ROOT."/images/");
define("DOWNLOAD_IMAGES_DIR_HTTP",  $HTTP_ROOT."images/");
define("HTTP_ROOT", $HTTP_ROOT);
define("HTTP_HOST", $_SERVER["HTTP_HOST"]);

if (empty($_SERVER["DOCUMENT_ROOT"]) || $_SERVER["DOCUMENT_ROOT"] != $DOCUMENT_ROOT) $_SERVER["DOCUMENT_ROOT"] = $DOCUMENT_ROOT;
if (file_exists(FUNCTIONSDIR."use_functions.php")) include(FUNCTIONSDIR."use_functions.php");
?>
