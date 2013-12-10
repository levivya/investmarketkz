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

if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__))) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__));

$page_title = $lang["page_title"];
$HOST = $_SERVER["HTTP_HOST"];

function deleteMenu($menu_owner) {
        global $db;
        $items = $db->fetchccol($db->query("select menu_id from menu where menu_owner=".$menu_owner));
        $db->query("delete from menu where menu_owner=".$menu_owner);
        $db->query("delete from users_groups_access where menu_id in (".implode(", ", $items).")");
        foreach($items as $menu_owner) {
               deleteMenu($menu_owner);
        }
}

function GetData($level, $owner) {
global $db, $color, $ifs, $menu_id, $_globalLimits;
               $query = "SELECT * FROM menu WHERE menu_owner = $owner ORDER by menu_order, menu_id";
               $nodes = $db ->fetchall($db->query($query));
               if(is_array($nodes) && sizeof($nodes) > 0) {
               $first = key($nodes);
               end($nodes);
               $last = key($nodes);
               reset($nodes);
               $allnodes = sizeof($nodes);
               foreach ($nodes as $key => $value) {
                                $nodes[$key]["menu_delete"] = $nodes[$key]["menu_id"] > 4;
                                $countNodes = get_level_items_count($value["menu_id"]);
                                $nodes[$key]["menu_item"] = ($nodes[$key]["menu_id"] == 1 || $nodes[$key]["menu_id"] > 4) && ($_globalLimits["site_structure_levels"] == -1 || $_globalLimits["site_structure_levels"] > $level) && ($_globalLimits["site_structure_level_items"] == -1 || $_globalLimits["site_structure_level_items"] > $countNodes);
                                switch(true) {
                                        case $key == $first && $countNodes > 0 && $allnodes == 1:
                                                $nodes[$key]["toc_image"] = "toc_closed_first_one";
                                                break;
                                        case $key == $first && $countNodes > 0 && $allnodes > 1:
                                                $nodes[$key]["toc_image"] = "toc_closed_first";
                                                $nodes[$key]["background_image"] = "toc_line";
                                                $nodes[$key]["background2_image"] = "toc_line";
                                                break;
                                        case $key == $first && $countNodes == 0  && $allnodes == 1:
                                                $nodes[$key]["toc_image"] = "toc_leaf_last";
                                                break;
                                        case $key == $first && $countNodes == 0  && $allnodes > 1:
                                                $nodes[$key]["toc_image"] = "toc_leaf_whole";
                                                $nodes[$key]["background_image"] = "toc_line";
                                                $nodes[$key]["background2_image"] = "toc_line";
                                                $nodes[$key]["background3_image"] = "toc_line";
                                                break;
                                        case $key == $last && $countNodes > 0:
                                                $nodes[$key]["toc_image"] = "toc_closed_last";
                                                break;
                                        case $key == $last && $countNodes == 0:
                                                $nodes[$key]["toc_image"] = "toc_leaf_last";
                                                break;
                                        case $countNodes > 0:
                                                $nodes[$key]["toc_image"] = "toc_closed_whole";
                                                $nodes[$key]["background3_image"] = "toc_line";
                                                $nodes[$key]["background_image"] = "toc_line";
                                                $nodes[$key]["background2_image"] = "toc_line";
                                                break;
                                        case $countNodes == 0 && $key != $last && $key != $first:
                                                $nodes[$key]["toc_image"] = "toc_leaf_whole";
                                                $nodes[$key]["background3_image"] = "toc_line";
                                                $nodes[$key]["background_image"] = "toc_line";
                                                $nodes[$key]["background2_image"] = "toc_line";
                                                break;
                                        case $countNodes == 0:
                                                $nodes[$key]["toc_image"] = "toc_line";
                                                break;
                                }
                                if ($countNodes > 0) {
                                        $nodes[$key]["opened"] = true;
                                } else {
                                        $nodes[$key]["opened"] = false;
                                }

                                if ($_SESSION["structure_opened"][$nodes[$key]["menu_id"]] == true) {
                                   $nodes[$key]["toc_image"] = str_replace("closed", "opened", $nodes[$key]["toc_image"]);
                                   $nodes[$key]["child_style"] = "";
                                } else {
                                   $nodes[$key]["child_style"] = "none";
                                }
                                $nodes[$key]["childs"] = GetData($level+1, $value["menu_id"]);
                                $nodes[$key]["menu_name"] = convert(htmlspecialchars($nodes[$key]["menu_name"], ENT_QUOTES), q);
                                $nodes[$key]["menu_active_disabled"] = (($nodes[$key]["menu_owner"] == 0 && $menu_id == 1) || ($nodes[$key]["menu_file"] == 0 && $nodes[$key]["menu_url"] == "")) ? "disabled" : "";
                                $nodes[$key]["menu_map_disabled"] = ($nodes[$key]["menu_owner"] == 0 && $menu_id == 1) ? "disabled" : "";
                                $nodes[$key]["delete"] = !($nodes[$key]["menu_owner"] == 0 && $menu_id == 1);
               }
               }
               return $nodes;
}

