<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
//get last data update
$query = "select UNIX_TIMESTAMP(max(action_date))  last_update_nm
          from ism_data_statistics
          where table_name='ism_pension_funds'
             	and data_id=".clean_int($id);

$stat=array();
$rc=sql_stmt($query, 1, $stat ,1);
// Last-Modified
$LastModified_unix = filemtime(__FILE__);
$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
$LastDepositUpdate = $stat['last_update_nm'][0];
if ($LastDepositUpdate >= $LastModified_unix)  $LastModified = gmdate('D, d M Y H:i:s', $LastDepositUpdate).' GMT';
header('Last-Modified: '. $LastModified);


//edit block
if (isset($edit))
{
	if ($grp==2)
	{
		$start_date=($start_date=="")?("NULL"):("'".substr($start_date,6,4)."-".substr($start_date,3,2)."-".substr($start_date,0,2)."'");
		$name=ltrim($name);
		$name=rtrim($name);
		$query = "update
				  		ism_pension_funds
		          set
						company_id=".$company_id."
		                ,name='".$name."'
		                ,start_date=".$start_date."
		                ,fund_type=".$type_id."
		                ,status=".$stat_id."
		                ,web_site='".$web_site."'
		                ,general_info='".$general_info."'
		                ,president='".$president."'
		                ,directors='".$directors."'
		                ,members_of_the_board='".$members_of_the_board."'
		                ,chief_accountant='".$chief_accountant."'
						,custodian='".$custodian."'
						,license='".$license."'
						,address='".$address."'
						,phone='".$phone."'
						,fax='".$fax."'
						,email='".$email."'
		          where
		          		fund_id=".$id;
	}
	else
	{
		$query = "update
						ism_pension_funds
	              set
	              		web_site='".$web_site."'
	                 	,general_info='".$general_info."'
	                 	,custodian='".$custodian."'
	                 	,auditor='".$auditor."'
	        	  where
	        	  		fund_id=".$id;
	}

	$result=exec_query($query);
	if ($result)
	{
		//update statistics
	   	$stat_query = "insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
	         		   values('ism_pension_funds',".$id.",1,current_date(),'".$user."','".$name."')";

	   $result=exec_query($stat_query);

	   echo '<div class="info-message">'.echoNLS('Данные изменены!','').'</div>';
	}
}


