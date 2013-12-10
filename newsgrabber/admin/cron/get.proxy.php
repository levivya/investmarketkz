<?php
ini_set("memory_limit", "100M");
set_time_limit(0);

$path = preg_replace("!admin/cron/get.proxy.php.*!", "", __FILE__);
$path = !empty($DOCUMENT_ROOT) ? $DOCUMENT_ROOT."/" : ($path != "/" ? $path : "./../../");
if (!@file_exists($path."config.php") || !@include_once($path."config.php")) {
   die("config.php not found");
}

include_once(LIBDIR."lib.db.mysql.php");
//include(HOMEDIR."units/functions.php");
//include_once(LIBDIR."lib.process.php");

$db = new Db();

//$process = new Process(HOMEDIR."PIDS/", __FILE__);
//if (!$process->Open()) exit;

$options = $db->fetchall("select * from options", "options_name");
foreach($options as $key => $opt) $options[$key] = $opt["options_value"];

ini_set('log_errors','on');

//ini_set('display_errors','off');
// nulling - включим отображение ошибок !
//ini_set('display_errors','on');

error_reporting(E_ALL ^ E_NOTICE);

$ch = curl_init ();
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

$proxy_lists = $db->fetchall($db->query("select * from proxy_lists where proxy_lists_interval > 0 and proxy_lists_mask <> '' and proxy_lists_deleted <> 'checked' and NOW() - interval proxy_lists_interval minute > proxy_lists_updated order by proxy_lists_updated - interval proxy_lists_interval minute asc limit 1"));
//echo "\n"."select * from proxy_lists where proxy_lists_interval > 0 and proxy_lists_mask <> '' and proxy_lists_deleted <> 'checked' and NOW() - interval proxy_lists_interval minute > proxy_lists_updated order by proxy_lists_updated - interval proxy_lists_interval minute asc limit 500"."\n".mysql_error();
//echo "lists: ".sizeof($proxy_lists)."\n";
//flush();

$ids = array();
foreach($proxy_lists as $list) {
   $ids[] = $list["proxy_lists_id"];
}
if (sizeof($ids) > 0) {
   $db->query("update proxy_lists set proxy_lists_status = 'start', proxy_lists_found = 0 where proxy_lists_id in (".implode(", ", $ids).")");
   //echo "\n"."update proxy_lists set proxy_lists_status = 'start', proxy_lists_found = 0 where proxy_lists_id in (".implode(", ", $ids).")"."\n".mysql_error();
}

