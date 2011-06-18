<?php
/*
display fund statistic
supported $type - pif and npf
*/

//define default type
if (!isset($type)) $type='pif';
switch ($type) {
    case 'pif': $tab='ism_fund_value';
    			break;
    case 'npf': $tab='ism_pension_fund_value';
    			break;
    break;
               }

// write to graph file
$fh = fopen('../amcharts/amstock/data.csv', 'w') or die("can't open file");
$query = "SELECT round(value,2) value,round(asset_value,0) volume, check_date date FROM $tab WHERE fund_id = $id order by check_date desc";
//echo $query;
$res = mysql_query($query);
while($obj = mysql_fetch_object($res)){
  $date = $obj->date;
  $value =  $obj->value;
  $volume =  $obj->volume;
  fwrite($fh, "$date,$volume,$value\n");
}
fclose($fh);
?>
<!-- amstock script-->
  <script type="text/javascript" src="../amcharts/amstock/swfobject.js"></script>
	<div id="flashcontent" class="search-block grey-block">
		<strong>Вам необходимо обновить Flash Player</strong>
	</div>

	<script type="text/javascript">
		var so = new SWFObject("../amcharts/amstock/amstock.swf", "amstock", "680", "500", "8", "#FFFFFF");
		so.addVariable("path", "../amcharts/amstock/");
		so.addVariable("settings_file", encodeURIComponent("../amcharts/amstock/amstock_settings.xml"));
		so.addVariable("preloader_color", "#184789");
		so.write("flashcontent");
	</script>
<!-- end of amstock script -->


