<?php
  class Template {

    var $dir  = "";
    var $files  = array();

    function Template ($dir = '') {
      global $_SERVER;
      if ($dir!="") {
        $this->dir = $dir;
      } else {
        $this->dir = TEMPLATESDIR;
      }
    }


    function fid_load ($fid, $filename, $vars='') {
      if (!empty($_SESSION["use_template"][$filename])) {
         $this->files[$fid] = $_SESSION["use_template"][$filename];
         $this->fid_array($fid, get_defined_constants());
         $_SESSION["use_template"][$filename] = "";
         return;
      }

      if ($this->dir == "mysql") {
         if (!is_object($GLOBALS["db"])) $GLOBALS["db"] = new Db();

         $filename = str_replace(".html", "", $filename);
         $this->files[$fid] = $GLOBALS["db"]->fetch("select templates_body from templates where templates_groups_id = '".$GLOBALS["options"]["templates_groups"]."' and templates_name = '$filename'", 0);
         if (empty($this->files[$fid])) die("No template: $fid");
      } else {
         if (!file_exists($this->dir.$filename)) die("No file: $this->dir.$filename");
         $this->files[$fid] = $GLOBALS["db"]->data_decode(file_get_contents($this->dir.$filename));

      }
      if (get_magic_quotes_runtime()) $this->files[$fid] = stripslashes($this->files[$fid]);
      if ($vars!='') {
         $this->fid_vars($fid, $vars);
      }

      $this->fid_array($fid, get_defined_constants());
      if ($fid == "main" && is_array($GLOBALS["_UPDATES"])) $this->fid_array("main", $GLOBALS["_UPDATES"], true);
    }


    function fid_pass ($filename) {
      readfile ($this->dir.$filename);
      }



    function fid_include ($fid, $filename){
      //$include = fread($fp = fopen($this->dir.$filename, 'r'), filesize($this->dir.$filename));
      //fclose($fp);
      $include = @file_get_contents($filename);
      $this->files[$fid] = str_replace("<include $filename>", $include, $this->files[$fid]);
    }

    function fid_read ($filename){

      $a = fread($fp = fopen($filename, 'r'), filesize($filename));
      fclose($fp);

      return $a;
    }


    function fid_parse ($fid) {
      while(is_long($pos = strpos($this->files[$fid], '<include '))){
        $pos += 9;
        $endpos = strpos($this->files[$fid], '>', $pos);
        $filename = substr($this->files[$fid], $pos, $endpos-$pos);
        $this->fid_include($fid, $filename);
        }
    }

    function fid_vars ($fid,$vars="") {
      if ($vars=="") return;

      $v = explode(',', $vars);
      foreach($v as $key) {
        $tvr = trim($key);
        if(strpos($this->files[$fid], '{'.$tvr.'}') !== false ) {
          global $$tvr;
          if (isset($$tvr)) $this->files[$fid] = str_replace('{'.$tvr.'}', $$tvr, $this->files[$fid]);
        }
      }
    }


    function fid_array ($fid, &$ar, $strip_if = false) {
      if (is_array($ar)) {
        foreach ($ar as $key => $value) {
          if(strpos($this->files[$fid], '{'.$key.'}') !== false ) {
            $this->files[$fid] = str_replace('{'.$key.'}', $value, $this->files[$fid]);
          }
          if ((strpos($this->files[$fid],"<if $key") !== false || strpos($this->files[$fid],"<if !$key") !== false) && $strip_if) {
            if ($value) {
                $this->files[$fid]=preg_replace("|(<if $key>(.*)</if $key>)|Ums","\\2",$this->files[$fid]);
                $this->files[$fid]=preg_replace("|(<if !$key>(.*)</if !$key>)|Ums","",$this->files[$fid]);
            } else {
                $this->files[$fid]=preg_replace("|(<if $key>(.*)</if $key>)|Ums","",$this->files[$fid]);
                $this->files[$fid]=preg_replace("|(<if !$key>(.*)</if !$key>)|Ums","\\2",$this->files[$fid]);
            }
          }
        }
      }
    }


    function fid_object ($fid, &$ob) {
      $ar = $ob;
      foreach ($ar as $key => $value) {
        if(strpos($this->files[$fid], '{'.$key.'}') !== false ) {
            $this->files[$fid] = str_replace('{'.$key.'}', $value, $this->files[$fid]);
        }
        if (strpos($this->files[$fid],"<if $key") !== false ) {
          if ($value ) {
            $this->files[$fid]=preg_replace("|<if $key>(.*)</if $key>|Ums","\\1",$this->files[$fid]);
            $this->files[$fid]=preg_replace("|<if !$key>(.*)</if !$key>|Ums","",$this->files[$fid]);
          } else {
            $this->files[$fid]=preg_replace("|<if $key>(.*)</if $key>|Ums","",$this->files[$fid]);
            $this->files[$fid]=preg_replace("|<if !$key>(.*)</if !$key>|Ums","\\1",$this->files[$fid]);
          }
        }
      }
    }

    function strip_loops($fid) {
      $this->files[$fid]=eregi_replace("<loop.+<\/loop [1-9A-Za-z_]+>","",$this->files[$fid]);
    }

  function fid_select($fid, $select, $a, $active = 0, $ass = false, $ass_title = ""){
    $loopcode = '';
    $n = sizeof($a);
    if ($ass) {
    foreach($a as $i => $value) {
      $selector .= "<option value=\"".$i."\"";
      if ((!is_array($active) && $i == $active) || (is_array($active) && in_array($i, $active))) {$selector .= " selected";}
      $selector .= ">".$value[$ass_title]."</option>\n";
    }
    } else {
    foreach($a as $i => $value) {
      $selector .= "<option value=\"".current($a[$i])."\"";
      if ((!is_array($active) && current($a[$i]) == $active) || (is_array($active) && in_array(current($a[$i]), $active))) {$selector .= " selected";}
      $selector .= ">".next($a[$i])."</option>\n";
    }
    }
    $this->files[$fid] = preg_replace("!<selector ".addcslashes($select, "[].!?\\()-*")."([^>]{0,})>!Ums", "<select name=".$select." \\1>".$selector."</select>", $this->files[$fid]);
  }


  function fid_loop ($fid, $loop, $a){
      $loopcode = '';
      $n = count($a);

      $pos1 = strpos($this->files[$fid], '<loop '.$loop.'>') + strlen('<loop '.$loop.'>');
      $pos2 = strpos($this->files[$fid], '</loop '.$loop.'>');

      $loopcode = substr($this->files[$fid], $pos1, $pos2-$pos1);

      $tag1 = substr($this->files[$fid], strpos($this->files[$fid], '<loop '.$loop.'>'),strlen('<loop '.$loop.'>'));
      $tag2 = substr($this->files[$fid], strpos($this->files[$fid], '</loop '.$loop.'>'),strlen('</loop '.$loop.'>'));
      if($loopcode != ''){
        $newcode = '';
        if (is_array($a)) {
          foreach($a as $row){
            $tempcode = $loopcode;
            foreach ($row as $key => $value) {
                if (!is_array($value)) $tempcode = str_replace('{'.$key.'}',$value, $tempcode);
                if (strpos($tempcode,"<if $key") !== false || strpos($tempcode,"<if !$key") !== false) {
                        if ($value) {
			                    $tempcode=preg_replace("|<if $key>(.*)</if $key>|Ums","\\1",$tempcode);
                                $tempcode=preg_replace("|<if !$key>(.*)</if !$key>|Ums","",$tempcode);
			            } else {
			                    $tempcode=preg_replace("|<if $key>(.*)</if $key>|Ums","",$tempcode);
                                $tempcode=preg_replace("|<if !$key>(.*)</if !$key>|Ums","\\1",$tempcode);
			            }
	            }
            }
            $newcode .= $tempcode;
          }
        }
        $this->files[$fid] = str_replace($tag1.$loopcode.$tag2, $newcode, $this->files[$fid]);
      }
    }


    function fid_loop2d ($fid, $loop, $a){

      $loopcode = '';

      $n = count($a);

      $pos1 = strpos($this->files[$fid], '<loop '.$loop.'>') + strlen('<loop '.$loop.'>');
      $pos2 = strpos($this->files[$fid], '</loop '.$loop.'>');

      $loopcode = substr($this->files[$fid], $pos1, $pos2-$pos1);

      if(!is_array($a) || !preg_match_all("/<loop ".$loop.">(.*<2d(\d{1,})>(.*)<\/2d>.*)<\/loop ".$loop.">/Ums", $this->files[$fid], $matches, PREG_SET_ORDER)) {
        return;
      }
      $loopcode = Array();

      foreach($matches as $key => $match) {
             $counter = 1;
             $num = 0;
             foreach($a as $a_key => $value) {
                 $tempcode = $match[3];
                 foreach ($value as $k => $v) {
                    if (!is_array($v)) {
                        $tempcode = str_replace('{'.$k.'}',$v, $tempcode);
	                    if (strpos($tempcode,"<if $k") !== false ) {
                            if ($v) {
                                $tempcode=preg_replace("|<if $k>(.*)</if $k>|Ums","\\1",$tempcode);
                                $tempcode=preg_replace("|<if !$k>(.*)</if !$k>|Ums","",$tempcode);
                            } else {
                                $tempcode=preg_replace("|<if $k>(.*)</if $k>|Ums","",$tempcode);
                                $tempcode=preg_replace("|<if !$k>(.*)</if !$k>|Ums","\\1",$tempcode);
			                }
	                    }

                    }
                 }

                 $d_code.= $tempcode;
                 $counter++;
                 if ($counter > $match[2] || $num+1 == sizeof($a)) {
                     $counter = 1;
                     $loopcode[] = str_replace('<2d'.$match[2].'>'.$match[3].'</2d>', $d_code, $match[1]);
                     $d_code = "";
                 }
                 $num++;
             }

             if (sizeof($a) % $match[2] != 0) {
                 $counter = floor(sizeof($a) / $match[2]) * $match[2];
             }
             $this->files[$fid] = str_replace($match[0], implode("\n", $loopcode), $this->files[$fid]);
             $loopcode = Array();
      }

    }


    function fid_tree ($fid, $tree, $a){
      $loopcode = '';

      function DrawChilds($code, $a) {
         if (is_array($a) && sizeof($a) > 0) {
             foreach($a as $row) {
                $tempcode = $code;
                foreach ($row as $key => $value) {
                   if (!is_array($value)) $tempcode = str_replace('{'.$key.'}',$value, $tempcode);
	               if (strpos($tempcode,"<if $key") !== false ) {
	                  if ($value) {
			             $tempcode=preg_replace("|<if $key>(.*)</if $key>|Ums", "\\1",$tempcode);
                         $tempcode=preg_replace("|<if \!$key>(.*)</if \!$key>|Ums", "",$tempcode);
			          } else {
			             $tempcode=preg_replace("|<if $key>(.*)</if $key>|Ums","",$tempcode);
                         $tempcode=preg_replace("|<if \!$key>(.*)</if \!$key>|Ums", "\\1",$tempcode);
			          }
	               }

                }
                $childs = DrawChilds($code, $row["childs"]);
                $result .= str_replace("<childs>", $childs, $tempcode);
             }
             return $result;
         } else {
             return "";
         }
      }

      $n = count($a);
      if (!is_array($a) || !preg_match_all("/<tree $tree>(.*)<\/tree $tree>/Ums", $this->files[$fid], $rec, PREG_SET_ORDER)) {
         preg_replace("/<tree $tree>(.*)<\/tree $tree>/Ums", "", $this->files[$fid]);
         return;
      }

      foreach($rec as $child) {
         $this->files[$fid] = str_replace($child[0], DrawChilds($child[1], $a), $this->files[$fid]);
      }
    }


    function fid_loop_obj ($fid, $loop, $r, $with_if = false){

      $loopcode = '';

      $pos1 = strpos($this->files[$fid], '<loop '.$loop.'>') + strlen('<loop '.$loop.'>');
      $pos2 = strpos($this->files[$fid], '</loop '.$loop.'>');

      $loopcode = substr($this->files[$fid], $pos1, $pos2-$pos1);

      $tag1 = substr($this->files[$fid], strpos($this->files[$fid], '<loop '.$loop.'>'),strlen('<loop '.$loop.'>'));
      $tag2 = substr($this->files[$fid], strpos($this->files[$fid], '</loop '.$loop.'>'),strlen('</loop '.$loop.'>'));

      if (!sizeof($r)>0)
        { $this->files[$fid] = str_replace($tag1.$loopcode.$tag2,"",$this->files[$fid]); return -1; }

      if($loopcode != ''){
        $newcode = '';
        if ($r) {

          foreach ($r as $b => $a ) {
            $tempcode = $loopcode;
            $ar=get_object_vars($a);

            if ($with_if) {
              $tempcode = $this->fid_if_block ($tempcode,$ar);
            }

            foreach ($a as $key => $value) {
              if (!is_array($value)) {
                        $tempcode = str_replace('{'.$key.'}',$value, $tempcode);
                        if (strpos($tempcode,"<if $key") !== false ) {
	                        if ($value) {
			                $tempcode=preg_replace("|<if $key>(.*)</if $key>|Ums","\\1",$tempcode);
			        } else {
			                $tempcode=preg_replace("|<if $key>(.*)</if $key>|Ums","",$tempcode);
			        }
	                }
              }
            }
            $newcode .= $tempcode;
          }
        }
        $this->files[$fid] = str_replace($tag1.$loopcode.$tag2, $newcode, $this->files[$fid]);
      }
    }

    function fid_loom ($fid, $loop, $r, $numfield=false, $numstart=0){

      $loopcode = '';

      $pos1 = strpos($this->files[$fid], '<loop '.$loop.'>') + strlen('<loop '.$loop.'>');
      $pos2 = strpos($this->files[$fid], '</loop '.$loop.'>');

      $loopcode = substr($this->files[$fid], $pos1, $pos2-$pos1);

      $tag1 = substr($this->files[$fid], strpos($this->files[$fid], '<loop '.$loop.'>'),strlen('<loop '.$loop.'>'));
      $tag2 = substr($this->files[$fid], strpos($this->files[$fid], '</loop '.$loop.'>'),strlen('</loop '.$loop.'>'));

      if (!$r || mysql_num_rows($r)==0)
        { $this->files[$fid] = str_replace($tag1.$loopcode.$tag2,"",$this->files[$fid]); return -1; }

      if($loopcode != ''){
        $newcode = '';
        $i=0;
        while ($a=mysql_fetch_assoc($r)) {
          $i++;
          $tempcode = $loopcode;
          foreach ($a as $key => $value) {


            if (!is_array($value)) {
              $tempcode = str_replace('{'.$key.'}',$value, $tempcode);
            }
            if (strpos($tempcode,"<if $key") !== false ) {
              if ($value >'' ) {
                $tempcode=eregi_replace("<if $key>(.*)</if $key>","\\1",$tempcode);
              } else {
                $tempcode=eregi_replace("<if $key>(.*)</if $key>","",$tempcode);
              }
            }
            if ($numfield) {
              $tempcode = str_replace('{'.$numfield.'}',htmlspecialchars($numstart+$i), $tempcode);
            }
          }
          $newcode .= $tempcode;
        }
        $this->files[$fid] = str_replace($tag1.$loopcode.$tag2, $newcode, $this->files[$fid]);
      }
    }

    function fid_echo ($fid) {

        echo $this->files[$fid];

    }

    function fid_get ($fid) {

      if (isset($this->files[$fid]))
        return $this->files[$fid];
      else
        return "";

    }


    function fid_show ($fid, $stripempty=true) {
      foreach($this->files as $key => $file) {
         $this->files[$key] = preg_replace_callback("!<include ([^>]+)>!", "r", $this->files[$key]);
      }

      do {
        $replaced=0;
        for(reset($this->files); $key = key($this->files); next($this->files)) {
          if ($key!=$fid) {
            if (strpos($this->files[$fid],"<tpl $key>") !== false ) {
              $replaced++;
              $this->files[$fid]=str_replace("<tpl ".$key.">",$this->files[$key],$this->files[$fid]);
            }
          }
        }
      } while ($replaced>0);

          if ($stripempty) {
            //$this->files[$fid]=preg_replace("/<loop(.*)\/loop [a-zA-Z0-9_]{1,}>/Ums","",$this->files[$fid]);
            //$this->files[$fid]=preg_replace("/<if(.*)\/if [a-zA-Z0-9_]{1,}>/Ums","",$this->files[$fid]);
            if (preg_match_all("!<loop ([^>]+)>!", $this->files[$fid], $regs)) {
               foreach($regs[1] as $loop) {
                  $this->files[$fid]=preg_replace("/<loop $loop>(.*)<\/loop $loop>/Ums","",$this->files[$fid]);
               }
            }
            if (preg_match_all("!<if ([^>]+)>!", $this->files[$fid], $regs)) {
               foreach($regs[1] as $if) {
                  $this->files[$fid]=preg_replace("/<if $if>(.*)<\/if $if>/Ums","",$this->files[$fid]);
               }
            }

            $this->files[$fid]=preg_replace("/{[_a-zA-Z0-9\-\._]+}/Ums","",$this->files[$fid]);
            $this->files[$fid]=preg_replace("/<selector(.*)>/Ums","",$this->files[$fid]);
            $this->files[$fid]=preg_replace("/<tpl(.*)>/Ums","",$this->files[$fid]);
            $this->files[$fid]=preg_replace("/<tree(.*)\/tree [a-zA-Z0-9_]{1,}>/Ums","",$this->files[$fid]);

          }
      echo $this->files[$fid];

    }


    function fid_if_obj ($fid,$ar) {
      $this->fid_if($fid,get_object_vars($ar));
    }


    function fid_if ($fid,$ar) {
      if (!is_array($ar)) return;
      foreach($ar as $key => $value) {
         if ($value == true || !empty($value)) {
            $this->files[$fid] = preg_replace("/(<if $key>(.*)<\/if $key>)/Ums", "\\2", $this->files[$fid]);
            $this->files[$fid] = preg_replace("/(<if !$key>(.*)<\/if !$key>)/Ums", "", $this->files[$fid]);
         } else {
            $this->files[$fid] = preg_replace("/(<if $key>(.*)<\/if $key>)/Ums", "", $this->files[$fid]);
            $this->files[$fid] = preg_replace("/(<if !$key>(.*)<\/if !$key>)/Ums", "\\2", $this->files[$fid]);
         }
      }
      /*
      while (is_long($pos = strpos($this->files[$fid], '<if '))) {

        $pos1 = strpos($this->files[$fid], '<if ');
        $pos2 = strpos($this->files[$fid], '>', $pos1);

        $ifname = substr ($this->files[$fid],$pos1+4,$pos2-$pos1-4);
        $ifcode = substr ($this->files[$fid],$pos2+1,strpos($this->files[$fid],'</if '.$ifname.'>',$pos2)-$pos2-1);

        $newcode = $ifcode;

        if ($ar[$ifname] == true || !empty($ar[$ifname])) {
          foreach ($ar as $key => $value)
            if (strpos($newcode, '{'.$key.'}'))
              $newcode = str_replace('{'.$key.'}', $value, $newcode);
          }
        else
          $newcode='';

        $this->files[$fid] = str_replace('<if '.$ifname.'>'.$ifcode.'</if '.$ifname.'>', $newcode, $this->files[$fid]);

        }
        */

      }

    function fid_if_block ($block,$ar) {

      while (is_long($pos = strpos($block, '<if '))) {

        $pos1 = strpos($block, '<if ');
        $pos2 = strpos($block, '>', $pos1);

        $ifname = substr ($block,$pos1+4,$pos2-$pos1-4);
        $ifcode = substr ($block,$pos2+1,strpos($block,'</if '.$ifname.'>',$pos2)-$pos2-1);

        $newcode = $ifcode;

        if (isset($ar[$ifname]) && $ar[$ifname]>'' ) {
          foreach ($ar as $key => $value)
            if (strpos($newcode, '{'.$key.'}') !== false )
              $newcode = str_replace('{'.$key.'}', $value, $newcode);
          }
        else
          $newcode='';

        $block = str_replace('<if '.$ifname.'>'.$ifcode.'</if '.$ifname.'>', $newcode, $block);

        }

      return $block;

      }

  } // End of class


