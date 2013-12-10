<html>
<head>
<title>Power Banner Manager - Read Me !</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<p> <font face="Trebuchet MS" size="2"><font size="4"><b>Read Me File !<br>
  </b></font>(05.10.2002.)</font><br>
  <br>
  <font face="Trebuchet MS" size="2">1) About Power Banner Manager<br>
  2) What is new in this version<br>
  3) Development History<br>
  4) System Requirements<br>
  5) Installation<br>
  6) Banner Insertation Code<br>
  <font face="Trebuchet MS" size="2">7) About Author</font> </font></p>
<hr size="1">
<p><font face="Trebuchet MS" size="2"><b>1) About Power Banner Manager</b></font></p>
<p><font face="Trebuchet MS" size="2">It is a banner rotation manager with very 
  friendly and easy to use graphical user interface and fast setup script. This 
  script is made for people who webmaster sites with big number of visitors and 
  want to place banners on site. You can use it for your own purposes or you can 
  install it on hosting providers, banner exchange sites and other sites that 
  needs instant banner changes. Script has features like creating administrators 
  and regular users of your banner database, creating banner zones for every group 
  of banners (banner format, dimension, etc), and many other that will be listed 
  on &quot;What is new in this version&quot; and &quot;Development History&quot; 
  part of the read me file. This script is freeware you can use it for all purposes 
  except selling and modifing.</font></p>
<p><font face="Trebuchet MS" size="2"><b>2) What is new in this version</b></font></p>
<p><font face="Trebuchet MS" size="2">This version as well as every other is enhanced 
  with ideas of users that have tested the script and mailed my with thier ideas. 
  Some of new features is: banner zones support (for every group of banners like 
  banner format, dimension, etc), multi-language support (few language files included 
  in this version), banner target window (now banners can redirect user in new 
  window and current), banner display type (banners can be displayed on site, 
  like popup window and javascript watermark), modified statistics (now you can 
  see total and monthly stats for selected banner) and some other stuff not that 
  important. I hope that users will continue to contact me with thier ideas and 
  sugerstions so the next version of Power Banner Manager can be much better :)</font></p>
<p><font face="Trebuchet MS" size="2"><b>3) Development History</b></font></p>
<p><font face="Trebuchet MS" size="2">(Version 1.0)<br>
  It is the first official release of Power Banner Manager. People who used old 
  version can see that this official version has completely different user interface 
  and many new features. In this version, there are three very important features: 
  First and the most important is Multi-User platform of administrating Power 
  Banner Manager. More about this feature in part 3 of this readme file. Second 
  feature is Flash type of banners supported in this version. From now on you 
  can add banners made in Flash. By adding flash banner, the some of features 
  are disabled like clicks counting and visitor information recording because 
  of flash format nature. Third new feature is possibility to enter how many times 
  selected banner can be displayed on site. This version is soo diferent from 
  version before and I hope you will like it. I have added registration in install.php 
  script so I can be in touch with all people who have downloaded this script 
  and to notify them after new version release. </font></p>
<p><font face="Trebuchet MS" size="2"> (Version beta 3)<br>
  There is few very big changes in Power Banner Manager beta 3 version. First 
  of all, I added visitors info, a new table in the database where will be stored 
  visitor informations like Visitors IP, Browser Info, Refered Info etc. Except 
  that, there is a little design changes in script, now you can choose color sheme 
  in config.php, the default is gray :) This beta 3 is more optimised and it works 
  faster (I have deleted some lines of code that I don't need). </font></p>
<p><font face="Trebuchet MS" size="2"> (Version Beta 2)<br>
  This beta 2 version is not much diferent from beta 1 except that authentification 
  part of admin.php script is now based on sessions (not on cookie like beta 1). 
  This is very important change on script becose I have recaved many mails from 
  people that didn't have cookies enabled in there browser.</font></p>
<p><font face="Trebuchet MS" size="2"><b>4) System Requirements</b></font></p>
<p>(Web Server)<br>
  Power Banner Manager will work on any server that supports PHP. It is tested 
  on Windows (IIS) and Linux (Apache) web server platforms. Script might work 
  on other platforms that supports PHP but it is not tested yet and it is not 
  supported.</p>
<p>(PHP)<br>
  Whole script was developed on 4.1.1. version of PHP but it is highly recommended 
  to install 4.2.0. version of PHP becouse of security reasons but the script 
  will also work fine on every version of PHP higher then 4.0.0.<br>
  NOTE: From 4.2.0 version of PHP, as default setting they have turned <b>register_globals</b> 
  variable to ON. Whole script is made without register_globars and changing it 
  to work with new settings would mean losing compatibility. This issue will be 
  fixed on next version.</p>
<p>(Database)<br>
  Script is developed for MySQL and it works on any version. There is no settings 
  for database, install script will do all the job.</p>
<p><b><font size="2" face="Trebuchet MS">5) Installation</font></b></p>
<p>First of all, before executing install.php you have to edit config.inc.php 
  file with you database informations. After that you can execute install.php 
  and enter the rest of information needed by the script. Clicking on &quot;Install&quot;, 
  the script will setup database and inform you with every step that is taken 
  in instalation.<br>
  NOTE: Remove install.php file after instalation !</p>
<p><b><font face="Trebuchet MS" size="2">6) Banner Insertation Code</font></b></p>
<p><font face="Trebuchet MS" size="2">There is a few ways of adding code to you 
  site. I will sort it as of purpose:<br>
  <br>
  1) If you want to insert randome banner from whole database (no mether what 
  user or zone) use:<br>
  <b>&lt;?php include &quot;banner.php&quot;; ?&gt;</b><br>
  <br>
  2) If you want to insert randome banner from user ( no mether what zone) use:<br>
  <b>&lt;?php uid=&quot;user_ID&quot;; include &quot;banner.php&quot;; ?&gt;</b><br>
  where user_ID is ID number of user that you want to display banner from.<br>
  <br>
  3) If you want to insert randome banner from user and zone use:<br>
  <b>&lt;?php uid=&quot;user_ID&quot;; zid=&quot;zone_ID&quot;; include &quot;banner.php&quot;; 
  ?&gt;</b><br>
  where user_ID is ID number of user that you want to display banner from and 
  zone_ID is ID number of selected zone.</font></p>
<p><b><font face="Trebuchet MS" size="2">7) About Author</font></b></p>
<p> <font face="Trebuchet MS" size="2">My name is Armin Kalajdzija and I am young 
  developer form Bosnia and Herzegovina. I am 17 years old and this is my first 
  year coding in PHP, before this I was coding in VisualBasic, Delphi, Pascal 
  and other things :) I have many completed projects that are made for some companies 
  around the world, more informations about that you can see on my home page. 
  </font></p>
<p><font face="Trebuchet MS" size="2">If you have any questions or sugestions 
  please send it to:<br>
  <a href="mailto:kalajdzija@hotmail.com">kalajdzija@hotmail.com </a><br>
  or visit my home page:<br>
  <a href="http://www.ak85.tk">http://www.ak85.tk</a></font></p>
</body>
</html>
