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
<title>Виртуальный счет (V-Счет)</title>
<meta name="Description" content="Оцените возможности инвестирования в ПИФы, не рискуя собственным капиталом." >
<meta name="Keywords" content="виртуальный счет, v-счет,инвестиции в пифы,Invest-Market.kz">
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
 <div class="title">Что такое V-счёт?</div>
      <div class="text">Услуга <strong>V-счёт</strong> представляет собой "Инвестиционный Симулятор", с помощью которого Вы можете осуществлять виртуальные инвестиции в казахстанские ПИФы, не рискуя собственными деньгами. Данная услуга, прежде всего, будет полезна тем, кто планирует инвестировать свои средства в паевые фонды, но по той или иной причине сомневается в данном способе вложения. Мы надеемся, что "Инвестиционный Симулятор" поможет Вам открыть для себя новые инвестиционные возможности и принять решение о размещении своих средств не только на банковских депозитах, но также и в ПИФах.</div>
      <div class="title">Как открыть V-счёт?</div>
      <div class="text">Для того чтобы открыть виртуальный счёт (V-счёт) в системе <strong>Invest-Market.kz</strong> Вам необходимо пройти процесс регистрации. После активации Вашего логина, Вы сможете войти в персонализированную область портала и воспользоваться "Инвестиционным Симулятором". В момент регистрации, на Ваш V-счёт единовременно начисляется 500 000 тенге, которые Вы можете использовать для виртуального приобретения паев фондов представленных в системе Invest-Market.kz.
      <br />
      Кроме того, при регистрации Вам предлагается определить сумму планируемых ежемесячных инвестиций. Эта сумма будет ежемесячно начисляться на Ваш V-счёт и Вы можете соответственно приобретать паи ПИФов на эти деньги.
      </div>
      <div class="title">Как пользоваться V-счёт-ом?</div>
      <div class="text"> После регистрации в системе Invest-Market.kz у Вас появиться возможность виртуальной покупки и продажи паев различных ПИФов.
       В момент покупки паев, с Вашего виртуального счета будут списаны средства (в зависимости от количества покупаемых паев и их последней опубликованной цены), а при продаже (погашении), средства будут зачислены  обратно на Ваш V-счёт (с учетом инвестиционного дохода или потерь). Тем самым, обладая виртуальным стартовым капиталом, Вы можете либо приумножить свое виртуальное благосостояние либо нет.
       </div>
       <!--
       <div class="title">Дополнительные преимущества V-Счета</div>
       <div class="text">Открыв V-Счет в системе Invest-Market.kz, Вы сможете получать ежедневную рассылку, содержащую последние экономические новости, аналитические материалы о рынке инвестиций, а так же <strong>цены паев и рейтинги казахстанских ПИФов</strong>. К тому же, у Вас появится возможность просмотра информации по ПИФам рисковых инвестиций, с возможностью их виртуального приобретения.</div>
       -->
       <script>
          function reg()
          {
           window.location = "registration.php"
          }
          function enter()
          {
           window.location = "login.php"
          }
       </script>
       <div class="search-block">
       <span>
              <input type="button" class="red" value="Регистрация" onclick="reg()" title="регистрация на сайте">
              <input type="button" value="Вход" onclick="enter()" title="вход">
       </span>
       </div>
</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>