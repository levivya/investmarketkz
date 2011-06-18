<?php
 $labels=array(1,2,3);
 $data=array(34,80,55);
 $data2=array(50,60,40);
 $width=346;
 $height=190;


/*
require($_SERVER["DOCUMENT_ROOT"]. "/main.cfg");
$INC_DIR = $_SERVER["DOCUMENT_ROOT"]. "/lib/";
require_once($INC_DIR. "phpchartdir.php");
include($INC_DIR. "mysql.inc");


 global $IMG_FONT;
 # Create a XYChart object of size 180 x 180 pixels with a blue background (0x9c9cce)
 $c = new XYChart($width,$height, 0xffffff, 0xffffff);

 # Set the plotarea and background.
 $c->setPlotArea(25, 20, $width-65, $height-55,  -1, -1, Transparent, 0xdedede);

 $c->setBackground(0xffffff,0xffffff);

 # Set the labels on the x axis.
 $c->xAxis->setLabels($labels);
 $c->xAxis->setLabelStyle($IMG_FONT, 9, 0x545050 );
 $c->xAxis->setColors(0xdedede,0x615F5F,0x0b4291,0x0b4291);
 $c->xAxis->setLabelGap(10);

 # y axis
 $c->yAxis->setMargin(100);
 $c->yAxis->setLabelStyle($IMG_FONT, 9, 0x545050, 0);
 $c->yAxis->setTickLength(-4, -2);
 $c->yAxis->setLinearScale(min($data2)-10,max($data2));

 # y2 axis
 $c->yAxis2->setLabelStyle($IMG_FONT, 9, 0x545050);
 $c->yAxis2->setColors(0xffffff,0x615F5F,0xdedede,0xdedede);


 # Label step
 $step=round(sizeof($labels)/5);
 $c->xAxis->setLabelStep($step);


 $layer = $c->addLineLayer($data,0x0b4291);
 $layer->setUseYAxis2();

 $layer2=$c->addBarLayer($data2,0xf563a7);
 $layer2->setBarWidth(2);
 $layer2->setBorderColor(Transparent);



# Output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
*/

require_once("../lib/FinanceChart.php");

# To compute moving averages starting from the first day, we need to get extra data
# points before the first day
$extraDays = 0;


# Now we read the data from the table into arrays
$timeStamps = $labels;
$highData = $data;
$lowData = $data;
$openData = $data;
$closeData = $data;
$volData = $data2;


# Create a FinanceChart object of width 640 pixels
$c = new FinanceChart($width);
$c->setPlotAreaStyle(0xffffff,0xCCCCCC,0xffffff,0xffffff,0xffffff);
$c->setPlotAreaBorder(0xffffff,2);
$c->setLegendStyle($IMG_FONT, 8, 0xCCCCCC, 0xffffff);


# Set the data into the finance chart object
$c->setData($timeStamps, $highData, $lowData, $openData, $closeData, $volData,  $extraDays);

# Add the main chart with 240 pixels in height
$chart=$c->addMainChart(240);

$c->setXAxisStyle($IMG_FONT, 9, 0x545050,0);
$c->setYAxisStyle($IMG_FONT, 9, 0x545050,0);
#$c->setYAxis2Style($IMG_FONT, 9, 0x545050,0);

# Add a 10 period simple moving average to the main chart, using brown color
$c->addLineIndicator2($chart, $data, 0x0b4291, Income);

# Add a 75 pixels volume bars sub-chart to the bottom of the main chart, using
# green/red/grey for up/down/flat days
$c->addVolBars(75, 0x99ff99, 0xff9999, 0x808080);


# Output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));

?>
