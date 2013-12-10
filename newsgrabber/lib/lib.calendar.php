<?php
class Calendar {
    # setFirstValidDate(string)     установка первой доступной даты для навигации, любой формат для strtotime
    # setLastValidDate(string)      установка последней доступной даты для навигации, любой формат для strtotime
    # setGetParams(string)          установка GET параметров, добавляемых к ссылке
    # setDate(string)               установка сразу всей даты, формат YYYY-MM-DD[ HH:II:SS] или DD[/.]MM[/.]YYYY
    # setDay(int|string)            установка текущего дня
    # setMonth(int|string);         установка текущего месяца
    # setYear(int|string);          установка текущего года
    # setHref(string);              установка базовой части ссылки
    # createCalc(null|string|array) создание кода календаря, на вход * или массив с днями, которые должны быть ссылками
    # getCalc(null|string|array)    возвращает код календаря, если его нет - вызывает createCalc
    # printCalc(null|string|array)  выводит код календаря на экран, если его нет - вызывает createCalc
    # setTemplate(string)           устанавливает шаблон календаря

    # Установка неизменяемых переменных

    var $selectedDay, $selectedMonth, $selectedYear;
    var $template;
    var $defaultTemplate;
    var $href;
    var $months = array(1=>'Январь',2=>'Февраль',3=>'Март',4=>'Апрель', 5=>'Май',6=>'Июнь',7=>'Июль',8=>'Август', 9=>'Сентябрь',10=>'Октябрь',11=>'Ноябрь', 12=>'Декабрь');
    var $month_rod = array(1=>'января',2=>'февраля',3=>'марта',4=>'апреля', 5=>'мая',6=>'июня',7=>'июля',8=>'августа', 9=>'сентября',10=>'октября',11=>'ноября', 12=>'декабря');
    var $days = array(0=>'Пн',1=>'Вт',2=>'Ср', 3=>'Чт',4=>'Пт',5=>'Сб',6=>'Вс');

    // установка цветности
    var $arrColors = array();

    // конструктор
    function Calendar( $arrSetColors = 0 ) {
        $this->setDefaultTemplate();

        if ($arrSetColors != 0) {
            if (count($arrSetColors) > 0 && is_array($arrSetColors)) {
               $this->arrColors = $arrSetColors;
            }
        } else {
            $this->arrColors['bgCalendar']          = '#FFFFFF'; // цвет фона календаря
            $this->arrColors['bgGrid']              = '#E0E0E0'; // цвет решётки календарных дней
            $this->arrColors['bgWeekDay']           = '#E0E0E0'; // цвет фона обозначения дня недели Пн, Вт, Ср, .....
            $this->arrColors['bgWorkWeek']          = '#FFFFFF'; // цвет фона рабочей недели с пон. по пятницу
            $this->arrColors['bgWeekendWeek']       = '#E0E0E0'; // цвет фона выходных Сб, Вс
            $this->arrColors['bgChoosedDay']        = '#E4EAF1'; // цвет фона выбранного дня
            $this->arrColors['clWeekDay']           = '#000000'; // цвет букв обозначения дня недели Пн, Вт...
            $this->arrColors['clWeekendDay']        = '#FF0000'; // цвет букв обозначения выходного дня недели Сб, Вс
            $this->arrColors['clDayLink']           = '#000000'; // цвет рабочих дней в календаре
            $this->arrColors['clWeekendDayLink']    = '#FF0000'; // цвет выходных дней в календаре
        }
    }

    function setGetParams($params) {
        $this->getParams = $params;
    }

    function setFirstValidDate($date) {
        $this->firstValidDate = strtotime($date);
    }

    function setLastValidDate($date) {
        $this->lastValidDate = strtotime($date);
    }


    function setDate ($date = "") {
        if (empty($date)) {
            $this->selectedDay = date("d");
            $this->selectedMonth = date("m");
            $this->selectedYear = date("Y");
            return;
        }
        if (preg_match("!([\d]{4})-([\d]{1,2})-([\d]{1,2})|([\d]{4})-([\d]{1,2})-([\d]{1,2}) [\d]{1,2}:[\d]{1,2}:[\d]{1,2} !", $date, $regs)) {
            $this->selectedDay = $regs[3];
            $this->selectedMonth = $regs[2];
            $this->selectedYear = $regs[1];
        }
        if (preg_match("!([\d]{1,2})[\./]([\d]{1,2})[\./]([\d]{2,4})!", $date, $regs)) {
            $this->selectedDay = $regs[1];
            $this->selectedMonth = $regs[2];
            $this->selectedYear = $regs[3];
        }
    }

