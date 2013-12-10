<?
$asd[] = "test=ok";
$asd[] = "REMOTE_ADDR=".$_SERVER["REMOTE_ADDR"];
$asd[] = "HTTP_X_FORWARDED_FOR=".$_SERVER["HTTP_X_FORWARDED_FOR"];
$asd[] = "HTTP_VIA=".$_SERVER["HTTP_VIA"];
$asd[] = "HTTP_PROXY_CONNECTION=".$_SERVER["HTTP_PROXY_CONNECTION"];

echo implode("&", $asd);
?>