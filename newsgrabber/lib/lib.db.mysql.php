<?php

class Db {

	var $conn;
        var $db_host = "";
        var $db_login = "";
        var $db_password = "";
        var $db_name = "";


	function Db() {
             global $db_login, $db_password, $db_name, $db_host;

             $this->db_name = $db_name;
             $this->db_host = $db_host;
             $this->db_login = $db_login;
             $this->db_password = $db_password;

             if (empty($this->db_name)) die("Не указана база данных.");
             $this->connect();
	}

  	function connect() {
        $this->conn = mysql_connect($this->db_host,$this->db_login,$this->db_password);
		if (!$this->conn) die(mysql_error());
		if (!mysql_select_db($this->db_name)) die("База данных указана не верно.");
		$res = mysql_query("SET NAMES cp1251");
		}

	function disconnect() {
		if ($this->conn) mysql_close($this->conn);
		}

    function query ($query, $data = array(), $notaddslashes = false) {
        if (is_array($data) && sizeof($data) > 0 && preg_match_all("!{([a-z0-9_]{1,})}!i", $query, $regs, PREG_SET_ORDER)) {
           foreach($regs as $field) {
               //$query = str_replace($field[0], (get_magic_quotes_gpc()||(get_magic_quotes_gpc() && !$addslashes))?$data[$field[1]]:addslashes($data[$field[1]]), $query);
               $query = str_replace($field[0], get_magic_quotes_gpc()||$notaddslashes?$data[$field[1]]:addslashes($data[$field[1]]), $query);
           }
        }
        return mysql_query($query);
		}

	function rows ($result) {
		if (!$result) return -1;
		return mysql_num_rows($result);
		}

	function fetch ($result,$i=-1) {
        if (is_string($result)) $result = $this->query($result);
        if (!$result) return array();
        if (mysql_num_rows($result) > 0) mysql_data_seek($result, 0);
        if ($i==-1) {
            $a = mysql_fetch_assoc ($result);
            if ($a && is_array($a)) {
               foreach($a as $key => $value) {
                  if (is_string($value)) $a[$key] = $value = ini_get("magic_quotes_runtime") ? stripslashes($value) : $value;
                  $a[$key] = $this->data_decode($value);
               }
            }
			return $a;
		} else {
            $data = @mysql_result ($result, 0, $i);
            if (is_string($data)) $data = magic_quotes_runtime() ? stripslashes($data) : $data;
            return $this->data_decode($data);
        }
		}

	function fetchall ($result, $byPrimary = false) {
		$r=array();

        if (is_string($result)) $result = $this->query($result);
        if ($result) {
            while ($a=mysql_fetch_assoc($result)) {
                if ($a && is_array($a)) {
                   foreach($a as $key => $value) {
                        if (is_string($value)) $a[$key] = $value = ini_get("magic_quotes_runtime") ? stripslashes($value) : $value;
                        $a[$key] = $this->data_decode($value);
                   }
                }
                if (!empty($byPrimary) && (is_numeric($a[$byPrimary]) && $a[$byPrimary] > 0 || is_string($a[$byPrimary]) && !empty($a[$byPrimary]))) {
                    $r[$a[$byPrimary]]=$a;
                } else {
                    $r[]=$a;
                }
            }
        }
		return $r;
		}

    function fetchccol ($result,$i=0) {
		$r=array();
        if (is_string($result)) $result = $this->query($result);
        if ($result) {
            if (mysql_num_rows($result)>0) mysql_data_seek($result, 0);
            while ($a=mysql_fetch_array($result,MYSQL_NUM)) {$r[]=$a[$i];}
        }
		return $r;
		}

	function fetchcol ($result,$i=0) {
		return ($this->fetchccol($result,$i));
		}

    function last_id() {
        return mysql_insert_id();
        }


    function data_encode($str, $key = SITE_KEY) {
	    $key = get_site_key("by script call");
	    if (empty($key) || $key == "SITE_KEY") return $str;
        if (empty($str)) return "";
        $temp = "";
	    while($i*strlen($key) < strlen($str)) {
           $temp .= (substr($str, $i*strlen($key), strlen($key)) ^ $key);
           $i++;
        }
        return ("CbSAIIC" ^ $key).$temp.("CbSAIIC" ^ $key);
    }

    function data_decode($str, $key = SITE_KEY) {
        $key = get_site_key("by script call");
        if (empty($key) || $key == "SITE_KEY") return $str;
        if (strpos($str, ("CbSAIIC" ^ $key)) === false) return $str;
        /*
        $temp = explode(("CbSAIIC" ^ $key), $str);
        if (sizeof($temp) < 3) return $str;
        while(strpos($str, ("CbSAIIC" ^ $key)) !== false) {
            if (strlen($str) < 15) return $str;
            $tmp = substr($str, 7+strpos($str, ("CbSAIIC" ^ $key)));
            $tmp = substr($tmp, 0, strpos($tmp, ("CbSAIIC" ^ $key)) !==false ? strpos($tmp, ("CbSAIIC" ^ $key)) : strlen($tmp));
            $temp = "";
            $i = 0;
            while($i*strlen($key) < strlen($tmp)) {
                 $temp .= (substr($tmp, $i*strlen($key), strlen($key)) ^ $key);
                 $i++;
            }
            if (strpos($str, ("CbSAIIC" ^ $key).$tmp.("CbSAIIC" ^ $key)) !== false)
                $str = str_replace(("CbSAIIC" ^ $key).$tmp.("CbSAIIC" ^ $key), $temp, $str);
        }
        */
        $k = addcslashes(("CbSAIIC" ^ $key), "[]!-.?*\\()|+{}^\$");
        preg_match_all("!".$k."(.*)".$k."!Ums", $str, $regs);
        foreach($regs[1] as $tmp) {
            $temp = "";
            $i = 0;
            while($i*strlen($key) < strlen($tmp)) {
                 $temp .= (substr($tmp, $i*strlen($key), strlen($key)) ^ $key);
                 $i++;
            }
            if (strpos($str, ("CbSAIIC" ^ $key).$tmp.("CbSAIIC" ^ $key)) !== false)
                 $str = str_replace(("CbSAIIC" ^ $key).$tmp.("CbSAIIC" ^ $key), $temp, $str);
        }

        return $str;
    }

}

function convert($string) {
/*
html = htmlspecialchars
sl = addslashes
q = " => &quot; (¤ї ўЁ№ї¤аїк¬а  и° html)
nl = nl2br
br = br => nl (\n)
sls = stripslashes
tag = strip_tags
*/

  if (!func_num_args()) return "";
  //if (!is_string($string) || empty($string)) return "";

  for ($i=1; $i<func_num_args(); $i++) {
      switch (func_get_arg($i)) {
        case html:
           $string = htmlspecialchars($string, ENT_NOQUOTES);
           break;
        case sl:
           if(!get_magic_quotes_gpc() && !get_magic_quotes_runtime()) $string = addslashes($string);
           break;
        case sls:
           $string = stripslashes($string);
           break;
        case tag:
           $string = strip_tags($string);
           break;
        case q:
           $string = str_replace("\"", "&quot;", $string);
           break;
        case nl:
           $string = nl2br($string);
           break;
        case br:
           $string = str_replace("<br>", "\n", $string);
           break;
        default:
      }
  }
  return $string;
}

?>