$menu_id = ($_GET["item"] > 0) ? $_GET["item"] : (($POST["item"] > 0) ? $POST["item"] : 0);
$action = (!empty($_GET["action"])) ? $_GET["action"] : ((!empty($_POST["action"])) ? $_POST["action"] : "");
$referer = (empty($_POST["referer"])) ? $_SERVER["HTTP_REFERER"] : $_POST["referer"];


if ($action == "delete") {
                $db->query("delete from menu where menu_id=".$_GET["item"]);
                $db->query("delete from users_groups_access where menu_id=".$_GET["item"]);
                if ($_GET["to"] == 0 || !is_numeric($_GET["to"])) {
                   deleteMenu($_GET["item"]);
                } else {
                   $db->query("update menu set menu_owner=".$_GET["to"]." where menu_owner=".$_GET["item"]);
                }
}


if ($action == "save") {
        if (is_array($_POST) && sizeof($_POST) > 0) {
            $_POST["menu_content"] = str_replace(DOWNLOAD_IMAGES_DIR_HTTP, "{DOWNLOAD_IMAGES_DIR_HTTP}", $_POST["menu_content"]);
            $_POST["menu_content"] = str_replace(HTTP_ROOT, "{HTTP_ROOT}", $_POST["menu_content"]);

           if ($_POST["menu_id"] > 0) {
                if ($_POST["menu_id"] == 1 || $db ->fetch($db->query("SELECT count(menu_id) FROM menu WHERE (menu_name = '".$_POST["menu_name"]."' or menu_dir = '".$_POST["menu_dir"]."') and menu_id <> '".$_POST["menu_id"]."'"), 0) == 0) {
                        $query="UPDATE menu SET
                               menu_name='".($_POST["menu_name"])."',".
                              "menu_dir='".$_POST["menu_dir"]."',
                               menu_description='".($_POST["menu_description"])."',
                               menu_title='".($_POST["menu_title"])."',
                               menu_keywords='".($_POST["menu_keywords"])."',";
                               if ($_POST["menu_id"] > 4) $query .= "menu_content='".convert($_POST["menu_content"], sl)."',";
                               $query .= "menu_active='".($_POST["menu_id"] < 6 ? 'checked' : $_POST["menu_active"])."'
                               WHERE menu_id=".$_POST["menu_id"];
                        $result = @$db->query ($query) or die ("<p><b>ERROR!!!</b></p><BR>".$query."<BR>".mysql_error());
                        $_GET["menu_id"] = $_POST["menu_id"];
                        $action = "";
                        header("Location: menu.php");
                        exit;
                } else {
                   $ErrorMessage = "Ошибка, раздел с таким названием или папкой уже существует!";
                   $action = "edit";
                   $_GET["menu_id"] = $_POST["menu_id"];
                }
           } else {
              $_POST["menu_owner"] = intval($_POST["menu_owner"]);
              $cLevel = get_level_count($_POST["menu_owner"]);
              $cLevelCount = get_level_items_count($_POST["menu_owner"]);
              if ($_globalLimits["site_structure_levels"] == -1 || $_globalLimits["site_structure_levels"] > $cLevel) {
                if ($_globalLimits["site_structure_level_items"] == -1 || $_globalLimits["site_structure_level_items"] > $cLevelCount) {
                  $exists = $db ->fetch($db->query("SELECT count(menu_id) FROM menu WHERE (menu_name = '".$_POST["menu_name"]."' or menu_dir = '".$_POST["menu_dir"]."') and menu_owner = '".$_POST["menu_owner"]."' and '".$_POST["menu_dir"]."' <> ''"), 0);
                  if ($exists == 0) {
                   $_POST["menu_order"] = $db ->fetch($db->query("SELECT MAX(menu_order)+1 FROM menu WHERE menu_owner = '".$_POST["menu_owner"]."'"), 0);
                   $sql="insert into menu(menu_name, menu_content, menu_owner, menu_title, menu_keywords, menu_description, menu_active, menu_dir, menu_date) values ('".$_POST["menu_name"]."', '".convert($_POST["menu_content"], sl)."', '".$_POST["menu_owner"]."', '".($_POST["menu_title"])."', '".($_POST["menu_keywords"])."', '".($_POST["menu_description"])."', '".$_POST["menu_active"]."', '".$_POST["menu_dir"]."', NOW())";

                   if ($db->query($sql)) {
                      $_GET["menu_id"] = $menu_id = mysql_insert_id();
                      $action = "";
                      header("Location: menu.php");
                      exit;
                   } else {
                      $ErrorMessage = "Ошибка добавления раздела!";
                      $action = "edit";
                   }

                  } else {
                   $ErrorMessage = "Ошибка, раздел с таким названием или папкой уже существует!";
                   $action = "edit";
                  }
                } else {
                  $ErrorMessage = "Ошибка, Вы не можете создавать более ".$_globalLimits["site_structure_level_items"]." страниц на уровне!";
                  $action = "edit";
                }
              } else {
                   $ErrorMessage = "Ошибка, Вы не можете создавать страницы с уровнем вложенности более ".$_globalLimits["site_structure_levels"]."!";
                   $action = "edit";
              }
           }
        }
}

