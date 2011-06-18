<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Рейтинг банков Казахстана</title>
<meta name="Description" content="Рейтинг надежности банков Казахстана составленные независимыми рейтинговыми агенствами." >
<meta name="Keywords" content="рейтинг банков казахстана, банк, fitch, fitch rating, s&p, standard & poor's, надежность, прогноз">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
</head>
<body>
<div id="container">
<!-- header -->
<?php
     // Connecting, selecting database
     $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
     $selected_menu='deposit';
     include '../includes/header.php';

	$rtypes['id'] = array (1,2);
	$rtypes['caption'] = array ('Standard & Poor\'s','Fitch Rating');

    if (!isset($rtype))  {$rtype=$rtypes['id'][0] ;}
	$RTypesMenuString = menu_list($rtypes['caption'],$rtype,$rtypes['id']);
	$RTypesMenuString = '<select name="rtype" class="fnt" cols="71" onchange=submit() >'.$RTypesMenuString.'</select>';

?>

<div class="one-column-block">
<div class="search-block"><form><ul><li><div>Рейтинговое агенство</div><?php echo $RTypesMenuString; ?></li></ul></form></div>
<div class="text">Кредитный <strong>рейтинг</strong> банка это мера его кредитоспособности, т.е. его способность выполнять взятые на себя финансовые обязательства (долги), которая рассчитывается  на основе прошлой и текущей финансовой истории, независимыми рейтинговыми агентствами. Говоря простыми словами, кредитный рейтинг <a href="banks.php" title="банки Казахстана ">банка</a> является основным индикатором его надежности. Чем выше позиция банка в рейтинге, тем, соответственно, вероятность обанкротиться у него меньше. Поэтому для людей, планирующих открыть <a href="/deposit/" title="выбрать депозит">депозит</a> или взять кредит, важно знать текущий рейтинг выбранного банка.</div>
<div class="yashare-auto-init" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>

