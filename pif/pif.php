<?php
        include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
        // Connecting, selecting database
        $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);


//upload statistic
if (isset($upload))
{  $fund_data_lines=explode("\n",$fund_data);
  for ($i=0;$i<sizeof($fund_data_lines);$i++)
   {
          $fund_data_item=explode(';',$fund_data_lines[$i]);

          //process data
          $date=substr($fund_data_item[0],6,4)."-".substr($fund_data_item[0],3,2)."-".substr($fund_data_item[0],0,2);
          $value=str_replace(",", ".",$fund_data_item[1]);
          $value=str_replace(" ", "",$value);
          $asset=str_replace(",", ".",$fund_data_item[2]);
          $asset=str_replace(" ", "",$asset);

          //update or insert data
          $query="select fund_id from ism_fund_value where fund_id=".$id." and check_date='".$date."'";
           //echo $query."<br>";
          $rc=sql_stmt($query, 1, $v=array() ,2);
          if ($rc>0)
           {
            //update
            $query="update ism_fund_value
                    set value=".$value."
                        ,asset_value=".$asset."
                    where fund_id=".$id." and check_date='".$date."'";
           }
           else
           {
            //insert
             $query="insert into ism_fund_value(fund_id,check_date,value,asset_value)
                     values(".$id.",'".$date."',".$value.",".$asset.")";

           }
          //echo $query."<br>";
          $result=exec_query($query);


   }

  echo '<div class="info-message">Данные загруженны!</div>';
}