function get_level_count($owner, $countedLevel = -1) {
    global $db;
    $countedLevel++;
    $owner = $db->fetch($db->query("select menu_owner from menu where menu_id = ".$owner), 0);
    if ($owner == 0) return $countedLevel;
    return get_level_count($owner, $countedLevel);
}

function get_level_items_count($owner) {
    global $db;
    return $db->fetch($db->query("select count(*) from menu where menu_owner = ".$owner), 0);
}


if ($action == "access_save") {
    $db->query("delete from users_groups_access where menu_id = '".$_POST["menu_id"]."'");
    if (is_array($_POST["users_groups"]))
    foreach($_POST["users_groups"] as $group) {
        $db->query("insert into users_groups_access values ('".$group."', '".$_POST["menu_id"]."')");
    }
    header("Location: menu.php");
    exit;
}

if ($action == "save_order") {
           if (is_array($_POST["order"]))
           foreach($_POST["order"] as $key => $value) {
                   if ($value == 0) $value = 1;
                   if (!empty($value) || is_numeric($value)) {
                        $sql="UPDATE menu SET menu_order = '$value' WHERE menu_id = '$key'";
                        $db->query($sql);
                   }
           }
           $sql="UPDATE menu SET menu_active = '', menu_main = '', menu_delimiter = '', menu_open = '', menu_map = ''";
           $db->query($sql);
           if (is_array($_POST["menu_map"]) && sizeof($_POST["menu_map"])>0)
           foreach($_POST["menu_map"] as $key => $value) {
                   $sql="UPDATE menu SET menu_map = 'checked' WHERE menu_id = $key";
                   $db->query($sql);
           }
           if (is_array($_POST["menu_open"]) && sizeof($_POST["menu_open"])>0)
           foreach($_POST["menu_open"] as $key => $value) {
                   $sql="UPDATE menu SET menu_open = 'checked' WHERE menu_id = $key";
                   $db->query($sql);
           }
           if (is_array($_POST["menu_active"]) && sizeof($_POST["menu_active"])>0)
           foreach($_POST["menu_active"] as $key => $value) {
                   $sql="UPDATE menu SET menu_active = 'checked' WHERE menu_id = $key";
                   $db->query($sql);
           }
           if (is_array($_POST["menu_main"]) && sizeof($_POST["menu_main"])>0)
           foreach($_POST["menu_main"] as $key => $value) {
                   $sql="UPDATE menu SET menu_main = 'checked' WHERE menu_id = $key";
                   $db->query($sql);
           }
           if (is_array($_POST["menu_delimiter"]) && sizeof($_POST["menu_delimiter"])>0)
           foreach($_POST["menu_delimiter"] as $key => $value) {
                   $sql="UPDATE menu SET menu_delimiter = 'checked' WHERE menu_id = $key";
                   $db->query($sql);
           }
           $action == "";
}

