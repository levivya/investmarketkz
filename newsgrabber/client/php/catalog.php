<?
$HUI_WAM1 = "θυκ χω τυτ ώτο-το ςαϊϊδεξδιτε";
$HUI_WAM2 = "ΥΣΙ ΒΫ ÒΣÒ ΧÒΞ-ÒΞ ΠΐΗΗΔΕΝΔΘÒΕ";

if (!defined("SITE_ID") ||
    !defined("CLIENT_HOST") ||
    !defined("VERSION") ||
    !defined("CATALOG_URL") ||
    !defined("UPDATES_URL") ||
    !defined("TEMPLATES_EDIT") ||
    !defined("TEMPLATES_GROUPS_ADD") ||
    !defined("DIE_ON_CHECK_UPDATE_ERROR") ||
    !is_object($db) ||
    !is_object($tpl))
    die("key error");

$parameters["start"] = 0;
$parameters["limit"] = 10;

$item = intval($_GET["item"]);
$gzip = function_exists("gzdeflate") && function_exists("gzinflate");
$q[] = "clientID=".SITE_ID;
$info["clientID"] = SITE_ID;
$info["clientKey"] = SITE_KEY;

$info = serialize($info);
if ($gzip) {
    //$info = gzdeflate($info);
    //$q[] = "g=1";
}
$q[] = "query=".urlencode($info);
//if ($item > 0) {
    $q[] = "category=".$item;
    if ($_GET["page"] > 0) $q[] = "p=".$_GET["page"];
    $q[] = "l=".$parameters["limit"];
//}

$result = file_get_contents(CATALOG_URL."?".implode("&", $q));
if ($gzip) {
    //$result = @gzinflate($result);
}
if ($result) $result = unserialize($result);

if (is_array($result["categories"]))
foreach($result["categories"] as $key => $cat) $result["categories"][$key]["current"] = $item == $key;

if (is_array($result["items"])) {
    $tpl->fid_load("content", "catalog.html");

    $page = $_GET["page"] > 0 ? $_GET["page"] : 1;
    $count = $result["items_count"];
    $nav["path"] = $item > 0 ? $HTTP_ROOT."catalog/id_".$item."" : $HTTP_ROOT."catalog";
    include(PHPDIR."pages.php");

    $tpl->fid_loop("content", "urls", $result["items"]);

    $tpl->fid_loop2d("content", "catalog", $result["categories"]);
    $tpl->fid_array("content", $result["categories"][$item]);

} else {
    $tpl->fid_load("content", "catalog.html");
    $tpl->fid_loop2d("content", "catalog", $result["categories"]);
}
?>