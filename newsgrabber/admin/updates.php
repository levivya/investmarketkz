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

use_functions("get_content");

if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__))) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__));

$info = array("siteID" => SITE_ID);
$host_pattertn = base64_encode($db->data_encode(serialize($info)));

if (!empty($_SESSION["updates_message"])) {
   $_UPDATES["message"] = $_SESSION["updates_message"];
   $_SESSION["updates_message"] = "";
}

                  //$_GET["updates"] = "restore";
switch($_GET["updates"]) {
   case "restore":
        if (!file_exists("ubak")) {
           $_UPDATES["message"] = "<P style=\"color: red\">BAKUP not found</P>";
        } else {
//           $result = @get_content("http://www.newsgrabber.inF0/download.php?siteID=".SITE_ID."&mode=filelist&submode=updates&host=".$host_pattertn."");
           $file_list = unserialize($db->data_decode(base64_decode($result["content"]), get_site_key("by script call")));
           if (is_array($file_list)) {
              $fnf = array(); 
              foreach($file_list["file"] as $file) {
                 if (!file_exists($DOCUMENT_ROOT."/admin/ubak/".$file)) {
                     $fnf[] = "admin/ubak/".$file;
                 } else {
                     if (file_exists($DOCUMENT_ROOT."/".$file)) unlink($DOCUMENT_ROOT."/".$file);
                     copy($DOCUMENT_ROOT."/admin/ubak/".$file, $DOCUMENT_ROOT."/".$file);
                 }
              }
              if (sizeof($fnf) > 0) $_SESSION["updates_message"] = "<P style=\"color: red\">".sizeof($fnf)." files are not restored </P>";
              $_SESSION["updates"] = false;
	          session_write_close();
              header("Location: updates.php");
              exit;
           } else {                                                                                                
              $_UPDATES["message"] = "<P style=\"color: red\">Server answer: ".$result["content"]."</P>";
           }
        }
        break;
   case "download":
        break;
//        header("Location: http://www.newsgrabber.inF0/download.php?siteID=".SITE_ID."&mode=updates&host=$host_pattertn");
        die();
        $ff = fopen("C:/www/sites/news_grabber/www/NGU.tgz", "wb");

        header("Accept-Ranges: bytes");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        header("Content-Type: application/x-tar");
        header("Content-Encoding: x-gzip");
        $tar_name = "NGUPDATES_".$_UPDATES["last_ver"].".tgz";
        header('Content-Disposition: attachment; filename="'.$tar_name.'"');
        header("Connection: close");

//        $fp = fsockopen ("www.newsgrabber.inF0", 80, $errno, $errstr, 30);
//        fwrite($fp, "GET /download.php?siteID=".SITE_ID."&mode=updates&host=$host_pattertn HTTP/1.0\r\nHost: www.newsgrabber.inF0\r\n\r\n");
        while (!feof($fp)) {
            if (!$save) $buffer .= fgets($fp,1024);
            if (!$save && substr($buffer, -4) == "\r\n\r\n") {
                if (preg_match("!Content-Length: ([0-9]{1,})!ms", $buffer, $regs) && $regs[1] > 0) {
                    header("Content-Length: ".$regs[1]);
                }
                $save = true;
            }
            if ($save) {
                $bbb = fread($fp, 1024);
                //$data .= $bbb;
                fwrite($ff, $bbb);
                echo $bbb;
            }
        }
        fclose($fp);
        fclose($ff);

        die();
        break;
   case "setup":
        $gzip = gzip_test();
        if ($gzip === "access fail") die("<font color=red>".HOMEDIR.": access denied</font><br>");
        if ($gzip === true) {
           echo "tar & gzip found<br>Загрузка архива....";
           flush();
//           $result = @get_content("http://www.newsgrabber.inF0/download.php?siteID=".SITE_ID."&mode=filelist&submode=updates&host=".$host_pattertn."");
           $file_list = unserialize($db->data_decode(base64_decode($result["content"]), get_site_key("by script call")));
           if (is_array($file_list)) {
              if (!file_exists("ubak")) {
                 $ubak = @mkdir("ubak");
                 if (!$ubak) "<br><font color=red>BAK DIR (admin/ubak) fail, no bakup!</font>";
              } else {
                 $ubak = true;
              }
              if ($_GET["nobak"] == 1) $ubak = false;
              if ($ubak) {                     
                     foreach($file_list["dir"] as $dir) {
                        if (!file_exists($DOCUMENT_ROOT."/admin/ubak/".$dir)) mkdir($DOCUMENT_ROOT."/admin/ubak/".$dir);
                     }
              }
              foreach($file_list["file"] as $file) {
                  if ($ubak) {
                     if (file_exists($DOCUMENT_ROOT."/admin/ubak/".$file)) @unlink($DOCUMENT_ROOT."/admin/ubak/".$file); 
                     copy($DOCUMENT_ROOT."/".$file, $DOCUMENT_ROOT."/admin/ubak/".$file);
                  }
                  if (file_exists($DOCUMENT_ROOT."/".$file)) unlink($DOCUMENT_ROOT."/".$file);
              }
           } else {
              die("<br><font color=red>Server answer: ".$result["content"]."</font>");
           }
//           $fp = fsockopen ("www.newsgrabber.inF0", 80, $errno, $errstr, 30);
           if (!$fp) {
                echo "<font color=red>FAIL</font><br>";
                $error = "Ошибка при получении архива. Установка не завершена.";
           } else {
//                fputs ($fp, "GET /download.php?siteID=".SITE_ID."&mode=updates&host=$host_pattertn HTTP/1.0\r\nHost: www.newsgrabber.inF0\r\n\r\n");
                $f = fopen(HOMEDIR."NGUPDATES_".$GLOBALS["_UPDATES"]["last_ver"].".tgz", "wb");
                while (!feof($fp)) {
                      if (!$save) $buffer .= fgets($fp,1024);
                      if (!$save && substr($buffer, -4) == "\r\n\r\n") {
                         $save = true;
                      }
                      if ($save) {
                        $buff = fgets($fp,1024);
                        if (strpos($buff, "ERROR:") !== false) die("<font color=red>".$buff."</font><br>");
                        fwrite($f, $buff);
                      }
                }
                fclose ($fp);
                fclose ($f);
                echo "<font color=green>OK</font><br>";
                echo "Распаковка архива....";
                flush();
                chdir(HOMEDIR);
                system("tar -xzf ".HOMEDIR."NGUPDATES_".$GLOBALS["_UPDATES"]["last_ver"].".tgz");
                @unlink(HOMEDIR."NGUPDATES_".$GLOBALS["_UPDATES"]["last_ver"].".tgz");

                if (is_array($file_list)) {
                    foreach($file_list["file"] as $file) {
                        if (!file_exists($DOCUMENT_ROOT."/".$file)) {
                            $install_ok = false;
                            $file_error = "error install file ".$DOCUMENT_ROOT."/".$file;
                            break;
                        }
                    }
                }
                if (file_exists(HOMEDIR."admin/uq.php")) {
                   include(HOMEDIR."admin/uq.php");
                   unlink(HOMEDIR."admin/uq.php");
                }

                echo $install_ok ? "<font color=green>OK</font><br>" : "<font color=red>".$file_error."</font><br>";
                chdir(HOMEDIR."admin/");

                flush();
                echo "";
           }

        } else {
           echo str_repeat(" ", 5000);
           echo "tar & gzip NOT found<br>Загрузка списка файлов....";
           flush();
//           $result = @get_content("http://www.newsgrabber.inF0/download.php?siteID=".SITE_ID."&mode=filelist&submode=updates&host=$host_pattertn");
           $file_list = unserialize($db->data_decode(base64_decode($result["content"]), get_site_key("by script call")));
           if (!file_exists("ubak")) {
              $ubak = @mkdir("ubak");
              if (!$ubak) "<br><font color=red>BAK DIR (admin/ubak) fail, no bakup!</font>";
           } else {
              $ubak = true;
           }
           if ($_GET["nobak"] == 1) $ubak = false;
           if ($ubak) {                     
              foreach($file_list["dir"] as $dir) {
                if (!file_exists($DOCUMENT_ROOT."/admin/ubak/".$dir)) mkdir($DOCUMENT_ROOT."/admin/ubak/".$dir);
              }
           }
           if (is_array($file_list)) {

              if (empty($error)) {
                 echo "<font color=green>OK</font><br>";
//                 $fp = fsockopen ("www.newsgrabber.inF0", 80, $errno, $errstr, 30);
                 $file_counter = 0;
                 $file = $file_list["file"][$file_counter];
                 echo "Загрузка файла ".$file."....";
                 flush();
                 if (is_resource($fp)) {
//                        fwrite($fp, "GET /download.php?siteID=".SITE_ID."&mode=get&submode=updates&file=".base64_encode($db->data_encode($file, get_site_key("by script call")))."&host=$host_pattertn&test_by_alex=1 HTTP/1.0\r\nHost: www.newsgrabber.inF0\r\nConnection: KeepAlive\r\n\r\n");
                        while (!feof($fp)) {
                            $buffer .= fgets($fp,1024);
                            if (strpos($buff, "ERROR:") !== false) die("<font color=red>".$buff."</font><br>");
                            if (strpos($buffer, "####end_of_file####") !== false) {
                                if (strpos($buffer, "####start_of_file####") !== false) $content = substr($buffer, 21 + strpos($buffer, "####start_of_file####"));
                                if (strpos($content, "####end_of_file####") !== false) $content = substr($content, 0, strpos($content, "####end_of_file####"));
                                $buffer = substr($buffer, 21 + strpos($buffer, "####end_of_file####"));
                                if ($ubak) {
                                    if (file_exists($DOCUMENT_ROOT."/admin/ubak/".$file)) @unlink($DOCUMENT_ROOT."/admin/ubak/".$file);
                                    @copy($DOCUMENT_ROOT."/".$file, $DOCUMENT_ROOT."/admin/ubak/".$file);
                                }
                                unlink($DOCUMENT_ROOT."/".$file);
                                $f = fopen($DOCUMENT_ROOT."/".$file, "wb");
                                $saved = fwrite($f, $content);
                                fclose ($f);
                                if ($saved > 0) {
                                    echo "<font color=green>OK</font><br>";
                                    flush();
                                } else {
                                    echo "<font color=red>FAIL</font>";
                                    $error = "Ошибка при получении файлов. Установка не завершена.";
                                    break;
                                }
                                $file_counter++;
                                if (!empty($file_list["file"][$file_counter])) {
                                    $file = $file_list["file"][$file_counter];
                                    echo "Загрузка файла ".$file."....";
                                    flush();
                                }
                            }
                        }
                 } else {
                    echo "<font color=red>FAIL</font>";
                    $error = "Ошибка при получении файлов. Установка не завершена.";
                 }
              }

              if (file_exists(HOMEDIR."admin/uq.php")) {
                 include(HOMEDIR."admin/uq.php");
                 unlink(HOMEDIR."admin/uq.php");
              }

           } else {
              $error = "Ошибка при получении списка файлов. Установка не завершена.";
              echo "<font color=red>FAIL</font>";
           }
        }
        if (empty($error)) {
          $_SESSION["updates"] = false;
          session_write_close();
          //check_updates();
          echo "<P>Обновление успешно установлено.</P>
          <P><a href=\"".HTTP_ROOT."admin/news.php\">back</a></P>";
        } else {
          echo "<P>".$error."</P>";
        }
        die();
        break;
}
                             