switch(true) {
   case $action == "edit":
        if ($_GET["menu_id"] > 0) {
                $action = $lang["caption_editing_item"];
                if (empty($ErrorMessage)) {
                        $query="SELECT menu.*, m1.menu_name as menu_owner_name, IF(m1.menu_name IS NULL, 'Первый уровень', m1.menu_name) as menu_owner_name, m1.menu_id as owner_id FROM menu LEFT JOIN menu as m1 ON (m1.menu_id=menu.menu_owner) WHERE menu.menu_id=".$_GET["menu_id"];
                        $result = @$db->query ($query) or die ("<p><b>ERROR!!!</b></p><BR>".$query."<BR>");
                        $menu = $db ->fetch($db->query($query));

                } else {
                        $menu = $_POST;
                        $ifs["ErrorMessage"] = true;
                        $query = "SELECT menu_id as menu_owner, menu_name as menu_owner_name FROM menu WHERE menu_id = ".$_POST["menu_owner"];
                        $menu = array_merge($menu, $db->fetch($db->query($query)));
                }

                $crlf2 = chr(13).chr(10);
                $crlf = chr(10);
                $menu["menu_content"] = htmlspecialchars($menu["menu_content"]);
        } else {
                $action = $lang["caption_adding_item"];
                if (empty($ErrorMessage)) {
                        $_GET["menu_owner"] = ($_GET["menu_owner"] > 0) ? $_GET["menu_owner"] : 1;
                        $query = "SELECT menu_id as menu_owner, menu_name as menu_owner_name FROM menu WHERE menu_id = ".$_GET["menu_owner"];
                        $menu = $db ->fetch($db->query($query));
                } else {
                        $menu = $_POST;
                        foreach($menu as $key => $value) {
                            if (get_magic_quotes_gpc()) $menu[$key] = stripslashes($value);
                            $menu[$key] = convert($menu[$key], q);
                        }
                        $ifs["ErrorMessage"] = true;
                        $query = "SELECT menu_id as menu_owner, menu_name as menu_owner_name FROM menu WHERE menu_id = ".$_POST["menu_owner"];
                        $menu = array_merge($menu, $db->fetch($db->query($query)));
                }

        }

        $menu["menu_full_edit"] = $menu["menu_id"] < 1 || $menu["menu_id"] > 4;

        $menu["menu_dir_edit"] = $menu["menu_owner"] == 0 || ($menu["menu_id"] > 0 && $menu["menu_id"] < 6);

        $menu["menu_content"] = str_replace("{DOWNLOAD_IMAGES_DIR_HTTP}", DOWNLOAD_IMAGES_DIR_HTTP, $menu["menu_content"]);
        $menu["menu_content"] = str_replace("{HTTP_ROOT}", HTTP_ROOT, $menu["menu_content"]);

        $tpl->fid_load("content", "menu.edit.html", "referer, action,ErrorMessage,HOST,HTTP_ROOT");

        $tpl->fid_array("content", $menu, true);
        //$tpl->fid_select("content", "menu_order", $order, $menu["menu_order"]);
        break;

   case $action == "access":
        $action = "Права доступа для групп";
        $ErrorMessage = "Указанный раздел меню не существует!";
        if ($_GET["menu_id"] > 0) {
                $menu = $db ->fetch($db->query ("SELECT menu.* FROM menu WHERE menu_id=".$_GET["menu_id"]));
                if ($menu["menu_id"] > 0) {
                    $action .= " к странице \"".$menu["menu_name"]."\"";
                    $users_groups = $db->fetchall($db->query("select users_groups.users_groups_id, users_groups_name, IF(users_groups_access.menu_id IS NULL, '', 'selected') as selected from users_groups left join users_groups_access on (users_groups_access.users_groups_id = users_groups.users_groups_id and users_groups_access.menu_id = ".$_GET["menu_id"].") order by users_groups_name"));
                    $ErrorMessage = "";
                }
        }
        $tpl->fid_load("content", "menu.access.html", "referer,action,ErrorMessage");
        if (empty($ErrorMessage)) {
                $tpl->fid_array("content", $menu);
                $tpl->fid_loop("content", "users_groups", $users_groups);
        }
        $tpl->fid_if("content", Array("ErrorMessage" => !empty($ErrorMessage), "!ErrorMessage" => empty($ErrorMessage)));
        break;
   default:
        $count_items = $db ->fetch($db->query("SELECT count(menu_id) as num FROM menu"), 0);
        $action = "Список разделов";
        $tpl->fid_load("content", "menu.items.html", "action,pages,page,count_items,HTTP_ROOT");
        $level = 0;
        $menu = GetData($level, 0);
        $ifs["add"] = ($menu_id != 1);
        $ifs["items"] = (is_array($menu) && sizeof($menu) > 0);
        if (is_array($menu)) $tpl->fid_tree("content", "menu", $menu);
        break;
}




