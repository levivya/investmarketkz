<HEAD>
<TITLE>Календурь</TITLE>
<META http-equiv="content-type" content="text/html; charset=windows-1251"/>
</HEAD>
<body onKeyPress="KeyPress()" leftmargin=0 rightmargin=0 topmargin=0 bottommargin=0>
<form name="calendar">
<script>
function KeyPress()
{
        if(window.event.keyCode == 27)
                window.close();
}


function setDate(year, month, day){
        var obj = '<?=$_GET["obj"];?>';
        var arr = new Array();
        arr["day"] = day;
        arr["month"] = (month > 0) ? month : document.forms["calendar"].month.options[document.forms["calendar"].month.selectedIndex].value;
        arr["year"] = (year > 0) ? year : document.forms["calendar"].year.options[document.forms["calendar"].year.selectedIndex].value;

        window.opener.document.all[obj + "_day"].value = arr["day"];
        window.opener.document.all[obj + "_month"].value = arr["month"];
        window.opener.document.all[obj + "_year"].value = arr["year"];
        window.close();
        return false;
}

</script>
<?
include("../../config.php");

if (empty($_GET["set_date"])) {
   $set_date = Array("year" => $_GET["year"], "month" => $_GET["month"], "day" => $_GET["day"]);
} else {
   $temp = explode("-", $_GET["set_date"]);
   $set_date = Array("year" => $temp[0], "month" => $temp[1], "day" => $temp[2]);
}


$month = (empty($_GET["month"])) ? date("m") : $_GET["month"];
$today = (empty($_GET["day"])) ? date("d") : $_GET["day"];
$year = (empty($_GET["year"])) ? date("Y") : $_GET["year"];

$cmonth = date("m");
$ctoday = date("d");
$cyear = date("Y");


if (strlen($today) == 1) $today = "0".$today;
if (strlen($month) == 1) $month = "0".$month;
if (strlen($year) == 2) $year = "20".$year;

   if ($month < 1) { $month = 12; }
   if ($month > 12) { $month = 1; }

   if ($year < 1900) { $year = 1970; }
   if ($year > 2035) { $year = 2035; }


$month = (isset($month)) ? $month : date("n",time());
$year  = (isset($year)) ? $year : date("Y",time());
$today = (isset($today))? $today : date("j", time());

//$daylong   = date("l",mktime(1,1,1,$month,$today,$year)); //день недели текст англ.
//$monthlong = date("F",mktime(1,1,1,$month,$today,$year)); //название месяца англ.

$dayone    = date("w",mktime(1,1,1,$month,1,$year)); //день недели цифрой
$dayone = ($dayone == 0) ? 7 : $dayone;
$numdays   = date("t",mktime(1,1,1,$month,1,$year)); //количество дней в месяце
$alldays   = array('Пн','Вт','Ср','Чт','Пт','<font color=red>Сб</font>','<font color=red>Вс</font>');
$next_year = $year + 1;
$last_year = $year - 1;
$next_month = $month + 1;
$last_month = $month - 1;
if ($today > $numdays) { $today--; }
        if($month == "1" ){$month_ru="январь";}
    elseif($month == "2" ){$month_ru="февраль";}
    elseif($month == "3" ){$month_ru="март";}
    elseif($month == "4" ){$month_ru="апрель";}
    elseif($month == "5" ){$month_ru="май";}
    elseif($month == "6" ){$month_ru="июнь";}
    elseif($month == "7" ){$month_ru="июль";}
    elseif($month == "8" ){$month_ru="август";}
    elseif($month == "9" ){$month_ru="сентябрь";}
    elseif($month == "10"){$month_ru="октябрь";}
    elseif($month == "11"){$month_ru="ноябрь";}
    elseif($month == "12"){$month_ru="декабрь";}
//echo $month;
//echo $dayone;



if(checkdate($month,29,$year) && $month==2) {
   //echo "это 29 мес!!! ";
   $dayone=7;
   }

echo '<link href="'.$HTTP_ROOT.'admin/css/style.css" rel="stylesheet" type="text/css">';


echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"250\"  style=\"border: 1px solid #DEDEDE; font-size: 0.9em; margin-bottom: 2px;\">";

//выводим название года
/*echo "<tr>
      <td align=center><a href=".$href."?show_date=".$last_year."-".$month."-".$today."&".$cal_query_string.">&laquo;</a></td>";
echo "<td width=100% class=\"cellbg\" colspan=\"5\" valign=\"middle\" align=\"center\">
      <b>".$year." г.</b></td>\n";
echo "<td align=center><a href=".$href."?show_date=".$next_year."-".$month."-".$today."&".$cal_query_string.">&raquo;</a></td>";
echo "</tr></table>";*/

echo "<select name=\"year\" style=\"width: 100%\" onChange=\"location.href='?obj=".$_GET["obj"]."&year='+this.options[this.selectedIndex].value+'&month=".$_GET["month"]."&day=".$_GET["day"]."&set_date=".implode("-", $set_date)."'\">";
for($_year = date("Y") - 50; $_year < date("Y") + 5; $_year++) {  echo "\n<option value=\"$_year\" ".(($_year == $year) ? "selected" : "").">$_year</option>"; }
echo "</select>";