if (!file_exists("updates.history.txt")) {
//   $result = @get_content("http://www.newsgrabber.inF0/updates.php?history");
   $_UPDATES["history"] = unserialize(base64_decode($result["content"]));
   $current_ver = strtotime("20".substr($_UPDATES["current_ver"], 0, 2)."-".substr($_UPDATES["current_ver"], 2, 2)."-".substr($_UPDATES["current_ver"], 4, 2));
   foreach($_UPDATES["history"] as $k => $u) {
       $updates_ver = strtotime("20".substr($u["updates_ver"], 0, 2)."-".substr($u["updates_ver"], 2, 2)."-".substr($u["updates_ver"], 4, 2));
       $_UPDATES["history"][$k]["not_installed"] = $current_ver < $updates_ver;
       if ($_UPDATES["current_ver"] == $u["updates_ver"] && $current_ver > strtotime("2006-06-29")) { 
          $_UPDATES["history"][$k]["restore"] = true;
       }
       
   }
}

if (!file_exists("ubak")) {
    $ubak = @mkdir("ubak");
    if (!$ubak) $_UPDATES["bak_dir_error"] = "<br><font color=red>BAK DIR (admin/ubak) fail, no bakup!</font><br>";
}

$tpl->fid_load("main", "index.main.html", "page_title");
$tpl->fid_load("content", "updates.html");