function menu_delete($id) {
global $db;
       $items = $db->fetchall($db->query("select menu_id from files where menu_owner = $id"));
       $db->query("delete from files where files_id = ".$id);
       foreach($items as $item) {
              menu_delete($item["menu_id"]);
       }
}

        if ($_GET["mode"] == "delete" && (($_GET["menu_id"] > 0 && $menu_id > 1) || ($_GET["menu_id"] > 1 && $menu_id == 1))) {
                $db->query("delete from menu where menu_id=".$_GET["menu_id"]);
                $db->query("delete from menu where menu_owner=".$_GET["menu_id"]);
        }

        if (!empty($_POST["save_order"])) {

           /*foreach($_POST["order"] as $key => $value) {
                   if (!empty($value) || is_numeric($value)) {
                        if ($key == 1 && $menu_id = 1) $value = 0;
                        $sql="UPDATE menu SET menu_order = '$value' WHERE menu_id = '$key'";
                        $db->query($sql);
                   }
           }*/
           $sql="UPDATE menu SET menu_active = '', menu_map = '' WHERE menu_id = $menu_id and menu_owner <> 0";
           $db->query($sql);
           if (is_array($_POST["menu_map"]) && sizeof($_POST["menu_map"])>0)
           foreach($_POST["menu_map"] as $key => $value) {
                   $sql="UPDATE menu SET menu_map = 'checked' WHERE menu_id = $key";
                   $db->query($sql);
           }
           if (is_array($_POST["menu_active"]) && sizeof($_POST["menu_active"])>0)
           foreach($_POST["menu_active"] as $key => $value) {
                   $sql="UPDATE menu SET menu_active = 'checked' WHERE menu_id = $key";
                   $db->query($sql);
           }
        }




        $tpl->fid_load("main", "index.main.html", "page_title");



        if (is_array($ifs)) $tpl->fid_if("content", $ifs);

foreach($tpl->files as $key => $value) {
   $tpl->fid_array($key, $lang);
}

$tpl->fid_show("main");
?>