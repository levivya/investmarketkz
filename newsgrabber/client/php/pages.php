<?
$HUI_WAM1 = "èõê ÷ù ôõô şôï-ôï òáúúäåîäéôå";
$HUI_WAM2 = "ÕÓÉ ÂÛ ÒÓÒ ×ÒÎ-ÒÎ ĞÀÇÇÄÅÍÄÈÒÅ";

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

if (!is_numeric($count)) $count = 0;
$num_pages=($parameters["limit"] > 0) ? ceil($count/$parameters["limit"]) : 0;
if ($num_pages > 1 && $parameters["start"] < 1) {
     $page = ($_GET['page'] == "") ? 1 : $_GET['page'];

     $temp_params = Array();
     foreach($_GET as $key => $value) {
             if ($key != "path" && $key != "page" && $key != "item") $temp_params[] = $key."=".$value;
     }
     if (!empty($params) || (is_array($temp_params) && sizeof($temp_params) > 0))$nav["params"] = "?".implode("&",$temp_params)."&".$params;

     $k= ($page<11) ? 1 : 0;
     $start = (floor(($page-1)/10)*10)+1 + $parameters["start"];
     $end = ($num_pages - $start > 9) ? $start+9 : $num_pages;
     for ($i=$start; $i<=$end; $i++) {
         if ($i == $page) {
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

     $nav['prev'] = $nav['prev'];
     $nav['next'] = $nav['next'];

     $from = (($page-1)*$num_rows);

     if ($count < $num_rows) {
        $tpl->pages = "";
     } else {


        $tpl->fid_load("pages", "pages.html", "");
        $tpl->fid_array("pages", $nav);
        $tpl->fid_loop("pages", "pages", $pages);
        $tpl->fid_if("pages", $ifs);
     }
} else {
        $tpl->files["pages"] = "";
}
?>