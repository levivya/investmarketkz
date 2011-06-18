<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<html>

<head>
  <title>Внести данные</title>
  <link type="text/css" href="../css/style.css" rel=stylesheet  />
  <meta name="Keywords" content="v-счет">
  <meta name="copyright" content="Invest-Market.kz">
  <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
  <meta HTTP-EQUIV="pragma" CONTENT="no-cache">
  <?php include '../includes/scripts.php';?>
</head>

<body style="background:#fff;" marginright="2" marginheight="2" leftmargin="2" topmargin="2" marginwidth="2">

<script type="text/javascript">
$(function(){
$.datepicker.setDefaults(
$.extend($.datepicker.regional["ru"])
);
$("#cdate").datepicker();
});
</script>


<?php
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

if (isset($add))
{

 $cdate=substr($cdate,6,4).'-'.substr($cdate,3,2).'-'.substr($cdate,0,2);
 $value=str_replace(",", ".",$value);
 $value=str_replace(" ", "",$value);
 $asset_value=str_replace(",", ".",$asset_value);
 $asset_value=str_replace(" ", "",$asset_value);

 $query="insert into ".$tab."(fund_id,check_date,value,asset_value) values(".$fund_id.",'".$cdate."',".$value.",".$asset_value.")";
 //echo $query;
 $result=exec_query($query);

 echo'<div class="info-message">'.echoNLS('Данные внесены','').'</div>';

}
else
{
echo '
<div class="title">'.$fund_name.'</div>
<form name="add_value">
 <input type="hidden" name="tab" value="'.$tab.'">
 <div class="search-block grey-block">
 <ul>
  <li><div>'.echoNLS('Дата','').'</div><input type="hidden" name="fund_id" value="'.$fund_id.'"><input type="text" name="cdate" id="cdate" value="'.$check_date.'"></li>
  <li><div>'.echoNLS('Цена пая\У.П.Е.','').'</div><input type="text" name="value" value=""></li>
  <li><div>'.echoNLS('СЧА\Активы','').'</div><input type="text" name="asset_value" value=""></li>
  <li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="submit" name="add" value="'.echoNLS('Внести','').'"></span></li>
 </ul>
 </div>
</form>
       ';
}

//disconnect from database
disconn($conn);

?>

</body>

</html>