<?php
if ($rtype == 1)
{
   $query="
           select
                    t.imetent_id bank_id
                   ,(select name from ism_banks where bank_id=t.imetent_id)           name
                   ,DATE_FORMAT(t.rating_date,'%d.%m.%Y')                             rating_date
                   ,(select desc_ru from ism_dictionary where id=t.sp_long_foring)    sp_long_foring_c
                   ,(select desc_ru from ism_dictionary where id=t.sp_short_foring)   sp_short_foring_c
                   ,(select desc_ru from ism_dictionary where id=t.sp_outlook_foring) sp_outlook_foring_c
                   ,(select desc_ru from ism_dictionary where id=t.sp_long_local)     sp_long_local_c
                   ,(select desc_ru from ism_dictionary where id=t.sp_short_local)    sp_short_local_c
                   ,(select desc_ru from ism_dictionary where id=t.sp_outlook_local)  sp_outlook_local_c
           from ism_sp_rating t
           where  t.rating_date=(select max(rating_date) from ism_sp_rating where imetent_id=t.imetent_id)
          ";
//echo $query;

$vbanks=array();
$rc=sql_stmt($query, 9, $vbanks ,2);

if ($rc>0)
{
   echo '
  <h1 class="title"><a href="deposits.php" class="more">Депозиты Казахстана</a>Рейтинг банков Казахстана- Standard & Poor\'s</h1>
  <div class="block1 nopad">
  <table class="tab-table" id="Rating">
  <thead>
  <tr>
    <th title="Банк">'.echoNLS('Банк','').'</th>
    <th title="Долгосрочный рейтинг в иностранной валюте">'.echoNLS('Долг. ин вал','').'</th>
    <th title="Краткосрочный рейтинг в иностранной валюте">'.echoNLS('Кратк. ин вал','').'</th>
    <th title="Прогноз">'.echoNLS('Прогноз','').'</th>
    <th title="Долгосрочный рейтинг в национальной валюте">'.echoNLS('Долг. нац вал','').'</th>
    <th title="Краткосрочный рейтинг в национальной валюте">'.echoNLS('Кратк. нац вал','').'</th>
    <th title="Прогноз">'.echoNLS('Прогноз','').'</th>
    <th title="Дата">'.echoNLS('Дата','').'</th>
  </tr>
  </thead>

  <tbody>';
for ($i=0;$i<sizeof($vbanks['bank_id']);$i++)
   {

	    $class=(fmod(($i),2)==0)?('odd'):('even');

        echo '
	          <tr class="'.$class.'">
    			  <td><a href="bank.php?id='.$vbanks['bank_id'][$i].'" title="'.$vbanks['name'][$i].'">'.$vbanks['name'][$i].'</a></td>
		          <td>'.$vbanks['sp_long_foring_c'][$i].'</td>
		          <td>'.$vbanks['sp_short_foring_c'][$i].'</td>
		          <td>'.$vbanks['sp_outlook_foring_c'][$i].'</td>
		          <td>'.$vbanks['sp_long_local_c'][$i].'</td>
		          <td>'.$vbanks['sp_short_local_c'][$i].'</td>
		          <td>'.$vbanks['sp_outlook_local_c'][$i].'</td>
		          <td>'.$vbanks['rating_date'][$i].'</td>
		      </tr>';
     }

echo '
  </tbody>
  </table>
  <div class="text">Официальное <a href="http://www.standardandpoors.ru/page.php?path=international" rel="nofollow">описание</a> рейтинга.</div>
        ';

}
}
else
{   $query="
           select
                    t.imetent_id         bank_id
                   ,(select name from ism_banks where bank_id=t.imetent_id)           name
                   ,DATE_FORMAT(t.rating_date,'%d.%m.%Y')                             rating_date
                   ,(select desc_ru from ism_dictionary where id=t.f_long_foring)     f_long_foring_c
                   ,(select desc_ru from ism_dictionary where id=t.f_long_local)      f_long_local_c
                   ,(select desc_ru from ism_dictionary where id=t.f_outlook_long)    f_outlook_long_c
                   ,(select desc_ru from ism_dictionary where id=t.f_short_foring)    f_short_foring_c
                   ,(select desc_ru from ism_dictionary where id=t.f_short_local)     f_short_local_c
                   ,(select desc_ru from ism_dictionary where id=t.f_individual)      f_individual_c
           from ism_fitch_rating t
           where  t.rating_date=(select max(rating_date) from ism_fitch_rating where imetent_id=t.imetent_id)
          ";
//echo $query;

$vbanks=array();
$rc=sql_stmt($query, 9, $vbanks ,2);

if ($rc>0)
{
   echo '
  <h1 class="title"><a href="deposits.php" class="more">Депозиты Казахстана</a>Рейтинг банков - Fitch Rating</h1>
  <div class="block1 nopad">
  <table class="tab-table" id="Rating">
  <thead>
  <tr>
    <th title="Банк">'.echoNLS('Банк','').'</th>
    <th title="Долгосрочный рейтинг в иностранной валюте">'.echoNLS('Долг. ин вал','').'</th>
    <th title="Прогноз">'.echoNLS('Прогноз','').'</th>
    <th title="Долгосрочный рейтинг в национальной валюте">'.echoNLS('Долг. нац вал','').'</th>
    <th title="Краткосрочный рейтинг в иностранной валюте">'.echoNLS('Кратк. ин вал','').'</th>
    <th title="Краткосрочный рейтинг в национальной валюте">'.echoNLS('Кратк. нац вал','').'</th>
    <th title="Индивидуальный">'.echoNLS('Индивид','').'</th>
    <th title="Дата">'.echoNLS('Дата','').'</th>
  </tr>
  </thead>

  <tbody>';


for ($i=0;$i<sizeof($vbanks['bank_id']);$i++)
   {

	    $class=(fmod(($i),2)==0)?('odd'):('even');

        echo '
	          <tr class="'.$class.'">
    			  <td><a href="bank.php?id='.$vbanks['bank_id'][$i].'" title="'.$vbanks['name'][$i].'">'.$vbanks['name'][$i].'</a></td>
		          <td>'.$vbanks['f_long_foring_c'][$i].'</td>
		          <td>'.$vbanks['f_outlook_long_c'][$i].'</td>
		          <td>'.$vbanks['f_long_local_c'][$i].'</td>
		          <td>'.$vbanks['f_short_foring_c'][$i].'</td>
		          <td>'.$vbanks['f_short_local_c'][$i].'</td>
		          <td>'.$vbanks['f_individual_c'][$i].'</td>
		          <td align=middle width=10%>'.$vbanks['rating_date'][$i].'</td>
		        </tr>';
     }

echo '
  </tbody>
  </table>
  <div class="text">Официальное <a href="http://www.fitchratings.ru/financial/banks/analitics/methodology/index.wbp" rel="nofollow">описание</a> рейтинга.</div>
        ';
}


}
?>
<script type="text/javascript">
$(document).ready(function(){
  // ---- tablesorter -----
  $("#Rating").tablesorter({
	widgets: ["zebra"],
	sortList:[[0,0]]
  });
  // ---- tablesorter -----
});
</script>


</div>

<br />
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

</div>



<!-- end of main body -->
<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>