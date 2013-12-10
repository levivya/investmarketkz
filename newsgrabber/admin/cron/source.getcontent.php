<?
@set_time_limit(0);
$path = preg_replace("!admin/cron/source.getcontent.php.*!", "", str_replace("\\", "/", __FILE__));
$path = !empty($DOCUMENT_ROOT) ? $DOCUMENT_ROOT."/" : ($path != "/" ? $path : "./../../");
if (!@file_exists($path."config.php") || !@include_once($path."config.php")) {
   die("config.php not found");
}

$HUI_WAM1 = "ихк чщ фхф юфп-фп тбъъдеодйфе";
$HUI_WAM2 = "ХУЙ ВЫ ТУТ ЧТО-ТО РАЗЗДЕНДИТЕ";

/* ----------------------------------------------- */
/* --- nulled by someone for http://nulled.ws/ --- */
/* ----------------------------------------------- */

use_functions("get_content,get_proxy,get_agent,rss_get_content,rss_get_swf,utf_to_win,get_remote_filesize,to_translit");

include_once(LIBDIR."lib.db.mysql.php");

$db = new Db();

$options = $db->fetchall("select * from options", "options_name");
foreach($options as $key => $opt) $options[$key] = $opt["options_value"];
$options["grab_text_imagesize_limit"] = explode(":", $options["grab_text_imagesize_limit"]);

$db->query("update rss set rss_proceed = '' where rss_last_update + interval (rss_interval+15) minute < NOW()");

if ($_GET["id"] > 0 || is_array($_GET["id"])) {
   if (!is_array($_GET["id"])) $_GET["id"] = array($_GET["id"]);
   $rss_ = $db->fetchall($db->query("select rss_last_update + interval rss_interval minute as update_time, rss.* from rss where rss_id in ('".implode("', '", $_GET["id"])."') order by update_time asc"));
} else {
   $rss_ = $db->fetchall($db->query("select rss_last_update + interval rss_interval minute as update_time, rss.* from rss where (rss_last_update = '0000-00-00 00:00:00' or rss_last_update + interval rss_interval minute < NOW()) and rss_active = 'checked' and rss_proceed != 'checked' order by update_time asc limit 5"));
}

