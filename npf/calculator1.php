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
<title>Расчет пенсии</title>
<meta name="Description" content="Расчет пенсии. Рассчитать сумму пенсионных накоплений." >
<meta name="Keywords" content="расчет пенсии, какая пенсия, пенсионный фонд, доходность пенсионного фонда">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
<script language="javascript">
	showMenuIndicator();
	document.mainform.pension_capital.focus();

	function doCalc()
	{
	        zeroBlanks(document.mainform);

	        var years_before_retirement = numval(document.mainform.gender.value == 1  ?
											<?php echo($MEN_RETIREMENT_AGE) ?> - numval(document.mainform.age.value):
											<?php echo($WOMEN_RETIREMENT_AGE) ?> - numval(document.mainform.age.value));
	        document.mainform.years_before_retirement.value = formatNumber(years_before_retirement);

	        var wage = numval(document.mainform.wage.value) / 100;
	        var capital = numval(document.mainform.capital.value);
	        var yield = numval(document.mainform.yield.value) / 100;

			var yield_monthly = Math.pow(1 + yield, 1 / 12) - 1;
	        document.mainform.yield_monthly.value = formatNumber(yield_monthly * 100, 2);

	        var wage_increase = numval(document.mainform.wage_increase.value) / 100;
	        var wage_increase_monthly = Math.pow(1 + wage_increase, 1 / 12) - 1;
	        document.mainform.wage_increase_monthly.value = formatNumber(wage_increase_monthly * 100, 2);

	        var progression = (1 + wage_increase_monthly) / (1 + yield_monthly);
	        document.mainform.progression.value = formatNumber(progression, 4);

//	        var pension_capital = (Math.pow(progression, 12 * years_before_retirement) - 1) / (progression - 1) * Math.pow(1 + yield_monthly, (12 * years_before_retirement - 1)) * wage * 10 + capital * Math.pow((1 + yield), years_before_retirement);
	        var pension_capital = (progression == 1 ?
	        						years_before_retirement * 12 :
	        						(Math.pow(progression, 12 * years_before_retirement) - 1) / (progression - 1)) *
	        							Math.pow(1 + yield_monthly, (12 * years_before_retirement - 1)) *
	        								wage * 10 + capital *
	        									Math.pow((1 + yield), years_before_retirement);
	        document.mainform.pension_capital.value = formatNumber(pension_capital,0);
            document.mainform.pension_capital.style.backgroundColor = "#E85A98";

	}
</script>
</head>

<body>
<div id="container">
<!-- header -->
<?php
     // Connecting, selecting database
     $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
     $selected_menu='npf';
     include '../includes/header.php';
     $income=(isset($income))?($income):(10);
?>
<!-- main body -->
<noindex>
  <div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="../banner.php" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
                      Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
    <!-- end sidebar2 -->
  </div>
</noindex>
<div class="mainContent">
<h1 class="title">Расчет пенсии<a href="/npf/" class="more">Пенсионные фонды Казахстана</a></h1>
<div class="text">Наверное у вас часто возникает вопрос <strong>какая пенсия</strong> ждет вас после оканчания трудовой деятельности. Поэтому мы разработали клькулятор, который сделает <strong>расчет пенсии</strong> простым и понятным. Вам всего лишь надо задать основеые параметры, после чего вы получить сумму итоговых пенсионных накоплений.</div>
<div class="yashare-auto-init" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>

<form name="mainform" action="JavaScript:doCalc()" method="post">
<div class="search-block grey-block">
<ul>
<li>
    <div>Пол</div>
    <span>
	<select name="gender">
					<option selected value=1>Мужской</option>
					<option value="2">Женский</option>
	</select>
	</span>
</li>
<li>
    <div title="текущие пенсионные накопления">Накопления</div>
	<input type="text" name="capital" size="14" value="2000000.00" onChange="value=formatNumber(value,2,0)">тенге (ваши текущие накопления)
</li>
<li>
    <div>Возраст</div>
	<input type="text" name="age" size="6" value="30" onChange="value=numval(value,2,0)">лет
	<input type="hidden" readonly name="years_before_retirement" size="6" value="33" onChange="value=numval(value,2,0)">
</li>
<li>
    <div>Заработная плата</div>
	<input type="text" name="wage" size="14" value="150000.00" onChange="value=numval(value,2,0)"> тенге
</li>
<li>
    <div title="доходность пенсионного фона (НПФ)">Доходность</div>
	<input type="text" name="yield" size="6" value="12.00" onChange="value=numval(value,2,0)">%
	<input type="hidden" readonly name="yield_monthly" size="6" value="0.95" onChange="value=numval(value,2,0)">  (годовая доходность выбранного НПФ)
</li>
<li>
    <div title="прогнозируемый ежегодный рост номинальной зарплаты">Рост зарплаты</div>
	<input type="text" name="wage_increase" size="6" value="10.00" onChange="value=numval(value,2,0)">%
	<input type="hidden" readonly name="wage_increase_monthly" size="6" value="0.80" onChange="value=numval(value,2,0)">
	<input type="hidden" readonly name="progression" size="6" value="0.9985" onChange="value=numval(value,4,0)"> (прогнозируемый ежегодный рост номинальной зарплаты)
</li>

<br>
&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="button" class="red" value="Рассчитать" onClick="doCalc()" title="рассчитать пенсию"></span>
<br /><br />
<li>
    <div title="итоговы пенсионные накопления">Итоговые накопления</div>
	<INPUT TYPE="TEXT" NAME="pension_capital" SIZE="14" READONLY>тенге (накопления при выходе на пенсию)
</li>
</ul>
</div>
</form>

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

</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>