// get npf data
$query = "select
		  		t.fund_id
                ,t.name
                ,c.name   company_name
                ,c.company_id
                ,t.status status_id
                ,t.fund_type fund_type_id
                ,d1.desc_".echoNLS('ru','')." status
                ,d2.desc_".echoNLS('ru','')." fund_type
                ,t.license
                ,DATE_FORMAT(t.start_date,'%d.%m.%Y') start_date
                ,t.president
                ,t.directors
                ,t.members_of_the_board
                ,t.chief_accountant
                ,t.custodian
                ,t.address
                ,t.phone
                ,t.fax
                ,t.email
                ,t.web_site
                ,t.general_info
          from ism_pension_funds t
                left join ism_dictionary d1  ON t.status=d1.id
                left join ism_dictionary d2  ON t.fund_type=d2.id
                left join ism_pension_companies  c  ON t.company_id=c.company_id
          where
             	t.fund_id=".clean_int($id);

		$vfund=array();
		$rc=sql_stmt($query, 21, $vfund ,1);

       //get fund last month income

       $query = "select
			              round(t.value,2)    last_value
			             ,tt.value  month_ago_value
			             ,round(t.value-tt.value,2) income
			             ,round(t.value-ttt.value,2) income_year
			             ,round(((t.value-tt.value)/tt.value)*100,2) month_income
			             ,round(((t.value-ttt.value)/ttt.value)*100,2) year_income
			             ,DATE_FORMAT(t.check_date,'%d.%m.%Y') check_date
		         from ism_pension_fund_value t, ism_pension_fund_value tt, ism_pension_fund_value ttt
		         where t.fund_id=".clean_int($id)."
		               and tt.fund_id=".clean_int($id)."
		               and ttt.fund_id=".clean_int($id)."
		               and t.check_date=(select max(check_date) from ism_pension_fund_value where fund_id=".clean_int($id).")
		               and tt.check_date=DATE_ADD(t.check_date, INTERVAL -1 MONTH)
   		               and ttt.check_date=DATE_ADD(t.check_date, INTERVAL -1 YEAR)
                ";
        $vfund_income=array();
		$rc=sql_stmt($query, 7, $vfund_income ,1);

        //no data exists
        if ($rc==0)
        {
          header('Location: /404.php');
          exit;
        }


        // get the last fund structure
        $query="select structure_id, date_format(max(structure_date),'%d.%m.%Y') max_date from ism_pension_fund_structure where fund_id=".$id." group by fund_id";
     	$vfund_stru_date=array();
	 	$rc=sql_stmt($query, 2, $vfund_stru_date ,1);

	 	if ($rc>0)
	 	{
         $query='select (select desc_ru from ism_dictionary where id=t.item) item, t.volume from ism_pension_fund_structure_item t where t.structure_id='.$vfund_stru_date['structure_id'][0].' order by t.item';
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

        //check for edit rights
		$edit_form=false;
		if (isset($grp) && $grp==2) //if admin
		{ $edit_form=true;}

		// set default tab_id
		$tab_id=(isset($tab_id))?($tab_id):(0);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Пенсионный фонд <?php echo $vfund['name'][0];?></title>
<meta name="Description" content="Пенсионный фонд <?php echo $vfund['name'][0];?>" >
<meta name="Keywords" content="пенсионный фонд <?php echo $vfund['name'][0];?>, купа, доходность, нпф">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
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
</head>


<body>
<div id="container">
<!-- header -->
<?php
        $selected_menu='npf';
        include '../includes/header.php';
?>
<!-- main body -->
  <div class="sidebar2">
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
        <li class="topic" title="Пенсионный фонд">Пенсионный фонд</li>
        <li class="first"><a href="#fragment-1" title="профиль пенсионного фонда">Профиль</a></li>
        <li><a href="#fragment-2" title="статистика стоимости УПЕ">Статистика</a></li>
        <li><a href="#fragment-3" title="портфель пенсионного фонда НПФ">Портфель</a></li>
        <li><a href="#fragment-4" title="активы пенсионного фонда">Активы</a></li>
      </ul>
  <div id="fragment-1">
<?php
if (!$edit_form)
 {
  echo '
         <div id="fund-container">
         <div class="left-block">
               <div class="big" title="'.$vfund['name'][0].'">'.$vfund['name'][0].'</div>
               <div class="small"><a href="company.php?id='.$vfund['company_id'][0].'" title="'.$vfund['company_name'][0].'">'.$vfund['company_name'][0].'</a></div>
               <div class="small">Адрес: '.$vfund['address'][0].'</div>
               <div class="small">Тел.: '.$vfund['phone'][0].'; Факс:'.$vfund['fax'][0].'</div>
         </div>
         <div class="right-block">
               <div class="big black" title="стоимость УПЕ">'.$vfund_income['last_value'][0].'&nbsp;<span class="mid '.($str=($vfund_income['income'][0]>0)?("green"):("")).' '.($str=($vfund_income['income'][0]<0)?("red"):("")).'" title="доходность за месяц">'.$vfund_income['income'][0].'&nbsp;('.$vfund_income['month_income'][0].'%)&nbsp;<a class="nyroModal" rev="modal" href="calculator.php?income='.$vfund_income['year_income'][0].'" title="расчет пенсии"><img src="../media/images/calculator.png" height="25px" alt="расчет пенсии" border="0"></a></span></div>
               <div class="small">Дата обнавления: '.$vfund_income['check_date'][0].'</div>
               <div class="small">Доходность за год: <span class="'.($str=($vfund_income['income_year'][0]>0)?("green"):("")).' '.($str=($vfund_income['income_year'][0]<0)?("red"):("")).'" title="доходность за год">'.$vfund_income['income_year'][0].'&nbsp;('.$vfund_income['year_income'][0].'%)</span></div>
               <div class="small">Лицензия: '.$vfund['license'][0].'&nbsp;от&nbsp;'.$vfund['start_date'][0].'</div>
               <div class="small">'. ($str=($vfund['web_site'][0]!='')?('<noindex><a href="'.$vfund['web_site'][0].'" rel="nofollow"> '.echoNLS($vfund['web_site'][0],'').'</a></noindex>'):('')).'</div>
         </div>

         </div>

        <div class="two-blocks">
		<div class="left-block">
		<div class="title" title="Стоимость УПЕ"><a class="more" href="#" onclick="ChangeTab(1);" title="статистика по стоимости УПЕ">Вся статистика</a>Стоимость УПЕ</div>
        <a class="more" href="#" onclick="ChangeTab(1);" title="статистика по стоимости УПЕ"><img src="../lib/graph.php?tab=ism_pension_fund_value&id_col=fund_id&interval=9&id_val='.clean_int($id).'&val_col=round((value),2)&height=230" alt="стоимость УПЕ" border="0"></a>
		</div>
		<div class="right-block">
		<div class="title" title="Портфель НПФ"><a class="more" href="#" onclick="ChangeTab(2);" title="портфель НПФ">Вся статистика</a>Портфель НПФ&nbsp;<font size=1>('.$vfund_stru_date['max_date'][0].')</font></div>
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

       <div class="title"><a onclick="ShowHide(); return false;" href="#">Управление фондом (Показать\Скрыть)</a></div>
         <div id="slidingDiv">
          <table class="tab-table">
		   <tr class="colored"><td>'.echoNLS('Председатель Правления','').'</td><td>'.$vfund['president'][0].'</td></tr>
           <tr><td>'.echoNLS('Совет директоров','').'</td><td>'.$vfund['directors'][0].'</td></tr>
           <tr class="colored"><td>'.echoNLS('Члены Правления','').'</td><td>'.$vfund['members_of_the_board'][0].'</td></tr>
           <tr><td>'.echoNLS('Главный бухгалтер','').'</td><td>'.$vfund['chief_accountant'][0].'</td></tr>
           <tr class="colored"><td>'.echoNLS('Кастодиан','').'</td><td>'.$vfund['custodian'][0].'</td></tr>
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
 { 	  //company list
$query="select
            company_id
            ,name
		from
			ism_pension_companies
		order
			by name";
$vcomps=array();
$rc=sql_stmt($query, 2, $vcomps ,2);

if (!isset($comp_id))  {$company_id=$vfund['company_id'][0];}
else {$company_id=$comp_id;}
$CompsMenuString = menu_list($vcomps['name'],$company_id,$vcomps['company_id']);
if (isset($comp_id))
{
$CompsMenuString = '<select name="company_id" DISABLED>'.$CompsMenuString.'</select>
                    <input type=hidden name="company_id" value="'.$comp_id.'">';
}
else
{
$CompsMenuString = '<select name="company_id" >'.$CompsMenuString.'</select>';
}

//type list
$query = "select
             id
             ,desc_".echoNLS('ru','')." name
          from
          	 ism_dictionary
          where
          	 grp=".$GRP_TYPE_NPF."
          order by
          	 name";
$vtype=array();
$rc=sql_stmt($query, 2, $vtype ,2);

if (!isset($type_id))  $type_id=$vfund['fund_type_id'][0];
$TypeMenuString = menu_list($vtype['name'],$type_id,$vtype['id']);
$TypeMenuString = '<select name="type_id">'.$TypeMenuString.'</select>';

//status list
$query = "select
		      id
              ,desc_".echoNLS('ru','')." name
          from
			  ism_dictionary
          where
			  grp=".$GRP_STAT_NPF."
          order by
			  name";
$vstat=array();
$rc=sql_stmt($query, 2, $vstat ,2);

if (!isset($stat_id))  $stat_id=$vfund['status_id'][0];
$StatMenuString = menu_list($vstat['name'],$stat_id,$vstat['id']);
$StatMenuString = '<select name="stat_id">'.$StatMenuString.'</select>';


  echo '
    <script type="text/javascript">
	$(function(){
	$.datepicker.setDefaults(
	$.extend($.datepicker.regional["ru"])
	);
	$("#start_date").datepicker();
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
		    <td><input type=text name=name value="'.$vfund['name'][0].'"></td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('КУПА','').'</td>
            <td>'.$CompsMenuString.'</td>
	      </tr>
   		  <tr class="colored">
		    <td>'.echoNLS('Тип','').'</td>
		    <td>'.$TypeMenuString.'</td>
		  </tr>
		  <tr>
          	<td>'.echoNLS('Статус','').'</td>
            <td>'.$StatMenuString.'</td>
	      </tr>
          <tr class="colored">
		    <td>'.echoNLS('Номер лицензии','').'</td>
		    <td><input type=text name=license value="'.$vfund['license'][0].'"></td>
		  </tr>
          <tr>
			<td>'.echoNLS('Дата выдачи лицензии','').'</td>
            <td><input type=text name=start_date id=start_date value="'.$vfund['start_date'][0].'"></td>
           </tr>
           <tr class="colored">
			<td>'.echoNLS('Председатель Правления','').'</td>
			<td><input type=text name=president value="'.$vfund['president'][0].'"></td>
           </tr>
           <tr>
			<td>'.echoNLS('Совет директоров','').'</td>
			<td><textarea name="directors" rows=4 cols=100>'.$vfund['directors'][0].'</textarea></td>
           </tr>
           <tr class="colored">
			<td>'.echoNLS('Члены Правления','').'</td>
			<td><textarea name="members_of_the_board" rows=4 cols=100>'.$vfund['members_of_the_board'][0].'</textarea></td>
           </tr>
           <tr>
			<td>'.echoNLS('Главный бухгалтер','').'</td>
			<td><input type=text name=chief_accountant value="'.$vfund['chief_accountant'][0].'"></td>
           </tr>
           <tr class="colored">
			<td>'.echoNLS('Кастодиан','').'</td>
			<td><input type=text name=custodian value="'.$vfund['custodian'][0].'"></td>
           </tr>
           <tr>
			<td>'.echoNLS('Адрес','').'</td>
			<td><input type=text name=address value="'.$vfund['address'][0].'"></td>
           </tr>
           <tr class="colored">
			<td>'.echoNLS('Телефон','').'</td>
			<td><input type=text name=phone value="'.$vfund['phone'][0].'"></td>
           </tr>
           <tr>
			<td>'.echoNLS('Факс','').'</td>
			<td><input type=text name=fax value="'.$vfund['fax'][0].'"></td>
           </tr>
           <tr class="colored">
			<td>'.echoNLS('e-mail','').'</td>
			<td><input type=text name=email value="'.$vfund['email'][0].'"></td>
           </tr>
		  <tr>
          	<td>'.echoNLS('Веб страница','').'</td>
            <td><input type=text name=web_site value="'.$vfund['web_site'][0].'"></td>
	      </tr>
            <tr class="colored">
	    		<td>'.echoNLS('Общая информация','').'</td>
                 <td><textarea name=general_info style=" font-size: 8pt;" rows=4 cols=100>'.$vfund['general_info'][0].'</textarea></td>
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
       $type='npf';
       include('../includes/fund_stat.php');
       ?>
  </div>
  <div id="fragment-3">
       <?php
       $type='npf';
       include('../includes/fund_structure.php');
       ?>

  </div>

  <div id="fragment-4">

  <?php

   //set period
   if (!isset($slast_date_a))
   {
    $query="select date_format(max(check_date), '%d.%m.%Y' ) slast_date, date_format(DATE_ADD(max(check_date), INTERVAL -1 YEAR), '%d.%m.%Y' ) sfirst_date from ism_pension_fund_value where fund_id=".clean_int($id);
	$vdate=array();
	$rc=sql_stmt($query, 2, $vdate ,1);

	$slast_date_a=$vdate['slast_date'][0];
	$sfirst_date_a=$vdate['sfirst_date'][0];
    }

    //format to mysql date
    $sdate=substr($sfirst_date_a,6,4)."-".substr($sfirst_date_a,3,2)."-".substr($sfirst_date_a,0,2);
    $edate=substr($slast_date_a,6,4)."-".substr($slast_date_a,3,2)."-".substr($slast_date_a,0,2);


    //create data file

    $query="
        select
                    date_format(check_date, '%m/%y' ) check_date_format
                   ,capital
                   ,liability
        from ism_pension_fund_value
        where fund_id=".clean_int($id)."
              and check_date between '".$sdate."' and '".$edate."'
        order by check_date
       ";
    //echo $query;
    //die();
    $vdata=array();
    $rc=sql_stmt($query, 3, $vdata ,2);
    $series="";
    $gid1="";
    $gid2="";

    for ($i=0;$i<sizeof($vdata['check_date_format']);$i++)
    {      $series.='<value xid="'.$i.'">'.$vdata['check_date_format'][$i].'</value>';
      $gid1.='<value xid="'.$i.'">'.$vdata['capital'][$i].'</value>';
      $gid2.='<value xid="'.$i.'">'.$vdata['liability'][$i].'</value>';

    }

    //write to data file
    $fh = fopen($path.'/amcharts/amcolumn/amcolumn_capital_liability_data.xml', 'w') or die("can't open file");
    fwrite($fh,'<?xml version="1.0" encoding="UTF-8"?><chart><series>'.$series.'</series><graphs><graph gid="1">'.$gid1.'</graph><graph gid="2">'.$gid2.'</graph></graphs></chart>');
    fclose($fh);

  ?>

  <!-- amcolumn script-->
  <script type="text/javascript" src="<?php echo $path;?>/amcharts/amcolumn/swfobject.js"></script>
	<div id="flashcontent3" class="search-block grey-block" align="top">
		<strong>Обновите ваш Flash Player</strong>
	</div>

	<script type="text/javascript">
		var so = new SWFObject("<?php echo $path;?>/amcharts/amcolumn/amcolumn.swf", "amcolumn", "680", "500", "8", "#FFFFFF");
		so.addVariable("path", "<?php echo $path;?>/amcharts/amcolumn/");
		so.addVariable("settings_file", encodeURIComponent("<?php echo $path;?>/amcharts/amcolumn/amcolumn_settings_capital_liability.xml"));
		so.addVariable("data_file", encodeURIComponent("<?php echo $path;?>/amcharts/amcolumn/amcolumn_capital_liability_data.xml"));
		so.addVariable("preloader_color", "#999999");
		so.write("flashcontent3");
	</script>
  <!-- end of amcolumn script -->

<script type="text/javascript">
$(function() {
		var dates = $( "#sfirst_date_a, #slast_date_a" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 3,
			onSelect: function( selectedDate ) {
				var option = this.id == "sfirst_date_a" ? "minDate" : "maxDate",
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
      <input id="sfirst_date_a" name="sfirst_date_a" value="<?php echo $sfirst_date_a;?>" />
      <input id="slast_date_a" name="slast_date_a" value="<?php echo $slast_date_a;?>" />
      &nbsp;&nbsp;<span><input type="submit" value="Выбрать"></span>
</div>

      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <input type="hidden" name="tab_id" value="3">
</form>



  </div>

</div>

</div>
<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>