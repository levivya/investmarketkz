<?
function rss_get_content($item, $content = false, $getImages = true, $getSwf = true) {
    global $options, $news_error, $news_errormessage;

    if ($item["rss_id"] < 1) {$news_error = "checked"; $news_errormessage = "Ошибка получения текста новости."; return ""; }
    $news_error = "";
    $news_errormessage = "";

    if ($content === false) {
        if ($options["grab_text_use_proxy"] == "checked") {
            do {
                $result = get_content($item["news_link"], $options["grab_text_use_proxy"] == "checked", 5, $item["news_link_old"]);
            } while (!empty($item["rss_uniq_id"]) && strpos($result["content"], $item["rss_uniq_id"]) === false);
        } else {
            $result = get_content($item["news_link"], false, 5, $item["news_link_old"]);
        }
    } else {
        $result =  array("headers" => "", "content" => $content);
    }

    if (preg_match("!Location:(.*)\n!i", $result["headers"], $regs) && !preg_match("!HTTP/1\.[01] 200!i", $result["headers"], $regs)) {
        $parsed = parse_url($item["news_link"]);
        $item["news_link"] = strtolower(trim($regs[1]));
        if (substr($item["news_link"], 0, 1) == "/") $item["news_link"] = "http://".$parsed["host"].$item["news_link"];
        if (substr($item["news_link"], 0, 4) != "http") $item["news_link"] = $item["dir_url"].$item["news_link"];
        if (!empty($item["rss_print_link_from"])) {
            if ($GLOBALS["rss"]["setNewLink"] === true) $GLOBALS["rss"]["news_link_old"] = $item["news_link"];
            if ($GLOBALS["rss"]["setNewLink"] === true) $GLOBALS["rss"]["changelink"] = true;
            $item["news_link"] = preg_replace("!".$item["rss_print_link_from"]."!", stripslashes($item["rss_print_link_to"]), $item["news_link"]);
        }
        if ($GLOBALS["rss"]["setNewLink"] === true) $GLOBALS["rss"]["news_link"] = $item["news_link"];
        $GLOBALS["item"]["news_link"] = $item["news_link"];
        $item_url = parse_url($item["news_link"]);
        $item_url["dir_path"] = "http://".$item_url["host"].$item_url["path"];
        if (preg_match("!\.[a-z0-9]{2,5}$!i", $item_url["dir_path"])) $item_url["dir_path"] = substr($item_url["dir_path"], 0, strpos($item_url["dir_path"], basename($item_url["dir_path"])));
        $GLOBALS["item"]["dir_url"] = $item["dir_path"];
        if ($options["grab_text_use_proxy"] == "checked") {
            do {
                $result = get_content($item["news_link"], $options["grab_text_use_proxy"] == "checked", 5, $item["news_link_old"]);
            } while (!empty($item["rss_uniq_id"]) && strpos($result["content"], $item["rss_uniq_id"]) === false);
        } else {
            $result = get_content($item["news_link"], false, 5, $item["news_link_old"]);
        }
    }
    // делим на заголовки и сам html
    $headers = $result["headers"];
    $tmp_content = $content = $result["content"];

    // если в заголовках встречаем koi8-r - перекодируем контент в вин
    if (strpos(strtolower($headers), "koi8-r") !== false || strpos(strtolower(substr($content, strpos(strtolower($content), "<head>"), strpos(strtolower($content), "</head>"))), "koi8-r")) $tmp_content = $content = convert_cyr_string($content, "k", "w");
    if (strpos(strtolower($headers), "utf-8") !== false || strpos(strtolower(substr($content, strpos(strtolower($content), "<head>"), strpos(strtolower($content), "</head>"))), "utf-8") !== false) {
        $tmp_content = $content = /*function_exists("iconv") ? iconv("UTF-8", "WINDOWS-1251", $content) : */utf_to_win($content);
    }

    if (!$content || empty($content)) {
        $news_error = "checked";
        $news_errormessage = "Ошибка получения текста новости.";
        return "";
    } else {
        if (!$item["rss_texttemplate_converted"]) {
            $item["rss_texttemplate"] = addcslashes($item["rss_texttemplate"], "[]!-.?*\\()|");
            $item["rss_texttemplate"] = str_replace("{get}", "(.*)", $item["rss_texttemplate"]);
            $item["rss_texttemplate"] = str_replace("{skip}", ".*", $item["rss_texttemplate"]);
            $item["rss_texttemplate"] = preg_replace("![\n\r\t]!s", "", $item["rss_texttemplate"]);
            $item["rss_texttemplate"] = preg_replace("!>[ ]{1,}<!s", "><", $item["rss_texttemplate"]);
            $item["rss_texttemplate_converted"] = true;
        }

        if (preg_match_all("!<remove>(.*)</remove>!Umsi", $item["rss_texttemplate"], $rem, PREG_SET_ORDER)) {
           $item["rss_texttemplate"] = trim(substr($item["rss_texttemplate"], 0, strpos($item["rss_texttemplate"], "<remove>")));
        }

        $content = preg_replace("![\n\r\t]!s", " ", $content);
        $content = preg_replace("!>[ ]{1,}<!s", "><", $content);
        if (!empty($item["rss_texttemplate"]) && preg_match("!".$item["rss_texttemplate"]."!Us", $content, $textregs)) {
            $temp = array();
            for($i=1; $i < sizeof($textregs); $i++) {
                $temp[] = $textregs[$i];
            }
            $textregs = $temp;
            $news_text = preg_replace("!<script[^>]+>.*</script>!Uis", "", implode("\n", $textregs));
            if (is_array($rem)) {
               foreach($rem as $r) {
                    $news_text = preg_replace("!(".trim($r[1]).")!Umsi", "", $news_text);
               }
            }

            $news_text = html_entity_decode($news_text);
            $news_text = preg_replace("! (class|style)=[\"']{1}[^\"']{1,}[\"']{1}!msi", "", $news_text);
            $news_text = preg_replace("! class=[^> ]{1,}!msi", "", $news_text);
            $news_text = str_replace('\\"', '"', $news_text);
            if (preg_match_all("!<a (.*)>(.*)</a>!Ui", $news_text, $url_regs)) {
                foreach($url_regs[0] as $url_key => $url) {
                    if (preg_match("!href=[\"']{0,1}([^ '\">]+)!i", $url, $href_regs)) {
                        $href = $href_regs[1];
                    }
                    $news_text = str_replace($url_regs[1][$url_key], "href=\"".$href."\"", $news_text);
                }
            }
            
            if ($getSwf) $item["rss_tags_leave"] .= ",<object>,<param>,<embed>";
            if ($getImages) $item["rss_tags_leave"] .= ",<img>";
            if ($item["rss_striptags_text"] == "checked") $news_text = strip_tags($news_text, $item["rss_tags_leave"]);
            if ($getImages) $news_text = rss_get_images($news_text, $item);
            if ($getSwf) $news_text = rss_get_swf($news_text, $item);
            $news_text = preg_replace("!(<[ph0-9]{1,}>[.\s\xA0]{0,2}</[ph0-9]{1,}>)|(<[ph0-9]{1,}>&nbsp;</[ph0-9]{1,}>)!Umsi", "", $news_text);
        } else {
            $news_error = "checked";
            $news_errormessage = "Ошибка обработки шаблона.";
            $news_text = $tmp_content;
        }
    }
    return $news_text;

}

