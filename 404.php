<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>404</title>
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include './includes/scripts.php';?>
</head>

<body>
<div id="container">
<!-- header -->
<?php
     // Connecting, selecting database
     $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
     $selected_menu='main';
     include './includes/header.php';
     $income=(isset($income))?($income):(10);
?>

<!-- main body -->
<!--
<div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="banner.php?zid=7624" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px" target="_parent">
                      Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
</div>
-->

<div class="mainContent">
<div class="text">
<font size="5px">404 - Несуществующая страница</font>
<br>
Страница, которую вы читаете, не существует.
<br><br>
Возможно интересующий вас материал находится в одном следующих разделов сайта.
</div>
<br>
<div class="index">
    <ul class="index-menu">
      <li class="m1"><a href="pif/">ПИФы</a></li>
      <li class="m2"><a href="deposit/">Депозиты</a></li>
      <li class="m3"><a href="npf/">НПФ</a></li>
    </ul>
</div>

<script>
          function reg() { window.location = "registration.php"}
</script>
<br>
<div class="search-block"><span>Интересуетесь инвестициями? Откройте V-Счет &nbsp;&nbsp;&nbsp;&nbsp; <input type="button" class="red" value="Регистрация" onclick="reg()"></span></div>

</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>