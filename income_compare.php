<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Last-Modified
$LastModified_unix = gmmktime(0, 0, 0, date('m'), date('d'), date('Y'));
$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
header('Last-Modified: '. $LastModified);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Куда вложить деньги</title>
<meta name="Description" content="Куда вложить деньги - cравнитt доходность финансовых инструментов (пенсионные фонды, пифы, ставка депозитов, пифкз, нпфкз, kase, инфляция, usd)" >
<meta name="Keywords" content="rуда вложить деньги, сравнить доходность, пиф, паевой фонд, доходность,пенсионный фонд, нпф, финансовые индексы, инфляция, ПИФКЗ, НПФКЗ, РТС, KASE, USD">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include 'includes/scripts.php';?>
<script>
  function ShowHide(){$("#slidingDiv").animate({"height": "toggle"}, { duration: 100 });}
</script>

</head>
<body>
<div id="container">
<!-- header -->
<?php
        // Connecting, selecting database
        $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
        $selected_menu='main';
        include 'includes/header.php';
?>
<div class="one-column-block">
<h1 class="title">Куда вложить деньги<a href="#" class="more" onclick="ShowHide(); return false;">Скрыть подсказку</a></h1>
<div class="text" id="slidingDiv">
Задумываетесь о том, куда вложить деньги? Хотите выбрать доходный <a href="/pif/">паевой фонд</a> или <a href="/deposit/">депозит</a>? Планируете оценить эффективность <a href="/npf/">пенсионных фондов Казахстана</a>? А может быть думаете вложить деньги в акции, золото или валюту? Чтобы помочь ответить на эти вопросы, мы создали инструмент, который поможет сравнить доходность различных финансовых инструментов, и понять какой из них является наиболее прибыльным.
<div class="yashare-auto-init" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>
</div>
<noindex>
<!-- amstock script-->
  <script type="text/javascript" src="./amcharts/amstock/swfobject.js"></script>
	<div id="flashcontent" class="search-block grey-block">
		<strong>Обновите ваш Flash Player</strong>
	</div>

	<script type="text/javascript">
		// <![CDATA[
		var so = new SWFObject("./amcharts/amstock/amstock.swf", "amstock", "955", "500", "8", "#FFFFFF");
		so.addVariable("path", "./amcharts/amstock/");
		so.addVariable("settings_file", encodeURIComponent("./amcharts/amstock/amstock_settings_multiple.xml"));
		so.addVariable("preloader_color", "#184789");
		so.addParam("wmode", "transparent");
		so.write("flashcontent");
		// ]]>
	</script>
<!-- end of amstock script -->

<script type="text/javascript"><!--
google_ad_client = "pub-2712511792023009";
/* 728x90, создано 24.09.10 */
google_ad_slot = "7735537292";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</noindex>
</div>
<!-- end of main body -->
<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>