//edit block
if (isset($edit))
{

 $fregistration_date=($registration_date=="")?("NULL"):("'".substr($registration_date,6,4)."-".substr($registration_date,3,2)."-".substr($registration_date,0,2)."'");
 $fbuild_end_date=($build_end_date=="")?("NULL"):("'".substr($build_end_date,6,4)."-".substr($build_end_date,3,2)."-".substr($build_end_date,0,2)."'");

 $registration_number=($registration_number=="")?("NULL"):(ltrim($registration_number));
 $registration_number=rtrim($registration_number);
 $limit_min_sum=($limit_min_sum=="")?("NULL"):(str_replace(",", ".",$limit_min_sum ));
 $limit_min_sum=str_replace(" ", "",$limit_min_sum);
 $next_min_sum=($next_min_sum=="")?("NULL"):(str_replace(",", ".",$next_min_sum ));
 $next_min_sum=str_replace(" ", "",$next_min_sum);
 $nominal_cost=($nominal_cost=="")?("NULL"):(str_replace(",", ".",$nominal_cost ));
 $nominal_cost=str_replace(" ", "",$nominal_cost);


if ($grp==2)
{
 $fstart_date=($start_date=="")?("NULL"):("'".substr($start_date,6,4)."-".substr($start_date,3,2)."-".substr($start_date,0,2)."'");
 $name=ltrim($name);
 $name=rtrim($name);

 $query="
        update  ism_funds
             set
                 registration_number='".$registration_number."'
                 ,company_id=".$company_id."
                 ,name='".$name."'
                 ,start_date=".$fstart_date."
                 ,fund_type=".$type_id."
                 ,status=".$stat_id."
                 ,invest_object=".$obj_id."
                 ,limit_min_sum=".$limit_min_sum."
                 ,next_min_sum=".$next_min_sum."
                 ,extra_charge='".$extra_charge."'
                 ,discount='".$discount."'
                 ,web_site='".$web_site."'
                 ,general_info='".$general_info."'
                 ,when_buy_sell='".$when_buy_sell."'
                 ,fund_life_time='".$fund_life_time."'
                 ,nominal_cost=".$nominal_cost."
                 ,mc_bonus='".$mc_bonus."'
                 ,cra_bonus='".$cra_bonus."'
                 ,fund_expences='".$fund_expences."'
                 ,registration_date=".$fregistration_date."
                 ,build_end_date=".$fbuild_end_date."
                 ,registrator='".$registrator."'
                 ,castodian='".$castodian."'
                 ,auditor='".$auditor."'
        where fund_id=".$id;

}
else
{
$query="
        update  ism_funds
             set  limit_min_sum=".$limit_min_sum."
                 ,next_min_sum=".$next_min_sum."
                 ,extra_charge='".$extra_charge."'
                 ,discount='".$discount."'
                 ,web_site='".$web_site."'
                 ,general_info='".$general_info."'
                 ,when_buy_sell='".$when_buy_sell."'
                 ,fund_life_time='".$fund_life_time."'
                 ,nominal_cost=".$nominal_cost."
                 ,mc_bonus='".$mc_bonus."'
                 ,cra_bonus='".$cra_bonus."'
                 ,fund_expences='".$fund_expences."'
                 ,registration_date=".$fregistration_date."
                 ,build_end_date=".$fbuild_end_date."
                 ,registrator='".$registrator."'
                 ,castodian='".$castodian."'
                 ,auditor='".$auditor."'
        where fund_id=".$id;

}

//echo $query;
$result=exec_query($query);
if ($result)
  {

   //update statistics
   $stat_query="
         insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
         values('ism_funds',".$id.",1,current_date(),'".$user."','".$name."')";
   //echo $query;
   $result=exec_query($stat_query);

   echo '<div class="info-message">'.echoNLS('Данные изменены!','').'</div>';
  }

}


        // get pif data
        $query="
            select
                  t.fund_id
                 ,t.registration_number
                 ,t.name
                 ,c.name   company_name
                 ,c.company_id
                 ,t.status status_id
                 ,DATE_FORMAT(t.registration_date, '%d.%m.%Y') registration_date
                 ,DATE_FORMAT(t.build_end_date, '%d.%m.%Y') build_end_date
                 ,t.fund_type fund_type_id
                 ,t.invest_object invest_object_id
                 ,d1.desc_".echoNLS('ru','')." status
                 ,d2.desc_".echoNLS('ru','')." fund_type
                 ,d3.desc_".echoNLS('ru','')." invest_object
                 ,DATE_FORMAT(t.start_date, '%d.%m.%Y') start_date
                 ,if(t.limit_min_sum=0,'',t.limit_min_sum) limit_min_sum
                 ,if(t.next_min_sum=0,'',t.next_min_sum) next_min_sum
                 ,t.extra_charge
                 ,t.discount
                 ,t.exchange
                 ,t.web_site
                 ,t.general_info
                 ,t.when_buy_sell
                 ,t.fund_life_time
                 ,if(t.nominal_cost=0,'',t.nominal_cost) nominal_cost
                 ,t.mc_bonus
                 ,t.cra_bonus
                 ,t.fund_expences
                 ,t.registrator
                 ,t.auditor
                 ,t.castodian
                 ,t.in_suppermarket
             from ism_funds t
                  LEFT JOIN ism_dictionary d1  ON t.status=d1.id
                  LEFT JOIN ism_dictionary d2  ON t.fund_type=d2.id
                  LEFT JOIN ism_dictionary d3  ON t.invest_object=d3.id
                  LEFT JOIN ism_companies  c  ON t.company_id=c.company_id
             where  t.fund_id=".clean_int($id);
		$vfund=array();
		$rc=sql_stmt($query, 31, $vfund ,1);


        //check for edit rights
		$edit_form=false;
		if (isset($grp) && $grp==1 && isset($comp_id) && $comp_id==$vfunds['company_id'][0]) //company
		{ $edit_form=true;}

		if (isset($grp) && $grp==2) //if admin
		{ $edit_form=true;}

		// set default tab_id
		$tab_id=(isset($tab_id))?($tab_id):(0);

        //no data exists
        if ($rc==0)
        {          header('Location: /404.php');
          exit;
        }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Паевой фонд (ПИФ):<?php echo $vfund['name'][0];?></title>
<meta name="Description" content="Паевой фонд(ПИФ)" >
<meta name="Keywords" content="пиф, паевой фонд, выбрать, управляющая компания, ук, доходность, калькулятор, анализ">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
</head>