foreach($proxy_lists as $list) {
  $db->query("UPDATE `proxy_lists` SET proxy_lists_status = 'process' WHERE `proxy_lists_id` =".$list["proxy_lists_id"]);
  if (strpos($list["proxy_lists_mask"], "host") < strpos($list["proxy_lists_mask"], "port")) {$host_key = 1; $port_key = 2;} else {$host_key = 2; $port_key = 1;}
  //$list["proxy_lists_mask"] = preg_replace_callback("!([\.\!\[\]\(\)\*\-\?])!", "sl", $list["proxy_lists_mask"]);
  /*
  $list["proxy_lists_mask"] = str_replace("{host}", "([\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3})", $list["proxy_lists_mask"]);
  $list["proxy_lists_mask"] = str_replace("{port}", "([\d]{2,4})", $list["proxy_lists_mask"]);
  $list["proxy_lists_mask"] = str_replace("{skip}", ".*", $list["proxy_lists_mask"]);
  */
  $list["proxy_lists_mask"] = str_replace("{host}", "([\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3})", $list["proxy_lists_mask"]);
  $list["proxy_lists_mask"] = str_replace("{port}", "([\d]{2,4})", $list["proxy_lists_mask"]);
  $list["proxy_lists_mask"] = str_replace("|", "\\|", $list["proxy_lists_mask"]);
  $content = file_get_contents($list["proxy_lists_url"]);
  $content = preg_replace("![\s]{1,}!", "|", $content);
  //echo $list["proxy_lists_url"]."\n";
  //flush();

  preg_match_all("!".$list["proxy_lists_mask"]."!ms", $content, $matches, PREG_SET_ORDER);
  //echo "found: ".sizeof($matches)."\n";
  //flush();
  $saved = 0;
  foreach($matches as $key => $proxy) {
     //echo $proxy[$host_key].":".$proxy[$port_key]." - ";
     if ($db->fetch($db->query("select count(proxy_id) from proxy where proxy_host = '".$proxy[$host_key]."' and proxy_port = '".$proxy[$port_key]."'"), 0) == 0) {
        //echo " add\n";
        $temp = $proxy[$host_key];
        $temp1 = $proxy[$port_key];
        $host_key = 1;
        $port_key = 2;
        $proxy[1] = $temp;
        $proxy[2] = $temp1;
        curl_setopt ($ch, CURLOPT_PROXY, $proxy[$host_key].":".$proxy[$port_key]);
        //echo $proxy[$host_key].":".$proxy[$port_key]." - ";
        //flush();
        curl_setopt ($ch, CURLOPT_HEADER, "1");
        curl_setopt ($ch, CURLOPT_URL, "http://".CLIENT_HOST.$HTTP_ROOT."admin/proxy_test.php");
        curl_setopt ($ch, CURLOPT_TIMEOUT, 5);

        $time = getmicrotime();
        $result = curl_exec ($ch);
        $headers = substr($result, 0, strpos($result, "\r\n\r\n"));
        $result = substr($result, strpos($result, "\r\n\r\n"));
        //echo "\n'".$result."'\n";
        if (strpos($result, "test=ok") !== false) {
           $time = getmicrotime() - $time;
           $result = explode("&", $result);
           //print_r($result);
           $temp = array();
           foreach($result as $value) {
              $value = explode("=", trim($value));
              $temp[$value[0]] = $value[1];
              //echo "\t\t".$value[0]." = ".$value[1]."\n";
           }
           $result = $temp;

           if ($result["test"] == "ok") {
              $proxy_anonymous = ($result["REMOTE_ADDR"] != @gethostbyname(CLIENT_HOST) &&
                                  strpos($result["HTTP_X_FORWARDED_FOR"], $_SERVER["SERVER_ADDR"]) === false &&
                                  strpos($result["HTTP_VIA"], $_SERVER["SERVER_ADDR"]) === false &&
                                  strpos($result["HTTP_PROXY_CONNECTION"], $_SERVER["SERVER_ADDR"]) === false) ? "checked" : "";
              $db->query("insert into proxy (proxy_host, proxy_port, proxy_anonymous, proxy_time, proxy_lists_id) values ('".$proxy[1]."', '".$proxy[2]."', '$proxy_anonymous', ".$time.", ".$list["proxy_lists_id"].")");
              //echo "\n"."insert into proxy (proxy_host, proxy_port, proxy_anonymous, proxy_time, proxy_lists_id) values ('".$proxy[1]."', '".$proxy[2]."', '$proxy_anonymous', ".$time.", ".$list["proxy_lists_id"].")"."\n".mysql_error();
              $saved++;
           } else {
              $error = true;
           }
           $db->query("UPDATE `proxy_lists` SET `proxy_lists_updated` = NOW( ), proxy_lists_found = ".(round(($key * 100) / sizeof($matches)))." WHERE `proxy_lists_id` =".$list["proxy_lists_id"]);
        } else {
           $error = true;
        }

        //echo (($error) ? " - fail" : " - ok")."\n";
        //flush();

        if ($error) {
           $db->query("insert into proxy (proxy_host, proxy_port, proxy_deleted) values ('".$proxy[1]."', '".$proxy[2]."', 'checked')");
           //echo "\n"."insert into proxy (proxy_host, proxy_port, proxy_deleted) values ('".$proxy[1]."', '".$proxy[2]."', 'checked')"."\n".mysql_error();
        }
        //flush();
        //$process->Save("1");
     } else {
        //echo " exists\n";
     }
     //flush();
  }
  $db->query("UPDATE `proxy_lists` SET `proxy_lists_updated` = NOW( ), proxy_lists_status = 'done', `proxy_lists_found` = '".$saved."' WHERE `proxy_lists_id` =".$list["proxy_lists_id"]);
  //echo "\n"."UPDATE `proxy_lists` SET `proxy_lists_updated` = NOW( ), proxy_lists_status = 'done', `proxy_lists_found` = '".$saved."' WHERE `proxy_lists_id` =".$list["proxy_lists_id"]."\n".mysql_error();
}

curl_close ($ch);

//$process->Kill();

function sl($matches) {
    return "\\".$matches[1];
}


function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}
?>