<?
//**************************************************************************************************************
//  Подключает заданные функции
//  параметр string список функций через запятую
//  параметр bool true - выводить сообщения об ошибках, false - не выводить
//  должна быть определена константа FUNCTIONSDIR - папка с файлами функций
//  возвращает true если все функции подключены
//             false если не определена константа FUNCTIONSDIR или не существует хотя бы один файл
//**************************************************************************************************************

function use_functions($functions, $echo = false) {
   if (!defined("FUNCTIONSDIR")) {
      if ($echo) echo "<p><font color=ff0000>Error use function <b>$function</b> (FUNCTIONSDIR not defined).</font></p>";
      return false;
   }
   $functions = explode(",", $functions);
   foreach($functions as $key => $function) {
      $function = trim($function);
      if (!function_exists($function)) {
         if (file_exists(FUNCTIONSDIR.$function.".php")) {
            include(FUNCTIONSDIR.$function.".php");
         } else {
            $script = (!empty($_SERVER["DOCUMENT_ROOT"]) ? $_SERVER["DOCUMENT_ROOT"] : realpath(".")).$_SERVER["PHP_SELF"];
            if ($echo) echo "<p><font color=ff0000>Error use function <b>$function</b> (file not exists) in ".$script.".</font></p>";
            return false;
         }
      }
   }
   return true;
}
?>