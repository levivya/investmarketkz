<?php
/*
display fund structure
supported $type - pif and npf
*/

//define default type
if (!isset($type)) $type='pif';

if ($type=='pif')
{
 $sturcture_grp=140;
 $tab1='ism_fund_structure';
 $tab2='ism_fund_structure_item';
 $tab3='ism_funds';
 $config='"../amcharts/amcolumn/amcolumn_settings_pif_structure.xml"';
}
else
{
 $sturcture_grp=170;
 $tab1='ism_pension_fund_structure';
 $tab2='ism_pension_fund_structure_item';
 $tab3='ism_pension_funds';
 $config='"../amcharts/amcolumn/amcolumn_settings_npf_structure.xml"';
}


// Connecting, selecting database
//$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

if (!isset($slast_date))
{
	if (!isset($subtype))
     {
	  $query="select date_format(max(structure_date), '%d.%m.%Y' ) slast_date, date_format(DATE_ADD(max(structure_date), INTERVAL -1 YEAR), '%d.%m.%Y' ) sfirst_date from ".$tab1." where fund_id=".$id;
	 }
	else
	{      $query="select date_format(max(structure_date), '%d.%m.%Y' ) slast_date, date_format(DATE_ADD(max(structure_date), INTERVAL -1 YEAR), '%d.%m.%Y' ) sfirst_date from ".$tab1;
	}
	$vdate=array();
	$rc=sql_stmt($query, 2, $vdate ,1);

	$slast_date=$vdate['slast_date'][0];
	$sfirst_date=$vdate['sfirst_date'][0];
}

//format to mysql date
$sdate=substr($sfirst_date,6,4)."-".substr($sfirst_date,3,2)."-".substr($sfirst_date,0,2);
$edate=substr($slast_date,6,4)."-".substr($slast_date,3,2)."-".substr($slast_date,0,2);

if (!isset($subtype))
{//funds
$query="
         select
        		 date_format(s.structure_date, '%m/%Y' ) structure_date
       			,ifnull(i.volume,0)  volume
        from ".$tab1." s, ism_dictionary d left join ".$tab2." i on i.item=d.id and i.structure_id=s.structure_id
        where d.grp=".$sturcture_grp."
              and s.fund_id=".$id."
              and s.structure_date between '".$sdate."' and '".$edate."'
        order by s.structure_date,d.id
       ";
}
else
{//indexes
$query="
        select
                   date_format(s.structure_date, '%m/%y' ) structure_date
                   ,round(sum(ifnull(i.volume,0))/count(s.fund_id),2) volume
        from ".$tab1." s, ism_dictionary d left join ".$tab2." i on i.item=d.id  and i.structure_id=s.structure_id
        where d.grp=".$sturcture_grp."
              and s.structure_date between '".$sdate."' and '".$edate."'
              and s.fund_id in (select fund_id from ".$tab3." where ".$type."kz=1)
        group by s.structure_date,d.id
        order by s.structure_date,d.id
       ";
}
//echo $query;
//die();
$vdata=array();
$rc=sql_stmt($query, 4, $vdata ,2);
//disconnect  from the database
//disconn($conn);

if (!isset($subtype))
{
$path='..';
}
else
{$path='.';
}

$fh = fopen($path.'/amcharts/amcolumn/amcolumn_data.txt', 'w') or die("can't open file");

$d=$vdata['structure_date'][0];
fwrite($fh, $vdata['structure_date'][0]);


for ($i=0;$i<sizeof($vdata['structure_date']);$i++)
{
 if ($d==$vdata['structure_date'][$i])
     fwrite($fh, ";".$vdata['volume'][$i]);
     else
     {     	fwrite($fh, "\n".$vdata['structure_date'][$i].";".$vdata['volume'][$i]);
     	$d=$vdata['structure_date'][$i];
     }
}

fclose($fh);


?>

<!-- amcolumn script-->
<script type="text/javascript" src="<?php echo $path;?>/amcharts/amcolumn/swfobject.js"></script>
	<div id="flashcontent2" class="search-block grey-block" align="top">
		<strong>Обновите ваш Flash Player</strong>
	</div>

	<script type="text/javascript">
		var so = new SWFObject("<?php echo $path;?>/amcharts/amcolumn/amcolumn.swf", "amcolumn", "680", "500", "8", "#FFFFFF");
		so.addVariable("path", "<?php echo $path;?>/amcharts/amcolumn/");
		so.addVariable("settings_file", encodeURIComponent(<?php echo $config;?>));
		so.addVariable("data_file", encodeURIComponent("<?php echo $path;?>/amcharts/amcolumn/amcolumn_data.txt"));
		so.addVariable("preloader_color", "#184789");
		so.write("flashcontent2");
	</script>
<!-- end of amcolumn script -->

<script type="text/javascript">
$(function() {
		var dates = $( "#sfirst_date, #slast_date" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 3,
			onSelect: function( selectedDate ) {
				var option = this.id == "sfirst_date" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	});
</script>

<form>
<div class="search-block grey-block">
      <input id="sfirst_date" name="sfirst_date" value="<?php echo $sfirst_date;?>" />
      <input id="slast_date" name="slast_date" value="<?php echo $slast_date;?>" />
      &nbsp;&nbsp;<span><input type="submit" value="Выбрать"></span>
</div>

      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <input type="hidden" name="tab_id" value="<?php $str=(isset($subtype))?(1):(2); echo $str; ?>">
      <input type="hidden" name="type" value="<?php $str=(isset($subtype))?($type.'kz'):($type); echo $str; ?>">
</form>
