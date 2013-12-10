<?
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

$tpl->fid_load("content", ((!empty($obj->Options["objects_options_list_template"])) ? $obj->Options["objects_options_list_template"]: "list.items.html"), "pages,page,count,page_subtitle");

$tpl->fid_loop("content", "rubrikators", $obj->Rubrikators);
$tpl->fid_loop("content", "rubrikators", $obj->Rubrikators);
if (is_array($obj->Rubrikators))
foreach($obj->Rubrikators as $rubrikator) {
       $values = Array();
       if ($rubrikator["HideFirst"] != true) $values[] = Array("IDField" => 0, "TitleField" => $lang["button_all"]);
       $values = (is_array($rubrikator["values"])) ? array_merge($values, $rubrikator["values"]) : array_merge($values, $db->fetchall($db->query("select ".$rubrikator["IDField"].", ".$rubrikator["TitleField"]." from ".$rubrikator["TableName"]." ".((!empty($rubrikator["Where"])) ? "WHERE ".$rubrikator["Where"] : "" )." order by ".$rubrikator["TitleField"])));

       $temp = (!empty($_GET[$rubrikator["IDField"]])) ? $_GET[$rubrikator["IDField"]] : ((!empty($_POST[$rubrikator["IDField"]])) ? $_POST[$rubrikator["IDField"]] : 0);
       $exist = false;
       if (!empty($temp))
       foreach($values as $value) {
           if ($value[$rubrikator["IDField"]] == $temp) {
                $exist = true;
                break;
           }
       }
       $current[$rubrikator["TitleField"]] = ($exist) ? $temp : 0;
       $tpl->fid_select("content", $rubrikator["TableName"], $values, $current[$rubrikator["TitleField"]]);
       if (!empty($current[$rubrikator["TitleField"]])) {
                $obj->links .= $rubrikator["IDField"]."=".$current[$rubrikator["TitleField"]]."&";
                if ($rubrikator["NotFiltered"] != true) $where[] = $obj->Table["table_name"].".".$rubrikator["IDField"]." = '".$current[$rubrikator["TitleField"]]."'";
       }
}

if (is_array($obj->Actions) && sizeof($obj->Actions)) {
   $tpl->fid_loop("content", "actions", $obj->Actions);
} else {
   $tpl->fid_loop("content", "actions", Array());
}

if (is_array($obj->GroupActions) && sizeof($obj->GroupActions)) {
   $tpl->fid_loop("content", "group_actions", $obj->GroupActions);
} else {
   $tpl->fid_loop("content", "group_actions", Array());
}

//$obj->items_per_page = 20;
$obj->items_page = ($_GET["page"] > 0) ? $_GET["page"] : (($_POST["page"] > 0) ? $_POST["page"] : 0);
$obj->count_items = $obj->CountItems((is_array($where) && sizeof($where)>0) ? implode(" and ", $where) : "");

$obj->Table["KeyName"] = $obj -> KeyName;

$tpl->fid_array("main", $obj->Table);
$tpl->fid_array("content", $obj->Table);

//$order = (!empty($_GET["order"])) ? $_GET["order"] : ((!empty($_POST["order"])) ? $_POST["order"] : "");
//$sort = (!empty($_GET["sort"])) ? $_GET["sort"] : ((!empty($_POST["sort"])) ? $_POST["sort"] : "ASC");
foreach($obj->Fields as $key => $value) {
       if (empty($order)) {
          $obj->OrderBy = $obj->Table["table_name"].".".$key." ".$sort;
          $obj->Fields[$key]["sort"] = ($sort == "ASC") ? "DESC" : "ASC";
          break;
       }
       if ($key == $order) {
          $obj->OrderBy = $key." ".$sort;
          $obj->Fields[$key]["sort"] = ($sort == "ASC") ? "DESC" : "ASC";
          break;
       }
}