function polygraf($text) {
global $tags;

$patterns[] = "/[ ]{2,}/Ums";
$patterns[] = "/=[ ]{0,}\"(.*)\"/Ums";
$patterns[] = "/\xA7/Ums";
$patterns[] = "/\([Cc—Ò]\)|\xA9/Ums";
$patterns[] = "/\([rR]\)|\xAE/Ums";
$patterns[] = "/\((tm|TM|ÚÏ|“Ã)\)|\x99/Ums";
$patterns[] = "/\xB0/Ums";
$patterns[] = "/(\x85|\.{3})/Ums";
$patterns[] = "/\x92/Ums";
$patterns[] = "/\x95/Ums";
$patterns[] = "/\xB1/Ums";
$patterns[] = "/\+-/Ums";
$patterns[] = "/[a-zA-Z‡-ˇ¿-ﬂ0-9_]\s+([.,?!:^;])/Ums";
$patterns[] = "/π[ ]{0,}([0-9\/-]*)/ms";
//$patterns[] = "/([\w\s.,!?;<>-][^=][\s]{1,}|>|<)\"(.*)\"/Ums";
$patterns[] = "/([a-zA-Z‡-ˇ¿-ﬂ0-9_](?:[.,!?])?)\"/Ums";
$patterns[] = "/\"([a-zA-Z‡-ˇ¿-ﬂ0-9_])/Ums";
$patterns[] = "/([a-zA-Z¿-ﬂ‡-ˇ]{1,}-[a-zA-Z¿-ﬂ‡-ˇ0-9]{1,})/ms";
$patterns[] = "/([ ]{1,}-[ ]{0,})|([ ]{0,}[-|-][ ]{1,})/ms";
$patterns[] = "/ ([a-zA-Z‡-ˇ¿-ˇ]{1,3})[\s]{1,}/Ums";
$patterns[] = "/&nbsp;([a-zA-Z‡-ˇ¿-ˇ]{1,3})[\s]{1,}/ms";

$replacements[] = " ";
$replacements[] = "='\\1'";
$replacements[] = "&#167;";
$replacements[] = "&#169;";
$replacements[] = "&#174;";
$replacements[] = "<sup><small>&trade;</small></sup>";
$replacements[] = "&#176;";
$replacements[] = "&#133;";
$replacements[] = "&#146;";
$replacements[] = "&#149;";
$replacements[] = "&#177;";
$replacements[] = "&plusmn;";
$replacements[] = "\\1";
$replacements[] = "<nobr>&#8470&nbsp;\\1</nobr>";
//$replacements[] = "\\1&laquo;\\2&raquo;";
$replacements[] = "\\1&raquo;";
$replacements[] = "&laquo;\\1";
$replacements[] = "<nobr>\\1</nobr>";
$replacements[] = "&nbsp;&#151; ";
$replacements[] = " \\1&nbsp;";
$replacements[] = "&nbsp;\\1&nbsp;";


$text = preg_replace_callback("/<(.*)>/Ums", "tags", $text);

$text = preg_replace_callback("/(\+[\d]{1,}[ ]{0,1}){0,1}(\([\d]{1,}\)[ ]{0,1}){0,1}([\d]{3})[-]{0,1}([\d]{2})[-]{0,1}([\d]{2})/ms", "tel", $text);
$text = preg_replace($patterns, $replacements, $text);
if(is_array($tags) && sizeof($tags)>0)
foreach($tags as $key => $value) {
  $text = str_replace("<tag".$key.">", "<".$value.">", $text);
}

return $text;
}
function tel($matches) {
  if (!empty($matches[1])) $text .= $matches[1]." ";
  if (!empty($matches[2])) $text .= $matches[2]." ";
  $text .= $matches[3]."-".$matches[4]."-".$matches[5];
  return " <nobr>".$text."</nobr>";
}

function tags($matches) {
  global $tags;
  $tags[] = $matches[1];
  return "<tag".(sizeof($tags)-1).">";
}

function r($m) {
  if (strtolower(substr($m[1], 0, 7)) != "http://" && strtolower(substr($m[1], -4)) == ".php") {
     ob_start();
     $c = preg_replace("!<\?php|<\?|\?>!", "", file_get_contents($m[1]));
     if (!empty($c)) $asd = @eval($c);
     $asd === false ? ob_clean() : $return = ob_get_clean();
  } else {
     $return = @file_get_contents($m[1]);
  }
 return $return;
}

?>