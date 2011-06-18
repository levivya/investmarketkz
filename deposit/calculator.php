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
<title>Депозитный калькулятор - рассчитать проценты по вкладам</title>
<meta name="Description" content="Депозитный калькулятор. Рассчитать проценты по вкладам Казахстанских Банков." >
<meta name="Keywords" content="депозитный калькулятор, депозит, годовая ставка, срок депозита, сумма депозита, калькулятор, тенге, доходность депозита, вклад, проценты">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<?php include '../includes/scripts.php';?>
<script language="javascript">
<!--
showMenuIndicator();

document.mainform.p.focus();

function doCalc()
{
        zeroBlanks(document.mainform);
        var p = numval(document.mainform.p.value);
        var c = 0
        var r = (numval(document.mainform.r.value)/100)/12;
        var m = numval(document.mainform.m.value);
        document.mainform.fvs.value = formatNumber(p + p*r*m,2);
        document.mainform.fvc.value = formatNumber(basicInvestment(p,r,m,c),2);
}
//-->
</script>
</head>
<body>
<div id="container">
<!-- header -->
<?php
     // Connecting, selecting database
     $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
     $selected_menu='deposit';
     include '../includes/header.php';
     $income=(isset($income))?($income):(10);
?>
<!-- main body -->
<noindex>
<div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity"><iframe src="../banner.php" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">Ваш браузер не поддерживает плавающие фреймы!</iframe></div>
    <!-- end sidebar2 -->
</div>
</noindex>
<div class="mainContent">
<h1 class="title">Депозитный калькулятор</h1>
<div class="text">Все еще сомниваетесь какой <strong>вклад</strong> выбрать из всего множества <a href="deposits.php">депозитов банков Казахстана</a>? Самым просты способом является оценка доходности депозитов и итоговая сумма накоплений по вкладу. Для этого мы создали <strong>депозитный калькулятор</strong>. Задайте ставку депозита, начальную сумму и планируемый срок вложений и депозитный калькулятор рассчитатет проценты по вкладу.</div>
<div class="yashare-auto-init" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>
<form name="mainform" action="JavaScript:doCalc()" method="post">
<div class="search-block grey-block">
<ul>
<li><div>Сумма депозита</div><INPUT TYPE="TEXT" NAME="p" SIZE="14" VALUE="500000.00" onChange="value=formatNumber(value,2,0)">тенге</li>
<li><div>Срок депозитаа</div><INPUT TYPE="TEXT" NAME="m" SIZE="6" VALUE="12" onChange="value=numval(value,2,0)">мес.</li>
<li><div>Годовая ставка</div><INPUT TYPE="TEXT" NAME="r" SIZE="6" VALUE="<?php echo $income; ?>" onChange="value=numval(value,2,0)">%</li>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="button" class="red" value="Рассчитать" onClick="doCalc()" title="Рассчитать доход от вклада"></span>
<br /><br />
<li><div>Итоговая сумма депозита</div><INPUT TYPE="TEXT" NAME="fvs" SIZE="14" READONLY>тенге, выплата процента в конце срока</li>
<li><div>Итоговая сумма депозита</div><INPUT TYPE="TEXT" NAME="fvc" SIZE="14" READONLY>тенге, выплата процента ежемесячно с капитализацией</li>
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