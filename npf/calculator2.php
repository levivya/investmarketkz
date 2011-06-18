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
<title>Государственная пенсия</title>
<meta name="Description" content="Расчет размера государственной пенсии, подлежащей выплате пожизненно за счет государственного бюджета лицу, достигшему пенсионного возраста после 1 января 2011 года." >
<meta name="Keywords" content="государственная пенсия,расчет размера государственной пенсии, трудовой стаж, государственная базовая пенсионная выплата, минимальная пенсия">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
<script language="javascript">
	showMenuIndicator();
	document.mainform.avg_income.focus();
	function doCalc()
	{
	        zeroBlanks(document.mainform);

	        var  avg_income = Math.min(numval(document.mainform.salary.value),39*numval(document.mainform.mrp.value))
	        var tmp1 = numval(document.mainform.gender.value) == 1 ? 25 : 20;
	        var tmp2 = numval(document.mainform.stazh.value) > 0.5 ? (0.6*numval(document.mainform.stazh.value))/tmp1 : 0;
            var kof = numval(document.mainform.stazh.value)> tmp1 ? Math.min (0.75, 0.6+0.01*(numval(document.mainform.stazh.value)-tmp1)) : tmp2;
            var gos_pens=Math.max(avg_income*kof,numval(document.mainform.min_pens.value))+ numval(document.mainform.base_pens.value);

	        document.mainform.avg_income.value = formatNumber(avg_income,3);
	        document.mainform.kof.value = formatNumber(kof,3);
   	        document.mainform.gos_pens.value = gos_pens;
   	        document.mainform.avg_income.style.backgroundColor = "#F1F1F1";
            document.mainform.kof.style.backgroundColor = "#F1F1F1";
            document.mainform.gos_pens.style.backgroundColor = "#E85A98";
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
<noindex>
<div class="mainContent">
<h1 class="title">Государственная пенсия - расчет<a href="/npf/" class="more">Пенсионные фонды Казахстана</a></h1>
<div class="text">
Расчет <strong>государственной пенсии</strong> производится согласно положениям Закона Республики Казахстан "О пенсионном обеспечении" и Закона Республики Казахстан  "О республиканском бюджете на 2011-2013 годы".
Расчет не учитывает льгот и компенсаций, предоставляемых отдельным категориям граждан (например, женщинам, родившим и воспитывающим 5 и более детей, пострадавшим в результате ядерных испытаний, военнослужащим и др.).
Более подробную информацию можно получить, обратившись  в филиалы Государственного Центра по Выплате Пенсий (ГЦВП).
</div>
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
    <div title="Трудовой стаж">Трудовой стаж</div>
	<input type="text" name="stazh" size="14" value="5" onChange="value=formatNumber(value,2,0)" title="Ваш трудовой стаж до 1 января 1998 года">до 1 января 1998 года (лет)
</li>
<li>
    <div title="Среднемесячная зарплата">Среднемесячная зарплата</div>
	<input type="text" name="salary" size="14" value="75000.00" onChange="value=formatNumber(value,2,0)" title="Ваша  среднемесячная зарплата в течение любых 3 последовательных лет (до уплаты налога)">в течение любых 3 последовательных лет (до уплаты налога)
</li>
<li>
    <div title="Месячный расчетный показатель (МРП)">МРП</div>
	<input type="text" name="mrp" size="6" value="1512.00" onChange="value=numval(value,2,0)" disabled style="background-color:#F1F1F1" title="Месячный расчетный показатель (МРП)">Месячный расчетный показатель в 2011 году (тенге)
</li>
<li>
    <div>Базовая ставка</div>
	<input type="text" name="base_pens" size="6" value="8000.00" onChange="value=numval(value,2,0)" disabled style="background-color:#F1F1F1" title="Государственная базовая пенсионная выплата">в 2011 году (тенге)
</li>
<li>
    <div title="Минимальная пенсия">Минимальная пенсия</div>
	<input type="text" name="min_pens" size="6" value="16047" onChange="value=numval(value,2,0)" disabled style="background-color:#F1F1F1" title="Минимальная пенсия">в 2011 году (тенге)
</li>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="button" class="red" value="Рассчитать" onClick="doCalc()" title="рассчитать пенсию"></span>
<br /><br />
<li>
    <div title="Среднемесячный доход">Среднемесячный доход</div>
	<INPUT TYPE="TEXT" NAME="avg_income" SIZE="14" READONLY title="Среднемесячный доход, принимаемый для расчета государственной пенсии">принимаемый для расчета государственной пенсии (тенге в месяц)
</li>
<li>
    <div>Коэффициент</div>
	<INPUT TYPE="TEXT" NAME="kof" SIZE="14" READONLY title="Коэффициент, учитывающий трудовой стаж до 1 января 1998 года">учитывающий трудовой стаж до 1 января 1998 года
</li>
<li>
    <div title="размер государственной пенсии">Размер гос. пенсии</div>
	<INPUT TYPE="TEXT" NAME="gos_pens" SIZE="14" READONLY title="Размер государственной пенсии">тенге в месяц (до уплаты налога)
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