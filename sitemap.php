<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Last-Modified
$LastModified_unix = filemtime(__FILE__);
$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
header('Last-Modified: '. $LastModified);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Карта сайта</title>
<meta name="Description" content="Карта сайта" >
<meta name="Keywords" content="карта сайта">
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
<div class="one-column-block">
<h1 class="title">Карта сайта</h1>
<ul class="list">
<li class="sm0"><a href="/">Главная</a></li>
<li class="sm1"><a href="/ask_question.php">Задать вопрос финансовому консультанту</a></li>
<li class="sm1"><a href="/income_compare.php">Куда вложить деньги</a></li>
<li class="sm1"><a href="/articles.php?type=investor_school">Школа инвестора</a></li>
<li class="sm1"><a href="/vportfolio.php">Виртуальные инвестиции (V-Счет)</a></li>
<li class="sm1"><a href="/media_list.php">iTV-Онлайн Видео</a></li>
<li class="sm1"><a href="/phpBB2/index.php" title="Форум">Форум</a></li>
<li class="sm1"><a href="/contact.php" title="Контакты">Контакты</a></li>
<li class="sm0"><a href="/deposit/">Вклады</a></li>
<li class="sm1"><a href="/deposit/deposits.php">Депозиты банков Казахстана</a></li>
<li class="sm1"><a href="/deposit/calculator.php">Депозитный калькулятор</a></li>
<li class="sm1"><a href="/deposit/banks.php">Банки Казахстана</a></li>
<li class="sm1"><a href="/deposit/banks_rating.php">Рейтинг банков</a></li>
<li class="sm0"><a href="/npf/">Пенсионные фонды Казахстана</a></li>
<li class="sm1"><a href="/npf/rating.php">Рейтинг пенсионных фондов</a></li>
<li class="sm1"><a href="/npf/calculator1.php">Расчет пенсии</a></li>
<li class="sm1"><a href="/npf/calculator2.php">Государственная пенсия</a></li>
<li class="sm1"><a href="/article_archive.php?type=investor_school&subtype_id=3&title=Моя пенсия">Советы будущему пенсионеру</a></li>
<li class="sm1"><a href="/im_index.php?type=npfkz">Индекс НПФКЗ</a></li>
<li class="sm0"><a href="/pif/">Паевые фонды (ПИФы) Казахстана</a></li>
<li class="sm1"><a href="/pif/funds.php" title="Паевые фонды">Паевые фонды</a></li>
<li class="sm1"><a href="/pif/rating.php">Рейтинг паевых фондов (ПИФов)</a></li>
<li class="sm1"><a href="/pif/map.php">Доходность-Риск паевых фондов</a></li>
<li class="sm1"><a href="/pif/calculator.php">Калькулятор ПИФов</a></li>
<li class="sm1"><a href="/pif/analysis.php">Анализ доходности паевых фондов</a></li>
<li class="sm1"><a href="/im_index.php?type=pifkz">Индекс ПИФКЗ</a></li>
<li class="sm0"><a href="/articles.php">Новости Казахстана</a></li>
<li class="sm1"><a href="/articles.php?type=news-bank">Новости банков</a></li>
<li class="sm1"><a href="/articles.php?type=news-npf">Новости НПФ</a></li>
<li class="sm1"><a href="/articles.php?type=news-site">Новости сайта</a></li>
<li class="sm1"><a href="/articles.php?type=analytic">Аналитика</a></li>
</ul>
</div>
<!-- end of main body -->
<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>