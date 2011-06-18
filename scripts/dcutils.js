//  You appear to be using an unsupported browser. You need to upgrade to version 4 or better of Netscape Navigator or Microsoft Internet Explorer before accessing this feature.

//<!--
// (c) Copyright 1997 - 2007 DataChimp / MoneyChimp

//if ((location.host.indexOf("moneychimp.com") < 0) && (location.host.indexOf("127.0.0.1") < 0) && (location.host.indexOf("localhost") < 0))
//	location.href = "http://www.moneychimp.com";


function showPopWin(url)
{
	var subWin = window.open("","DCsubwin","width=500,height=300,resizable=yes");
	subWin.location.href = "http://" + location.host + url;
	if (subWin.opener == null) subWin.opener = window;
	subWin.opener.name = "opener";
}

function showCalculator(cmode,params)
{
	var url = "/calculator/popup/calculator.htm";
	if (cmode) url += "?mode="+cmode;
	showPopWin(url);
}

function showGlossary(entry)
{
	var url = "/glossary/popup/glossary.htm";
	if (entry) url += "?entry="+entry;
	showPopWin(url);
}

function showSearch()
{
	showPopWin("/search/search.htm");
}

function showBook(entry)
{
	//showPopWin("/books/" + entry + ".htm");
	location.href = "/books/#" + entry;
}

function showAmazon(url)
{
	if (url == null)
	{
		url = "http://www.amazon.com/exec/obidos/redirect-home/moneychimp-20";
	}
	else if (url.length == 10)
	{
		var asin = url;
		url = "http://www.amazon.com/exec/obidos/ASIN/" + asin + "/moneychimp-20";
		//url = "http://www.amazon.com/exec/obidos/ASIN/" + asin + "/ref=ase_moneychimp-20";
		//url = "http://www.amazon.com/exec/obidos/redirect?tag=moneychimp-20&path=ASIN/" + asin;
	}
	
	var y = 400;
	
	var ySc = 0;
	if (document.all != null && parseInt(navigator.appVersion) >= 4) //IE
		ySc = document.body.clientHeight - 20;

	if (ySc > y) y = ySc;
	
	var amznwin = window.open(url,"mcAmznWin","location=yes,menubar=yes,scrollbars=yes,status=yes,toolbar=yes,resizable=yes,width=780,height=" + y + ",screenX=10,screenY=10,top=10,left=10");
	
	var ver = navigator.appVersion;
	var i = ver.indexOf("MSIE ");
	if (i >= 0) ver = ver.substring(i + 5);
	if (parseFloat(ver) >= 5)
		amznwin.focus();
}

function errMsg() {if (confirm("Your browser is not configured to support this feature.  \n\nWould you like to go to the Technical Help page for more information?")) window.location.href = "/techhelp.htm";}

function formErrMsg(msg,fld)
{
	if (fld != null) 
	{
		fld.focus();
		fld.select();
	}

	alert(msg);
}	

function showMenuIndicator()
{
	if (!document.images) return;
	var i;
	for (i = 0; i < document.images.length; i++)
	{
		if (document.images[i].name)
			if (location.href.indexOf("/" + document.images[i].name + ".") >= 0)
				document.images[i].src = "/images/menu/menuindicator.gif";
	}
}
function getCtrlVal(ctrl)
{
	if (ctrl.value != null)
	{
		return ctrl.value;
	}
	else if (ctrl.selectedIndex != null)
	{
		if (ctrl.selectedIndex >= 0)
			return ctrl.options[ctrl.selectedIndex].value;
	}
	else if (ctrl.length != null)
	{
		var i;
		for (i = 0; i < ctrl.length; i++)
		{
			if (ctrl[i].checked) return ctrl[i].value;
		}
	}
	return null;
}

function setCtrlVal(ctrl,val)
{
	if (ctrl.value != null)
	{
		ctrl.value = val;
	}
	else if (ctrl.selectedIndex != null)
	{
		var i;
		for (i = 0; i < ctrl.length; i++)
		{
			if (ctrl.options[i].value == val)
			{
				ctrl.selectedIndex = i;
				break;
			}
		}			
	}
	else if (ctrl.length != null)
	{
		var i;
		for (i = 0; i < ctrl.length; i++)
		{
			if (ctrl[i].value == val)
			{
				ctrl[i].checked = true;
				break;
			}
		}
	}
}

