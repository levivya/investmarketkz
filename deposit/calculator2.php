<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Калькулятор вклада</title>
<meta name="Description" content="Рассчитать доходность вклада." >
<meta name="Keywords" content="вклад, ставка, срок вклада, сумма вклада,калькулятор, тенге">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">

<SCRIPT LANGUAGE="JavaScript">
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
</SCRIPT>
</head>

<body>
<form name="mainform" action="JavaScript:doCalc()" method="post">
<div class="search-block">
<ul>
<li><div>Сумма вклада</div><INPUT TYPE="TEXT" NAME="p" SIZE="14" VALUE="500000.00" onChange="value=formatNumber(value,2,0)">тенге</li>
<li><div>Срок вклада</div><INPUT TYPE="TEXT" NAME="m" SIZE="6" VALUE="12" onChange="value=numval(value,2,0)">мес.</li>
<li><div>Годовая ставка</div><INPUT TYPE="TEXT" NAME="r" SIZE="6" VALUE="<?php echo $income; ?>" onChange="value=numval(value,2,0)">%</li>
<li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="button" class="red" value="Рассчитать" onClick="doCalc()" title="Рассчитать доход от вклада"></span>
<br /><br />
<li><div>Итоговая сумма вклада</div><INPUT TYPE="TEXT" NAME="fvs" SIZE="14" READONLY>тенге, выплата процента в конце срока</li>
<li><div>Итоговая сумма вклада</div><INPUT TYPE="TEXT" NAME="fvc" SIZE="14" READONLY>тенге, выплата процента ежемесячно с капитализацией</li>
<br />
<li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="button" value="Закрыть" class="nyroModalClose" id="closeBut"></span></li>
</ul>
</div>
</form>
</body>
</html>
