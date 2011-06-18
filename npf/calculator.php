<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<html>
<head>
  <title>Расчет пенсии</title>
  <link type="text/css" href="../css/style.min.css" rel=stylesheet  />
  <meta name="Keywords" content="Расчет пенсии">
  <meta name="copyright" content="Invest-Market.kz">
  <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
  <meta HTTP-EQUIV="pragma" CONTENT="no-cache">

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
	}
  </script>


</head>
<body style="background:#fff;" marginright="2" marginheight="2" leftmargin="2" topmargin="2" marginwidth="2">
<?php if (!isset($income)) $income='12.00'; ?>

<form name="mainform" action="JavaScript:doCalc()" method="post">
<div class="search-block">
<ul>
<li>
    <div>Пол</div>
    <span><select name="gender"><option selected value=1>Мужской</option><option value="2">Женский</option></select></span>
</li>
<li>
    <div>Накопления</div>
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
    <div>Доходность</div>
	<input type="text" name="yield" size="6" value="<?php echo $income; ?>" onChange="value=numval(value,2,0)">%
	<input type="hidden" readonly name="yield_monthly" size="6" value="0.95" onChange="value=numval(value,2,0)">  (годовая доходность выбранного НПФ)
</li>
<li>
    <div>Рост зарплаты</div>
	<input type="text" name="wage_increase" size="6" value="10.00" onChange="value=numval(value,2,0)">%
	<input type="hidden" readonly name="wage_increase_monthly" size="6" value="0.80" onChange="value=numval(value,2,0)">
	<input type="hidden" readonly name="progression" size="6" value="0.9985" onChange="value=numval(value,4,0)"> (прогнозируемый ежегодный рост зарплаты)
</li>
<li><div>&nbsp;</div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="button" class="red" value="Рассчитать" onClick="doCalc()" title="рассчитать пенсию"></span>
</li>
<br />
<li>
    <div>Итоговые накопления</div>
	<INPUT TYPE="TEXT" NAME="pension_capital" SIZE="14" READONLY>тенге (накопления при выходе на пенсию)
</li>
<br />
<li><div>&nbsp;</div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="button" value="Закрыть" class="nyroModalClose" id="closeBut"></span>
</li>

</ul>
</div>
</form>

</body>
</html>

