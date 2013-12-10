<?
$HUI_WAM1 = "èõê ÷ù ôõô þôï-ôï òáúúäåîäéôå";
$HUI_WAM2 = "ÕÓÉ ÂÛ ÒÓÒ ×ÒÎ-ÒÎ ÐÀÇÇÄÅÍÄÈÒÅ";

/* ----------------------------------------------- */
/* --- nulled by someone for http://nulled.ws/ --- */
/* ----------------------------------------------- */

$_config_loaded = false;
@include ("config.php");
if ($_config_loaded !== true) {
   die("config.php not found");
}

if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__))) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__));

$action = (!empty($_GET["action"])) ? $_GET["action"] : ((!empty($_POST["action"])) ? $_POST["action"] : "");
$action = !empty($_POST["group_action"]) ? "group_action" : $action;

class proxy extends SiteObject {
        function GetObject($id) {
           global $db, $tpl;
           $get = parent::GetObject($id, false);
           return $get;
        }
}



$obj = new proxy("proxy");
$obj->Table = Array("table_name" => "proxy", "table_title" => "Proxy ñåðâåðà");
$page_title = "Proxy ñåðâåðà";
$obj->Fields["proxy_id"]["field_title"] = $lang["title_proxy_id"];
$obj->Fields["proxy_host"]["field_title"] = $lang["title_proxy_host"];
$obj->Fields["proxy_port"]["field_title"] = $lang["title_proxy_port"];
$obj->Fields["proxy_anonymous"]["field_title"] = $lang["title_proxy_anonymous"];
$obj->Fields["proxy_anonymous"]["no_convert"] = true;
$obj->Fields["proxy_time"]["field_title"] = $lang["title_proxy_time"];

$obj->Options["objects_options_show_fields"] = "proxy_id,proxy_host,proxy_anonymous,proxy_time,proxy_host as delete_name, CONCAT(proxy_host, ':', proxy_port) as proxy_host, '1' as `test`, IF(proxy_anonymous = 'checked', '+', '&nbsp;') as proxy_anonymous";
$obj->Options["objects_options_required_fields"] = Array(
                                                           Array("field_name" => "proxy_host", "js_error" => "proxy_host == ''", "as" => "!=''", "errormsg" => $lang["error_proxy_host"]),
                                                           Array("field_name" => "proxy_port", "js_error" => "proxy_port == ''", "as" => "!=''", "errormsg" => $lang["error_proxy_port"])
                                                        );
$obj->Options["objects_options_edit_fields"] = "proxy_host,proxy_port,proxy_anonymous";
$obj->add = true;
$obj->activate = false;
$obj->edit = true;
$obj->delete = true;
$obj->items_per_page = 20;
$obj->HTTP_ROOT = $HTTP_ROOT;

$obj->Actions = Array(Array("action" => "test", "action_image" => "misc.gif", "action_title" => $lang["button_test"]));

$where[] = "proxy_deleted <> 'checked' and proxy_errors = 0";
//$obj->Links["proxy_lists_id"] = Array("TableName" => "proxy_lists", "SourceFieldName" => "proxy_lists_id", "DestinationFieldName" => "proxy_lists_id", "DestinationTitleFieldName" => "proxy_lists_name", "Where" => "proxy_lists_id <> 2");
$obj->Rubrikators["proxy_lists"] = Array("TableName" => "proxy_lists", "TableTitle" => $lang["title_proxy_lists_id"], "IDField" => "proxy_lists_id", "TitleField" => "proxy_lists_url", "Where" => "proxy_lists_deleted <> 'checked'");