function rss_get_images($text, $rss) {
global $options, $images, $parsed_link, $parsed_images;
   $regs = $iregs = $aregs = $rrr = array();

   // получаем содержимое всех img
   if (preg_match_all("!<img (.*)>!Ui", $text, $regs)) {
      // обрабатываем каждый полученый результат
      foreach($regs[1] as $regs_key => $img) {
         // флаг сохранения картинки в тексте
         $image_saved = false;
         
         // ищем урл картинки
         $img = preg_replace("!src[ ]{1,}=!", "src=", $img);
         $img = preg_replace("!src=[ ]{1,}!", "src=", $img);
         if (substr($img, strpos($img, "src=")+4, 1) == "\"" || substr($img, strpos($img, "src=")+4, 1) == "'") {
            $ex = preg_match("!src=[\"']{0,1}([^'\"]+)!i", $img, $iregs);
         } else {
            $ex = preg_match("!src=([^ >]+)!i", $img, $iregs);
         }
         if ($ex) {
            $images_key = sizeof($images);
            $image = strpos($iregs[1], "/") !== false ? substr(strrchr($iregs[1], "/"), 1) : $iregs[1];
            // если это картинка
            if (!is_array($parsed_images)) $parsed_images = array();
            if (in_array(strtolower(substr($image, -3)), array("gif", "jpg", "png")) || strtolower(substr($image, -4)) == "jpeg") {
               // если такую картинку еще не обрабатывали
               if (!in_array($image, $parsed_images)) {
                  // добавляем ее в массив обработанных картинок
                  $parsed_images[] = $image;

                  if (strpos($iregs[1], "?") !== false) $iregs[1] = substr($iregs[1], 0, strpos($iregs[1], "?"));

                  // получаем размер картинки
                  $parsed_link = parse_url($rss["dir_url"]);
                  //if (strpos($iregs[1], " ") !== false) $iregs[1] = str_replace(" ", "%20", $iregs[1]);
                  switch(true) {
                     case substr($iregs[1], 0, 7) == "http://":
                          $source = $iregs[1];
                          $sizes[$image] = $size = @getimagesize(strpos($source, " ") !== false ? str_replace(" ", "%20", $source) : $source);
                          break;
                     case substr($iregs[1], 0, 1) == "/":
                          $source = "http://".$parsed_link["host"].$iregs[1];
                          $sizes[$image] = $size = @getimagesize(strpos($source, " ") !== false ? str_replace(" ", "%20", $source) : $source);
                          break;
                     default:
                          $source = $rss["dir_url"].$iregs[1];
                          $sizes[$image] = $size = @getimagesize(strpos($source, " ") !== false ? str_replace(" ", "%20", $source) : $source);
                          /*
                          if (strpos(strrchr($parsed_link["dir_url"], "/"), ".") === false) {
                             $source = "http://".$parsed_link["host"].$rss["dir_url"].(substr($parsed_link["dir_url"], -1) == "/" ? "" : "/").$iregs[1];
                             $sizes[$image] = $size = @getimagesize($source);
                          } else {
                             $source = "http://".$parsed_link["host"].substr($parsed_link["dir_url"], 0, -1*strlen(strrchr($parsed_link["dir_url"], "/")))."/".$iregs[1];
                             $sizes[$image] = $size = @getimagesize($source);
                             if (!$size) {
                                $source = "http://".$parsed_link["host"].$parsed_link["dir_url"].(substr($parsed_link["dir_url"], -1) == "/" ? "" : "/").$iregs[1];
                                $sizes[$image] = $size = @getimagesize($source);
                             }
                          }
                          */
                          break;
                  }
               } else {
                  $size = $sizes[$image];
               }

               // если размер удовлетворяет условиям
               if (($size[0] >= $options["grab_text_imagesize_limit"][0] || $options["grab_text_imagesize_limit"][0] < 1) &&
                   ($size[1] >= $options["grab_text_imagesize_limit"][1] || $options["grab_text_imagesize_limit"][1] < 1) &&
                   ($size[0] <= $options["grab_text_imagesize_limit"][2] || $options["grab_text_imagesize_limit"][2] < 1) &&
                   ($size[1] <= $options["grab_text_imagesize_limit"][3] || $options["grab_text_imagesize_limit"][3] < 1)) {
                  $image_saved = true;
                  $size = get_remote_filesize(strpos($source, " ") !== false ? str_replace(" ", "%20", $source) : $source);
                  // добавляем картинку в массив
                  //if (strpos($image, " ") !== false) $image = str_replace(" ", "%20", $image);
                  $images[$images_key]["image_source"] = ($rss["rss_reciveimages"] == "checked") ? $image : $iregs[1];
                  $images[$images_key]["image_name"] = $image;

                  // пытаемся для нее получить альт
                  if (preg_match("!alt[ ]{0,1}=[ ]{0,1}[\"']{1}([^\"']+)[\"']{1}!Ui", $img, $aregs)) {
                     $images[$images_key]["alt"] = $aregs[1];
                  }
                  // если стоит флаг загрузить картинки
                  if ($rss["rss_reciveimages"] == "checked") {
                     $copy = true;
                     clearstatcache();
                     if (file_exists(DOWNLOAD_IMAGES_DIR.$image) && $size != filesize(DOWNLOAD_IMAGES_DIR.$image)) {
                        $ext = strrchr($image, ".");
                        $name = substr($image, 0, -1*strlen($ext));
                        $i = 1;
                        if (empty($name)) $name = "image";
                        while(file_exists(DOWNLOAD_IMAGES_DIR.$name.$i.$ext)) {
                            $i++;
                        }
                        $image = $name.$i.$ext;
                        $images[$images_key]["image_source"] = $image;
                     } elseif (file_exists(DOWNLOAD_IMAGES_DIR.$image) && $size == filesize(DOWNLOAD_IMAGES_DIR.$image)) {
                        $copy = false;
                     }
                     // заменяем в тексте урлы картинок на локальные
                     $text = str_replace($iregs[1], "{DOWNLOAD_IMAGES_DIR_HTTP}".$image."", $text);
                     // копируем картинку к себе, если файла с таким именем еще нет
                     if ($copy) {
                        $aaa = @copy(strpos($source, " ") !== false ? str_replace(" ", "%20", $source) : $source, DOWNLOAD_IMAGES_DIR.$image);
                        //$isizes = explode("", $GLOBALS["options"]["rss_sizes"]);
                        //if (is_array($isizes)) {
                        //   if (!empty($GLOBALS["options"]["rss_mogrify_path"]) && file_exists($GLOBALS["options"]["rss_mogrify_path"])) {
                        ///      copy(DOWNLOAD_IMAGES_DIR.$image, DOWNLOAD_IMAGES_DIR."sm_".$image);
                        //      @system("/usr/local/bin/mogrify -quality 75 -geometry 170x10000 ".DOWNLOAD_IMAGES_DIR."sm_".$image);
                        //   } elseif ($GLOBALS["options"]["rss_usegd"]) {

                        //   }
                        //}
                     }
                  } else {
                     $text = str_replace($iregs[1], $source, $text);
                  }
               }
            }
         }

         if (!$image_saved) {
            $text = str_replace($regs[0][$regs_key], "", $text);
         }
      }
   }
   return $text;
}


