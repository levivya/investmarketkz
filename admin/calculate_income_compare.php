<?php
include("../lib/misc.inc");

// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
set_time_limit ( 600 ) ;
echo '<div class="info-message"><a href="index.php">'.echoNLS('На страницу Администратора','').'</a></div>';
flush();

$fh = fopen('../amcharts/amstock/amstock_settings_multiple.xml', 'w') or die("can't open file");
$head_txt='
<?xml version="1.0" encoding="UTF-8"?>
<!-- Only the settings with values not equal to defaults are in this file. If you want to see the
full list of available settings, check the amstock_settings.xml file in the amstock folder. -->
<settings>
  <margins>5</margins>
  <text_size>10</text_size>

  <header>
    <enabled>false</enabled>
  </header>

  <number_format>
    <letters>
       <letter number="1000">K</letter>
       <letter number="1000000">M</letter>
       <letter number="1000000000">B</letter>
    </letters>
  </number_format>

  <data_sets>
';
fwrite($fh, $head_txt);

//get pifkz
$pifkz_txt='
    <data_set did="pifkz">
       <title>ПИФКЗ</title>
       <short>ПИФКЗ</short>
       <description></description>
       <file_name>data_pifkz.csv</file_name>
       <main_drop_down selected="true"></main_drop_down>
       <csv>
         <reverse>true</reverse>
         <separator>,</separator>
         <date_format>YYYY-MM-DD</date_format>
         <decimal_separator>.</decimal_separator>
         <columns>
           <column>date</column>
           <column>close</column>
         </columns>
       </csv>
    </data_set>
          ';
fwrite($fh, "\n".$pifkz_txt);

$query="
          select
                   pifkz_point value
                  ,date_format(check_date, '%Y-%m-%d' ) check_date_format
          from  ism_index_pifkz
          order by check_date desc";

$vdata=array();
$rc=sql_stmt($query, 2, $vdata ,2);

$fh_pifkz_data = fopen('../amcharts/amstock/data_pifkz.csv', 'w') or die("can't open file");
for ($j=0;$j<sizeof($vdata['value']);$j++)
{
fwrite($fh_pifkz_data, $vdata['check_date_format'][$j].','.$vdata['value'][$j]."\n");
}
fclose($fh_pifkz_data);

//get npfkz
$npfkz_txt='
    <data_set did="pifkz">
       <title>НПФКЗ</title>
       <short>НПФКЗ</short>
       <description></description>
       <file_name>data_npfkz.csv</file_name>
       <main_drop_down selected="false"></main_drop_down>
       <compare_list_box selected="true"></compare_list_box>
       <csv>
         <reverse>true</reverse>
         <separator>,</separator>
         <date_format>YYYY-MM-DD</date_format>
         <decimal_separator>.</decimal_separator>
         <columns>
           <column>date</column>
           <column>close</column>
         </columns>
       </csv>
    </data_set>
          ';
fwrite($fh, "\n".$npfkz_txt);

$query="
          select
                   npfkz_point value
                  ,date_format(check_date, '%Y-%m-%d' ) check_date_format
          from  ism_index_npfkz
          order by check_date desc";

$vdata=array();
$rc=sql_stmt($query, 2, $vdata ,2);

$fh_npfkz_data = fopen('../amcharts/amstock/data_npfkz.csv', 'w') or die("can't open file");
for ($j=0;$j<sizeof($vdata['value']);$j++)
{
fwrite($fh_npfkz_data, $vdata['check_date_format'][$j].','.$vdata['value'][$j]."\n");
}
fclose($fh_npfkz_data);

// get indexes
$query="
          select
                   f.index_id
                  ,f.name name
          from ism_indexes f
       ";
$vidxs=array();
$rc=sql_stmt($query, 2, $vidxs ,2);

for ($i=0;$i<sizeof($vidxs['index_id']);$i++)
{

$body_txt='
    <data_set did="idx_'.$vidxs['index_id'][$i].'">
       <title>'.$vidxs['name'][$i].'</title>
       <short>'.$vidxs['name'][$i].'</short>
       <description></description>
       <file_name>data_idx_'.$vidxs['index_id'][$i].'.csv</file_name>
       <main_drop_down selected="false"></main_drop_down>
       <csv>
         <reverse>true</reverse>
         <separator>,</separator>
         <date_format>YYYY-MM-DD</date_format>
         <decimal_separator>.</decimal_separator>
         <columns>
           <column>date</column>
           <column>close</column>
         </columns>
       </csv>
    </data_set>
          ';
fwrite($fh, "\n".$body_txt);


// prepare dataset
$fh_data = fopen('../amcharts/amstock/data_idx_'.$vidxs['index_id'][$i].'.csv', 'w') or die("can't open file");

$query="
          select
                   value
                  ,date_format(check_date, '%Y-%m-%d' ) check_date_format
          from  ism_index_value
          where index_id=".$vidxs['index_id'][$i]."
          order by check_date desc";

$vdata=array();
$rc=sql_stmt($query, 2, $vdata ,2);

for ($j=0;$j<sizeof($vdata['value']);$j++)
{
fwrite($fh_data, $vdata['check_date_format'][$j].','.$vdata['value'][$j]."\n");
}

fclose($fh_data);
}




