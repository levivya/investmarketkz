<?php
require($_SERVER["DOCUMENT_ROOT"]. "/main.cfg");
$INC_DIR = $_SERVER["DOCUMENT_ROOT"]. "/lib/";
require_once($INC_DIR. "phpchartdir.php");
include($INC_DIR. "mysql.inc");

// set default type
if (!isset($type)) $type='simple';


// Simple graph ===========================================================================
if ($type=='simple')
{
function graph($width=346,$height=190,$labels,$data)
{
 global $IMG_FONT;
 # Create a XYChart object of size 180 x 180 pixels with a blue background (0x9c9cce)
 $c = new XYChart($width,$height, 0xffffff, 0xffffff);

 # Set the plotarea and background.
 $c->setPlotArea(25, 20, $width-65, $height-55,  -1, -1, Transparent, 0xdedede);

 $c->setBackground(0xffffff,0xffffff);

 #caption
 #$textBoxObj = $c->addText(20, 25, "Invest-Market.kz", $IMG_FONT, 8, 0x666666);
 #$textBoxObj->setAlignment(TopLeft);


 # Set the labels on the x axis.
 $c->xAxis->setLabels($labels);
 $c->xAxis->setLabelStyle($IMG_FONT, 9, 0x545050 );
 $c->xAxis->setColors(0xdedede,0x615F5F,0x0b4291,0x0b4291);
 #$c->xAxis->setLabelOffset(20);
 $c->xAxis->setLabelGap(10);

 $c->yAxis2->setLabelStyle($IMG_FONT, 9, 0x545050);
 $c->yAxis2->setColors(0xffffff,0x615F5F,0xdedede,0xdedede);

 # Label step
 $step=round(sizeof($labels)/5);
 $c->xAxis->setLabelStep($step);


 $layer = $c->addAreaLayer();
 $dataSetObj = $layer->addDataSet($data);
 $dataSetObj->setDataColor(0x808080ff,0x0b4291);

 # Set the default line width to 1 pixels
 //$layer->setLineWidth(0);

 $layer->setUseYAxis2();

 # output the chart
 header("Content-type: image/png");
 return($c->makeChart2(PNG));
}


// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

if (!isset($date_col)) $date_col='check_date';
if (!isset($val_col)) $val_col='value';
if (!isset($interval)) $interval=6;

//get max date
$query="select max(".$date_col.") mdate from ".$tab;
$vmdate=array();
$rc=sql_stmt($query, 1, $vmdate ,1);
$max_date=$vmdate['mdate'][0];

//date format
if (!isset($date_format)) $date_format='day';
switch ($date_format) {
    case 'day': $select_stmt_date_col="date_format(".$date_col.", '%d.%m.%y')  labels";
    break;
    case 'month': $select_stmt_date_col="ELT( MONTH(".$date_col."), 'январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь')  labels";
    break;
           }

// date condition
if (!isset($last_date))
{ $date_condition="and ".$date_col.">DATE_ADD('".$max_date."', INTERVAL -".$interval." MONTH)";
}
else
{ $date_condition="and ".$date_col." between '".$first_date."' and '".$last_date."'";
}

$id_val=(int)$id_val;

$query="
          select
                  ".$val_col."   data
                 ,".$select_stmt_date_col."
          from   ".$tab."
          where  ".$id_col."=".$id_val."
                 ".$date_condition."
          order by ".$date_col;
//echo $query;
//die();
$vdata=array();
$rc=sql_stmt($query, 2, $vdata ,2);

$labels=$vdata['labels'];
$data=$vdata['data'];
//print_r($labels);

//disconnect  from the database
disconn($conn);

//set default size
if (!isset($width)) $width=346;
if (!isset($height)) $height=190;

print graph($width,$height,$labels,$data);

}

// Pie graph ===========================================================================

if ($type=='pie')
{
function graph($width=250,$height=257,$labels,$numbers)
{
 global $numbers,$COLOR_IMG,$IMG_FONT;
 # data
 $data = split(",", $numbers);

 # The labels for the pie chart
 $labels = split(",", $labels);

 # Create a PieChart object of size 360 x 300 pixels
 $c = new PieChart($width, $height);

 # Set the center of the pie at (180, 140) and the radius to 100 pixels
 $c->setPieSize(round($width/2), round($height/2), round($width/2)-46);

 $c->setColors2(DataColor, $COLOR_IMG);

 $c->setLabelStyle($IMG_FONT,8, 0x000066);
 # Set the pie data and the pie labels
 $c->setData($data, $labels);

 $c->setLabelFormat("{percent}%");

  # output the chart
 header("Content-type: image/png");
 return($c->makeChart2(PNG));
}
print graph(250,257,$labels,$data);

}



?>