switch($action) {
       case "group_action":
           if (is_array($_POST["selected"]) && sizeof($_POST["selected"]) > 0) {
               switch($_POST["group_action"]) {
                   case "delete":
                        $obj->Delete($_POST["selected"]);
                        break;
                   case "activate":
                        $obj->Activate($_POST["selected"]);
                        break;
                   case "deactivate":
                        $obj->DeActivate($_POST["selected"]);
                        break;
               }
           }
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
           break;
      case "activate":
           if ($_GET["item"] > 0) $obj->Activate($_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "deactivate":
           if ($_GET["item"] > 0) $obj->DeActivate($_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "delete":
           if ($_GET["item"] > 0) $obj->Delete($_GET["item"]);
           header("Location: ".$_SERVER["HTTP_REFERER"]);
           exit;
      case "test":
           $obj->GetObject($_GET["item"]);
           $ch = curl_init ();
           curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt ($ch, CURLOPT_PROXY, $obj->proxy_host.":".$obj->proxy_port);
           curl_setopt ($ch, CURLOPT_HEADER, "1");
           curl_setopt ($ch, CURLOPT_URL, "http://".$_SERVER["HTTP_HOST"].$HTTP_ROOT."admin/proxy_test.php");
           curl_setopt ($ch, CURLOPT_TIMEOUT, 10);
           $time = getmicrotime();
           $result = curl_exec ($ch);
           curl_close ($ch);
           $time = getmicrotime() - $time;
           //echo $result;
           $headers = substr($result, 0, strpos($result, "\r\n\r\n"));
           $result = substr($result, strpos($result, "\r\n\r\n")+4);

           if (strpos($result, "test=ok") !== false) {
              $result = explode("&", $result);
              $temp = array();
              foreach($result as $value) {
                 $value = explode("=", trim($value));
                 $temp[$value[0]] = $value[1];
                 //echo "\t\t".$value[0]." = ".$value[1]."\n";
              }
              $result = $temp;
              echo "Proxy ".$obj->proxy_host.":".$obj->proxy_port." test ok.<br>";
              echo "time - ".$time."<br>";
              echo "REMOTE_ADDR = ".$result["REMOTE_ADDR"]."<br>";
              echo "HTTP_X_FORWARDED_FOR = ".$result["HTTP_X_FORWARDED_FOR"]."<br>";
              echo "HTTP_VIA = ".$result["HTTP_VIA"]."<br>";
              echo "HTTP_PROXY_CONNECTION = ".$result["HTTP_PROXY_CONNECTION"]."<br>";
           } else {
              echo "Proxy ".$obj->proxy_host.":".$obj->proxy_port." test error.";
           }
           exit;
           break;
      case "edit":
           if ($_GET["item"] == "new") {
              //$obj->Options["objects_options_edit_template"] = "add.proxy.html";
              $obj->Options["objects_options_edit_fields"] = "proxy_host";
              $obj->Fields["proxy_host"]["field_title"] = "Proxy:<br>host:port";
              $obj->Fields["proxy_host"]["field_type"] = "text";
              $obj->Options["objects_options_required_fields"] = Array(
                                                           Array("field_name" => "proxy_host", "js_error" => "proxy_host == ''", "as" => "!=''", "errormsg" => "Óêàæèòå ñïèñîê proxy!")
                                                        );
              if (!empty($_POST["proxy_host"])) {
                 $proxyList = explode("\n", $_POST["proxy_host"]);
                 foreach($proxyList as $proxy) {
                    $proxy = explode(":", trim($proxy));
                    if ($db->fetch($db->query("select count(proxy_id) from proxy where proxy_host = '".$proxy[0]."' and proxy_port = '".$proxy[1]."'"), 0) == 0) {
                       $db->query("insert into proxy (proxy_host, proxy_port) values('".$proxy[0]."', '".$proxy[1]."')");
                    }
                 }
                 header("Location: proxy.php");
                 exit;
              }
           }
           include("php/edit.php");

           break;
      default:
           $order = (!empty($_GET["order"])) ? $_GET["order"] : ((!empty($_POST["order"])) ? $_POST["order"] : "");
           $sort = (!empty($_GET["sort"])) ? $_GET["sort"] : ((!empty($_POST["sort"])) ? $_POST["sort"] : "ASC");
           include("php/items.php");
}

$tpl->fid_load("main", "index.main.html", "page_title");
$tpl->fid_array("content", $options);
$tpl->fid_array("main", $options);

foreach($tpl->files as $key => $value) {
   $tpl->fid_array($key, $lang);
}


$tpl->fid_show("main");


function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

?>