// get pension funds
$query="
          select
                   f.fund_id
                  ,f.name name
          from ism_pension_funds f
          where f.status!=".$PFUND_CLOSED;

$vfunds=array();
$rc=sql_stmt($query, 2, $vfunds ,2);

for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
{

$body_txt='
    <data_set did="npf_'.$vfunds['fund_id'][$i].'">
       <title>НПФ: '.$vfunds['name'][$i].'</title>
       <short>'.substr($vfunds['name'][$i],0,4).'</short>
       <description></description>
       <file_name>data_npf_'.$vfunds['fund_id'][$i].'.csv</file_name>
       <main_drop_down selected="false"></main_drop_down>
       <csv>
         <reverse>true</reverse>
         <separator>,</separator>
         <date_format>YYYY-MM-DD</date_format>
         <decimal_separator>.</decimal_separator>
         <columns>
           <column>date</column>
           <column>close</column>
         </columns>
       </csv>
    </data_set>
          ';
fwrite($fh, "\n".$body_txt);


// prepare dataset
$fh_data = fopen('../amcharts/amstock/data_npf_'.$vfunds['fund_id'][$i].'.csv', 'w') or die("can't open file");

$query="
          select
                   value
                  ,date_format(check_date, '%Y-%m-%d' ) check_date_format
          from  ism_pension_fund_value
          where fund_id=".$vfunds['fund_id'][$i]."
          order by check_date desc";

$vdata=array();
$rc=sql_stmt($query, 2, $vdata ,2);

for ($j=0;$j<sizeof($vdata['value']);$j++)
{
fwrite($fh_data, $vdata['check_date_format'][$j].','.$vdata['value'][$j]."\n");
}

fclose($fh_data);
}

//get funds
$query="
          select
                   f.fund_id
                  ,f.name name
          from ism_funds f
               ,ism_fund_value tt
          where f.fund_id=tt.fund_id
                and f.fund_type!=".$RISK_INVEST_OBJ."
                and tt.check_date=(select max(check_date) from ism_fund_value where fund_id=f.fund_id)
                and tt.check_date>=DATE_ADD(NOW(), INTERVAL -1 MONTH)
       ";
$vfunds=array();
$rc=sql_stmt($query, 2, $vfunds ,2);

for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
{

$body_txt='
    <data_set did="pif_'.$vfunds['fund_id'][$i].'">
       <title>ПИФ: '.$vfunds['name'][$i].'</title>
       <short>'.substr($vfunds['name'][$i],0,4).'</short>
       <description></description>
       <file_name>data_pif_'.$vfunds['fund_id'][$i].'.csv</file_name>
       <main_drop_down selected="false"></main_drop_down>
       <csv>
         <reverse>true</reverse>
         <separator>,</separator>
         <date_format>YYYY-MM-DD</date_format>
         <decimal_separator>.</decimal_separator>
         <columns>
           <column>date</column>
           <column>close</column>
         </columns>
       </csv>
    </data_set>
          ';
fwrite($fh, "\n".$body_txt);


// prepare dataset
$fh_data = fopen('../amcharts/amstock/data_pif_'.$vfunds['fund_id'][$i].'.csv', 'w') or die("can't open file");

$query="
          select
                   value
                  ,date_format(check_date, '%Y-%m-%d' ) check_date_format
          from  ism_fund_value
          where fund_id=".$vfunds['fund_id'][$i]."
          order by check_date desc";

$vdata=array();
$rc=sql_stmt($query, 2, $vdata ,2);

for ($j=0;$j<sizeof($vdata['value']);$j++)
{
fwrite($fh_data, $vdata['check_date_format'][$j].','.$vdata['value'][$j]."\n");
}

fclose($fh_data);

}



