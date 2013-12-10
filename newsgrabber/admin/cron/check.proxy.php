<?php
ini_set("memory_limit", "100M");
error_reporting(0);
set_time_limit(0);

$path = preg_replace("!admin/cron/check.proxy.php.*!", "", __FILE__);
$path = !empty($DOCUMENT_ROOT) ? $DOCUMENT_ROOT."/" : ($path != "/" ? $path : "./../../");
if (!@file_exists($path."config.php") || !@include_once($path."config.php")) {
   die("config.php not found");
}

require(LIBDIR."lib.db.mysql.php");
$db = new Db();

//$process = new Process(HOMEDIR."PIDS/", __FILE__);
//if (!$process->Open()) exit;

$result = @mysql_query("select * from options");
while ($temp = @mysql_fetch_assoc($result)) {
  $options[$temp["options_name"]] = $temp["options_value"];
}

$ch = curl_init ();
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                                                             //where                                                                                                // limit 200
$proxy_list = $db->fetchall(mysql_query("select * from proxy where proxy_deleted <> 'checked' and ((proxy_checked <> 'checked' and proxy_checked_date < NOW() - interval 1 hour) or (proxy_checked = 'checked' and proxy_checked_date < NOW() - interval 1 hour)) order by proxy_id limit 100"));
//echo "<br>"."select * from proxy where proxy_deleted <> 'checked' and ((proxy_checked <> 'checked' and proxy_checked_date < NOW() - interval 1 hour) or (proxy_checked = 'checked' and proxy_checked_date < NOW() - interval 1 hour)) order by proxy_id limit 100"."<br>".mysql_error();
$ids = array();
foreach($proxy_list as $proxy) {
   $ids[] = $proxy["proxy_id"];
}
if (sizeof($ids) > 0) {
   mysql_query("update proxy set proxy_checked = 'checked', proxy_checked_date = NOW() where proxy_id in (".implode(", ", $ids).")");
   //echo "<br>"."update proxy set proxy_checked = 'checked', proxy_checked_date = NOW() where proxy_id in (".implode(", ", $ids).")"."<br>".mysql_error();
}
//echo "proxy: ".sizeof($ids)."<br>";
foreach($proxy_list as $proxy) {
    mysql_query("delete from proxy where proxy_id <> ".$proxy["proxy_id"]." and proxy_host = '".$proxy[$host_key]."' and proxy_port = '".$proxy[$port_key]."'");
    if (mysql_affected_rows() > 0) //echo "<font color=red>deleted - ".mysql_affected_rows()."</font><br>";
    mysql_query("update proxy set proxy_checked = 'checked', proxy_checked_date = NOW() where proxy_id in (".implode(", ", $ids).") and proxy_id > ".$proxy["proxy_id"]);

    //echo $proxy["proxy_host"].":".$proxy["proxy_port"]." (".($checked[$proxy["proxy_host"].$proxy["proxy_port"]] ? "old" : "new").") - ";
    if ($checked[$proxy["proxy_host"].$proxy["proxy_port"]] != true) {
        $checked[$proxy["proxy_host"].$proxy["proxy_port"]] = true;
        curl_setopt ($ch, CURLOPT_PROXY, $proxy["proxy_host"].":".$proxy["proxy_port"]);
        //flush();
        curl_setopt ($ch, CURLOPT_HEADER, "");
        curl_setopt ($ch, CURLOPT_URL, "http://".$_SERVER["HTTP_HOST"].$HTTP_ROOT."admin/proxy_test.php");

        curl_setopt ($ch, CURLOPT_TIMEOUT, 10);
        $time = getmicrotime();
        $result = curl_exec ($ch);
        $error = false;
        if (strpos($result, "test=ok") !== false) {
           $time = getmicrotime() - $time;
           $result = explode("&", $result);
           foreach($result as $value) {
             $value = explode("=", $value);
             $temp[$value[0]] = $value[1];
           }
           $result = $temp;
           if ($result["test"] == "ok") {
              //$proxy_anonymous = ($result["REMOTE_ADDR"] != "209.160.40.72" && $result["HTTP_X_FORWARDED_FOR"] == "" && $result["HTTP_VIA"] == "" && $result["HTTP_PROXY_CONNECTION"] == "") ? "checked" : "";
              $proxy_anonymous = ($result["REMOTE_ADDR"] != @gethostbyname(CLIENT_HOST) &&
                                  strpos($result["HTTP_X_FORWARDED_FOR"], $_SERVER["SERVER_ADDR"]) === false &&
                                  strpos($result["HTTP_VIA"], $_SERVER["SERVER_ADDR"]) === false &&
                                  strpos($result["HTTP_PROXY_CONNECTION"], $_SERVER["SERVER_ADDR"]) === false) ? "checked" : "";
              mysql_query("update proxy set proxy_anonymous = '$proxy_anonymous', proxy_time = '".$time."', proxy_errors = 0, proxy_checked = '', proxy_checked_date = NOW() where proxy_id = ".$proxy["proxy_id"]);
              //echo "ok [ok = ".$result["test"]."]<br>";
              //echo nl2br(print_r($result, true));
              //echo "<br>"."update proxy set proxy_anonymous = '$proxy_anonymous', proxy_time = '".$time."', proxy_errors = 0, proxy_checked = '', proxy_checked_date = NOW() where proxy_id = ".$proxy["proxy_id"]."<br>".mysql_error();
              //flush();
           } else {
              $error = true;
           }
        } else {
           $error = true;
        }
        if ($error) {
           //echo "fail";
           if ($proxy["proxy_errors"] == 2) {
                mysql_query("update proxy set proxy_deleted = 'checked', proxy_checked = '', proxy_checked_date = NOW() where proxy_id = ".$proxy["proxy_id"]);
                //echo "<br>"."update proxy set proxy_deleted = 'checked', proxy_checked = '', proxy_checked_date = NOW() where proxy_id = ".$proxy["proxy_id"]."<br>".mysql_error();
           } else {
                mysql_query("update proxy set proxy_errors = proxy_errors + 1, proxy_checked = '', proxy_checked_date = NOW() where proxy_id = ".$proxy["proxy_id"]);
                //echo "<br>"."update proxy set proxy_errors = proxy_errors + 1, proxy_checked = '', proxy_checked_date = NOW() where proxy_id = ".$proxy["proxy_id"]."<br>".mysql_error();
           }
        }
        //$process->Save("1");
    }
    //echo "<br>";
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