<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Публикация RSS новостей</title>
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
<div class="title"><a class="more" href="index.php">Панель администратора</a>Публикация RSS новостей</div>

<script type="text/javascript">
$(function(){
$("#sdate").datepicker();
$("#edate").datepicker();
$('#data').dataTable(
				{	"bPaginate": true,
					"bLengthChange": true,
					"bFilter": false,
					"bSort": false,
					"bInfo": true,
					"iDisplayLength":25,
					"bAutoWidth": false }
					);
});
</script>

<?php
if (!isset($group1))
{
 $group1='ism_news';
}
if ($group1=='ism_news')
{
$ntype_id = array (0,1,2,3,4,5,6,7,9);
$ntype_caption = array ('МИРОВЫЕ РЫНКИ','СОБЫТИЕ','НОВОСТИ, СТАТЬИ И ИНТЕРВЬЮ', 'НОВОСТИ УК','НОВОСТИ КОМПАНИЙ И M&A','НОВОСТИ БАНКОВ И РЕГУЛЯТОРОВ','СТАТЬИ И ИНТЕРВЬЮ - БАНКИ','ФИНАНСОВЫЙ КРИЗИС','НОВОСТИ НПФ');
}
else
{
$ntype_id = array (0,1);
$ntype_caption = array ('МИРОВЫЕ РЫНКИ','КАЗАХСТАН');
}

if  (!isset($ntype)) $ntype=2;

$ContTypeMenuString = menu_list($ntype_caption,$ntype,$ntype_id);
$ContTypeMenuString = '<select name="ntype" class="fnt" cols="71" >'.$ContTypeMenuString.'</select>';


$query="
            select
                   DATE_FORMAT(DATE_ADD(current_date(), INTERVAL -1 DAY),'%d-%m-%Y') sdate
                  ,DATE_FORMAT(current_date(),'%d-%m-%Y')    edate
       ";
$vdate=array();
$rc=sql_stmt($query, 2, $vdate ,1);

if (!isset($sdate)) $sdate=$vdate['sdate'][0];
if (!isset($edate))  $edate=$vdate['edate'][0];

$start_date=substr($sdate,6,4)."-".substr($sdate,3,2)."-".substr($sdate,0,2);
$end_date=substr($edate,6,4)."-".substr($edate,3,2)."-".substr($edate,0,2);



//publish
if (isset($publish))
{
  $query="
          select
                 t.news_id
         from news t
         where   t.published=0
                 and t.news_date between '".$start_date."' and DATE_ADD('".$end_date."', INTERVAL 1 DAY)
        ";

  //echo $query;

  $vnews_id=array();
  $rc=sql_stmt($query, 1, $vnews_id ,2);

  for ($i=0;$i<sizeof($vnews_id['news_id']);$i++)
   {

         if (isset(${'news_'.$vnews_id['news_id'][$i]}))
         {

           //echo  $vnews_id['news_id'][$i].'<br>';

           //++ Load news ========================

           if ($group1=='ism_news')  $query_stmt='ism_news(title,news_date,body,ntype,posted_date)';
           else                      $query_stmt='ism_analytics(title,analyt_date,body,atype,posted_date)';

           $query='
                     insert  into '.$query_stmt.'
                     select
                             t.news_title
                            ,t.news_date
                            ,CONCAT(t.news_text,"<br>Источник: ",(select rss_title from rss where rss_id=t.rss_id))
                            ,'.$ntype.'
                            ,"'.date('Y-m-d').'"
                      from news t
                      where t.news_id='.$vnews_id['news_id'][$i].'
                  ';

          //echo $query;

          $result=exec_query($query);

          //++ Update field
           $query='
                     update news
                     set published=1
                     where news_id='.$vnews_id['news_id'][$i].'
                  ';
          $result=exec_query($query);

          if ($result)
            {
	             echo '<div class="info-message">'.echoNLS('Данные внесены!','').'</div>';
            }

         }
   }

}



$query="
          select
                 t.news_id
                ,t.groups_id
                ,(select groups_name from groups where groups_id=t.groups_id) groups_name
                ,t.rss_id
                ,(select rss_title from rss where rss_id=t.rss_id) rss_title
                ,t.news_date
                ,t.news_title
                ,t.news_link
         from news t
         where   t.published=0
                 and t.news_date between '".$start_date."' and DATE_ADD('".$end_date."', INTERVAL 1 DAY)
         order by t.news_date desc
        ";
//echo $query;


$vnews=array();
$rc=sql_stmt($query, 8, $vnews ,2);

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
    arr=lists[\'analytics\'];
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




echo '   <form action="publish_rss_news.php" method="post" name="set_date">

          <div class="search-block grey-block">
          <ul>
             <li><div>'.echoNLS('Публиковать в блок','').'</div>
              <input type="radio" name="group1" id="group1" value="ism_news" '.($str=($group1=='ism_news')?('checked'):('')).' onClick="changeList(\'ism_news\')">'.echoNLS('Новости','').'
              <input type="radio" name="group1" id="group1" value="ism_analytics" '.($str=($group1=='ism_analytics')?('checked'):('')).' onClick="changeList(\'ism_analytics\')">'.echoNLS('Аналитика','').'
            </li>
            <li>
                 <div>'.echoNLS('Период','').'</div><input type="text" name="sdate" id="sdate" value="'.$sdate.'"><input type="text" name="edate" id="edate" value="'.$edate.'">&nbsp;&nbsp;<span><input type="submit"  value="'.echoNLS('Обновить','').'" class="button"></span>
            </li>
            <li><div>'.echoNLS('Загрузить в блок','').'</div>'.$ContTypeMenuString.'</li>
          </ul>
          </div>
         <input type=hidden name=page value='.$page.'>
     ';
//echo $str;

if ($rc>0)
{
echo '
<div class="search-block">
<span><input type="submit" name="publish" class="button" value="Загрузить"></span>
</div>

<table  id="data" class="tab-table">
<thead>
 <tr>
  <th></td>
  <th>'.echoNLS('Дата','').'</th>
  <th>'.echoNLS('Заголовок','').'</th>
  <th>'.echoNLS('Группа','').'</th>
  <th>'.echoNLS('Источник','').'</th>
 </tr>
</thead>
</tbody>
';

for ($i=0;$i<sizeof($vnews['news_id']);$i++)
   {

$str2=(fmod(($i+1),2)==0)?("bgColor=#f3f3f3"):("");

echo '<tr '.$str2.'>
          <td><input type="checkbox" id="news_'.$vnews['news_id'][$i].'" name="news_'.$vnews['news_id'][$i].'"></td>
          <td>'.$vnews['news_date'][$i].'</td>
          <td><a href="'.$vnews['news_link'][$i].'" target="_blank">'.$vnews['news_title'][$i].'</a></td>
          <td>'.$vnews['groups_name'][$i].'</td>
          <td>'.$vnews['rss_title'][$i].'</td>
        </tr>';
     }

echo '</tbody></table>

<div class="search-block">
<span><input type="submit" name="publish" class="button" value="Загрузить"></span>
</div>

      ';



}
else
{
 echo  echoNLS('Нет данных.','');

}

echo '</form>';

?>

</div>

<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>