    function setDay ($day) {
        $this->selectedDay = $day;
    }

    function setMonth ($month) {
        $this->selectedMonth = $month;
    }

    function setYear ($year) {
        $this->selectedYear = $year;
    }

    function setHref($href_string) {
        $this->href = $href_string;
    }

    function setAndCheckDefaults () {
        /*
        if (!$this->selectedDay ) {
            $this->selectedDay = date('d');
        }
        */
        if (!$this->selectedMonth) {
            $this->selectedMonth = date('m');
        }
        if (!$this->selectedYear) {
            $this->selectedYear = date('Y');
        }
    }

    function getCalc($validDays = "") {
        if (empty($this->printTable)) $this->createCalc($validDays);
        return $this->printTable;
    }

    function printCalc($validDays = "") {
        if (empty($this->printTable)) $this->createCalc($validDays);
        echo $this->printTable;
    }

    function createCalc($validDays = "") {
	    $monthOrig;
	    $monthTest;
	    $monthName;
	    $firstday;
	    $dayRow;
	    $lastDay = 31;

	    $this->setAndCheckDefaults();   # make sure we do the right thing(s)

	    # Получаем день недели первого дня месяца:
	    $firstDay = date('w',mktime(0,0,0,$this->selectedMonth,1,$this->selectedYear));
	    if($firstDay > 0) $firstDay--;
	    else $firstDay = 6;

	    $lastDay = 31;
	    do {
		    $monthOrig = date('m',mktime(0,0,0,$this->selectedMonth,1,$this->selectedYear));
		    $monthTest = date('m',mktime(0,0,0,$this->selectedMonth,$lastDay,$this->selectedYear));
		    if ($monthTest != $monthOrig) { $lastDay -= 1; };
	    } while ($monthTest != $monthOrig);

	    # $monthName = date('F',mktime(0,0,0,$this->selectedMonth,1,$this->selectedYear));
	    $monthName = $this->months[(int)$this->selectedMonth];

	    $dayRow = 0;
	    $day = 0;
	    $weekday = 0;
	    $adjustedDay = 0;

        $this->printTable = $this->template;

        // получаем loop заголовка календаря
        preg_match("!<loop dayNames>(.*)</loop dayNames>!U", $this->printTable, $regs);
        // если заголовок получен
        $result = "";
        if (sizeof($regs) > 0) {
            // перебираем дни недели
            for($i=0; $i<=6; $i++) {
                $template = $regs[1];
                $template = preg_replace("!<if isWeekendDay>(.*)</if isWeekendDay>!U", ($i>4) ? "\\1" : "", $template);
                $template = preg_replace("!<if \!isWeekendDay>(.*)</if \!isWeekendDay>!U", ($i>4) ? "" : "\\1", $template);
                $template = str_replace("{dayName}", $this->days[$i], $template);
                $result .= $template;
            }
            $this->printTable = str_replace($regs[0], $result, $this->printTable);
        }


        $resultRow = $resultDay = "";
        // получаем loop строки
        if (preg_match("!<loop rows>(.*)</loop rows>!Us", $this->printTable, $regsRow)) {
            $templateRow = $regsRow[1];
            // получаем loop дней
            if (preg_match("!<loop days>(.*)</loop days>!Us", $this->printTable, $regsDay)) {
                $templateDay = $regsDay[1];

                // считаем и рисуем строки
                $rowsCount = ceil(($lastDay + $firstDay) / 7);
                for($rowNumber = 1; $rowNumber <= $rowsCount; $rowNumber++) {
                    $resultRow = "";
                    for($dayNumber = 0; $dayNumber < 7; $dayNumber++) {
                        $resultDay = $templateDay;
                        $isEmpty = $rowNumber == 1 && $dayNumber < $firstDay || $rowNumber == $rowsCount && $adjustedDay == $lastDay;
                        if (!$isEmpty)
                            $adjustedDay++;
                        $resultDay = preg_replace("!<if empty>(.*)</if empty>!Us", $isEmpty ? "\\1" : "", $resultDay);
                        $resultDay = preg_replace("!<if \!empty>(.*)</if \!empty>!Us", $isEmpty ? "" : "\\1", $resultDay);

                        $isChoosedDay = !$isEmpty && isset($this->selectedDay) && ($this->selectedDay == $adjustedDay);
                        $resultDay = preg_replace("!<if isChoosedDay>(.*)</if isChoosedDay>!Us", $isChoosedDay ? "\\1" : "", $resultDay);
                        $resultDay = preg_replace("!<if \!isChoosedDay>(.*)</if \!isChoosedDay>!Us", $isChoosedDay ? "" : "\\1", $resultDay);

                        $isWeekendDay = $dayNumber > 4;
                        $resultDay = preg_replace("!<if isWeekendDay>(.*)</if isWeekendDay>!Us", $isWeekendDay ? "\\1" : "", $resultDay);
                        $resultDay = preg_replace("!<if \!isWeekendDay>(.*)</if \!isWeekendDay>!Us", $isWeekendDay ? "" : "\\1", $resultDay);

                        $isLink = !$isEmpty && $this->selectedDay != $adjustedDay && $validDays == "*" || is_array($validDays) && in_array($adjustedDay, $validDays);
                        $resultDay = preg_replace("!<if isLink>(.*)</if isLink>!Us", $isLink ? "\\1" : "", $resultDay);
                        $resultDay = preg_replace("!<if \!isLink>(.*)</if \!isLink>!Us", $isLink ? "" : "\\1", $resultDay);

                        $isCurentDay = !$isEmpty && date("Ymd") == date("Ymd", strtotime($this->selectedYear."-".$this->selectedMonth."-".$adjustedDay));
                        $resultDay = preg_replace("!<if isCurentDay>(.*)</if isCurentDay>!Us", $isCurentDay ? "\\1" : "", $resultDay);
                        $resultDay = preg_replace("!<if \!isCurentDay>(.*)</if \!isCurentDay>!Us", $isCurentDay ? "" : "\\1", $resultDay);

                        $resultDay = str_replace("{dayValue}", $adjustedDay, $resultDay);
                        $resultDay = str_replace("{Year}", $this->selectedYear, $resultDay);
                        $resultDay = str_replace("{Month}", $this->selectedMonth, $resultDay);
                        $resultDay = str_replace("{Day}", $adjustedDay<10?"0".$adjustedDay:$adjustedDay, $resultDay);

                        $resultRow .= $resultDay;
                    }
                    $rows .= str_replace($regsDay[0], $resultRow, $templateRow);
                }
                $this->printTable = str_replace($regsRow[0], $rows, $this->printTable);
            }
        }

        foreach($this->arrColors as $key => $value) {
            $this->printTable = str_replace("{".$key."}", $value, $this->printTable);
        }

        $this->printTable = str_replace("{currentMonthName}", $this->months[intval(date("m"))], $this->printTable);
        $this->printTable = str_replace("{currentDate}", date("d")." ".$this->month_rod[intval(date("m"))]." ".date("Y"), $this->printTable);
        $this->printTable = str_replace("{currentDay}", date("d"), $this->printTable);
        $this->printTable = str_replace("{currentMonth}", date("m"), $this->printTable);
        $this->printTable = str_replace("{currentYear}", date("Y"), $this->printTable);

        $this->printTable = str_replace("{selectedMonthName}", $this->months[intval($this->selectedMonth)], $this->printTable);
        $this->printTable = str_replace("{selectedDate}", $this->selectedDay." ".$this->month_rod[intval($this->selectedMonth)]." ".$this->selectedYear, $this->printTable);
        $this->printTable = str_replace("{selectedDay}", $this->selecteDay, $this->printTable);
        $this->printTable = str_replace("{selectedMonth}", $this->selectedMonth, $this->printTable);
        $this->printTable = str_replace("{selectedYear}", $this->selectedYear, $this->printTable);

        $this->printTable = str_replace("{href}", $this->href, $this->printTable);
        $this->printTable = str_replace("{getParams}", !empty($this->getParams) ? "?".$this->getParams : "", $this->printTable);

        //prevYearLink
        //$this->printTable = str_replace("{currentYear}", date("Y"), $this->printTable);
        $this->setPrevNextLinks();
    }