function getHrefParam(paramName)
{
	var i = location.href.indexOf("?" + paramName + "=");
	if (i < 0) i = location.href.indexOf("&" + paramName + "=");
	if (i < 0) return null;

	var j = location.href.indexOf("&", i + 1);
	if (j < 0) j = location.href.length;
	return unescape(location.href.substring(i + 2 + paramName.length, j));
}

function zeroBlanks(formname)
{
	var i, ctrl;
	for (i = 0; i < formname.elements.length; i++)
	{
		ctrl = formname.elements[i];
		if (ctrl.type == "text")
		{
			if (makeNumeric(ctrl.value) == "")
				ctrl.value = "0";
		}
	}
}

function filterChars(s, charList)
{
	var s1 = "" + s; // force s1 to be a string data type
	var i;
	for (i = 0; i < s1.length; )
	{
		if (charList.indexOf(s1.charAt(i)) < 0)
			s1 = s1.substring(0,i) + s1.substring(i+1, s1.length);
		else
			i++;
	}
	return s1;
}

function makeNumeric(s)
{
	return filterChars(s, "1234567890.-");
}

function numval(val,digits,minval,maxval)
{
	val = makeNumeric(val);
	if (val == "" || isNaN(val)) val = 0;
	val = parseFloat(val);
	if (digits != null)
	{
		var dec = Math.pow(10,digits);
		val = (Math.round(val * dec))/dec;
	}
	if (minval != null && val < minval) val = minval;
	if (maxval != null && val > maxval) val = maxval;
	return parseFloat(val);
}

function formatNumber(val,digits,minval,maxval)
{
	var sval = "" + numval(val,digits,minval,maxval);
	var i;
	var iDecpt = sval.indexOf(".");
	if (iDecpt < 0) iDecpt = sval.length;
	if (digits != null && digits > 0)
	{
		if (iDecpt == sval.length)
			sval = sval + ".";
		var places = sval.length - sval.indexOf(".") - 1;
		for (i = 0; i < digits - places; i++)
			sval = sval + "0";
	}
	var firstNumchar = 0;
	if (sval.charAt(0) == "-") firstNumchar = 1;
	for (i = iDecpt - 3; i > firstNumchar; i-= 3)
		sval = sval.substring(0, i) + "," + sval.substring(i);

	return sval;
}

function presentValue(fv,r,y)
{
	return fv/Math.pow(1+r,y);
}

function futureValue(p,r,y)
{
	return p*Math.pow(1+r,y);
}

function returnRate(pv,fv,y)
{
	return Math.pow(fv/pv,1.0/y) - 1.0;
}

function geomSeries(z,m,n)
{
	var amt;
	if (z == 1.0) amt = n + 1;
	else amt = (Math.pow(z,n + 1) - 1)/(z - 1);
	if (m >= 1) amt -= geomSeries(z,0,m-1);
	return amt;
}

function basicInvestment(p,r,y,c)
{
	if (c == null) c = 0;

	return futureValue(p,r,y) + c*geomSeries(1+r,1,y);
}

function annuityPayout(p,r,y)
{
	return futureValue(p,r,y-1)/geomSeries(1+r,0,y-1);
}

function mortgagePayment(p,r,y)
{
	return futureValue(p,r,y)/geomSeries(1+r,0,y-1);
}

function randN(m,s)
{
	return s*Math.sqrt(-2*Math.log(Math.random()))*Math.cos(2*Math.PI*Math.random()) + m;
}

function logNmean(m,s)
{
	return Math.log(m) - (Math.pow(logNsigma(m,s),2)/2);
}

function logNsigma(m,s)
{
	return Math.sqrt(Math.log(Math.pow(s/m,2) + 1));
}

function gmEst(r_am,s)
{
	return Math.sqrt(Math.pow(1 + r_am, 2) - Math.pow(s,2)) - 1;
}

function numOrder(n, m)
{
	return n - m;
}
//-->