function se($parser, $name, $attr) {
    global $items, $cur_t, $cur_a,$links_o,$links_n, $rss_item_tag_name;
    global $rels,$rels_n,$rels_o;
    global $pres,$pres_n,$pres_o;

    $cur_t = $name;
    $cur_a = $attr;
    $name = strtolower($name);
    if ($links_o) {
       $links_n = $links_n == NULL ? 0 : $links_n;
       $items[$links_n][$name]["attributes"] = $cur_a = $attr;
    }
    switch($name) {
       case $rss_item_tag_name:
            $links_o = true;
            break;
       default: break;
    }
}

function ee($parser, $name) {
    global $cur_t,$links_n,$links_o, $rss_item_tag_name;
    global $rels,$rels_n,$rels_o;
    global $pres,$pres_n,$pres_o;
    $name = strtolower($name);
    switch($name) {
       case $rss_item_tag_name:
            $links_o = false;
            $links_n++;
            break;
       default: break;
    }
    $cur_t = "";
    $cur_a = "";
}

function cd($parser, $data) {
    global $items, $cur_t,$links,$links_n,$links_o;
    global $rels,$rels_n,$rels_o;
    global $pres,$pres_n,$pres_o;
    global $pocc,$gf,$gr, $encode;
    $cur_t = strtolower($cur_t);
    if ($links_o && !empty($cur_t)) {
        $items[$links_n][$cur_t]["value"] .= stripslashes(str_replace($gf,$gr,$data));
    }
}

?>