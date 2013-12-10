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

$objects_options_edit_fields = "";

switch (true) {
       case ($_POST["action"] == "save"):
            $_COOKIE["return"] = ($_POST["return"] == 1) ? "checked" : "";
            setcookie("return", (($_POST["return"] == 1) ? "checked" : ""), mktime() + 31536000);

            if (!empty($obj->Options["objects_options_save_fields"])) {
                $objects_options_edit_fields = $obj->Options["objects_options_edit_fields"];
                $obj->Options["objects_options_edit_fields"] = $obj->Options["objects_options_save_fields"];
            }

            if (!$obj->SaveObject($_POST)) {
               $_POST["return"] = "";
               if (!empty($objects_options_edit_fields)) {
                        $obj->Options["objects_options_edit_fields"] = $objects_options_edit_fields;
               }
               $fields = explode(",", $obj->Options["objects_options_edit_fields"]);
               foreach($fields as $field) {
                  if (get_magic_quotes_gpc()) $_POST[$field] = stripslashes($_POST[$field]);
                  $obj->{$field} = $GLOBALS["db"]->data_decode($_POST[$field]);
               }
            }

            if (!empty($objects_options_edit_fields)) {
                $obj->Options["objects_options_edit_fields"] = $objects_options_edit_fields;
            }

            $_GET["item"] = $item = $obj->KeyValue;
            $action = $lang["caption_editing_item"];
            if ($_POST["return"] == "1") {
               header("Location: ".$_POST["referer"]);
               exit;
            }
            break;
       case (empty($_GET["item"]) || $_GET["item"] == "new"):
            $action = $lang["caption_adding_item"];
            $item = 0;
            break;
       case ($_GET["item"] > 0):
            $item = $_GET["item"];
            $action = $lang["caption_editing_item"];
            break;
}

