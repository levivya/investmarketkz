<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

$query='
         select
                  max(min_sum)/1000 max_min_sum
                 ,max(min_period) max_min_period
         from ism_deposits
         ';

$vdeps=array();
$rc=sql_stmt($query, 2, $vdeps ,1);

$query='
         select
                 d.bank_id
                ,(select name from ism_banks where bank_id=d.bank_id)  name
                ,count(d.deposit_id) cnt
		from ism_deposits d
		group by d.bank_id
		order by cnt desc';

$vbanks=array();
$rc=sql_stmt($query, 3, $vbanks ,2);

// Last-Modified
$LastModified_unix = filemtime(__FILE__);
$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
header('Last-Modified: '. $LastModified);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Депозиты банков Казахстана</title>
<meta name="Description" content="Депозиты банков Казахстана, депозиты в тенге, депозиты в долларах, депозиты в евро. Рейтинг депозитов по доходности." >
<meta name="Keywords" content="депозиты банков казахстана, депозиты банков, депозиты казахстана,депозиты, ставка депозита, тип депозита, банки казахстана, минимальный срок, минимальная сумма, доходность">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<?php include '../includes/scripts.php';?>
<script type="text/javascript" src="/scripts/ui/minified/jquery.ui.slider.min.js"></script>
<script type="text/javascript" src="/scripts/ui/minified/jquery.ui.accordion.min.js"></script>
<script type="text/javascript">
   function get(){
     var banks="";
     <?php for ($i=0;$i<sizeof($vbanks['bank_id']);$i++) echo "if (document.getElementById('bank_".$vbanks['bank_id'][$i]."').checked) banks=banks+'".$vbanks['bank_id'][$i].",';"; ?>
     banks=banks.substr(0,banks.length-1);
     $.post('get_data.php',{kzt: document.form.kzt.checked, usd: document.form.usd.checked, eur: document.form.eur.checked, min_sum: $( "#slider-min-sum" ).slider( "value"), min_period: $( "#slider-min-period" ).slider( "value"), banks: banks},
             function(output){             	 $('#data').html(output).show()
           });
   }

   function All (){<?php for ($i=0;$i<sizeof($vbanks['bank_id']);$i++) echo "document.getElementById('bank_".$vbanks['bank_id'][$i]."').checked=true;"; ?> get();}
   function clearAll (){<?php for ($i=0;$i<sizeof($vbanks['bank_id']);$i++) echo "document.getElementById('bank_".$vbanks['bank_id'][$i]."').checked=false;"; ?> get();}

   function set_chbox (var1){
        var vchb=document.getElementById(var1);
        if (vchb.checked) {vchb.checked=false;} else {vchb.checked=true;}
        get();
    }

   $(function() {

        $( "#accordion" ).accordion({
			autoHeight: false,
			navigation: true
		});

        $( "#slider-min-sum" ).slider({
			value: 15,
			min: 0,
			max: <?php echo $vdeps['max_min_sum'][0];?>,
			step: 15,
			stop: function( event, ui ) {
				$( "#minsum" ).val( ui.value);
			    $( "#minsum" ).css("width", ui.value.toString().length*10 + "px");
  	            var banks="";
     			<?php for ($i=0;$i<sizeof($vbanks['bank_id']);$i++) echo "if (document.getElementById('bank_".$vbanks['bank_id'][$i]."').checked) banks=banks+'".$vbanks['bank_id'][$i].",';"; ?>
     			banks=banks.substr(0,banks.length-1);
 			    $.post('get_data.php',{kzt: document.form.kzt.checked, usd: document.form.usd.checked, eur: document.form.eur.checked, min_sum: ui.value,min_period: $( "#slider-min-period" ).slider( "value"), banks:banks},
                     function(output){$('#data').html(output).show()});
			}
		});
        $( "#minsum" ).val( $( "#slider-min-sum" ).slider( "value" ));

		$( "#slider-min-period" ).slider({
			value: 12,
			min: 1,
			max: <?php echo $vdeps['max_min_period'][0];?>,
			step: 1,
			stop: function( event, ui ) {
				$( "#minperiod" ).val( ui.value);
  		        var banks="";
     			<?php for ($i=0;$i<sizeof($vbanks['bank_id']);$i++) echo "if (document.getElementById('bank_".$vbanks['bank_id'][$i]."').checked) banks=banks+'".$vbanks['bank_id'][$i].",';"; ?>
     			banks=banks.substr(0,banks.length-1);
				$.post('get_data.php',{kzt: document.form.kzt.checked, usd: document.form.usd.checked, eur: document.form.eur.checked, min_period: ui.value, min_sum: $( "#slider-min-sum" ).slider( "value"), banks:banks},
                     function(output){$('#data').html(output).show()});
			}
		});
		$( "#minperiod" ).val( $( "#slider-min-period" ).slider( "value" ));

	});
</script>
</head>
<body onload="get();">
<div id="container">
<!-- header -->
<?php
$selected_menu='deposit';
include '../includes/header.php';

