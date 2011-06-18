<?php
require($_SERVER["DOCUMENT_ROOT"]. "/main.cfg");
$INC_DIR = $_SERVER["DOCUMENT_ROOT"]. "/lib/";
require_once($INC_DIR. "phpchartdir.php");
include($INC_DIR. "mysql.inc");


// get data
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

//if (isset($funds) && $funds!="" && isset($colors) && $colors!="")
if (isset($funds) && $funds!="" )
{
$fund_id=split(",", $funds);
//get date with data
$query="
         SELECT  max(t.check_date) max_date
         FROM    ism_fund_value t
         WHERE   t.check_date between '".$sdate."' and '".$edate."'
                 and t.fund_id in (".$funds.")
        ";
//echo $query;
//die();

$rc=sql_stmt($query, 1, $vdate ,1);
$max_date=$vdate['max_date'][0];

$query="
         SELECT  max(check_date) max_date
         FROM    ism_index_pifkz t
         WHERE   t.check_date between '".$sdate."' and '".$edate."'
        ";
//echo $query;
//die();

$rc=sql_stmt($query, 1, $vdate ,1);
$max_index_date=$vdate['max_date'][0];


//dinamy sql block
$select_stmt="";
$from_stmt="";


for ($i=0;$i<sizeof($fund_id);$i++)
{
//get  first value
$query="
          select
                        t.fund_id
                       ,(".$invest_amount."/t.value) number_of_funds
          from ism_fund_value t
          where t.fund_id=".$fund_id[$i]."
                and t.check_date=(select min(check_date) from ism_fund_value where fund_id=t.fund_id and check_date>='".$sdate."')
       ";

//echo $query;
$rc=sql_stmt($query, 2, $vdata ,1);
$number_of_funds=$vdata['number_of_funds'][0];

$select_stmt.=" ,ifnull(".$number_of_funds."*t".$fund_id[$i].".value,".$invest_amount.")   value".$fund_id[$i];
$from_stmt.=" left join ism_fund_value t".$fund_id[$i]." on t".$fund_id[$i].".check_date=tt.check_date and t".$fund_id[$i].".fund_id=".$fund_id[$i];
}

//get  first value for index
$query="
          select
                        (".$invest_amount."/t.pifkz_point) number_of_index
          from ism_index_pifkz t
          where t.check_date=(select min(check_date) from ism_index_pifkz where check_date between '".$sdate."' and '".$max_index_date."' and pifkz_point is not null)
       ";

//echo $query;
//die();
$rc=sql_stmt($query, 1, $vdata ,1);
$number_of_index=$vdata['number_of_index'][0];


//main query
$query="
         select
                tt.check_date
                ,DATE_FORMAT( tt.check_date, '%d.%m.%y' )  format_check_date
                ".$select_stmt."
                ,ifnull(".$number_of_index."*i.pifkz_point,".$invest_amount.")   index_value
         from (
     			 select adddate('".$sdate."',id) check_date
      			 from tab
                 where adddate('".$sdate."',id)<='".$max_date."'
              ) tt
         ".$from_stmt."
         left join ism_index_pifkz i on i.check_date=tt.check_date
         order by tt.check_date
       ";

//echo $query;
//die();

$rc=sql_stmt($query, (sizeof($fund_id)+3), $vdata ,2);
$labels=$vdata['format_check_date'];
$index_data=$vdata['index_value'];

for ($i=0;$i<sizeof($fund_id);$i++)
{
 $key='value'.$fund_id[$i];
 ${"data".$fund_id[$i]}=$vdata[$key];
}

}
else
{
//only index
$query="
         SELECT  max(check_date) max_date
         FROM    ism_index_pifkz t
         WHERE   t.check_date between '".$sdate."' and '".$edate."'
        ";
//echo $query;
//die();

$rc=sql_stmt($query, 1, $vdate ,1);
$max_date=$vdate['max_date'][0];

//get  first value for index
$query="
          select
                        (".$invest_amount."/t.pifkz_point) number_of_index
          from ism_index_pifkz t
          where t.check_date=(select min(check_date) from ism_index_pifkz where check_date between '".$sdate."' and '".$max_date."' and pifkz_point is not null)
       ";

//echo $query;
$rc=sql_stmt($query, 1, $vdata ,1);
$number_of_index=$vdata['number_of_index'][0];


$query="
         select
                tt.check_date
                ,DATE_FORMAT( tt.check_date, '%d.%m.%y' )  format_check_date
                ,ifnull(".$number_of_index."*i.pifkz_point,".$invest_amount.")   index_value
         from (
     			 select adddate('".$sdate."',id) check_date
      			 from tab
                 where adddate('".$sdate."',id)<='".$max_date."'
              ) tt
         left join ism_index_pifkz i on i.check_date=tt.check_date
         order by tt.check_date
       ";

//echo $query;
//die();

$rc=sql_stmt($query, 3, $vdata ,2);
$labels=$vdata['format_check_date'];
$index_data=$vdata['index_value'];
}


# Create an XYChart object of size 600 x 300 pixels, with a light blue (EEEEFF)
# background, black border, 1 pxiel 3D border effect and rounded corners
$c = new XYChart(706, 316, 0xFFFFFF , 0xFFFFFF, 0);
$c->setPlotArea(55, 20, 610, 260, 0xffffff, -1, 0xCCCCCC, 0xCCCCCC);


# Set the labels on the x axis.
$c->xAxis->setLabels($labels);
$c->xAxis->setLabelStyle($IMG_FONT, 9, 0x545050);
$c->yAxis->setLabelStyle($IMG_FONT, 9, 0x545050);
$c->yAxis2->setLabelStyle($IMG_FONT, 9, 0x545050);

# Display 1 out of 3 labels on the x-axis.
$step=round(sizeof($labels)/10);
$c->xAxis->setLabelStep($step);

$c->xAxis->setColors(0xCCCCCC, 0x545050);
$c->yAxis->setColors(0xCCCCCC, 0x545050);
$c->yAxis2->setColors(0xCCCCCC, 0x545050);
$c->yAxis2->setWidth(1);
$c->xAxis->setWidth(1);

# Add a line layer to the chart
$layer = $c->addLineLayer();

# Set the default line width to 1 pixels
$layer->setLineWidth(1);

$max_val=0;
$min_val=$invest_amount;

if (isset($funds) && $funds!="" )
{
for ($i=0;$i<sizeof($fund_id);$i++)
{
  $layer->addDataSet(${"data".$fund_id[$i]}, $COLOR_IMG[$i]);
  if (max(${"data".$fund_id[$i]})>$max_val) $max_val=max(${"data".$fund_id[$i]});
  if (min(${"data".$fund_id[$i]})<$min_val) $min_val=min(${"data".$fund_id[$i]});

}
}

//print index line
$layer->addDataSet($index_data, 0xff0000);
if (max($index_data)>=$max_val) $max_val=max($index_data);
if (min($index_data)<=$min_val) $min_val=min($index_data);

#$layer->setUseYAxis2();
$c->yAxis->setLinearScale($min_val,$max_val);

$mark = $c->yAxis->addMark($invest_amount, 0x000000);
# Set the mark line width to 2 pixels
$mark->setLineWidth(1);


$c->syncYAxis(1 / $invest_amount,-1);
$c->yAxis2->setLabelFormat("{={value}*100}%");


//disconnect from database
disconn($conn);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>