//выводим название месяца
/*echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"250\" bgcolor=\"#f2f2f6\" style=\"border: 1px solid #dfdfe5; font-size: 0.9em; margin-bottom: 2px;\">";
echo "<tr>
      <td align=center><a href=".$href."?show_date=".$year."-".$last_month."-".$today."&".$cal_query_string.">&laquo;</a></td>";
echo "<td width=100% class=\"cellbg\" colspan=\"5\" valign=\"middle\" align=\"center\">
      <b>".$month_ru."</b></td>\n";
echo "<td align=center><a href=".$href."?show_date=".$year."-".$next_month."-".$today."&".$cal_query_string.">&raquo;</a></td>";
echo "</tr></table>";*/
echo "<select name=\"month\" style=\"width: 100%\" onChange=\"location.href='?obj=".$_GET["obj"]."&year=".$_GET["year"]."&month='+ this.options[this.selectedIndex].value +'&day=".$_GET["day"]."&set_date=".implode("-", $set_date)."'\">
                            <option value=\"1\" ".(($month == 1) ? "selected" : "").">январь</option>
                            <option value=\"2\" ".(($month == 2) ? "selected" : "").">февраль</option>
                            <option value=\"3\" ".(($month == 3) ? "selected" : "").">март</option>
                            <option value=\"4\" ".(($month == 4) ? "selected" : "").">апрель</option>
                            <option value=\"5\" ".(($month == 5) ? "selected" : "").">май</option>
                            <option value=\"6\" ".(($month == 6) ? "selected" : "").">июнь</option>
                            <option value=\"7\" ".(($month == 7) ? "selected" : "").">июль</option>
                            <option value=\"8\" ".(($month == 8) ? "selected" : "").">август</option>
                            <option value=\"9\" ".(($month == 9) ? "selected" : "").">сентябрь</option>
                            <option value=\"10\" ".(($month == 10) ? "selected" : "").">октябрь</option>
                            <option value=\"11\" ".(($month == 11) ? "selected" : "").">ноябрь</option>
                            <option value=\"12\" ".(($month == 12) ? "selected" : "").">декабрь</option>
                            </select>";



echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"250\" style=\"border: 1px solid #DEDEDE; font-size: 0.9em;\"><tr>";
//выводим дни недели
foreach($alldays as $value) {
  echo "<td style=\"border: 1px solid #DEDEDE;\" valign=\"middle\" align=\"center\" width=\"10%\" >
        <b>".$value."</b></td>\n";
}
echo "</tr>\n<tr>\n";


//выводим пустые дни месяца как пробелы
/*for ($i = 0; $i < ($dayone-1); $i++) {
  echo "<td bgcolor=#DEDEDE  valign=\"middle\" align=\"center\" >&nbsp;</td>\n";
}  */
if ($dayone-1 > 0) echo "<td bgcolor=#DEDEDE  valign=\"middle\" align=\"center\" colSpan=\"".($dayone-1)."\">&nbsp;</td>\n";
$i = $dayone-1;

//выводим дни месяца
for ($zz = 1; $zz <= $numdays; $zz++) {
  if ($i >= 7) {  echo "</tr>\n<tr>\n"; $i=0; }
  if ($today == $zz && $month == $set_date["month"] && $year == $set_date["year"]) $background = "background: #80FF80"; else $background = "";

  if ($zz == $ctoday && $month == $cmonth && $year == $cyear) {
    echo "<td style=\"border: 1px solid #DEDEDE; $background \" valign=\"middle\" align=\"center\" >";
    echo "<a style=\"color: red; text-decoration: none;\" href=\"javascript:void(0);\" onClick=\"setDate('$year', '$month', '$zz'); return false;\"><b>".$zz."</b></a>";
    echo "</td>\n";
  }
  else {
    echo "<td style=\"border: 1px solid #DEDEDE; $background\" valign=\"middle\" align=\"center\" >";
    echo "<a style=\"color: black; text-decoration: none;\" href=\"javascript:void(0);\" onClick=\"setDate('$year', '$month', '$zz'); return false;\">".$zz."</a>";
    echo "</td>\n";
  }

  $i++;
}


$create_emptys = 7 - ((($dayone-1) + $numdays) % 7);
if ($create_emptys == 7) { $create_emptys = 0; }

//выводим пустые ячейки
if ($create_emptys != 0) {
  echo "<td valign=\"middle\" align=\"center\" colspan=\"".$create_emptys."\" bgcolor=#DEDEDE></td>\n";
}

echo "</tr>";
echo "</table>";

$now_month = date("n",time());
$now_year  = date("Y",time());
$now_today = date("j", time());

//выводим сегодняшнюю дату с ссылкой
echo "<table border=0 cellpadding=4 cellspacing=1 width=250>";
echo "<tr class=\"listHeader\">
      <td width=100% align=center><a href=\"javascript:void(0);\" onClick=\"setDate('".$now_year."', '".date("m")."', '".$now_today."');\">
      <font color=red>.: Сегодня: ".$now_today.".".date("m").".".$now_year." :.</font></a></td>";
echo "</tr></table>";
?>
</form>
</body>
