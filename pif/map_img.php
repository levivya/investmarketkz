<?php
require($_SERVER["DOCUMENT_ROOT"]. "/main.cfg");
$INC_DIR = $_SERVER["DOCUMENT_ROOT"]. "/lib/";
require_once($INC_DIR. "phpchartdir.php");
include($INC_DIR. "mysql.inc");


// get data
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

$where="";
$vfunds=array();

if (isset($funds) && $funds!="" )
{
$funds="'".$funds."'";
$funds = str_replace(",", "','", $funds);
$where="t.fund_id in (".$funds.") ";

$query="
   SELECT
           t.fund_id
          ,t.name
          ,round(DATEDIFF(curdate(),t.start_date)/365,2)   period
          ,round(tt.avg_income,2)       avg_income
          ,round(tt.avg_volat,2)        avg_volat
   FROM
         ism_funds t
        ,ism_fund_year_avg_income tt
   WHERE   ".$where."
          AND t.fund_id=tt.fund_id
          AND tt.check_date=(select max(check_date) from ism_fund_year_avg_income where fund_id=t.fund_id)
       ";

//echo $query;
//die();

$rc=sql_stmt($query, 5, $vfunds ,2);
//disconnect from database
disconn($conn);
}


# Create a XYChart object of size 450 x 420 pixels
$c = new XYChart(693, 244, 0xFFFFFF , 0xFFFFFF, 0);

# Set the plotarea at (55, 65) and of size 350 x 300 pixels, with a light grey border
#$c->addTitle2(Top, "Карта Рынка", $IMG_FONT, 10, 0x06186E, 0xd2dff0);

$c->setPlotArea(33, 10, 645, 204, 0xffffff, -1, 0xCCCCCC, 0xCCCCCC, -1);


$textBoxObj = $c->addText(640, 195, "СГД", $IMG_FONT, 8, 0x000000);
$textBoxObj2 = $c->addText(40, 15, "Волатильность", $IMG_FONT, 8, 0x000000);



$c->xAxis->setColors(0xCCCCCC, 0x545050);
$c->xAxis->setLabelStyle($IMG_FONT, 9, 0x545050);
$c->yAxis->setColors(0xCCCCCC, 0x545050);
$c->yAxis->setLabelStyle($IMG_FONT, 9, 0x545050);
$c->xAxis->setWidth(1);



for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
   {
    $layer = $c->addScatterLayer($vfunds['avg_income'][$i],$vfunds['avg_volat'][$i], "", SquareShape, 12, $COLOR_IMG[$i], $COLOR_IMG[$i]);
   }


$layer = $c->addScatterLayer($avg_year_income,$volat, "", SquareShape, 12, 0xff0000 ,0xff0000 );

$yMark = $c->yAxis->addMark($volat, 0xff0000, "");
$xMark = $c->xAxis->addMark($avg_year_income, 0xff0000, "");


$c->yAxis->setLabelFormat("{value}%");
$c->xAxis->setLabelFormat("{value}%");

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));



?>