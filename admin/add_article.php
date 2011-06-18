<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Добавить материал</title>
<meta name="Description" content="Панель администратора" >
<meta name="Keywords" content="">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
</head>


<body>
<div id="container">
<?php
        //Connecting, selecting database
        $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
  		$selected_menu='main';
		include '../includes/header.php';
?>

<div class="one-column-block">
<div class="title"><a class="more" href="index.php">Панель администратора</a>Добавить материал</div>

<script type="text/javascript">
$(function(){
$.datepicker.setDefaults(
$.extend($.datepicker.regional["ru"])
);
$("#vdate").datepicker();
});
</script>

<script type="text/javascript">
$(document).ready(function(){

$("#body").htmlbox({
skin:"blue",
toolbars:[["bold","italic","underline","strike","separator","undo","redo","separator","left","center","right","justify","separator","ol","ul","indent","outdent","separator","link","unlink","image"]],
about:false
});

});
</script>


<?php
if (!isset($group1)) $group1='ism_news';
if (!isset($title)) $title="";
if (!isset($body)) $body="";


if (isset($add)) //add article
{
 if ($title=="")
 {
   echo '<div class="error-message">'.echoNLS('Поле "Заголовок" не может быть пустым!','').'</div>';
 }
else
 {
 $vdate=($vdate!="")?("'".substr($vdate,6,4)."-".substr($vdate,3,2)."-".substr($vdate,0,2)."'"):("NULL");
 $query="";
 switch ($group1) {
    case 'ism_news': $query="ism_news(title,news_date,body,ntype,posted_date)";
    break;
    case 'ism_analytics': $query="ism_analytics(title,analyt_date,body,atype,posted_date)";
    break;
    case 'ism_investor_school':$query="ism_investor_school(title,vdate,body,cont_type)";
    break;
}
if ($group1 == 'ism_investor_school')
 $query="
         insert  into ".$query."
         values('".$title."',".$vdate.",'".$body."',".$ntype.")";
else
 $query="
         insert  into ".$query."
         values('".$title."',".$vdate.",'".$body."',".$ntype.",'".date('Y-m-d')."')";

 $result=exec_query($query);
 if ($result)
   {
     $title="";
     $vdate="";
     $body="";
     echo '<div class="info-message">'.echoNLS('Данные внесены!','').'</div>';
   }


 }

}


if ($group1=='ism_news')
{
$ntype_id = array (0,1,2,3,4,5,6,7,8,9);
$ntype_caption = array ('МИРОВЫЕ РЫНКИ','СОБЫТИЕ','НОВОСТИ, СТАТЬИ И ИНТЕРВЬЮ', 'НОВОСТИ УК','НОВОСТИ КОМПАНИЙ И M&A','НОВОСТИ БАНКОВ И РЕГУЛЯТОРОВ','СТАТЬИ И ИНТЕРВЬЮ - БАНКИ','ФИНАНСОВЫЙ КРИЗИС','НОВОСТИ САЙТА','НОВОСТИ НПФ');
}
else
{
$ntype_id = array (0,1,2);
$ntype_caption = array ('МИРОВЫЕ РЫНКИ','КАЗАХСТАН','Invest-Market.kz');
}

if  (!isset($ntype)) $ntype=2;

$ContTypeMenuString = menu_list($ntype_caption,$ntype,$ntype_id);
$ContTypeMenuString = '<select name="ntype">'.$ContTypeMenuString.'</select>';


echo '
<script language="javascript">

