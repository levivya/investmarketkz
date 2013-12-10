<?
function get_remote_filesize($url) {
    $url = parse_url($url);
    if (empty($url["host"])) return false;
    if (empty($url["path"])) return false;
    if (empty($url["scheme"])) $url["scheme"] = "http";
    if ($url["scheme"] == "ftp" && empty($url["port"])) $url["port"] = "21";
    if (empty($url["port"])) $url["port"] = "80";

    $x=0;
    $fp = @fsockopen($url["host"], $url["port"], $errno, $errstr, 30);
    if (!$fp) return false;

    fputs($fp,"HEAD ".$url["path"]." HTTP/1.0\nHOST: ".$url["host"]."\n\n");
    while(!@feof($fp)) $x.=@fgets($fp,128);
    @fclose($fp);
    return ereg("Content-Length: ([0-9]+)",$x,$size) ? $size[1] : 0;
}

?>