<?
function get_agent() {
    $agents = array(
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)",
                        "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; ru) Opera 8.01",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows 98)",
                        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.6) Gecko/20050225 Firefox/1.0.1",
                        "Mozilla/4.0 (compatible; MSIE 5.01; Windows 98)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; MyIE2; .NET CLR 1.1.4322)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Maxthon)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1) Opera 7.50 [en]",
                        "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.10) Gecko/20050717 Firefox/1.0.6",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; MRA 3.0 (build 00614))",
                        "Mozilla/5.0 (Windows; U; Windows NT 5.0; ru-RU; rv:1.7.10) Gecko/20050717 Firefox/1.0.6",
                        "Opera/6.01 (Windows 2000; U) [ru]",
                        "Mozilla/5.0 (Windows; U; Windows NT 5.0; ru-RU; rv:1.7.7) Gecko/20050414 Firefox/1.0.3",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; FunWebProducts-MyWay; MRA 4.2 (build 01102))",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; snprtz|dialno; HbTools 4.7.0)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; .NET CLR 1.1.4322)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; MRA 4.0 (build 00768))",
                        "Mozilla/4.0 (compatible; MSIE 5.5; Windows 98; Win 9x 4.90; OptusIE55-31)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; MRA 4.1 (build 00961); .NET CLR 1.1.4322)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; .NET CLR 1.1.4322)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; MRA 4.1 (build 00975); .NET CLR 1.1.4322)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; MRA 4.2 (build 01102))",
                        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; Hotbar 4.6.1)",
                        "Mozilla/4.0 (compatible; MSIE 5.0; Windows 98)",
                        "Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows 2000) Opera 7.0 [en]",
                        "Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 5.0)",
                        "Mozilla/4.0 (compatible; MSIE 5.0; Windows 98; DigExt)",
                        "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.8a5) Gecko/20041122");

    return $agents[rand(0, sizeof($agents)-1)];
}
?>