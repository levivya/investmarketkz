<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Депозиты банков Казахстана</title>
<meta name="Description" content="Все депозиты (вклады) банков Казахстана. Ставки, доходность, условия, рейтинг и много другое." >
<meta name="Keywords" content="депозиты казахстана, вклады, выгодные депозиты, ставка депозита, депозит, вклад, банки, как выбрать депозит, рейтинг банков, калькулятор депозитов, новости банков, вкладчик">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
</head>

<body>
<div id="container">
<!-- header -->
<?php
     // Connecting, selecting database
     $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
     $selected_menu='deposit';
     include '../includes/header.php';
?>

<!-- main body -->
  <div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="../banner.php" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
                      Ваш браузер не поддерживает плавающие фреймы!</iframe>
    <!--<a href="#"><img src="media/images/banner2.gif" width="240" height="399" alt="img" /></a> -->
    </div>

    <br />
    <div class="title">Реклама от партнеров</div>
    <script type="text/javascript"><!--
	google_ad_client = "pub-2712511792023009";
	/* 250x250, создано 24.09.10 */
	google_ad_slot = "2344662444";
	google_ad_width = 250;
	google_ad_height = 250;
	//-->
	</script>
	<script type="text/javascript"
	src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>


    <!-- end sidebar2 -->
  </div>

 <!-- DEPOSIT Rating -->
<?php

$query="
   SELECT
           t.deposit_id
          ,t.name
          ,t.bank_id
          ,(select name from ism_banks where bank_id=t.bank_id)  bank_name
          ,rate_12m
          ,min_sum
   FROM
         ism_deposits t
        ,ism_deposit_types tt
   WHERE  t.deposit_id=tt.deposit_id
          and tt.type_id=".$DEP_KZT."
          and t.rate_12m is not null
   ORDER BY t.rate_12m desc
   LIMIT 0,7
        ";

$vdeps=array();
$rc=sql_stmt($query, 6, $vdeps ,2);

?>
<div class="mainContent">

<div class="title" title="Депозиты банков Казахстана">Депозиты банков Казахстана</div>
<div class="text">
Банковские <strong>вклады</strong>, являются наиболее популярным  средством получения дополнительного дохода, которое предлагают <a href="banks.php" title="банки Казахстана">банки Казахстана</a>. К тому  же, это самый надежный способ вложения денег, так как их сохранность гарантируется специальным фондом страхования вкладов. <strong>Депозиты банков</strong> исчисляются десятками и очень сложно выбрать самые выгодные. Нужна максимальная <strong>ставка</strong> - воспользуйтесь сервисом <a href="deposits.php" title="выбрат депозит">Выбрать депозит</a>.
</div>

<div class="two-blocks">
<div class="left-block">
<div class="title" title="Выгодные депозиты">Выгодные депозиты</div>
 <table class="tab-table" id="Rating">
  <thead>
  <tr>
    <th title="">Вклады</th>
    <!--
    <th title="Банк" width="20%">Банк</th>
    -->
    <th class="right" title="ставка депозита, % годовых">%</th>
    <th class="right" title="калькулятор доходности"></th>
  </tr>
  </thead>
  <tbody>
  <?php
  if ($rc>0)
  {
	  for ($i=0;$i<sizeof($vdeps['deposit_id']);$i++)
	  {
	    $class=(fmod(($i),2)==0)?('odd'):('even');
        echo '
	          <tr class="'.$class.'">
    			<td><a href="deposit.php?id='.$vdeps['deposit_id'][$i].'" title="'.$vdeps['name'][$i].'">'.$vdeps['name'][$i].'</a></td>
    			<!--<td><a href="bank.php?id='.$vdeps['bank_id'][$i].'" title="'.$vdeps['bank_name'][$i].'">'.$vdeps['bank_name'][$i].'</a></td>-->
    			<td class="right">'.$vdeps['rate_12m'][$i].'</td>
    			<td class="right"><a class="nyroModal" rev="modal" href="calculator2.php?income='.$vdeps['rate_12m'][$i].'" title="рассчитать доходность вклада"><img src="../media/images/calculator.png" height="20px" alt="рассчитать доходность вклада" border="0"></a></td>
  			  </tr>
  			 ';
	  }
  }
  ?>
  <tr><td class="info" colspan="3">TOP 7 лучших депозитов в тенге, которые прдлагают банки Казахстана.</td></tr>
  </tbody>
</table>

                <!-- News ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                <?php
                $query="
							    SELECT
							           t.news_id  id
							          ,DATE_FORMAT(t.news_date,'%d.%m.%Y') vdate_format
							          ,t.title
							          ,t.special
							  FROM
							          ism_news t
							  WHERE ntype=5
							  ORDER BY t.news_date desc
							  LIMIT 0,8
                            ";

				$vnews=array();
				$rc=sql_stmt($query, 4, $vnews ,2);
                ?>
                <!-- News ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->

    <div class="title"><a class="more" href="../articles.php" title="Новости банков">Все новости</a>Новости банков</div>
    <ul class="list">
  <?php
    for ($i=0;$i<sizeof($vnews['id']);$i++)
     {
       echo '
            <li><a href="../article.php?id='.$vnews['id'][$i].'&type=news" title="'.$vnews['title'][$i].'">'.$vnews['title'][$i].'</a></li>
          ';
     }
   ?>



    </ul>



</div>
<div class="right-block">
<div class="title" title="Начинающий вкладчик"><a class="more" href="../articles.php?type=investor_school" title="Школа инвестора">Школа инвестора</a>Начинающий вкладчик</div>