<body>

<div id="container">
<!-- header -->
<?php
        $selected_menu='pif';
        include '../includes/header.php';
?>

<script>
  $(document).ready(function(){

    var $tabs = $('#tabs').tabs(); // first tab selected
  	$tabs.tabs('select', <?php echo $tab_id?>);

    $("#slidingDiv").animate({"height": "hide"}, { duration: 100 });

    });

    function ChangeTab(id) {
        var $tabs = $('#tabs').tabs(); // first tab selected
    	$tabs.tabs('select', id);
    	}
</script>


<!-- main body -->
  <div class="sidebar2">
    <!--
	<div class="title">Дополнительные возможности</div>
	<ul class="add-menu">
		<li><a href="analysis.php">Анализ тренда</a></li>
	    <li class="m2"><a href="calculator.php">Калькулятор</a></li>
	</ul>
	-->
    <?php

    //+++ Income +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    $query="
     SELECT
			 fund_today.value today
			,DATE_FORMAT(fund_today.check_date, '%d.%m.%Y') check_date
			,round((fund_today.value-fund_day.value),2) day
            ,round(((fund_today.value-fund_day.value)/fund_day.value)*100,2) day_persent
			,round((fund_today.value-fund_12month.value),2) 12month
			,round(((fund_today.value-fund_12month.value)/fund_12month.value)*100,1) 12month_persent
	 FROM ism_fund_value fund_today
          left join ism_fund_value fund_day on fund_today.fund_id=fund_day.fund_id and  fund_day.check_date=DATE_ADD(fund_today.check_date,INTERVAL -1 DAY)
          left join ism_fund_value fund_12month on fund_today.fund_id=fund_12month.fund_id  and fund_12month.check_date=DATE_ADD(fund_today.check_date,INTERVAL -12 MONTH)
	 WHERE fund_today.check_date=(select max(check_date) from ism_fund_value where fund_id=".clean_int($id).")
           and fund_today.fund_id=".clean_int($id)."
        ";

    //echo $query;

    $vincome=array();
    $rc=sql_stmt($query, 6, $vincome ,1);


    //+++ Structure ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

     // get the last fund structure
     $query="select max(structure_id) structure_id, date_format(max(structure_date),'%d.%m.%Y') max_date from ism_fund_structure where fund_id=".$id." group by fund_id";
     //echo $query;
     $vfund_stru_date=array();
	 $rc=sql_stmt($query, 2, $vfund_stru_date ,1);

	 if ($rc>0)
	 {
         //get risk assets
         $query='select sum(t.volume) risk_asset from ism_fund_structure_item t where t.structure_id='.$vfund_stru_date['structure_id'][0].' and t.item in (147,150)';
         $vrisk_asset=array();
    	 $rc=sql_stmt($query, 1, $vrisk_asset ,1);

         $risk=0;
         if ($vrisk_asset['risk_asset'][0]<=20) $risk=1;
         if ($vrisk_asset['risk_asset'][0]>20 && $vrisk_asset['risk_asset'][0]<=50) $risk=2;
         if ($vrisk_asset['risk_asset'][0]>50) $risk=3;


         //get fund volatility
         $query='select t.avg_volat from ism_fund_year_avg_income t where t.fund_id='.$id.' and t.check_date=(select max(check_date) from ism_fund_year_avg_income where fund_id='.$id.')';
         $vvolat=array();
    	 $rc=sql_stmt($query, 1, $vvolat ,1);

         //get avg year income and volat for index pifkz
         $data=get_sgd_volat();
         $avg_year_income=$data['avg_year_income'];
         $volat=$data['volat'];

         if ($vvolat['avg_volat'][0]>$volat && $vvolat['avg_volat'][0]< 2*$volat) $risk=$risk+1;
         if ($vvolat['avg_volat'][0]> 2*$volat) $risk=$risk+2;

         if ($risk<2) $risk_cap='Низкий';
         if ($risk>=2 && $risk<4) $risk_cap='Средний';
         if ($risk>=4) $risk_cap='Высокий';

          //get list of assets
         $query='select (select desc_ru from ism_dictionary where id=t.item) item, t.volume from ism_fund_structure_item t where t.structure_id='.$vfund_stru_date['structure_id'][0].' order by t.item';
         //echo $query;
         $vfund_stru_data=array();
	     $rc=sql_stmt($query, 2, $vfund_stru_data ,2);


         $fh = fopen('../amcharts/ampie/ampie_data.xml', 'w') or die("can't open file");
		 fwrite($fh, '<?xml version="1.0" encoding="UTF-8"?><pie>');

		 for ($i=0;$i<sizeof($vfund_stru_data['item']);$i++)
		 {
    		 fwrite($fh, '<slice title="'.$vfund_stru_data['item'][$i].'" pull_out="false">'.$vfund_stru_data['volume'][$i].'</slice>');
		 }

  		 fwrite($fh, '</pie>');
		 fclose($fh);


	 }

    ?>

    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="../banner.php" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
     Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
    <!-- end sidebar2 -->
  </div>

