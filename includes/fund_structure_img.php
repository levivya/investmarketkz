<?php
require($_SERVER["DOCUMENT_ROOT"]. "/main.cfg");
$INC_DIR = $_SERVER["DOCUMENT_ROOT"]. "/lib/";
require_once($INC_DIR. "phpchartdir.php");
include($INC_DIR. "mysql.inc");
if ($type=='pif')
{
 $sturcture_grp=140;
 $tab1='ism_fund_structure';
 $tab2='ism_fund_structure_item';
}
else
{
 $sturcture_grp=170;
 $tab1='ism_pension_fund_structure';
 $tab2='ism_pension_fund_structure_item';
}


// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

$query="select id,desc_ru from ism_dictionary where grp=".$sturcture_grp." order by id";
$vstruc=array();
$rc=sql_stmt($query, 2, $vstruc ,2);


$query="
         select
        		 s.structure_id structure_id
       			,date_format(s.structure_date, '%m/%y' ) structure_date
       			,d.desc_ru
       			,ifnull(i.volume,0)  volume
        from ".$tab1." s, ism_dictionary d left join ".$tab2." i on i.item=d.id and i.structure_id=s.structure_id
        where d.grp=".$sturcture_grp."
              and s.fund_id=".$id."
              and s.structure_date between '".$sdate."' and '".$edate."'
        order by s.structure_id,d.id
       ";
//echo $query;
//die();
$vdata=array();
$rc=sql_stmt($query, 4, $vdata ,2);
//disconnect  from the database
disconn($conn);

# The data for the bar chart
$labels = array();
for ($i=0;$i<sizeof($vdata['structure_id'])/sizeof($vstruc['id']);$i++)
{    array_push($labels, $vdata['structure_date'][$i*sizeof($vstruc['id'])]);
}
#echo sizeof($labels);
#die();


for ($i=0;$i<sizeof($vstruc['id']);$i++)
{	${'data'.$i}=array();
	$j = $i;
    while ($j < (sizeof($vdata['structure_id'])))
    {
      array_push(${'data'.$i}, $vdata['volume'][$j]);
      $j=$j+sizeof($vstruc['id']);
    }
}

# Create an XYChart object of size 600 x 300 pixels, with a light blue (EEEEFF)
# background, black border, 1 pxiel 3D border effect and rounded corners
$c = new XYChart(706, 480, 0xFFFFFF , 0xFFFFFF, 0);
$c->setPlotArea(10, 20, 640, 290, 0xffffff, -1, 0xCCCCCC, 0xCCCCCC);

# Add a legend box at (400, 100)
$legend=$c->addLegend(10, 330,true,$IMG_FONT,9);
$legend->setCols(2);
$legend->setBackground(0xffffff,0xffffff);
$legend->setFontColor(0x545050);
$legend->setKeyBorder(Transparent);
$legend->setFontSize(9);
# Set the labels on the x axis.
$c->xAxis->setLabels($labels);
$c->xAxis->setLabelStyle($IMG_FONT, 9, 0x545050);
$c->yAxis2->setLabelStyle($IMG_FONT, 9, 0x545050);


$c->xAxis->setColors(0xCCCCCC, 0x545050);
$c->yAxis->setColors(0xCCCCCC, 0x545050);
$c->yAxis2->setColors(0xCCCCCC, 0x545050);
$c->yAxis2->setWidth(1);
$c->xAxis->setWidth(1);

# Set the labels on the x axis
$c->xAxis->setLabels($labels);

# Add a stacked bar layer and set the layer 3D depth to 8 pixels
$layer = $c->addBarLayer2(Percentage);

# Add the three data sets to the bar layer
for ($i=0;$i<sizeof($vstruc['id']);$i++)
{
  $layer->addDataSet(${'data'.$i}, $COLOR_IMG[$i+1], $vstruc['desc_ru'][$i]);
}
$layer->setBorderColor(Transparent);
$layer->setUseYAxis2();

# Enable bar label for the whole bar
#$layer->setAggregateLabelStyle();

# Enable bar label for each segment of the stacked bar
$layer->setDataLabelStyle();

# Output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