$referer = (empty($_POST["referer"])) ? (!empty($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : $_SERVER["PHP_SELF"]) : $_POST["referer"];
$return = $_COOKIE["return"];
$referer = preg_replace("/action=(activate|deactivate)/", "", $referer);
if ($item > 0 && !$obj->Error) {
  $obj->GetObject($item);
}

$template = (!empty($obj->Options["objects_options_edit_template"])) ? $obj->Options["objects_options_edit_template"] : "edit.html";
//$tpl->fid_load("main", "main.html", "page_title");
$tpl->fid_load("content", $template, "action,referer,return");
                                      //
$tpl->fid_array("main", $obj->Table);
$tpl->fid_array("content", $obj->Table);

if (is_array($obj->Options["objects_options_required_fields"]))
foreach($obj->Options["objects_options_required_fields"] as $require) {
       $obj->Fields[$require["field_name"]]["field_title"] = $obj->Fields[$require["field_name"]]["field_title"]."<font color=red>*</font>";
}

$fields = explode(",", $obj->Options["objects_options_edit_fields"]);
$temp = Array();
foreach($fields as $field) {
       $temp[$field] = $obj->Fields[$field];
       $temp[$field]["as_plain"] = false;
       $temp[$field]["as_text"] = false;
       $temp[$field]["as_select"] = false;
       $temp[$field]["as_textarea"] = false;
       $temp[$field]["as_multiplie"] = false;
       $temp[$field]["as_enabler"] = false;
       $temp[$field]["as_password"] = false;
       $temp[$field]["as_file"] = false;
       $temp[$field]["as_date"] = false;
       $temp[$field]["as_datetime"] = false;
       $temp[$field]["as_time"] = false;
       switch(true) {
          case ($obj->Fields[$field]["field_type"] == "!edit"):
               $temp[$field]["as_plain"] = true;
               break;
          case ($obj->Fields[$field]["field_type"] == "date"):
               $temp[$field]["as_date"] = true;
               break;
          case ($obj->Fields[$field]["field_type"] == "datetime"):
               $temp[$field]["as_datetime"] = true;
               break;
          case ($obj->Fields[$field]["field_type"] == "time"):
               $temp[$field]["as_time"] = true;
               break;
          case ($obj->Fields[$field]["field_type"] == "password"):
               $temp[$field]["as_password"] = true;
               break;
          case ($obj->Fields[$field]["field_type"] == "file"):
               $temp[$field]["as_file"] = true;
               break;
          case (($obj->Fields[$field]["field_type"] == "int" || $obj->Fields[$field]["field_type"] == "double" || $obj->Fields[$field]["field_type"] == "varchar") && !is_array($obj->{$field."_values"})):
               $temp[$field]["as_text"] = true;
               break;
          case ($obj->Fields[$field]["field_type"] == "text" && !is_array($obj->{$field."_values"})):
               $temp[$field]["as_textarea"] = true;
               break;
          case ($obj->Fields[$field]["field_type"] == "enum" && ($obj->Fields[$field]["params"] == "'','checked'" || $obj->Fields[$field]["params"] == "'checked',''")):
               $temp[$field]["as_enabler"] = true;
               break;                                                                                             // || is_array($obj->{$field."_values"})
          case (($obj->Fields[$field]["field_type"] == "enum" && ($obj->Fields[$field]["params"] != "'','checked'" || $obj->Fields[$field]["params"] != "'checked',''"))):
               if (!is_array($obj->{$field."_values"}) && $obj->Fields[$field]["field_type"] == "enum") {
                  if (!is_array($obj->Links[$field])) {
                        $obj->{$field."_values"} = explode(",", $obj->Fields[$field]["params"]);
                  } else {
                        $result = $db->query("select distinct ".$obj->Links[$field]["DestinationFieldName"].", ".((!empty($obj->Links[$field]["DestinationTitleFieldPseudonim"])) ? $obj->Links[$field]["DestinationTitleFieldPseudonim"]." as ".$obj->Links[$field]["DestinationTitleFieldName"] : $obj->Links[$field]["DestinationTitleFieldName"])." from ".$obj->Links[$field]["TableName"]." ".((!empty($obj->Links[$field]["Where"])) ? "WHERE ".$obj->Links[$field]["Where"] : "" )." order by ".$obj->Links[$field]["DestinationTitleFieldName"]." asc");
                        $obj->{$field."_values"} = $db->fetchccol($result, 0);
                        if ($obj->Links[$field]["FirstEmpty"]) array_unshift($obj->{$field."_values"}, "");
                        $obj->{$field."_titles"} = $db->fetchccol($result, 1);
                        if ($obj->Links[$field]["FirstEmpty"]) array_unshift($obj->{$field."_titles"}, "");
                  }
               }
               foreach($obj->{$field."_values"} as $key => $value) {
                         $value = preg_replace("/^'(.*)'$/", "\\1", $value);
                         $obj->{$field."_values"}[$key] = Array($value, (!empty($obj->{$field."_titles"}[$key])) ? $obj->{$field."_titles"}[$key] : $value);
               }
               $temp[$field]["as_select"] = true;
               break;
          case ($obj->Fields[$field]["field_type"] == "set"):
               $temp[$field]["as_select"] = true;
               $temp[$field]["as_multiplie"] = true;
               if (!is_array($obj->Links[$field])) {
                        if (!is_array($obj->{$field."_values"}) && !is_array($obj->{$field."_titles"})) $obj->{$field."_values"} = explode(",", $obj->Fields[$field]["params"]);
               } else {
                        $result = $db->query("select distinct ".$obj->Links[$field]["DestinationFieldName"].", ".((!empty($obj->Links[$field]["DestinationTitleFieldPseudonim"])) ? $obj->Links[$field]["DestinationTitleFieldPseudonim"]." as ".$obj->Links[$field]["DestinationTitleFieldName"] : $obj->Links[$field]["DestinationTitleFieldName"])." from ".$obj->Links[$field]["TableName"]." ".((!empty($obj->Links[$field]["Where"])) ? "WHERE ".$obj->Links[$field]["Where"] : "" )." order by ".$obj->Links[$field]["DestinationTitleFieldName"]." asc");
                        $obj->{$field."_values"} = $db->fetchccol($result, 0);
                        if ($obj->Links[$field]["FirstEmpty"]) array_unshift($obj->{$field."_values"}, "");
                        $obj->{$field."_titles"} = $db->fetchccol($result, 1);
                        if ($obj->Links[$field]["FirstEmpty"]) array_unshift($obj->{$field."_titles"}, "");
               }
               foreach($obj->{$field."_values"} as $key => $value) {
                         $value = preg_replace("/^'(.*)'$/", "\\1", $value);
                         $obj->{$field."_values"}[$key] = Array($value, (!empty($obj->{$field."_titles"}[$key])) ? $obj->{$field."_titles"}[$key] : $value);
               }
               break;
       }

}
$fields = $temp;
$tpl->fid_loop("content", "fields", $fields);
$tpl->fid_loop("content", "req_values", $obj->Options["objects_options_required_fields"]);
$tpl->fid_loop("content", "required", $obj->Options["objects_options_required_fields"]);
                      //die($tpl->files["content"]);
foreach($obj->Fields as $key => $value) {
        if (($value["field_type"] == "varchar" || $value["field_type"] == "text") && !$value["no_convert"] && is_string($obj->{$key})) {
                $obj->{$key} = convert($obj->{$key}, html, q);
                $obj->{$key} = str_replace("{", "&#123;", $obj->{$key});
                $obj->{$key} = str_replace("}", "&#125;", $obj->{$key});
        }
        if ($value["field_type"] == "!edit" && is_array($obj->{$key."_titles"}) && sizeof($obj->{$key."_titles"}) > 0) {
         $obj->{"current_".$key} = $obj->{$key};
         $obj->{$key} = $obj->{$key."_titles"}[$obj->{$key}];
        }
        /*if ($value["field_type"] == "date") {
               if ($obj->{$key} == "0000-00-00") {
                  $obj->{$key."_day"} = $obj->{$key."_month"} = $obj->{$key."_year"} = "";
               } else {
                  $obj->{$key."_day"} = substr($obj->{$key}, 8, 2);
                  $obj->{$key."_month"} = substr($obj->{$key}, 5, 2);
                  $obj->{$key."_year"} = substr($obj->{$key}, 0, 4);
               }
        }*/


}

foreach($fields as $field) {
   if ($field["as_select"]) {
      if ($field["field_type"] == "set" && !is_array($obj->{$field["field_name"]})) $obj->{$field["field_name"]} = explode(",", $obj->{$field["field_name"]});
      $tpl->fid_select("content", $field["field_name"], $obj->{$field["field_name"]."_values"}, $obj->{$field["field_name"]});
   }
   //if (is_array($_POST) && $obj->Error) $obj->{$field["field_name"]} = $_POST[$field["field_name"]];
}

$tpl->fid_object("content", $obj);


?>