$show_fields = explode(",", ((!empty($obj->Options["objects_options_sql_fields"])) ? $obj->Options["objects_options_sql_fields"] : $obj->Options["objects_options_show_fields"]));
if (sizeof($obj->Fields) != sizeof($show_fields) || strlen($obj->Options["objects_options_show_fields"]) > 0) {
   $temp = Array();
   foreach ($show_fields as $value) {
        if (is_array($obj->Fields[$value]) && sizeof($obj->Fields[$value])>0)
                $temp[] = $obj->Fields[$value];
   }
   $show_fields = $temp;
} else {
   $show_fields = $obj->Fields;
}

$tpl->fid_loop("content", "fields", $show_fields);

$tpl->fid_loop("content", "fields_values", $show_fields);
//die($tpl->files["content"]);
if ($obj->count_items > 0)
        $items = $obj->Getitems((is_array($where) && sizeof($where)>0) ? implode(" and ", $where) : "");
else
        $items = Array();
foreach($items as $key => $value) {
        foreach($value as $k => $v) {
                if ($obj->Fields[$k]["field_type"] == "enum" && !empty($obj->{$k."_titles"}[$value[$k]])) $items[$key][$k] = $obj->{$k."_titles"}[$value[$k]];
                if ($obj->Fields[$k]["no_convert"] !== true) {
                        $items[$key][$k] = convert($v, html, q);
                        $items[$key][$k] = str_replace("{", "&#123;", $items[$key][$k]);
                        $items[$key][$k] = str_replace("}", "&#125;", $items[$key][$k]);
                }
        }
        $items[$key]["delete_name"] = preg_replace("/[\n\r]/ms", " ", str_replace("'", "&#146;", convert($items[$key]["delete_name"], sl)));
}
$tpl->fid_loop("content", "items", $items, true);



        $obj->items_per_page = ($obj->items_per_page < 1) ? 20 : $obj->items_per_page;
        $page = intval(($_GET['page'] < 1) ? 1 : $_GET['page']);
       
        $num_pages=ceil($obj->count_items/$obj->items_per_page);
        if ($page > $num_pages) $page = 1;
        $k= ($page<11) ? 1 : 0;
        $start = (floor(($page-1)/10)*10)+1;
        $end = ($num_pages - $start > 9) ? $start+9 : $num_pages;
        for ($i=$start; $i<=$end; $i++) {
                if ($i == $obj->items_page) {
                    $ifs["current_page$i"] = true;
                } else {
                    $ifs["current_page$i"] = false;
                }
                $pages[] = array(num_page => $i);
        }

        if ($num_pages > 10) {
                $nav['prev'] = ceil($page/10)*10-10;
                $nav['next'] = ceil($page/10)*10 + 1;
                $ifs['next_href'] = (floor(($page-1)/10) == floor($num_pages/10)) ? false : true;
                $ifs['prev_href'] = ($page < 11) ? false : true;
        } else {
                $nav['prev'] = $page;
                $nav['next'] = $page;
                $ifs['next_href'] = false;
                $ifs['prev_href'] = false;
        }
        if ($page>1) {
               $nav['prev_page'] = $page-1;
               $ifs['prev_page'] = true;
        } else {
               $nav['prev_page'] = 1;
               $ifs['prev_page'] = false;
        }
        if ($page < $num_pages) {
               $nav['next_page'] = $page+1;
               $ifs['next_page'] = true;
        } else {
               $nav['next_page'] = $num_pages;
               $ifs['next_page'] = false;
        }

        $nav['prev'] = "page=".$nav['prev'];
        $nav['next'] = "page=".$nav['next'];

        $from = (($page-1)*$num_rows);

        $ifs['pages'] = ($num_pages < 2) ? false : true;
        if ($ifs['pages']) {
                $nav["params"] = "&".$obj->links."&order=$order&sort=$sort";
                $tpl->fid_load("pages", "pages.html", "pages,page");
                $tpl->fid_array("pages", $nav);
                $tpl->fid_loop("pages", "pages", $pages);
        } else {
           $tpl->files["pages"] = "";
        }

$tpl->fid_if("pages", $ifs);
$obj->rubrikators = (sizeof($obj->Rubrikators) > 0);
$tpl->fid_object("content", $obj);
$tpl->fid_if_obj("content", $obj);
?>