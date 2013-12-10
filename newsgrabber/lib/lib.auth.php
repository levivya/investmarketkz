<?
include_once(LIBDIR."lib.obj.php");
class user extends SiteObject {
var $users_id = -1;
var $users_groups_id = 2;
var $users_groups_name = "Посетитель";
var $users_current_hash = "";

      function user() {
           parent::SiteObject("users");
           $this->Table = Array("table_name" => "users", "table_title" => "Пользователи");
           if ($this->CheckSession()) {
              foreach($_SESSION["user"] as $key => $value) {
                     $this->{$key} = $value;
              }
           }
      }

      function GetUser($user_login, $user_password, $by_cookie = false) {
          if ($by_cookie) {
                $result = @mysql_query("select users_id from users where users_login = '".$_COOKIE["users_login"]."' and users_password = '".$_COOKIE["users_password"]."'");
          } else {
                $result = @mysql_query("select users_id from users where users_login = '$user_login' and users_password = PASSWORD('$user_password')");
          }
          if (@mysql_num_rows($result) == 0) {
             $this->Error = true;
             $this->ErrorMessage = "Указанные логин и/или пароль не верны.";
             return false;
          }
          $id = mysql_result($result, 0, 0);
          $temp = parent::GetObject($id);
          if ($temp) {
                $_SESSION["by_cookie"] = $by_coockie;
                if ($this->users_groups_id > 0) $this->users_groups_name = mysql_result(mysql_query("select users_groups_name from users_groups where users_groups_id = ".$this->users_groups_id), 0, 0);
                $this->SetSession();
          } else {
                $_SESSION["by_cookie"] = false;
                setcookie("users_login");
                setcookie("users_password");
                $this->ClearSession();
          }
          return $temp;

      }

      function CheckLogin($user_login = "", $user_password = "") {
          $by_cookie = ($user_login == "" && $user_password == "" && $_COOKIE["users_login"] != "" && $_COOKIE["users_password"] != "");
          if (!empty($user_login) && !empty($user_password) || $by_cookie) {
             return $this->GetUser($user_login, $user_password, $by_cookie);
          } else {
             $this->Error = true;
             $this->ErrorMessage = "Указажите логин и/или пароль.";
             return false;
          }
      }

      function CheckAccess($item, $item_type = "dir") {
               if (!$this->CheckSession()) {
                  $this->Error = true;
                  $this->ErrorMessage = "Необходимо пройти авторизацию.";
                  return false;
               }
               return true;
      }

      function CheckSession() {
          //$temp = mysql_fetch_object(mysql_query("select users_id from users where users_id = '".$_SESSION["user"]->users_id."' and users_current_hash = '".$_SESSION["user"]->users_current_hash."'"));
          $current_login = $_SESSION["user"]->users_login;
          $current_password = $_SESSION["user"]->users_password;                                                                                                                            
          if ($_SESSION["logged_in"] == true && strlen($_SESSION["user"]->users_current_hash) == 32 && $_SESSION["user"]->users_id > 0 && parent::GetObject($_SESSION["user"]->users_id) && $current_login == $this->users_login && $current_password == $this->users_password && $this->users_active != "") {
             //if ($_SESSION["user"]->users_id != $this->users_id) parent::GetObject($_SESSION["user"]->users_id);
             if ($this->users_groups_id > 0) $this->users_groups_name = mysql_result(mysql_query("select users_groups_name from users_groups where users_groups_id = ".$this->users_groups_id), 0, 0);
             $this->SetSession();
             return true;
          } else {
             $this->ClearSession();
             return false;
          }
      }

      function SetSession() {
          $_SESSION["logged_in"] = true;
          $this->users_current_hash = md5(time().$this->users_login.$this->users_password);
          if ($_SESSION["by_cookie"]) {
                setcookie("users_login", $this->users_login, time()+3600 * 24 * 365);
                setcookie("users_password", $this->users_password, time()+3600 * 24 * 365);
          }
          foreach($this->Fields as $key) {
                $_SESSION["user"]->{$key["field_name"]} = $this->{$key["field_name"]};
          }
          $_SESSION["user"]->users_groups_name = $this->users_groups_name;
          @mysql_query("update users set users_current_hash = '".$this->users_current_hash."' where users_id = '".$this->users_id."'");
      }

      function ClearSession() {
          $_SESSION["logged_in"] = false;
          $_SESSION["user"] = false;
      }
}


?>