$tpl->fid_array("content", $_UPDATES, true);
$tpl->fid_loop("content", "updates", $_UPDATES["history"]);
$tpl->fid_array("content", $options);
$tpl->fid_array("main", $options);
foreach($tpl->files as $key => $value) {
   $tpl->fid_array($key, $lang);
}

$tpl->fid_show("main");


function gzip_test() {
   $file_content = "H4sIAIeIcUQAA+3QMQrCQBBG4TnKniDOmsl6hCAWsbazWLAQFpIJenwDKQSLgEWS5n3N30zxmOrgefDK3y6r0aiazERnvzs5SVSrrVaLxzTdx8YaCbpe0tc4+L0PQfpSFl/weuT83CJoW+3tfA3dZe8MAAAAAAAAAAAAAAAAAMCfPovrDskAKAAA";
   $f = @fopen(HOMEDIR."text.tar.gz", "wb");
   if (!is_resource($f)) {
        return "access fail";
   }
   @fwrite($f, base64_decode($file_content));
   @fclose($f);
   chdir(HOMEDIR);
   @exec("tar -xzf ".HOMEDIR."text.tar.gz", $arr);
   chdir(HOMEDIR."admin/");
   $test_str = @file_get_contents(HOMEDIR."test.txt");
   if (file_exists(HOMEDIR."text.tar.gz")) @unlink(HOMEDIR."text.tar.gz");
   if (file_exists(HOMEDIR."test.txt")) @unlink(HOMEDIR."test.txt");
   return $test_str == "GZIP OK";
}
?>