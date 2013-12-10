<?
//**************************************************************************************************************
//  Возвращает хидеры и контент указаного URL
//  параметр $url(string) - требуемый URL
//  параметр $useProxy(bool) - использовать ли прокси (должна быть определена функция get_proxy)
//  параметр $timeout(int) - таймаут
//  должно быть активное соединение с MySQL базой
//  возвращает assoc array если контент получен
//             false если произошла ошибка
//**************************************************************************************************************

function get_content($url, $useProxy = false, $timeout = 600, $referer = "") {
    if (function_exists("curl_init")) {
        $ch = @curl_init();

        if (function_exists("get_agent")) {
            $ag = get_agent();
            curl_setopt ($ch, CURLOPT_USERAGENT, $ag);
        } else {
            curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        }

        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt ($ch, CURLOPT_REFERER, $referer);

        if ($useProxy && function_exists("get_proxy")) {
           $proxy = get_proxy();
           if ($proxy) curl_setopt ($ch, CURLOPT_PROXY, $proxy["proxy_host"].":".$proxy["proxy_port"]);
        }
        curl_setopt ($ch, CURLOPT_HEADER, 1);
        curl_setopt ($ch, CURLOPT_URL, $url);
        $result = @curl_exec ($ch);
        @curl_close ($ch);
        if (get_magic_quotes_runtime()) $result = stripslashes($result);

        $pos = 0;
        while(preg_match("!HTTP/1\.[01] !i", $result, $regs, PREG_OFFSET_CAPTURE, $pos)) {
            $headers = substr($result, $pos, strpos($result, "\r\n\r\n", $pos));
            $pos += strpos($result, "\r\n\r\n")+4;
        }
        $content = substr($result, $pos);
        return array("content" => empty($content) ? @file_get_contents($url) : $content, "headers" => $headers);
   } else {
        $content = @file_get_contents($url);
        return array("content" => $content, "headers" => $headers);
   }
   return false;
}
?>