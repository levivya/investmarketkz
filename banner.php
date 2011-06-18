<?php
/******************************************************************************
Power Banner Manager 1.5 !
(banner.php file)

Copyright Armin Kalajdzija, 2002.
E-mail: kalajdzija@hotmail.com
WebSite: http://www.ak85.tk
******************************************************************************/

include "pbmadmin/config.inc.php";
$bancount = 0;
$varcount = 0;
$rande = false;
$abcount = 0;


if (isset($hostname) and isset($database) and isset($db_login) and isset($db_pass)) {
    $dbconn = mysql_connect($hostname, $db_login, $db_pass) or die("Could not connect");

    mysql_select_db($database) or die("Could not select database");

    if (isset($uid) and ($uid <> "")) {
          $query = "SELECT src,alt,url,name,id,type,dis_times,dised_times,target,dtype FROM powerban WHERE uid=$uid";
          if (isset($zid) and ($zid <> "")) {
             $query = $query." AND zone=".$zid;
          }
    }else if (isset($zid) and ($zid <> "")) {
          $query = "SELECT src,alt,url,name,id,type,dis_times,dised_times,target,dtype FROM powerban WHERE zone=$zid";
          if (isset($uid) and ($uid <> "")) {
             $query = $query." AND uid=".$uid;
          }
    }else{
          $query = "SELECT src,alt,url,name,id,type,dis_times,dised_times,target,dtype FROM powerban";
    }
    $result = mysql_query($query) or die("Query failed");
    $numrows = mysql_num_rows ($result);

    while ($rows = mysql_fetch_row($result)) {
    $bancount = $bancount + 1;
    $banner[$bancount] = "$rows[0]|$rows[1]|$rows[2]|$rows[3]|$rows[4]|$rows[5]|$rows[6]|$rows[7]|$rows[8]|$rows[9]";
    }


while ($rande <> true) {

if ($abcount == $bancount) {
    $rande = true;
    print "No more banners to display";
}else{

$display_banner = rand(1,$bancount);      //generates the randome number from 1 to the number of banners :)

list($src,$alt,$link,,$bid,$type,$dis_times,$dised_times,$target,$dtype,$location) = split('[|]',$banner[$display_banner]);

    if (($dis_times > $dised_times) or ($dis_times == 0)) {
    $rande = true;
    $dised_times = $dised_times + 1;
    $query = "UPDATE powerban SET dised_times=$dised_times WHERE id=$bid";
    $result = mysql_query($query) or die("Query failed");

    $cdate = date("Y-m-d");
    $query = "INSERT INTO powerban_stats_views (id, date) VALUES ('$bid', '".$cdate."')";
    $result = mysql_query($query) or die("Query failed");

    mysql_close($dbconn);

    if ($type == 1) {                   //image check
       if ($dtype == 1) {
          echo "<a href='pbmadmin/visit.php?id=$bid' target='$target'><img src='$src' alt='$alt' border=0></a>";   //displays the image on site
       }else if ($dtype == 2) {
          $fp = fopen ("pbmadmin/tmp/bantemp.htm", "w");
          fputs($fp,"<title>$alt</title>");
          fputs($fp,"<a href='../visit.php?id=$bid' target='$target'><img src='$src' alt='$alt' border=0></a>");
          fclose($fp);
          echo "<script language='JavaScript'>
function popup() {
var f = document.forms[0];
var docServerPath = 'pbmadmin/tmp/bantemp.htm';
window1=window.open(docServerPath,'messageWindow1','scrollbars=no,width=490,height=70');
}</script>
<body onload='popup()'></body>";

       }else if ($dtype == 3) {
          echo "<head>
          <script language='JavaScript'>
<!--
function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf('?'))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_showHideLayers() { //v3.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}
//-->
</script>
</head>
<DIV CLASS='jsbrand' ID='jsbrand'
STYLE='position:absolute;top:1;visibility:hide;; width: 480px; height: 71px' zIndex='1000' ALIGN='right'>
  <p align='left'><a href='#'><img src='pbmadmin/images/advertisement.gif' onClick=";
  echo chr(34);
  echo "MM_showHideLayers('jsbrand','','hide')";
  echo chr(34);
  echo " alt='Click to close banner' width='120' height='10' border='0'></a><br>
    <a href='pbmadmin/visit.php?id=$bid' target='$target'><img src='$src' alt='$alt' border='0'></a></p>
  </DIV>
<p>
  <script language='Javascript1.2'>
<!--
// you must keep the following lines on when you use this
// original idea from the Geocities Watermark
// © Nicolas - http://www.javascript-page.com

var window_says  = '$alt';
var image_width = 88;
var image_height = 31;
var left_from_corner = 380;
var up_from_corner = 40;

var JH = 0;
var JW = 0;
var JX = 0;
var JY = 0;
var left = image_width + left_from_corner + 17;
var up = image_height + up_from_corner + 15;

if(navigator.appName == 'Netscape') {
var wm = document.jsbrand;
}

if (navigator.appVersion.indexOf('MSIE') != -1){
var wm = document.all.jsbrand;
}

wm.onmouseover = msover
wm.onmouseout = msout

function watermark() {

 if(navigator.appName == 'Netscape') {
   JH = window.innerHeight
   JW = window.innerWidth
   JX = window.pageXOffset
   JY = window.pageYOffset
   wm.visibility = 'hide'
   wm.top = (JH+JY-up)
   wm.left = (JW+JX-left)
   wm.visibility= 'show'
 }

 if (navigator.appVersion.indexOf('MSIE') != -1){
  if (navigator.appVersion.indexOf('Mac') == -1){
   wm.style.display = 'none';
   JH = document.body.clientHeight;
   JW = document.body.clientWidth;
   JX = document.body.scrollLeft;
   JY = document.body.scrollTop;";

if ($location == 1) {
   print "wm.style.top = (JY+10);";
   print "wm.style.left =(JX+5);";
}else if ($location == 2) {
   print "wm.style.top = (JY+10);";
   print "wm.style.left =(JW+JX-left);";
}else if ($location == 3) {
   print "wm.style.top = (JH+JY-up);";
   print "wm.style.left =(JX+5);";
}else if ($location == 4) {
   print "wm.style.top = (JH+JY-up);";
   print "wm.style.left =(JW+JX-left);";
}
echo "   wm.style.display = '';
  }
 }
}

function msover() {
    window.status = window_says;
    return true;
}

function msout() {
    window.status = '';
    return true;
}

setInterval('watermark()',100);
//-->
</script>";
       }
    }else if($type == 2) {                      // flash check
       $swfdims = split('[x]',$link);
       print "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0' width='$swfdims[0]' height='$swfdims[1]'>";
       print "<param name=movie value='$src'>";
       print "<param name=quality value=high>";
       print "<embed src='$src' quality=high pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash' wmode=opaque type='application/x-shockwave-flash' width='$swfdims[0]' height='$swfdims[1]'>";
       print "</embed></object>";
    }
    }else{
        $rande = false;
        $abcount = $abcount + 1;
    }
}
}
}
?>

