<?
echo "document.getElementById('curtime').innerHTML = '".date("H:i".($_GET["s"] == "1" ? ":s" : ""))."';";
?>