if (!is_array($rss_) || sizeof($rss_) < 1) die();
foreach($rss_ as $rss) {
        $ids[] = $rss["rss_id"];
}
$db->query("update rss set rss_last_update = NOW(), rss_proceed = 'checked' where rss_id in (".implode(",", $ids).")");
foreach($rss_ as $rss) {
    /*debug*/
    $rss["rss_reciveswf"] = "checked";
    
    $message = "";
    $contnue = false;
    $rss_id = $rss["rss_id"];
    $rss["rss_fields"] = unserialize($rss["rss_fields"]);
    $items = array();

    if (!empty($rss["rss_replacement"])) {
        $rss["rss_replacement"] = explode("\n", $rss["rss_replacement"]);
        foreach($rss["rss_replacement"] as $k => $r) {
            if (substr($r, -1) == "\r") $r = substr($r, 0, -1);
            if (!empty($r)) {
                $r = explode("|", $r);
                $rss["rss_replacement"][$k] = array($r[0], $r[1]);
            }
        }
    } else {
        $rss["rss_replacement"] = false;
    }

    if ($options["grab_text_use_proxy"] == "checked") {
       do {
          $result = get_content($rss["rss_url"], $options["grab_text_use_proxy"] == "checked", 30);
       } while (!empty($rss["rss_uniq_id"]) && strpos($result["content"], $rss["rss_uniq_id"]) === false);
    } else {
       $result = get_content($rss["rss_url"], false, 60);
    }
    $rss_url = parse_url($rss["rss_url"]);
    $xml = $result["content"];
    $headers = $result["headers"];
    if ($_GET["id"] < 1 && preg_match("!<lastBuildDate>(.*)</lastBuildDate>!i", $xml, $regs)) {
        if (!empty($rss["lastBuildDate"]) && !empty($regs[1]) && $regs[1] == $rss["lastBuildDate"]) {
            $contnue = true;
        }
    }
    /*
    $fp = fsockopen ($rss_url["host"], 80, $errno, $errstr, 30);
    $xml = "";
    if ($fp) {
        fputs ($fp, "GET ".$rss_url["path"].(!empty($rss_url["query"]) ? "?".$rss_url["query"] : "")." HTTP/1.0\r\nUser-Agent: $ag\r\nHost: ".$rss_url["host"]."\r\n\r\n");
        while (!feof($fp)) {
            $buffer = fgets ($fp,1024);
            if (strpos("\r\n\r\n", $buffer) !== false) {
                $headers = $buffer;
                $save = true;
                $buffer = "";
            }

            if ($save) $xml .= $buffer;
            if ($_GET["id"] < 1 && preg_match("!<lastBuildDate>(.*)</lastBuildDate>!i", $xml, $regs)) {
                if (!empty($rss["lastBuildDate"]) && !empty($regs[1]) && $regs[1] == $rss["lastBuildDate"]) {
                    $contnue = true;
                    break;
                }
            }
        }
        fclose ($fp);
    }
    */

    if (!$contnue) {
        $items = array();
        $links_n = 0;

        if (empty($rss["rss_titletemplate"])) {
                if (!$rss["rss_fields"] || empty($rss["rss_fields"])) die("RSS fields error");
                foreach($rss["rss_fields"] as $key => $value) {
                    if (in_array($key, array("rss_news_title", "rss_news_link")) && empty($value)) die("RSS field $key error");
                }

                if ($xml[0] != "<") $xml = substr($xml, strpos($xml, "<"));
                if (strpos(strtolower($headers), "koi8-r") !== false || strpos(strtolower($xml), "encoding=\"koi8-r\"") !== false) $xml = convert_cyr_string($xml, "k", "w");
                if (strpos(strtolower($headers), "utf-8") !== false || strpos(strtolower($xml), "encoding=\"utf-8\"") !== false) $xml = utf_to_win($xml);

                if (strpos($xml, "<item>") !== false || strpos($xml, "<item ") !== false) {
                    $rss_item_tag_name = "item";
                } elseif (strpos($xml, "<entry>") !== false || strpos($xml, "<entry ") !== false) {
                   $rss_item_tag_name = "entry";
                }

                $xmlParser = xml_parser_create();
                xml_parser_set_option($xmlParser,XML_OPTION_CASE_FOLDING,TRUE);
                xml_parser_set_option($xmlParser,XML_OPTION_SKIP_WHITE,TRUE);
                xml_set_element_handler($xmlParser,"se","ee");
                xml_set_character_data_handler($xmlParser,"cd");
                xml_parse($xmlParser,$xml);
                xml_parser_free($xmlParser);
                $from_rss = true;
                foreach($items as $key => $item) {
                    if (strpos($rss["rss_fields"]["rss_news_title"], ":") !== false) {
                        $temp = explode(":", $rss["rss_fields"]["rss_news_title"]);
                        $items[$key]["news_title"] = $items[$key][$rss["rss_fields"]["rss_news_title"]]["attributes"][$temp[1]];
                    } else {
                        $items[$key]["news_title"] = $items[$key][$rss["rss_fields"]["rss_news_title"]]["value"];
                    }
                    if (strpos($rss["rss_fields"]["rss_news_description"], ":") !== false) {
                        $temp = explode(":", $rss["rss_fields"]["rss_news_description"]);
                        $items[$key]["news_description"] = $items[$key][$rss["rss_fields"]["rss_news_description"]]["attributes"][$temp[1]];
                    } else {
                        $items[$key]["news_description"] = $items[$key][$rss["rss_fields"]["rss_news_description"]]["value"];
                    }
                    if (strpos($rss["rss_fields"]["rss_news_text"], ":") !== false) {
                        $temp = explode(":", $rss["rss_fields"]["rss_news_text"]);
                        $items[$key]["news_text"] = $items[$key][$rss["rss_fields"]["rss_news_text"]]["attributes"][$temp[1]];
                    } else {
                        $items[$key]["news_text"] = $items[$key][$rss["rss_fields"]["rss_news_text"]]["value"];
                    }
                    if (strpos($rss["rss_fields"]["rss_news_link"], ":") !== false) {
                        $temp = explode(":", $rss["rss_fields"]["rss_news_link"]);
                        $item["news_link"] = $items[$key]["news_link"] = $items[$key][$temp[0]]["attributes"][$temp[1]];
                    } else {
                        $item["news_link"] = $items[$key]["news_link"] = $items[$key][$rss["rss_fields"]["rss_news_link"]]["value"];
                    }

                    if (!empty($rss["rss_print_link_from"])) {
                        $item["news_link_old"] = $item["news_link"];
                        $item["news_link"] = preg_replace("!".$rss["rss_print_link_from"]."!", $rss["rss_print_link_to"], $item["news_link"]);
                    }
                    $item_url = parse_url($item["news_link"]);
                    $item_url["dir_path"] = $item_url["path"];
                    $items[$key]["dir_url"] = "http://".$item_url["host"].$item_url["dir_path"];
                    if (preg_match("!\.[a-z0-9]{2,5}$!i", $items[$key]["dir_url"])) $items[$key]["dir_url"] = substr($items[$key]["dir_url"], 0, strpos($items[$key]["dir_url"], basename($items[$key]["dir_url"])));
                }


        } else {
                if (strpos(strtolower($headers), "koi8-r") !== false || strpos(strtolower(substr($xml, strpos(strtolower($xml), "<head>"), strpos(strtolower($xml), "</head>"))), "koi8-r")) $xml = convert_cyr_string($xml, "k", "w");
                if (strpos(strtolower($headers), "utf-8") !== false || strpos(strtolower(substr($xml, strpos(strtolower($xml), "<head>"), strpos(strtolower($xml), "</head>"))), "utf-8")) $xml = utf_to_win($xml);

                preg_match_all("!{(title|image|link|description|text)}!U", $rss["rss_titletemplate"], $regs);
                $positions["image"] = $positions["link"] = $positions["description"] = $positions["text"] = false;
                $positions["title"] = array();
                foreach($regs[1] as $k => $r) {
                    if ($r == "title") {
                       $positions[$r][] = $k+1;
                    } else {
                       $positions[$r] = $k+1;
                    }
                }
                if (preg_match_all("!{([^}]{0,})}{title}!U", $rss["rss_titletemplate"], $regs)) {
                    $delimeters = array();
                    foreach($regs[1] as $k => $r) {
                       $delimeters[] = $r;
                       $rss["rss_titletemplate"] = str_replace($regs[0][$k], "{title}", $rss["rss_titletemplate"]);
                    }
                }

                $rss["rss_titletemplate"] = addcslashes($rss["rss_titletemplate"], "[]!-.?*\\()|");
                $rss["rss_titletemplate"] = preg_replace("!{title}|{image}|{link}|{description}|{text}!U", "(.*)", $rss["rss_titletemplate"]);
                $rss["rss_titletemplate"] = str_replace("{skip}", ".*", $rss["rss_titletemplate"]);
                $rss["rss_titletemplate"] = preg_replace("![\n\r\t]!s", "", $rss["rss_titletemplate"]);
                $rss["rss_titletemplate"] = preg_replace("!>[ ]{1,}<!s", "><", $rss["rss_titletemplate"]);
                $xml = preg_replace_callback("!<pre>(.*)</pre>!Uims", "nlbr", $xml);
                $xml = preg_replace("![\n\r\t]!s", "", $xml);
                $xml = preg_replace("!>[ ]{1,}<!s", "><", $xml);
                $surl = parse_url($rss["rss_url"]);
                $surl["dir_path"] = trim("http://".$surl["host"].$surl["path"]);
                //if (preg_match("!(\.aspx|\.asp|\.cgi|\.pl|\.py|\.shtml|\.shtm|\.html|\.htm|\.php3|\.php|\.xml)$!", $surl["dir_path"])) $surl["dir_path"] = substr($surl["path"], 0, strpos($surl["dir_path"], basename($surl["path"])));
                if (preg_match("!\.[a-z0-9]{2,5}$!i", $surl["dir_path"])) $surl["dir_path"] = substr($surl["dir_path"], 0, strpos($surl["dir_path"], basename($surl["dir_path"])));
                if (substr($surl["dir_path"], -1) == "/") $surl["dir_path"] = substr($surl["dir_path"], 0, -1);
                if (preg_match_all("!".$rss["rss_titletemplate"]."!Ums", $xml, $regs, PREG_SET_ORDER)) {
                 //print_r($regs);die();
                    foreach($regs as $found) {
                        foreach($found as $kkk => $vvv) $found[$kkk] = html_entity_decode($vvv);
                        $found[$positions["link"]] = $positions["link"] === false ? "" : $found[$positions["link"]];
                        if (substr($found[$positions["link"]], 0, 1) == "/") $found[$positions["link"]] = "http://".$surl["host"].$found[$positions["link"]];
                        if (substr($found[$positions["link"]], 0, 4) != "http") $found[$positions["link"]] = $surl["dir_path"]."/".$found[$positions["link"]];
                        $item_url = parse_url($found[$positions["link"]]);
                        $item_url["dir_path"] = "http://".$item_url["host"].$item_url["path"];
                        if (preg_match("!\.[a-z0-9]{2,5}$!i", $item_url["dir_path"])) $item_url["dir_path"] = substr($item_url["dir_path"], 0, strpos($item_url["dir_path"], basename($item_url["dir_path"])));
                        $title = "";
                        if (sizeof($positions["title"]) > 0) {
                           foreach($positions["title"] as $pkey => $pos) {
                              $title .= $found[$pos].$delimeters[$pkey];
                           }
                        }
                        $items[] = array("news_title" => $title, "news_link" => $positions["link"] === false ? "" : $found[$positions["link"]], "news_description" => (($positions["image"] !== false) ? "<img src=\"".$found[$positions["image"]]."\">\n" : "").(($positions["description"] !== false) ? $found[$positions["description"]] : ""), "news_text" => $positions["text"] === false ? "" : $found[$positions["text"]], "dir_url" => $item_url["dir_path"]);
                   }

                }
                $from_html = true;
        }

        $saved = 0;
        if (is_array($items)) {
            $items = array_reverse($items);
            
            foreach($items as $items_key => $item) {

                $item["news_title"] = preg_replace("![\n\t\r]!", "", $item["news_title"]);
                $item["news_title"] = preg_replace("![ ]{2,}!", " ", $item["news_title"]);

                $parsed_link = parse_url($item["news_link"]);
                $parsed_link["dir_url"] = $item["dir_url"];
                $_SESSION["Host"] = $Host = "http://".$parsed_link["host"]."/";
                $news_text = "";
                $images = $alts = Array();
                $item["news_guid"] = (!empty($item["guid"]["value"])) ? $item["guid"]["value"] : $item["news_link"];
                /*if ($options["shingle_check"] == "checked") {
                    $exists = $db->fetch($db->query("select count(*) from news_shingles where shingle = ".abs(crc32($item["news_guid"]))." and type = 'title'"), 0);
                } else {
                */
                    $exists = $db->fetch($db->query("select count(*) from news where (news_link = '".$item["news_link"]."' and '' <> '".$item["news_link"]."') or news_title = '".strip_tags($item["news_title"])."' or (news_guid = '".$item["news_guid"]."' and '' <> '".$item["news_guid"]."')"), 0);
                //}
                if ($exists == 0) {
                    $item["news_description"] = trim(rss_get_images($item["news_description"], array_merge($item, $rss)));
                    $item["news_description"] = html_entity_decode(strip_tags($item["news_description"]));

                    $news_error = "";
                    $news_errormessage = "";
                    if ($rss["rss_recivetext"] == "checked" && empty($item["news_text"])) {
                        if (!empty($rss["rss_print_link_from"])) {
                            $item["news_link_old"] = $item["news_link"];
                            $item["news_link"] = preg_replace("!".$rss["rss_print_link_from"]."!", $rss["rss_print_link_to"], $item["news_link"]);
                        }

                        // получаем контент
                        $news_text = rss_get_content(array_merge($item, $rss));
                    } else {
                        $news_text = empty($item["news_text"]) ? "" : $item["news_text"];
                        if (!empty($item["news_text"])) $item["news_text"] = rss_get_images($item["news_text"], $item);
                    }

                    if (is_array($rss["rss_replacement"]) && !empty($news_text)) {
                        foreach($rss["rss_replacement"] as $r) {
                            $news_text = preg_replace("!".$r[0]."!i", $r[1], $news_text);
                        }
                    }

                    $news_active = (!empty($news_error) ||
                                    ($options["grab_manual_moderate"] == "checked" && $rss["rss_ignore_global_manual"] != "checked") ||
                                    ($options["grab_manual_moderate"] != "checked" && $rss["rss_ignore_global_manual"] == "checked")
                                    ) ? "" : "checked";
                    if (empty($news_text)) {
                        $news_error = "checked";
                        $news_errormessage = ($rss["rss_recivetext"] == "") ? "Получение текста отключено." : "Ошибка обработки текста.";
                        $news_active = '';
                    } elseif ($news_error != "checked") {
                        if (empty($item["news_description"]) && $options["grab_text_first_chars_use"] > 0) {
                            /*
                            $item["news_description"] = substr(strip_tags(trim($news_text)), 0, $options["grab_text_first_chars_use"]);
                            $item["news_description"] = preg_split("!([\.\!\?]+)!", $item["news_description"], -1, PREG_SPLIT_DELIM_CAPTURE);
                            if (sizeof($item["news_description"]) > 1) {
                                array_pop($item["news_description"]);
                            }
                            $item["news_description"] = strip_tags(trim(implode("", $item["news_description"])));
                            */
                            /*
                            $item["news_description"] = substr(strip_tags(trim(preg_replace("![ ]{2,}!", " ", preg_replace("!</p>|<br[^>]{0,}>!i", " ", $news_text)))), 0, $options["grab_text_first_chars_use"]);
                            $item["news_description"] = preg_split("!([\.\!\?]+)!", $item["news_description"], -1, PREG_SPLIT_DELIM_CAPTURE);
                            if (sizeof($item["news_description"]) > 1) {
                                array_pop($item["news_description"]);
                                for($i = sizeof($item["news_description"])-1; $i >= 0; $i--) {
                                   //$item["news_description"][$i-1]
                                   $ccc = strpos(strrev($item["news_description"][$i-1]), " ");
                                   if ($ccc < 3) {
                                      $item["news_description"][$i] = "";
                                      $item["news_description"][$i-1] = "";
                                      $i--;
                                   } else {
                                      break;
                                   }
                                }
                            }
                            $item["news_description"] = strip_tags(trim(implode("", $item["news_description"])));
                            */
                            $item["news_description"] = substr(strip_tags(trim(preg_replace("![ ]{2,}!", " ", preg_replace("!</p>|<br>!i", " ", $news_text)))), 0, $GLOBALS["options"]["grab_text_first_chars_use"]);
                            $item["news_description"] = str_replace(chr(160), "", $item["news_description"]);
                            $item["news_description"] = preg_split("!([\.\!\?]+)!", $item["news_description"], -1, PREG_SPLIT_DELIM_CAPTURE);
                            if (sizeof($item["news_description"]) > 2) {
                                array_pop($item["news_description"]);
                                if (sizeof($item["news_description"]) > 2)
                                for($i = sizeof($item["news_description"])-1; $i >= 0; $i--) {
                                   $ccc = strpos(strrev($item["news_description"][$i-1]), " ");
                                   if ($ccc < 3) {
                                      $item["news_description"][$i] = "";
                                      $item["news_description"][$i-1] = "";
                                      $i--;
                                   } else {
                                      break;
                                   }
                                }
                            } elseif (sizeof($item["news_description"]) == 1) {
                               $item["news_description"][0] = explode(" ", $item["news_description"][0]);
                               array_pop($item["news_description"][0]);
                               $item["news_description"][0] = implode(" ", $item["news_description"][0])."...";
                            }
                            $item["news_description"] = trim(implode("", $item["news_description"]));
                            
                        }

                    }

                    $item["news_title"] = strip_tags($item["news_title"]);
                    $item["news_trans_title"] = to_translit(trim($item["news_title"]));
                    if (intval($options["transtitle_maxlength"]) > 0 && strlen($item["news_trans_title"]) > intval($options["transtitle_maxlength"])) $item["news_trans_title"] = substr($item["news_trans_title"], 0, intval($options["transtitle_maxlength"]));
                    $i = "";
                    while($db->fetch($db->query("select count(*) from news where news_trans_title = '".$item["news_trans_title"].$i."'"), 0) > 0) {
                       $i++;
                    }
                    $item["news_trans_title"] .= $i;

                    //$exists = $db->fetch($db->query("select count(*) from news where news_guid = '".$item["news_guid"]."'"), 0);

                    $item["rss_id"] = $rss["rss_id"];
                    $item["groups_id"] = $rss["groups_id"];
                    $item["news_text"] = $news_text;

                    if (get_magic_quotes_gpc()) {
                       $item["news_title"] = addslashes($item["news_title"]);
                       $item["news_text"] = addslashes($item["news_text"]);
                       $item["news_description"] = addslashes($item["news_description"]);
                       $images[0]['alt'] = addslashes($images[0]['alt']);
                    }

                    /*
                    $item["news_title"] = addslashes($db->data_encode(strip_tags($item["news_title"])));
                    $item["news_text"] = addslashes($db->data_encode($news_text));
                    $item["news_description"] = addslashes($db->data_encode($item["news_description"]));
                    */
                    $item["news_date"] = "NOW()";
                    $item["news_pubDate"] = addslashes($item["pubDate"]);
                    $item["news_image"] = addslashes($images[0]['image_source']);
                    $item["news_imagealt"] = $images[0]['alt'];
                    $item["news_active"] = $news_active;
                    $item["news_error"] = $news_error;
                    $item["news_errormessage"] = $news_errormessage;

                    $save = true;
                    use_functions("create_shingles,delete_files,resize_images");

                    if (!empty($images[0]['image_source'])) resize_images($images[0]['image_source']);

                    if ($options["shingle_check"] == "checked") {
                        $shingles = create_shingles($news_text);
                        if (is_array($shingles)) {
                            $ex = $db->fetchall("SELECT news_id, COUNT( DISTINCT shingle ) AS ccc FROM news_shingles WHERE shingle IN ( ".implode(", ", $shingles)." ) GROUP BY news_id HAVING ( ccc *100 ) / ".sizeof($shingles)." > ".$options["shingle_check_limit"]);
                            if (sizeof($ex) > 0) {
                                $save = false;
                            }
                        }
                    }

                    if ($save) {
                        $db->query("insert into news (rss_id, groups_id, news_date, news_title, news_trans_title, news_description, news_text, news_guid, news_pubDate, news_link, news_image, news_imagealt, news_active, news_error, news_errormessage, hash) values ('{rss_id}', '{groups_id}', {news_date}, '{news_title}', '{news_trans_title}', '{news_description}', '{news_text}', '{news_guid}', '{news_pubDate}', '{news_link}', '{news_image}', '{news_imagealt}', '{news_active}', '{news_error}', '{news_errormessage}', IF('".$news_active."' =  'checked', '".(empty($images[0]['image_source']) ? "0" : "1").$rss["groups_id"]."', NULL))", $item);
                        $id = mysql_insert_id();
                        $images = array();
                        if (mysql_error()) {
                            $save = false;
                            //echo "\ninsert into news (rss_id, groups_id, news_date, news_title, news_description, news_text, news_guid, news_pubDate, news_link, news_image, news_imagealt, news_active, news_error, news_errormessage) values ('".$rss["rss_id"]."', '".$rss["groups_id"]."', NOW(), '".addslashes($item["title"])."', '', '', '".$item["guid"]."', '".$item["pubDate"]."', '".$item["link"]."', '".$images[0]['image_source']."', '".$images[0]['alt']."', '$news_active', '$news_error', '$news_errormessage')\n".mysql_error();
                        } else {
                            if ($options["shingle_check"] == "checked") {
                                if ($options["grab_shingles_days"] > 0) $db->query("delete from news_shingles where `date` < NOW() - INTERVAL ".$options["grab_shingles_days"]." DAY and type <> 'title'");
                                $db->query("insert into news_shingles values(".$id.", ".abs(crc32($item["news_guid"])).", 'title', NOW())");
                                foreach($shingles as $ss) {
                                    $db->query("insert into news_shingles values(".$id.", ".$ss.", '', NOW())");
                                }
                            }
                            $saved++;
                            $db->query("update options set options_value = options_value+1 where options_name = 'grab_last_id'");
                            if (!empty($options["grab_manual_email_addres"]) && $options["grab_manual_email_count"] > 0) {
                                $options["grab_last_id"] = $db->fetch($db->query("select options_value from options where options_name = 'grab_last_id'"), 0);
                                if ($options["grab_last_id"] >= $options["grab_manual_email_count"]) {
                                    $db->query("update options set options_value = 0 where options_name = 'grab_last_id'");
                                    mail($options["grab_manual_email_addres"], "NewsGrab report", "Сграблено ".$options["grab_manual_email_count"]." новых новостей.", "From: ".$options["grab_manual_email_addres"]."\nMIME-Version: 1.0\nContent-Type: text/plain; charset=\"WINDOWS-1251\"\nContent-Transfer-Encoding: 8bit\n");
                                    //$db->query("update options set options_value = '".$id."' where options_name = 'grab_last_id'");
                                }
                            }
                        }
                    } else {
                       foreach($images as $img) {
                            if (!empty($img['image_source'])) {
                                //if (@file_exists(DOWNLOAD_IMAGES_DIR.$img['image_source'])) @unlink(DOWNLOAD_IMAGES_DIR.$img['image_source']);
                                //if (@file_exists(DOWNLOAD_IMAGES_DIR."sm_".$img['image_source'])) @unlink(DOWNLOAD_IMAGES_DIR."sm_".$img['image_source']);
                                //resize_images($img['image_source']);
                                delete_files(DOWNLOAD_IMAGES_DIR, addcslashes($img['image_source'], "[]!-.?*\\()|"));
                    		delete_files(DOWNLOAD_IMAGES_DIR, "prw_[\d]{0,}x[\d]{0,}_of_".addcslashes($img['image_source'], "[]!-.?*\\()|"));

                            }
                       }
                    }
                    $last_news = $db->fetchccol($db->query("select news_error from news where rss_id = ".$rss["rss_id"]." order by news_id desc limit 10"));
                    $errors = 0;
                    foreach($last_news as $n) if ($n == "checked") $errors++;
                    if ($errors == 10) {
                        $message = "RSS: ".$rss["rss_title"]." [ ".$rss["rss_url"]." ]\r\n".
                                   "10 last news fail (template error?)";
                        mail($options["grab_manual_email_addres"], "NewsGrab report", $message, "From: ".$options["grab_manual_email_addres"]."\nMIME-Version: 1.0\nContent-Type: text/plain; charset=\"WINDOWS-1251\"\nContent-Transfer-Encoding: 8bit\n");
                        $rss_status = $message;
                    }

                }
            }

        } else {
            if (!empty($options["grab_manual_email_addres"])) {
               $message = "RSS: ".$rss["rss_title"]." [ ".$rss["rss_url"]." ]\r\n".
                          "no links found (template error?)";
               mail($options["grab_manual_email_addres"], "NewsGrab report", $message, "From: ".$options["grab_manual_email_addres"]."\nMIME-Version: 1.0\nContent-Type: text/plain; charset=\"WINDOWS-1251\"\nContent-Transfer-Encoding: 8bit\n");
               $rss_status = $message;
            }

        }
    }
    $db->query("update rss set rss_last_update = NOW(), rss_proceed = '', rss_status = '".$message."' where rss_id = ".$rss["rss_id"]);
    $message = "";
}



function conv($matches) {
                global $options;
                $Host = $_SESSION["Host"];
                if (preg_match("!href=[\"']{0,1}([^ >'\"]+)!", $matches[1], $rrr)) {
                        $old_rrr = $rrr[1];
                        if (substr($rrr[1], 0, 4) != "http") {
                                if (substr($rrr[1], 0, 1) == "/") $rrr[1] = substr($rrr[1], 1);
                                $matches[1] = str_replace($old_rrr, $Host.$rrr[1], $matches[1]);

                        }
                }
                $matches[1] = "<a ".$matches[1]." ".($options["grab_links_open_in_blank"] == "checked" ? "target=_blank>" : "");
                for($i=0; $i<strlen($matches[1]);$i++) {
                        $str[] = ord($matches[1]{$i});
                }
                $matches[1] = "<script>document.write(String.fromCharCode(".implode(",", $str).")+ '".addslashes($matches[2])."</a>"."');</script>";

                return $matches[1];
}


?>