<div class="mainContent">
<div id="tabs">
      <ul>
        <li class="topic">Паевой фонд (ПИФ)</li>
        <li class="first"><a href="#fragment-1" title="профиль паевого фонда">Профиль</a></li>
        <li><a href="#fragment-2" title="статистика цены пая">Статистика</a></li>
        <li><a href="#fragment-3" title="структура портфеля пифа">Структура портфеля</a></li>
        <!--
        <li><a href="#fragment-4">Документы</a></li>
        -->
      </ul>
  <div id="fragment-1">
<?php
if (!$edit_form)
 {
  echo '
         <div id="fund-container">
         <div class="left-block">
               <div class="big">'.$vfund['name'][0].'</div>
               <div class="small"><a href="company.php?id='.$vfund['company_id'][0].'" title="'.$vfund['company_name'][0].'">'.$vfund['company_name'][0].'</a></div>
               <div class="small">Тип: '.$vfund['fund_type'][0].'</div>
               <div class="small">Работает с: '.$vfund['start_date'][0].'</div>
         </div>
         <div class="right-block">
               <div class="big black">'.$vincome['today'][0].'&nbsp;<span class="mid '.($str=($vincome['day'][0]>0)?("green"):("")).' '.($str=($vincome['day'][0]<0)?("red"):("")).'">'.$vincome['day'][0].'&nbsp;('.$vincome['day_persent'][0].'%)&nbsp;<a href="calculator.php" title="калькулятор пифов"><img src="../media/images/calculator.png" height="25px" alt="калькулятор пифов" border="0"></a><!--&nbsp;<a href="analysis.php" title="анализ тренда доходности пифа"><img src="../media/images/invest_trend.png" height="25px" alt="анализ тренда доходности пифа" border="0"></a>--></span></div>
               <div class="small">Дата:&nbsp;'.$vincome['check_date'][0].'</div>
               <div class="small">За год:&nbsp;<span class="'.($str=($vincome['12month'][0]>0)?("green"):("")).' '.($str=($vincome['12month'][0]<0)?("red"):("")).'">'.$vincome['12month'][0].'&nbsp;('.$vincome['12month_persent'][0].'%)</span></div>
               <div class="small">Риск:&nbsp;<span class="risk'.$risk.'"> </span>('.$risk_cap.')</div>
         </div>
         </div>
    ';

     $grph_val=($vincome['today'][0]<=10000)?('value'):('value/1000');
     $grph_cap=($vincome['today'][0]<=10000)?('Цена пая'):('Цена пая, тыс. тенге');

      echo '
      <div class="two-blocks">
		<div class="left-block">
		<div class="title"><a class="more" href="#" onclick="ChangeTab(1);" title="статистика цены пая">Вся статистика</a>'.$grph_cap.'</div>
		<img src="../lib/graph.php?tab=ism_fund_value&id_col=fund_id&interval=9&id_val='.$id.'&val_col=round(('.$grph_val.'),2)&height=230"  alt="стоимость пая" />
		</div>
		<div class="right-block">
		 <div class="title"><a class="more" href="#" onclick="ChangeTab(2);" title="структура портфеля фонда">Вся статистика</a>Портфель ПИФа&nbsp;<font size=1>('.$vfund_stru_date['max_date'][0].')</font></div>
         <!-- ampie script-->
				<script type="text/javascript" src="../amcharts/ampie/swfobject.js"></script>
					<div id="flashcontent_pie"  align="center">
						<strong>You need to upgrade your Flash Player</strong>
					</div>

					<script type="text/javascript">
						var so = new SWFObject("../amcharts/ampie/ampie.swf", "ampie", "210", "210", "8", "#FFFFFF");
						so.addVariable("path", "../amcharts/ampie/");
						so.addVariable("settings_file", encodeURIComponent("../amcharts/ampie/ampie_settings.xml"));
						so.addVariable("data_file", encodeURIComponent("../amcharts/ampie/ampie_data.xml"));
						so.addVariable("preloader_color", "#FFFFFF");
						so.addParam("wmode", "transparent");
                        so.write("flashcontent_pie");
				 </script>
		 <!-- end of ampie script -->
         <div class="info"><font size=1>Для получения подробной информации наведите курсор на изображение.</font></div>
      </div>
	  </div>

      <script type="text/javascript">
         //<![CDATA[
         function ShowHide(){
         $("#slidingDiv").animate({"height": "toggle"}, { duration: 100 });
          }
         //]]>
      </script>

      <div class="title"><a onclick="ShowHide(); return false;" href="#" title="дополнительная информация по паевому фонду">Дополнительная информация (Показать\Скрыть)</a></div>
         <div id="slidingDiv">

         <table class="tab-table">
          <tr class="colored">
		    <td>'.echoNLS('Минимальная сумма (первый взнос)','').'</td>
		    <td>'.$str=(($vfund['limit_min_sum'][0]!='')?(number_format($vfund['limit_min_sum'][0], 2, ',', ' ')):('')).'</td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Минимальная сумма (послед. взносы)','').'</td>
            <td>'.$str=(($vfund['next_min_sum'][0]!='')?(number_format($vfund['next_min_sum'][0], 2, ',', ' ')):('')).'</td>
	      </tr>
           <tr class="colored">
		    <td>'.echoNLS('Купить и погасить пай можно','').'</td>
		    <td>'.$vfund['when_buy_sell'][0].'</td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Надбавки (при покупке пая)','').'</td>
            <td>'.$vfund['extra_charge'][0].'</td>
	      </tr>
           <tr class="colored">
		    <td>'.echoNLS('Скидки (при погашении пая)','').'</td>
		    <td>'.$vfund['discount'][0].'</td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Веб страница','').'</td>
            <td>'. ($str=($vfund['web_site'][0]!='')?('<a href="'.$vfund['web_site'][0].'"> '.echoNLS($vfund['web_site'][0],'').'</a>'):('')).'</td>
	      </tr>
   		  <tr class="colored">
          	<td>'.echoNLS('Общая информация','').'</td>
            <td>'.$vfund['general_info'][0].'</td>
	      </tr>
		  <tr>
		    <td>'.echoNLS('Национальный идентификационный номер','').'</td>
		    <td>'.$vfund['registration_number'][0].'</td>
		  </tr>
		  <tr class="colored">
          	<td>'.echoNLS('Номинальная стоимость пая','').'</td>
            <td>'.$str=(($vfund['nominal_cost'][0]!='')?(number_format($vfund['nominal_cost'][0], 2, ',', ' ')):('')).'</td>
	      </tr>
		  <tr>
		    <td>'.echoNLS('Дата регистрации','').'</td>
		    <td>'.$vfund['registration_date'][0].'</td>
		  </tr>
		  <tr class="colored">
          	<td>'.echoNLS('Дата окончания формирования','').'</td>
            <td>'.$vfund['build_end_date'][0].'</td>
	      </tr>
	      <tr>
		    <td>'.echoNLS('Срок функционирования фонда','').'</td>
		    <td>'.$vfund['fund_life_time'][0].'</td>
		  </tr>
		  <tr class="colored">
		    <td>'.echoNLS('Вознаграждения УК','').'</td>
		    <td>'.$vfund['mc_bonus'][0].'</td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Вознаграждения кастодиану, регистратору и аудитору','').'</td>
            <td>'.$vfund['cra_bonus'][0].'</td>
	      </tr>
		  <tr class="colored">
		    <td>'.echoNLS('Расходы фонда','').'</td>
		    <td>'.$vfund['fund_expences'][0].'</td>
		  </tr>
		  <tr>
		    <td>'.echoNLS('Кастодиан','').'</td>
		    <td>'.$vfund['castodian'][0].'</td>
		  </tr>
		  <tr class="colored">
          	<td>'.echoNLS('Регистратор','').'</td>
            <td>'.$vfund['registrator'][0].'</td>
	      </tr>
		  <tr>
		    <td>'.echoNLS('Аудитор','').'</td>
		    <td>'.$vfund['auditor'][0].'</td>
		  </tr>
     </table>
     </div>

     <script type="text/javascript"><!--
		google_ad_client = "pub-2712511792023009";
		/* 728x90, создано 24.09.10 */
		google_ad_slot = "7735537292";
		google_ad_width = 728;
		google_ad_height = 90;
		//-->
	  </script>

      <script type="text/javascript"
      src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
      </script>


       ';
 }
 else
 {
//company list
$query="
          select
                   company_id
                  ,name
          from ism_companies
          order by name

       ";
$vcomps=array();
$rc=sql_stmt($query, 2, $vcomps ,2);

if (!isset($company_id))  {$company_id=$vfund['company_id'][0];}
$CompsMenuString = menu_list($vcomps['name'],$company_id,$vcomps['company_id']);
$CompsMenuString = '<select name="company_id">'.$CompsMenuString.'</select>';

//type list
$query="
         select
                   id
                  ,desc_".echoNLS('ru','')." name
          from ism_dictionary
          where grp=".$GRP_TYPE."
          order by name

       ";
$vtype=array();
$rc=sql_stmt($query, 2, $vtype ,2);

if (!isset($type_id))  $type_id=$vfund['fund_type_id'][0];
$TypeMenuString = menu_list($vtype['name'],$type_id,$vtype['id']);
$TypeMenuString = '<select name="type_id">'.$TypeMenuString.'</select>';

//status list
$query="
          select
                   id
                  ,desc_".echoNLS('ru','')." name
          from ism_dictionary
          where grp=".$GRP_STAT."
          order by name

       ";
$vstat=array();
$rc=sql_stmt($query, 2, $vstat ,2);

if (!isset($stat_id))  $stat_id=$vfund['status_id'][0];
$StatMenuString = menu_list($vstat['name'],$stat_id,$vstat['id']);
$StatMenuString = '<select name="stat_id">'.$StatMenuString.'</select>';

//invest object list
$query="
          select
                   id
                  ,desc_".echoNLS('ru','')." name
          from ism_dictionary
          where grp=".$GRP_OBJ."

       ";
$vobj=array();
$rc=sql_stmt($query, 2, $vobj ,2);

if (!isset($obj_id))  $obj_id=$vfund['invest_object_id'][0];
$ObjMenuString = menu_list($vobj['name'],$obj_id,$vobj['id']);
$ObjMenuString = '<select name="obj_id">'.$ObjMenuString.'</select>';


  echo '

     <script type="text/javascript">
	$(function(){
	$.datepicker.setDefaults(
	$.extend($.datepicker.regional["ru"])
	);
	$("#start_date").datepicker();

	$.datepicker.setDefaults(
	$.extend($.datepicker.regional["ru"])
	);
	$("#registration_date").datepicker();

	$.datepicker.setDefaults(
	$.extend($.datepicker.regional["ru"])
	);
	$("#build_end_date").datepicker();


	});
	</script>

	<script language="JavaScript">
	function IsNumeric(obj)
	{
	   var ValidChars = "0123456789.";
	   var IsNumber=true;
	   var Char;
	   var sText=obj.value;

	   for (i = 0; i < sText.length && IsNumber == true; i++)
	      {
	       Char = sText.charAt(i);
	       if (ValidChars.indexOf(Char) == -1)
	          {
	          alert("'.echoNLS('Неверное значение, оно должно быть числовым!','Wrong value, It should be numeric!').'");
	          IsNumber = false;
	          obj.focus();
	          }
	     }
	   return IsNumber;
	}
     </script>


    <form name="edit_form" method=post>
    <input type=hidden name=id value="'.$id.'">

         <table class="tab-table">
		  <tr class="colored">
		    <td>'.echoNLS('Название','').'</td>
		    <td>'.($str=($grp==2)?('<input type=text name=name value="'.$vfund['name'][0].'">'):($vfund['name'][0])).'</td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Управляющая компания','').'</td>
            <td>'.($str=($grp==2)?($CompsMenuString):('<a href="company.php?id='.$vfund['company_id'][0].'" title="'.$vfund['company_name'][0].'">'.$vfund['company_name'][0].'</a>')).'</td>
	      </tr>
   		  <tr class="colored">
		    <td>'.echoNLS('Тип','').'</td>
		    <td>'.($str=($grp==2)?($TypeMenuString):($vfund['fund_type'][0])).'</td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Статус','').'</td>
            <td>'.($str=($grp==2)?($StatMenuString):($vfund['status'][0])).'</td>
	      </tr>
          <tr class="colored">
		    <td>'.echoNLS('Объект инвестирования','').'</td>
		    <td>'.($str=($grp==2)?($ObjMenuString):($vfund['invest_object'][0])).'</td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Фонд работает с','').'</td>
            <td>'.($str=($grp==2)?('<input name="start_date" id="start_date" value="'.$vfund['start_date'][0].'">'):($vfund['start_date'][0])).'</td>
	      </tr>
          <tr class="colored">
		    <td>'.echoNLS('Минимальная сумма (первый взнос)','').'</td>
		    <td><input type=text name=limit_min_sum value="'.$vfund['limit_min_sum'][0].'" onblur="IsNumeric(this)"></td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Минимальная сумма (послед. взносы)','').'</td>
            <td><input type=text name=next_min_sum value="'.$vfund['next_min_sum'][0].'"  style=" font-size: 8pt;" onblur="IsNumeric(this)"></td>
	      </tr>
           <tr class="colored">
		    <td>'.echoNLS('Купить и погасить пай можно','').'</td>
		    <td><textarea name=when_buy_sell rows=4 cols=90>'.$vfund['when_buy_sell'][0].'</textarea></td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Надбавки (при покупке пая)','').'</td>
            <td><textarea name=extra_charge rows=4 cols=90>'.$vfund['extra_charge'][0].'</textarea></td>
	      </tr>
           <tr class="colored">
		    <td>'.echoNLS('Скидки (при погашении пая)','').'</td>
		    <td><textarea name=discount rows=4 cols=90>'.$vfund['discount'][0].'</textarea></td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Веб страница','').'</td>
            <td><input type=text name=web_site value="'.$vfund['web_site'][0].'"></td>
	      </tr>
   		  <tr class="colored">
          	<td>'.echoNLS('Общая информация','').'</td>
            <td><textarea name=general_inforows rows=4 cols=90>'.$vfund['general_info'][0].'</textarea></td>
	      </tr>
		  </table>

       <div class="title">Данные по стоимости пая и СЧА</div>
      ';
      include('pif_stat_tab.php');

      echo '
      <div class="two-blocks">
		<div class="left-block">
		<div class="title">Цена пая, тыс. тг.</div>
		<img src="../lib/graph.php?tab=ism_fund_value&id_col=fund_id&interval=9&id_val='.$id.'&val_col=round((value/1000),2)"  alt="img" />
		</div>
		<div class="right-block">
		<div class="title">СЧА, млн. тг.</div>
		<img src="../lib/graph.php?tab=ism_fund_value&id_col=fund_id&interval=9&id_val='.$id.'&val_col=round((asset_value/1000000),2)"  alt="img" />
		</div>
	 </div>

	 <div class="title">Дополнительная информация</div>
     <table class="tab-table">
		  <tr class="colored">
		    <td>'.echoNLS('Национальный идентификационный номер','').'</td>
		    <td>'.($str=($grp==2)?('<input type=text name=registration_number value="'.$vfund['registration_number'][0].'">'):($vfund['registration_number'][0])).'</td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Номинальная стоимость пая','').'</td>
            <td><input type=text name=nominal_cost value="'.$vfund['nominal_cost'][0].'" onblur="IsNumeric(this)"></td>
	      </tr>
		  <tr class="colored">
		    <td>'.echoNLS('Дата регистрации','').'</td>
		    <td><input type=text name=registration_date id=registration_date value="'.$vfund['registration_date'][0].'"></td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Дата окончания формирования','').'</td>
            <td><input type=text name=build_end_date id=build_end_date value="'.$vfund['build_end_date'][0].'"></td>
	      </tr>
	      <tr class="colored">
		    <td>'.echoNLS('Срок функционирования фонда','').'</td>
		    <td><input type=text name=fund_life_time value="'.$vfunds['fund_life_time'][0].'"></td>
		  </tr>

     </table>
     <br />
     <div class="title">Вознаграждения и расходы</div>
     <table class="tab-table">
		  <tr class="colored">
		    <td>'.echoNLS('Вознаграждения УК','').'</td>
		    <td><textarea name=mc_bonus rows=4 cols=90>'.$vfund['mc_bonus'][0].'</textarea></td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Вознаграждения кастодиану, регистратору и аудитору','').'</td>
            <td><textarea name=cra_bonus rows=4 cols=90>'.$vfund['cra_bonus'][0].'</textarea></td>
	      </tr>
		  <tr class="colored">
		    <td>'.echoNLS('Расходы фонда','').'</td>
		    <td><input type=text name=fund_expences value="'.$vfund['fund_expences'][0].'"></td>
		  </tr>
     </table>

     <br />
     <div class="title">Инфраструктура фонда</div>
     <table class="tab-table">
		  <tr class="colored">
		    <td>'.echoNLS('Кастодиан','').'</td>
		    <td><textarea name=castodian rows=4 cols=90>'.$vfund['castodian'][0].'</textarea></td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Регистратор','').'</td>
            <td><textarea name=registrator rows=4 cols=90>'.$vfund['registrator'][0].'</textarea></td>
	      </tr>
		  <tr class="colored">
		    <td>'.echoNLS('Аудитор','').'</td>
		    <td><textarea name=auditor rows=4 cols=90>'.$vfund['auditor'][0].'</textarea></td>
		  </tr>
     </table>
     <div class="search-block">
     <span>
         <input type="submit"  name="edit" value="'.echoNLS('Изменить','').'">
         <input type="reset"   value="'.echoNLS('Отменить','').'">
     </span>
     </div>
     </form>
       ';

 }
?>
  </div>
  <div id="fragment-2">
       <?php
       $type='pif';
       include('../includes/fund_stat.php');
       if ($edit_form)  echo '<a class="nyroModal" rev="modal" href="../admin/upload_fund_data.php?id='.$id.'" title="Внести данные">Загрузить данные</a>';
       ?>
  </div>
  <div id="fragment-3">
       <?php
       $type='pif';
       include('../includes/fund_structure.php');
       ?>

  </div>
  <!--

  <div id="fragment-4">

  </div>
  -->
</div>

</div>
<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>