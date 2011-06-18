<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Реклама на сайте Invest-market.kz</title>
<meta name="Description" content="Реклама на сайте Invest-market.kz" >
<meta name="Keywords" content="реклама,Invest-Market.kz">
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
 <div class="title">Аудитория</div>
      <div class="text">
      Сайт «Invest-Market.kz» является одним из самых авторитетных и популярных информационно-аналитических порталов посвященных вопросам управления личными финансами в Казахстане.  Его аудиторию составляет наиболее активная и образованная часть населения, которая заинтересована в правильном управлении собственными деньгами, используя различные финансовые продукты.
      <br><br>
      Напишите нам на <a href="mailto:customer-service@invest-market.kz">customer-service@invest-market.kz</a>.
      <br><br>
      Основные метрики<br>
	  <table border="1" cellpadding="20" width="400">
	  <tr><td colspan="2"><b>Возраст</b></td><td colspan="2"><b>Пол</b></td></tr>
	  <tr><td>От 18 до 24 лет</td><td>26%</td><td>Мужской</td><td>64%</td></tr>
	  <tr><td>От 25 до 44 лен</td><td>70%</td><td>Женский</td><td>36%</td></tr>
	  <tr><td>Более 44 лет</td><td>3%</td><td colspan="2"></td></tr>
	  </table>
      </div>
      <div class="title">Тарифы</div>
      <div class="text">
      Главный раздел сайта<br>
      <table border="1" cellpadding="20" width="400">
	  <tr><td><b>Вариант</b></td><td align="right"><b>Стоимость за 1000 показов(*)(**)</b></td></tr>
	  <tr><td>Вариант 1 (470х60)</td><td align="right">800 тенге</td>
	  <tr><td>Вариант 2 (240х400)</td><td align="right">900 тенге</td>
	  </table>
	  <br>
      Другие разделы  сайта (НОВОСТИ, ПИФЫ, ДЕПОЗИТЫ, НПФ)<br>
      <table border="1" cellpadding="20" width="400">
	  <tr><td><b>Вариант</b></td><td align="right"><b>Стоимость за 1000 показов(*)(**)</b></td></tr>
	  <tr><td>Вариант 1 (470х60)</td><td align="right">600 тенге</td>
	  <tr><td>Вариант 2 (240х400)</td><td align="right">700 тенге</td>
	  </table>
      <br>
      <img src="media/images/reklama.png" alt="" border="0"><br>
      (*) - Минимальный объем размещения — 100 000 показов.<br>
      (**) - Скидка для рекламных агентств — 3%.
      </div>
</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>