$query='
         select
                 CASE currency WHEN 0 THEN "kzt" WHEN 1 THEN "usd" ELSE "eur" END currency
                ,CASE currency WHEN 0 THEN "тенге" WHEN 1 THEN "долларах" ELSE "евро" END currency_cap
                ,count(deposit_id) cnt
         from ism_deposits
         group by currency';

$vcurr=array();
$rc=sql_stmt($query, 3, $vcurr ,2);
?>
<div class="sidebar2">
<div style="font-size:0;height:22px;"></div>
<form name="form">
<div id="accordion">
	<h1><a href="#">Валюта депозита</a></h1>
	<div>
        <table width="100%">
<?php
for ($i=0;$i<sizeof($vcurr['currency']);$i++)
{
    $checked="";
	if (!isset($bank_id)) $checked=($vcurr['currency'][$i]=='kzt')?("checked"):("");
    else $checked="checked";

	echo '<tr><td><input type="checkbox" name="'.$vcurr['currency'][$i].'" id="'.$vcurr['currency'][$i].'" onClick="get();" '.$checked.'  style="vertical-align: middle"/><label for="'.$vcurr['currency'][$i].'" style="padding-left:.5em; color:#00377B;">'.strtoupper($vcurr['currency'][$i]).'</label></td><td align="right"><a onclick="set_chbox(\''.$vcurr['currency'][$i].'\');" title="депозиты в '.$vcurr['currency_cap'][$i].'">'.$vcurr['cnt'][$i].'</a></td></tr>';
}
?>
       </table>
    </div>
	<h1><a href="#">Мин. сумма не более: <input type="text" id="minsum" style="border:0; background:transparent; font-weight:bold; width:22px"/>тыс.</a></h1>
	<div>
          <table width="100%" height="35px" style="color:#00377B">
          <tr><td align="left">MIN</td><td align="right">MAX</td></tr>
          <tr><td colspan="2"><div id="slider-min-sum"></div></td></tr>
          </table>
    </div>
	<h1><a href="#">Мин. срок не более: <input type="text" id="minperiod" style="border:0; background:transparent; font-weight:bold; width:20px"/> мес.</a></h1>
    <div>
          <table width="100%" height="35px" style="color:#00377B">
          <tr><td align="left">MIN</td><td align="right">MAX</td></tr>
          <tr><td colspan="2"><div id="slider-min-period"></div></td></tr>
          </table>
    </div>
	<h1><a href="#">Банки</a></h1>
	<div>
        <table width="100%">
        <tr><td align="left"><a onclick="All();">выбрать все</a>|<a onclick="clearAll();">очистить</a></td><td align="right">депозитов</td></tr>
        <tr><td colspan="2" style="font-size:0;height:10px;"></td></tr>
<?php
for ($i=0;$i<sizeof($vbanks['bank_id']);$i++)
{
    $checked="";
	if (isset($bank_id)) $checked=($vbanks['bank_id'][$i]==$bank_id)?("checked"):("");
    else $checked="checked";
    echo '<tr><td><input type="checkbox" name="bank_'.$vbanks['bank_id'][$i].'" id="bank_'.$vbanks['bank_id'][$i].'" onClick="get();" '.$checked.'  style="vertical-align: middle"/><label for=bank_"'.$vbanks['bank_id'][$i].'" style="padding-left:.5em; color:#00377B;">'.$vbanks['name'][$i].'</label></td><td align="right"><a onclick="set_chbox(\'bank_'.$vbanks['bank_id'][$i].'\');">'.$vbanks['cnt'][$i].'</a></td></tr>';
}
?>
       </table>
    </div>

</form>
<div class="yashare-auto-init" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>
</div>
<noindex>
    <br />
    <div class="title">Реклама от партнеров</div>
    <script type="text/javascript"><!--
	google_ad_client = "pub-2712511792023009";
	/* 250x250, создано 24.09.10 */
	google_ad_slot = "2344662444";
	google_ad_width = 250;
	google_ad_height = 250;
	//-->
	</script>
	<script type="text/javascript"
	src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
    <!-- end sidebar2 -->

</div>
</noindex>

<div class="mainContent">
<h1 class="title">Депозиты банков Казахстана</h1>
<div id="data"></div>
<div class="text">В данном разделе собраны актуальные <strong>депозиты банков Казахстана</strong>, поэтому выбрав депозиты в тенге или в другой валюте, вы можете оценить лучшие ставки. Так же, вы можете задавать дополнительные критерии поиска, например минимальную сумму, необходимую для открытия депозита, или минимальный срок вложений. К тому же, можно ограничить отображение только тех депозитов, которые предоставляют конкретные <a href="banks.php">банки Казахстана</a>. Все депозиты банков будут отсортированы по годовой процентной ставке, после чего можно воспользоваться депозитным калькулятор и рассчитать возможный доход.</div>
</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>