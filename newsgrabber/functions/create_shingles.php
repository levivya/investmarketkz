<?

function create_shingles($text) {
   $text = html_entity_decode($text);
   $text = preg_replace("!<script[^>]{0,}>.*</script>!Uis", "", $text);
   $text = strip_tags($text);
   $text = preg_replace("![^àáâãäå¸æçèéêëìíîïğñòóôõö÷øùüûúışÿÀÁÂÃÄÅ¨ÆÇÈÉÊËÌÍÎÏĞÑÒÓÔÕÖ×ØÙÜÛÚİŞßa-z0-9 ]!i", " ", $text);
   $text = preg_replace("![\s]{2,}!", " ", $text);
   $text = trim($text);
   $text = explode(" ", $text);
   $step = 0;
   $count = 5;
   $delim = 25;
   $shingles = $allshingles = array();
   if (is_array($text)) {
      while(($step + $count) < sizeof($text)) {
        $temp = implode(" ", array_slice($text, $step, $count));
        $temp1 = $temp;
        $temp = abs(crc32($temp));
        if (!in_array($temp, $shingles)) {
            $allshingles[] = $temp;
            if ($temp % $delim == 0) $shingles[] = $temp;
        }
        $step++;
      }
   }
   $shingles = sizeof($shingles) < 10 ? $allshingles : $shingles;
   return $shingles;
}
?>