<ul class="list dark">
	<!-- Investor school ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
	<?php
	$query="
			select
   	  				t.id  id
	 				,t.title
			from
	 				ism_investor_school t
	 		where cont_type=1
		    order by t.vdate desc
	        limit 0,5
	            ";

	$vis=array();
	$rc=sql_stmt($query, 2, $vis ,2);
	for ($i=0;$i<sizeof($vis['id']);$i++)
     {
     	echo '<li><a href="../article.php?type=investor_school&id='.$vis['id'][$i].'" title="'.$vis['title'][$i].'">'.$vis['title'][$i].'</a></li>';
     }
	?>
	<!-- Investor school ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
</ul>

<div class="title" title="Ставка депозитов в тенге">Среднерыночная ставка депозитов, %</div>
<a href="../income_compare.php"><img src="../lib/graph.php?tab=ism_index_value&id_col=index_id&interval=24&id_val=5&val_col=value&date_format=month"  alt="ставка депозита" /></a>
<div class="info"><font size=1><a href="../income_compare.php">Сравнить доходность</a> вкладов с доходностью других инструментов.</font></div>
<br>
<?php
$bank_rating_sql='';
$rating_type=rand(1,2);

switch ($rating_type) {
case 1:
   $bank_rating_cap="S&P";
   $bank_rating_sql="
                       select
                                 t.imetent_id bank_id
                                ,(select name from ism_banks where bank_id=t.imetent_id) bank_name
                                ,DATE_FORMAT(t.rating_date,'%d.%m.%Y') rating_date
                                ,(select desc_ru from ism_dictionary where id=t.sp_long_foring) long_foring
                      from ism_sp_rating t
                      where t.rating_date=(select max(rating_date) from ism_sp_rating where imetent_id=t.imetent_id)
                      order by  t.sp_long_foring
                    ";
   break;
case 2:
   $bank_rating_cap="Fitch Rating";
   $bank_rating_sql="
                       select
                                 t.imetent_id  bank_id
                                ,(select name from ism_banks where bank_id=t.imetent_id) bank_name
                                ,DATE_FORMAT(t.rating_date,'%d.%m.%Y') rating_date
                                ,(select desc_ru from ism_dictionary where id=t.f_long_foring) long_foring
                      from ism_fitch_rating t
                      where t.rating_date=(select max(rating_date) from ism_fitch_rating where imetent_id=t.imetent_id)
                      order by  t.f_long_foring
                    ";
   break;
}

$vbanks=array();
$rc=sql_stmt($bank_rating_sql, 4, $vbanks ,2);
?>
<div class="title" title="Рейтинг банков"><a class="more" href="banks_rating.php" title="рейтинг надежности казахстанских банков">Весь рейтинг банков</a>Рейтинг банков - <?php echo $bank_rating_cap; ?></div>
<table class="tab-table" id="BankRating">
  <thead>
  <tr>
    <th>Банки</th>
    <th title="Долгосрочный рейтинг в иностранной валюте">Рейтинг</th>
    <th title="Дата присвоения рейтинга">Дата</th>
  </tr>
  </thead>
  <tbody>
  <?php
  if ($rc>0)
  {
	  for ($i=0;$i<sizeof($vbanks['bank_id']);$i++)
	  {
	    $class=(fmod(($i),2)==0)?('odd'):('even');
        echo '
	          <tr class="'.$class.'">
    			<td><a href="bank.php?id='.$vbanks['bank_id'][$i].'" title="'.$vbanks['bank_name'][$i].'">'.$vbanks['bank_name'][$i].'</a></td>
    			<td>'.$vbanks['long_foring'][$i].'</td>
    			<td>'.$vbanks['rating_date'][$i].'</td>
  			  </tr>
  			 ';
	  }
  }
  ?>
  </tbody>
</table>

</div>
</div>

<div class="title">Справка</div>
<div class="text">
<strong>Депозит</strong>  или по-другому <strong>банковский вклад</strong>  — это сумма денег, которую вкладчик передает в банк с целью получения дохода в виде процентов, образующегося в результате финансовый деятельности. Проще говоря, банк занимается перераспределением средств, между теми у кого они в избытке, и теми  кто в них нуждается.  Например, если один человек имеет сбережения, и хочет открыть депозит, то другой наоборот хочет взять кредит на покупку автомобиля, в этом случае финансовая организация выступает виде посредника.
Существуют следующие основные <strong>виды вкладов</strong>:
<br><br>
<strong>Срочный вклад</strong>  - депозит, имеющий четко определяющий срок договора и не предусматривающий изъятие средств, до его окончания. Такой способ вложения является более  доходным относительно других видов, так как обеспечивает высокую ставку. К тому же, <strong>банк</strong> обязан выдать полную сумму вклада, по первому требованию вкладчика, но он так же взимает штрафы при достроечном расторжении депозитного договора.
<br><br>
<strong>Вклад до востребования</strong> — депозит, в котором не указан срок вклада, и средства могут быть  возвращены в любой момент, без начисления каких либо пений. Данная гибкость компенсируется относительно низкими ставками по депозитам до востребования, что соответственно означает низкий доход.
<br>
<br>
Поэтому, если вы заранее представляете, на какой срок вы хотите открыть депозит, и точно уверены, что до этого момента денги не понадобятся, то рекомендуется использовать срочный вклад, как максимально выгодный.
</div>


</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>