$foot_txt='
  </data_sets>

  <charts>
  	<chart cid="0">
  	  <bg_color></bg_color>
      <border_color>#CCCCCC</border_color>
      <border_alpha>100</border_alpha>

      <grid>
        <x>
          <dashed></dashed>
        </x>


        <y_right>
          <color>cccccc</color>
          <alpha>100</alpha>
          <dashed></dashed>
        </y_right>
      </grid>

      <legend>
        <graph_on_off>false</graph_on_off>
        <fade_others_to>10</fade_others_to>
        <show_date>true</show_date>
      </legend>

  		<graphs>
  			<graph gid="0">
  			  <axis>right</axis>
  				<type>line</type>
  				<data_sources>
  				  <close>close</close>
          </data_sources>

          <compare_source>close</compare_source>

  		    <legend>
            <date key="false" title="false"><![CDATA[<b>{close}</b>]]></date>
            <period key="true" title="true"><![CDATA[Отк.:<b>{open}</b> Мин.:<b>{low}</b> Мах.:<b>{high}</b> Пос.:<b>{close}</b>]]></period>
            <date_comparing key="true" title="true"><![CDATA[{close.percents}]]></date_comparing>
            <period_comparing key="true" title="true"><![CDATA[{close.percents}]]></period_comparing>
          </legend>
  			</graph>
  		</graphs>
  	</chart>
  </charts>

  <date_formats>
    <legend>
      <weeks>month DD, YYYY</weeks>
    </legend>
  </date_formats>


  <data_set_selector>
    <width>200</width>
    <max_comparing_count>6</max_comparing_count>
 	<main_drop_down_title>Выбрать из списка:</main_drop_down_title>
 	<compare_list_box_title>Сравнить с (НПФ, ПИФы и индексы):</compare_list_box_title>
 	<balloon_text>{title}</balloon_text>
 	<drop_down>
      <scroller_color>F561A5</scroller_color>
    </drop_down>
  </data_set_selector>

  <period_selector>
    <date_format>DD-MM-YYYY</date_format>

    <button>
      <bg_color_hover></bg_color_hover>
      <bg_color_selected></bg_color_selected>
      <text_color_hover>ffffff</text_color_hover>
      <text_color_selected>ffffff</text_color_selected>
    </button>

	<periods>
    <period type="DD" count="10">10Д</period>
    	<period type="MM" count="1">1M</period>
    	<period type="MM" count="6">6M</period>
    	<period selected="true" type="YYYY" count="1">12M</period>
    	<period type="YYYY" count="3">36M</period>
    	<period type="YTD" count="0">YTD</period>
    <period type="MAX">MAX</period>
	</periods>

		<custom_period_title>Период:</custom_period_title>
  </period_selector>

  <header>
    <enabled></enabled>
    <text><![CDATA[<b>{title}</b> ({short}) {description}]]></text>
    <text_size>12</text_size>
  </header>

  <background>
    <alpha>100</alpha>
	<color>FFFFFF</color>
  </background>

  <plot_area>
    <border_color>cccccc</border_color>
  </plot_area>

  <export_as_image>
   <file>amcharts/amstock/export.php</file>
  </export_as_image>


  <scroller>
    <enabled>true</enabled>
    <height>50</height>
    <graph_data_source>close</graph_data_source>
    <bg_color>f5f5f5,ffffff</bg_color>
    <resize_button_style>dragger</resize_button_style>
  </scroller>

  <strings>
    <!-- [Processing data] (text) -->
    <processing_data>Обработка данных</processing_data>
    <!-- [Loading data] (text) -->
    <loading_data>Загрузка данных</loading_data>
    <!-- [Check date format] (text) -->
    <wrong_date_format></wrong_date_format>
    <!-- [Export as image] (text) -->
    <export_as_image></export_as_image>
    <!-- [Collecting data] (text) -->
    <collecting_data></collecting_data>
    <!-- [No data] (text) -->
    <no_data></no_data>

    <!-- In case your axis values display duration instead of numbers, these units
    will be used to format duration -->
    <duration_units>
      <!-- [] unit of seconds -->
      <ss></ss>
      <!-- [:] unit of minutes -->
      <mm></mm>
      <!-- [:] unit of hours -->
      <hh></hh>
      <!-- [d. ] unit of days -->
      <DD></DD>
    </duration_units>

 	  <months>
    	<jan>Янв</jan>
    	<feb>Фев</feb>
    	<mar>Мар</mar>
    	<apr>Апр</apr>
    	<may>Май</may>
    	<jun>Июн</jun>
    	<jul>Июл</jul>
    	<aug>Авг</aug>
    	<sep>Сен</sep>
    	<oct>Окт</oct>
    	<nov>Ноя</nov>
    	<dec>Дек</dec>
  	</months>

  	<weekdays>
  	   <sun>Вс</sun>
  	   <mon>Пн</mon>
  	   <tue>Вт</tue>
  	   <wed>Ср</wed>
  	   <thu>Чт</thu>
  	   <fri>Пт</fri>
  	   <sat>Сб</sat>
  	</weekdays>
  </strings>

</settings>

          ';

fwrite($fh, "\n".$foot_txt);
fclose($fh);

//disconnect  from the database
disconn($conn);
echo '<div class="info-message">DONE.</div>';

?>