    function setPrevNextLinks() {
        $selectedDate = strtotime($this->selectedYear."-".$this->selectedMonth."-".($this->selectedDay>0?$this->selectedDay:date("d")));

        // установка дата - 1 год
        $prevYearDate = strtotime($this->selectedYear."-".$this->selectedMonth."-".$this->selectedDay." -1 Year");
        // проверка и переустановка дата - 1 год
        $prevYearDate = ($this->firstValidDate >= $selectedDate) ? 0 : (($this->firstValidDate > 0) ? ($this->firstValidDate >= $prevYearDate ? $this->firstValidDate : $prevYearDate) : $prevYearDate);
        $this->printTable = preg_replace("!<if prevYearLink>(.*)</if prevYearLink>!Us", $prevYearDate>0 ? "\\1" : "", $this->printTable);
        $this->printTable = preg_replace("!<if \!prevYearLink>(.*)</if \!prevYearLink>!Us", $prevYearDate>0 ? "" : "\\1", $this->printTable);
        if ($prevYearDate > 0) {
           $this->printTable = str_replace("{prevYearLinkYear}", date("Y", $prevYearDate), $this->printTable);
           $this->printTable = str_replace("{prevYearLinkMonth}", date("m", $prevYearDate), $this->printTable);
           $this->printTable = str_replace("{prevYearLinkDay}", date("d", $prevYearDate), $this->printTable);
        }

        // установка дата + 1 год
        $nextYearDate = strtotime($this->selectedYear."-".$this->selectedMonth."-".$this->selectedDay." +1 Year");
        // проверка и переустановка дата + 1 год
        $nextYearDate = ($this->lastValidDate <= $selectedDate) ? 0 : (($this->lastValidDate > 0) ? ($this->lastValidDate <= $nextYearDate ? $this->lastValidDate : $nextYearDate) : $nextYearDate);
        $this->printTable = preg_replace("!<if nextYearLink>(.*)</if nextYearLink>!Us", $nextYearDate>0 ? "\\1" : "", $this->printTable);
        $this->printTable = preg_replace("!<if \!nextYearLink>(.*)</if \!nextYearLink>!Us", $nextYearDate>0 ? "" : "\\1", $this->printTable);
        if ($nextYearDate > 0) {
           $this->printTable = str_replace("{nextYearLinkYear}", date("Y", $nextYearDate), $this->printTable);
           $this->printTable = str_replace("{nextYearLinkMonth}", date("m", $nextYearDate), $this->printTable);
           $this->printTable = str_replace("{nextYearLinkDay}", date("d", $nextYearDate), $this->printTable);
        }

        // установка дата - 1 месяц
        $prevMonthDate = strtotime($this->selectedYear."-".$this->selectedMonth."-".$this->selectedDay." -1 Month");
        // проверка и переустановка дата - 1 месяц
        $prevMonthDate = ($this->firstValidDate >= $selectedDate) ? 0 : (($this->firstValidDate > 0) ? ($this->firstValidDate >= $prevMonthDate ? $this->firstValidDate : $prevMonthDate) : $prevMonthDate);
        $this->printTable = preg_replace("!<if prevMonthLink>(.*)</if prevMonthLink>!Us", $prevMonthDate>0 ? "\\1" : "", $this->printTable);
        $this->printTable = preg_replace("!<if \!prevMonthLink>(.*)</if \!prevMonthLink>!Us", $prevMonthDate>0 ? "" : "\\1", $this->printTable);
        if ($prevMonthDate > 0) {
           $this->printTable = str_replace("{prevMonthLinkYear}", date("Y", $prevMonthDate), $this->printTable);
           $this->printTable = str_replace("{prevMonthLinkMonth}", date("m", $prevMonthDate), $this->printTable);
           $this->printTable = str_replace("{prevMonthLinkDay}", date("d", $prevMonthDate), $this->printTable);
        }

        // установка дата + 1 месяц
        $nextMonthDate = strtotime($this->selectedYear."-".$this->selectedMonth."-".$this->selectedDay." +1 Month");
        // проверка и переустановка дата + 1 месяц
        $nextMonthDate = ($this->lastValidDate <= $selectedDate) ? 0 : (($this->lastValidDate > 0) ? ($this->lastValidDate <= $nextMonthDate ? $this->lastValidDate : $nextMonthDate) : $nextMonthDate);
        $this->printTable = preg_replace("!<if nextMonthLink>(.*)</if nextMonthLink>!Us", $nextMonthDate>0 ? "\\1" : "", $this->printTable);
        $this->printTable = preg_replace("!<if \!nextMonthLink>(.*)</if \!nextMonthLink>!Us", $nextMonthDate>0 ? "" : "\\1", $this->printTable);
        if ($nextMonthDate > 0) {
           $this->printTable = str_replace("{nextMonthLinkYear}", date("Y", $nextMonthDate), $this->printTable);
           $this->printTable = str_replace("{nextMonthLinkMonth}", date("m", $nextMonthDate), $this->printTable);
           $this->printTable = str_replace("{nextMonthLinkDay}", date("d", $nextMonthDate), $this->printTable);
        }

    }

