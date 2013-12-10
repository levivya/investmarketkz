<?
$HUI_WAM1 = "θυκ χω τυτ ώτο-το ςαϊϊδεξδιτε";
$HUI_WAM2 = "ΥΣΙ ΒΫ ÒΣÒ ΧÒΞ-ÒΞ ΠΐΗΗΔΕΝΔΘÒΕ";

/* ----------------------------------------------- */
/* --- nulled by someone for http://nulled.ws/ --- */
/* ----------------------------------------------- */

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

$tpl->fid_load($obj->Options["objects_options_template_name"], $obj->Options["objects_options_view_template"], "action,referer,return");


if ($item > 0) $obj->GetObject($item);

foreach($obj->Fields as $key => $value) {
        if ($value["field_type"] == "varchar") $obj->{$key} = convert($obj->{$key}, nl);
        if ($value["field_type"] == "text") $obj->{$key} = convert($obj->{$key}, nl);
        $obj->{$key} = str_replace("{", "&#123;", $obj->{$key});
        $obj->{$key} = str_replace("}", "&#125;", $obj->{$key});
}

if (preg_match("|([\d]{4})-([\d]{2})-([\d]{2}) ([\d]{2}):([\d]{2}):([\d]{2})|", $obj->news_date, $regs)) {
   $obj->news_date = $regs[4].":".$regs[5]." ".$regs[3].".".$regs[2].".".$regs[1];
   $obj->news_day = $regs[3];
   $obj->news_month = $regs[2];
   $obj->news_year = $regs[1];
   $obj->news_hour = $regs[4];
   $obj->news_minute = $regs[5];
}
$tpl->fid_object($obj->Options["objects_options_template_name"], $obj);


?>