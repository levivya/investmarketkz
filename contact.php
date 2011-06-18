<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Контакты</title>
<meta name="Description" content="Контакты" >
<meta name="Keywords" content="контакты,Invest-Market.kz">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include 'includes/scripts.php';?>
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

<!-- main body -->

  <div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="banner.php?zid=7624" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
                      Ваш браузер не поддерживает плавающие фреймы!</iframe>
    <!--<a href="#"><img src="media/images/banner2.gif" width="240" height="399" alt="img" /></a> -->
    </div>
    <!-- end sidebar2 -->
  </div>

<div class="mainContent">
<div class="title">Контакты</div>
<div class="text">
          <b>Связаться с нами</b><br>
		  <!--
		  <b>Офис в Алматы</b><br>
		  ТОО «Invest-Market.kz»<br>
		  Казахстан, Алматы,<br>
		  ул. Фурманова 65<br>
		  Бизнес центр «Алматы Курылыс»<br>
          оф. 607, 6 этаж<br>
          -->
          <!--
		  Тел.: +7 (727) 327 86 58<br>
          -->
          Тел.: +7 (777) 597 39 80; +7 (701) 915 17 17<br>
          <!--
          Тел.: +7 (701) 579 44 00<br>
		  Факс: +7 (727) 292 19 12<br>
		  -->
          E-mail:<A HREF="mailto:customer-service@invest-market.kz">customer-service@invest-market.kz</A><br>
          <!--
          <br>
          <b>Офис в Астане</b><br>
		  ТОО «Invest-Market.kz»<br>
		  Казахстан, Астана,<br>
          ул. Абая 63 (уг. Чокана Валиханова, жилой комплекс Тараз)<br>
          оф. 336<br>
          Тел. +7 (7172) 40 55 22<br>
          E-mail:<A HREF="mailto:customer-service@invest-market.kz">customer-service@invest-market.kz</A><br>
          <br>
          <b>Служба поддержки</b><br>
          E-mail: <A HREF="mailto:support@invest-market.kz">support@invest-market.kz</A><br>
          -->
          <br>
          Ознакомьтесь с <a href="reklama.php">условиями</a> размещения рекламы на нашем сайте.
  </div>

</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>