    function setTemplate($template) {
        $this->template = $template;
    }

    function setDefaultTemplate() {
        $this->defaultTemplate =
        $this->template = '
            <table border="0" cellpadding="2" cellspacing="1" width="100%" bgcolor="#f2f2f6" style="border: 1px solid #dfdfe5; font-size: 0.9em; margin-bottom: 2px;">
                <tr>
                    <td align=center><if prevYearLink><a href="{href}?date={prevYearLinkYear}-{prevYearLinkMonth}-{prevYearLinkDay}">&laquo;</a></if prevYearLink></td>
                    <td width=100% valign="middle" align="center"><b>{selectedYear} г.</b></td>
                    <td align=center><if nextYearLink><a href="{href}?date={nextYearLinkYear}-{nextYearLinkMonth}-{nextYearLinkDay}">&raquo;</a></if nextYearLink></td>
                </tr>
            </table>
            <table border="0" cellpadding="2" cellspacing="1" width="100%" bgcolor="#f2f2f6" style="border: 1px solid #dfdfe5; font-size: 0.9em; margin-bottom: 2px;">
                <tr>
                    <td align=center><if prevMonthLink><a href="{href}?date={prevMonthLinkYear}-{prevMonthLinkMonth}-{prevMonthLinkDay}">&laquo;</a></if prevMonthLink></td>
                    <td width=100% valign="middle" align="center"><b>{selectedMonthName}</b></td>
                    <td align=center><if nextMonthLink><a href="{href}?date={nextMonthLinkYear}-{nextMonthLinkMonth}-{nextMonthLinkDay}">&raquo;</a></if nextMonthLink></td>
                </tr>
            </table>

            <table width=100% border=0 cellspacing=1 cellpadding=2 bgcolor="{bgGrid}">
                <tr bgcolor={bgWeekDay}>
                    <loop dayNames><td align=center><if isWeekendDay><font style="color: {bgWeekendDay};"></if isWeekendDay><b>{dayName}</b><if isWeekendDay></font></if isWeekendDay></td></loop dayNames>
                </tr>
                <loop rows>
                <tr bgcolor={bgWorkWeek}>
                    <loop days>
                    <td<if !empty> align=center</if !empty><if isWeekendDay> bgcolor={bgWeekendWeek}</if isWeekendDay><if isChoosedDay> bgcolor="{bgChoosedDay}"</if isChoosedDay>>
                    <if !empty>
                    <if isLink><a href="{href}?date={Year}-{Month}-{Day}"></if isLink>
                    <font color="<if !isWeekendDay>{clDayLink}</if !isWeekendDay><if isWeekendDay>{clWeekendDayLink}</if isWeekendDay>">{dayValue}</font>
                    <if isLink></a></if isLink>
                    <if !isLink></if !isLink>
                    </if !empty>
                    <if empty>&nbsp;</if empty>
                    </td></loop days>
                </tr></loop rows>
            </table>';

    }

    function getDefaultTemplate() {
        return $this->defaultTemplate;
    }

}
?>