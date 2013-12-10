<?
function to_translit($str) {
    $transchars =array (
    "E1"=>"A",
    "E2"=>"B",
    "F7"=>"V",
    "E7"=>"G",
    "E4"=>"D",
    "E5"=>"E",
    "B3"=>"Jo",
    "F6"=>"Zh",
    "FA"=>"Z",
    "E9"=>"I",
    "EA"=>"I",
    "EB"=>"K",
    "EC"=>"L",
    "ED"=>"M",
    "EE"=>"N",
    "EF"=>"O",
    "F0"=>"P",
    "F2"=>"R",
    "F3"=>"S",
    "F4"=>"T",
    "F5"=>"U",
    "E6"=>"F",
    "E8"=>"H",
    "E3"=>"C",
    "FE"=>"Ch",
    "FB"=>"Sh",
    "FD"=>"W",
    "FF"=>"X",
    "F9"=>"Y",
    "F8"=>"Q",
    "FC"=>"Eh",
    "E0"=>"Ju",
    "F1"=>"Ja",

    "C1"=>"a",
    "C2"=>"b",
    "D7"=>"v",
    "C7"=>"g",
    "C4"=>"d",
    "C5"=>"e",
    "A3"=>"jo",
    "D6"=>"zh",
    "DA"=>"z",
    "C9"=>"i",
    "CA"=>"i",
    "CB"=>"k",
    "CC"=>"l",
    "CD"=>"m",
    "CE"=>"n",
    "CF"=>"o",
    "D0"=>"p",
    "D2"=>"r",
    "D3"=>"s",
    "D4"=>"t",
    "D5"=>"u",
    "C6"=>"f",
    "C8"=>"h",
    "C3"=>"c",
    "DE"=>"ch",
    "DB"=>"sh",
    "DD"=>"w",
    "DF"=>"x",
    "D9"=>"y",
    "D8"=>"",
    "DC"=>"eh",
    "C0"=>"ju",
    "D1"=>"ja",
    );

    $str = html_entity_decode($str);
    $str = preg_replace("!<script[^>]{0,}>.*</script>!Uis", "", $str);
    $str = strip_tags($str);
    $str = preg_replace("![^àáâãäå¸æçèéêëìíîïğñòóôõö÷øùüûúışÿÀÁÂÃÄÅ¨ÆÇÈÉÊËÌÍÎÏĞÑÒÓÔÕÖ×ØÙÜÛÚİŞßa-z0-9 ]!i", " ", $str);
    $str = preg_replace("![\s]{2,}!", " ", $str);
    $str = trim($str);
    $ns = convert_cyr_string($str, "w", "k");
    for ($i=0;$i<strlen($ns);$i++) {
        $c=substr($ns,$i,1);
        $a=strtoupper(dechex(ord($c)));
        if (isset($transchars[$a])) {
            $a=$transchars[$a];
        } else if (ctype_alnum($c)){
            $a=$c;
        } else if (ctype_space($c)){
            $a='-';
        } else {
            $a='';
        }


        $b.=$a;
    }
    return $b;
}
?>