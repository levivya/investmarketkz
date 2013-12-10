<?
function delete_files($dir, $mask = "") {
   if (empty($dir) || !file_exists($dir) || !is_dir($dir)) return false;
   $d = dir($dir);
   while (false !== ($entry = $d->read())) {
      if ($entry != "." && $entry != ".." && (!empty($mask) && preg_match("!^".$mask."\$!", $entry, $regs))) {
         @unlink($dir.$entry);
      }
   }
   $d->close();
}
?>