function changeList( box ) {

   var lists = new Array();

   // First set of text and values
   lists[\'news\']    = new Array();
   lists[\'news\'][0] = new Array(\'МИРОВЫЕ РЫНКИ\',\'СОБЫТИЕ\',\'НОВОСТИ, СТАТЬИ И ИНТЕРВЬЮ\', \'НОВОСТИ УК\',\'НОВОСТИ КОМПАНИЙ И M&A\',\'НОВОСТИ БАНКОВ И РЕГУЛЯТОРОВ\',\'СТАТЬИ И ИНТЕРВЬЮ - БАНКИ\',\'ФИНАНСОВЫЙ КРИЗИС\');
   lists[\'news\'][1] = new Array(0,1,2,3,4,5,6,7);

   // Second set of text and values
   lists[\'analytics\']    = new Array();
   lists[\'analytics\'][0] = new Array(\'МИРОВЫЕ РЫНКИ\',\'КАЗАХСТАН\');
   lists[\'analytics\'][1] = new Array(0,1);

   // 3rd set of text and values
   lists[\'investor_school\']    = new Array();
   lists[\'investor_school\'][0] = new Array(\'ДОСТУПНО О ПИФАХ\',\'ВСЕ О ВКЛАДАХ\', \'МОЯ ПЕНСИЯ\', \'СОВЕТЫ ЗАЕМЩИКУ\');
   lists[\'investor_school\'][1] = new Array (0,1,3,2);


   //clear the list
   while ( document.set_date.ntype.options.length ) document.set_date.ntype.options[0] = null;
   //fill date

   var arr = new Array();


   if (box=="ism_news")
   {
    arr=lists[\'news\'];
   }
   else
   {
     if (box=="ism_analytics")
      {
        arr=lists[\'analytics\'];
      }
      else
      {      	arr=lists[\'investor_school\'];      }
   }


	for ( i = 0; i < arr[0].length; i++ ) {

		// Create a new drop down option with the
		// display text and value from arr

		option = new Option( arr[0][i], arr[1][i] );

		// Add to the end of the existing options

		document.set_date.ntype.options[document.set_date.ntype.options.length] = option;
	}

	// Preselect option 0

	document.set_date.ntype.selectedIndex=0;



  }
</script>

     ';


echo '   <form method="post" name="set_date">

          <div class="search-block grey-block">
          <ul>
             <li><div>'.echoNLS('Публиковать в блок','').'</div>
              <input type="radio" name="group1" id="group1" value="ism_news" '.($str=($group1=='ism_news')?('checked'):('')).' onClick="changeList(\'ism_news\')">'.echoNLS('Новости','').'
              <input type="radio" name="group1" id="group1" value="ism_analytics" '.($str=($group1=='ism_analytics')?('checked'):('')).' onClick="changeList(\'ism_analytics\')">'.echoNLS('Аналитика','').'
              <input type="radio" name="group1" id="group1" value="ism_investor_school" '.($str=($group1=='ism_investor_school')?('checked'):('')).' onClick="changeList(\'ism_investor_school\')">'.echoNLS('Школа инвестора','').'
            </li>
            <li><div>'.echoNLS('Блок','').'</div>'.$ContTypeMenuString.'</li>
            <li><div>'.echoNLS('Заголовок','').'</div><input type=text name=title value="'.$title.'" size="80"></li>
            <li><div>'.echoNLS('Дата','').'</div><input type=text name=vdate id=vdate></li>
          </ul>
          </div>
              <div class="search-block grey-block">
		    <div>'.echoNLS('Сообщение','').'</div><textarea name=body id=body rows=20 cols=143>'.$body.'</textarea><br />
		    <div>&nbsp;</div>&nbsp;&nbsp;&nbsp;
		           <span>
          			  <input type="submit"  name="add" value="'.echoNLS('Добавить','').'">
			          <input type="reset"   value="'.echoNLS('Отменить','').'">
		           </span>
		    </div>

     ';
//echo $str;


echo '</form>';
?>

<div class="title">Удалить материал</div>
<?php
//delete
if (isset($delete_is))
{
  $query="
           delete from ism_investor_school
           where id=".$is_id."
         ";
  $result=exec_query($query);
  echo '<div class="info-message">'.echoNLS('Удалено!','').'</div>';
}

if (isset($delete_n))
{
  $query="
           delete from ism_news
           where news_id=".$n_id."
         ";
  $result=exec_query($query);
  echo '<div class="info-message">'.echoNLS('Удалено!','').'</div>';
}

if (isset($delete_a))
{
  $query="
           delete from ism_analytics
           where analyt_id=".$a_id."
         ";
  $result=exec_query($query);
  echo '<div class="info-message">'.echoNLS('Удалено!','').'</div>';
}


//news
$query="
          select
                   news_id
                  ,concat(concat(news_date,' - '),concat(substring(title,1,30),'...')) title
          from ism_news
          order by news_date desc

       ";
$vn=array();
$rc=sql_stmt($query, 2, $vn ,2);

if (!isset($n_id))  {$n_id=$vn['id'][0] ;}
$NewsMenuString = menu_list($vn['title'],$n_id,$vn['id']);
$NewsMenuString = '<select name="n_id">'.$NewsMenuString.'</select>';

//analytics
$query="
          select
                   analyt_id
                  ,concat(concat(analyt_date,' - '),concat(substring(title,1,30),'...')) title
          from ism_analytics
          order by analyt_date desc

       ";
$va=array();
$rc=sql_stmt($query, 2, $va ,2);

if (!isset($a_id))  {$a_id=$vn['id'][0] ;}
$AnalytMenuString = menu_list($va['title'],$a_id,$va['id']);
$AnalytMenuString = '<select name="a_id">'.$AnalytMenuString.'</select>';

//investor school
$query="
          select
                   id
                  ,concat(concat(vdate,' - '),concat(substring(title,1,30),'...')) title
          from ism_investor_school
          order by vdate desc

       ";
$vis=array();
$rc=sql_stmt($query, 2, $vis ,2);

if (!isset($is_id))  {$is_id=$vis['id'][0] ;}
$InvestorSchoolMenuString = menu_list($vis['title'],$is_id,$vis['id']);
$InvestorSchoolMenuString = '<select name="is_id">'.$InvestorSchoolMenuString.'</select>';
?>
<form name="delete_form">
<div class="search-block grey-block">
<ul>
<li><div>Новости</div><?php echo $NewsMenuString;?>&nbsp;&nbsp;&nbsp;<span><input type="submit"  name="delete_n" value="Удалить"></span></li>
<li><div>Аналитика</div><?php echo $AnalytMenuString;?>&nbsp;&nbsp;&nbsp;<span><input type="submit"  name="delete_a" value="Удалить"></span></li>
<li><div>Школа Инвестора</div><?php echo $InvestorSchoolMenuString;?>&nbsp;&nbsp;&nbsp;<span><input type="submit"  name="delete_is" value="Удалить"></span></li>
</ul>
</div>
</form>


</div>

<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>