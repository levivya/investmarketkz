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

if (!empty($obj->Options["objects_options_list_template"])) $tpl->fid_load($obj->Options["objects_options_template_name"], $obj->Options["objects_options_list_template"], "pages,page,count");

$list_if["found"] = false;
$list_if["!found"] = true;

$num_rows = $obj->items_per_page;
$obj->items_page = ($_GET["page"] > 0) ? $_GET["page"] : (($_POST["page"] > 0) ? $_POST["page"] : 1);
//$count = $obj->count_items = $obj->CountItems((is_array($where) && sizeof($where)>0) ? implode(" and ", $where) : "");
$count = $obj->count_items = $obj->CountItems($where);

$tpl->fid_object($obj->Options["objects_options_template_name"], $obj);
if ($count > 0) {
        include(PHPDIR."pages.php");
        $items = $obj->Getitems((is_array($where) && sizeof($where)>0) ? $where : "");
        $tpl->fid_loop($obj->Options["objects_options_template_name"], $obj->Options["objects_options_loop_name"], $items);
        $list_if["found"] = true;
        $list_if["!found"] = false;
}
$tpl->fid_if_obj($obj->Options["objects_options_template_name"], $obj);


?>