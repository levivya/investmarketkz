<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

//get last data update
$query = "select UNIX_TIMESTAMP(max(action_date))  last_update_nm
          from ism_data_statistics
          where table_name='ism_banks'
             	and data_id=".clean_int($id);

$stat=array();
$rc=sql_stmt($query, 1, $stat ,1);
// Last-Modified
$LastModified_unix = filemtime(__FILE__);
$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
$LastDepositUpdate = $stat['last_update_nm'][0];
if ($LastDepositUpdate >= $LastModified_unix)  $LastModified = gmdate('D, d M Y H:i:s', $LastDepositUpdate).' GMT';
header('Last-Modified: '. $LastModified);


//check for edit rights
$edit_form=false;
if (isset($grp) && $grp==2) //if admin
{$edit_form=true;}


// edit data ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if (isset($edit))
{
// update head photo
if ( isSet($_FILES['head_photo']) )
{
   $target_path = "../media/images/banks/headphoto/" . (int)$id . ".jpg";
   if(move_uploaded_file($_FILES['head_photo']['tmp_name'], $target_path)) chmod($target_path , 0644 );
}
//update logo
if ( isSet($_FILES['bank_logotype']) )
{
	$target_path = "../media/images/banks/logotype/" . (int)$id . ".jpg";
	if(move_uploaded_file($_FILES['bank_logotype']['tmp_name'], $target_path)) chmod($target_path , 0644 );
}


//update main data
$licence_recived_date=substr($licence_recived_date,6,4)."-".substr($licence_recived_date,3,2)."-".substr($licence_recived_date,0,2);

$query="
        update  ism_banks
             set  name='".$name."'
                 ,full_name='".$full_name."'
                 ,address='".$address."'
                 ,licence_recived_date='".$licence_recived_date."'
                 ,licence_number='".$licence_number."'
                 ,web_site='".$web_site."'
                 ,history='".$history."'
				 ,head_bio='".$head_bio."'
				 ,stakeholders='".$stakeholders."'
				 ,phone='".$phone."'
        where bank_id=".$id."
       ";
$result=exec_query($query);
if ($result)
  {
  //update statistics
  $stat_query="
        insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
        values('ism_banks',".$id.",1,current_date(),'".$user."','".$name."')";
     //echo $query;
  $result=exec_query($stat_query);
  echo '<div class="info-message">Данные изменены!</div>';
  }


}
// end edit data ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// select the bank data
$query="
        select
                  name
                 ,full_name
                 ,address
                 ,DATE_FORMAT(licence_recived_date, '%d.%m.%Y') licence_recived_date
                 ,licence_number
                 ,city
                 ,web_site
                 ,general_info
                 ,DATE_FORMAT(founded, '%Y') founded
                 ,key_people
                 ,field
                 ,history
                 ,owners_managers
                 ,main_activities
                 ,actives_passives
                 ,strategy
   				 ,head_bio
   				 ,stakeholders
   				 ,phone
        from ism_banks
        where bank_id=".clean_int($id)."
       ";

//echo $query;

$vbank=array();
$rc=sql_stmt($query, 19, $vbank ,1);

        //no data exists
        if ($rc==0)
        {
          header('Location: /404.php');
          exit;
        }

//get list of cities for branches
// select the bank data
/*
$query="
        select
                  id
                  ,desc_ru city
                  ,concat(concat(concat(concat('{id:',id),', name:''),desc_ru),''}') test
        from ism_dictionary
        where  grp=189";
//echo $query;
//exit;

$vcities=array();
$rc=sql_stmt($query, 3, $vcities ,2);

//print_r($vcities['test']);

$cities=implode(",", $vcities['test']);
*/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Банк <?php echo $vbank['name'][0]?></title>
<meta name="Description" content="Банк <?php echo $vbank['name'][0]?>. Депозиты, контакты." >
<meta name="Keywords" content="банк <?php echo $vbank['name'][0]?>,депозиты,вклады,ставка, доход">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
</head>


<body>

<div id="container">
<!-- header -->
<?php
        $selected_menu='deposit';
        include '../includes/header.php';
?>
<form name="edit_form" method="post" enctype="multipart/form-data">

  <div class="sidebar2">

   <div class="title">Рейтинговые оценки</div>
   <?php
   	//check for logo file
   	$logo_file ="../media/images/banks/logotype/" . (int)$id . ".jpg";
   	if (file_exists($logo_file)) { $logo_img='<img src="'.$logo_file.'" width="250" height="130" alt="" />'; }
   	else { $logo_img='';}


  	if ($edit_form)
	{
		 echo 'Загрузить логотип банка(ширина:250px;высота:130px):<input type="file" name="bank_logotype" /><br/>';
	}

    echo $logo_img;

   //S&P
   $query="
           select
                    DATE_FORMAT(t.rating_date,'%d.%m.%Y')                             rating_date
                   ,(select desc_ru from ism_dictionary where id=t.sp_long_foring)    sp_long_foring_c
                   ,(select desc_ru from ism_dictionary where id=t.sp_long_local)     sp_long_local_c
           from ism_sp_rating t
           where   t.imetent_id=".clean_int($id)."
                   and t.rating_date=(select max(rating_date) from ism_sp_rating where imetent_id=t.imetent_id)
          ";
  //echo $query;

  $vsp=array();
  $rc=sql_stmt($query, 3, $vsp ,1);

  if ($rc>0)
  {
  echo '
  <div class="rating-item">
  <strong>Standard&Poor\'s</strong> <span>('.$vsp['rating_date'][0].')</span><br />
	Межд. шкала в ин. вал.: '.$vsp['sp_long_foring_c'][0].'<br />
	Межд. шкала в нац. вал.: '.$vsp['sp_long_local_c'][0].'
  </div>
   ';
  }
   //Fitch Rating
   $query="
           select
                    DATE_FORMAT(t.rating_date,'%d.%m.%Y')                             rating_date
                   ,(select desc_ru from ism_dictionary where id=t.f_long_foring)     f_long_foring_c
                   ,(select desc_ru from ism_dictionary where id=t.f_long_local)     sp_long_local_c
           from ism_fitch_rating t
           where   t.imetent_id=".clean_int($id)."
                   and t.rating_date=(select max(rating_date) from ism_fitch_rating where imetent_id=t.imetent_id)
          ";
  //echo $query;

  $vf=array();
  $rc=sql_stmt($query, 3, $vf ,1);

  if ($rc>0)
  {
  echo '
  <div class="rating-item">
   <strong>Fitch Ratings</strong> <span>('.$vf['rating_date'][0].')</span><br />
  	Межд. шкала в ин. вал.: '.$vf['f_long_foring_c'][0].'
  </div> ';

  }
  //get deposits
  $query="
           select
                 t.deposit_id
                 ,t.name
           from ism_deposits t
           where t.bank_id=".clean_int($id)."
           order by t.name";
  //echo $query;
  $vdeps=array();
  $rc=sql_stmt($query, 2, $vdeps ,2);

  if ($rc>0)
  {
  	 echo '<div class="title">Депозиты</div><ul class="list2">';
  	 for ($i=0;$i<sizeof($vdeps['deposit_id']);$i++)
     {
     	echo '<li><a href="deposit.php?id='.$vdeps['deposit_id'][$i].'" title="'.$vdeps['name'][$i].'">'.$vdeps['name'][$i].'</a></li>';
     }
  	 echo '</ul>';
  }

  ?>
  <!-- end sidebar2 -->
  </div>
  <div class="mainContent">



<div id="tabs">
      <ul>
      <li class="topic"><?php echo $vbank['name'][0]?></li>
        <li class="first"><a href="#fragment-1" title="Общая информация о банке">Общая информация</a></li>
        <li><a href="#fragment-2" title="рейтинги банка">Рейтинги</a></li>
        <!--<li><a href="#fragment-3" title="филиалы и отделения банка">Филиалы</a></li>-->
      </ul>
<div id="fragment-1">
<div class="grey-block search-block">

<script type="text/javascript">
$(function(){
  $.datepicker.setDefaults(
        $.extend($.datepicker.regional["ru"])
  );
  $("#licence_recived_date").datepicker();
});
</script>
<?php
if ($edit_form)
{
?>
<script type="text/javascript">
$(document).ready(function(){

$("#head_bio").htmlbox({
skin:"blue",
toolbars:[["bold","italic","underline","strike","separator","undo","redo","separator","left","center","right","justify","separator","ol","ul","indent","outdent","separator","link","unlink","image"]],
about:false
});

$("#history").htmlbox({
skin:"blue",
toolbars:[["bold","italic","underline","strike","separator","undo","redo","separator","left","center","right","justify","separator","ol","ul","indent","outdent","separator","link","unlink","image"]],
about:false
});

$("#stakeholders").htmlbox({
skin:"blue",
toolbars:[["bold","italic","underline","strike","separator","undo","redo","separator","left","center","right","justify","separator","ol","ul","indent","outdent","separator","link","unlink","image"]],
about:false
});

});
</script>

<?php
}
?>



<?php
if ($edit_form)
{  echo '
	      	<b>Название:</b>&nbsp;<input type=text name=name value="'.$vbank['name'][0].'" size="40"><br />
		  	<b>Полное название:</b>&nbsp;<input type=text name=full_name value="'.$vbank['full_name'][0].'" size="80"><br />
			<b>Номер и дата выдачи лицензии:</b> № <input type=text name=licence_number value="'.$vbank['licence_number'][0].'" size="5"> от <input type=text name=licence_recived_date id=licence_recived_date value="'.$vbank['licence_recived_date'][0].'"><br />
			<b>Адрес головного офиса:</b>&nbsp;<input type=text name=address value="'.$vbank['address'][0].'" size="80"><br />
			<b>Единый номер:</b>&nbsp;<input type=text name=phone value="'.$vbank['phone'][0].'" size="80"><br />
			<br />
			Сайт: <input type=text name=web_site value="'.$vbank['web_site'][0].'" size="40">
	     ';

}
else
{	echo '
	      	<b>Название:</b>&nbsp;'.$vbank['name'][0].'<br />
		  	<b>Полное название:</b>&nbsp;'.$vbank['full_name'][0].'<br />
			<b>Номер и дата выдачи лицензии:</b>&nbsp;№'.$vbank['licence_number'][0].' от '.$vbank['licence_recived_date'][0].'<br />
			<b>Адрес головного офиса:</b>&nbsp;'.$vbank['address'][0].'<br />
			<b>Единый номер:</b>&nbsp;'.$vbank['phone'][0].'<br />
			<br />
			Сайт: <noindex><a href="'.$vbank['web_site'][0].'" rel="nofollow">'.$vbank['web_site'][0].'</a></noindex>
	     ';}

?>
</div>

<?php
//check for head photo
$photo_file ="../media/images/banks/headphoto/" . (int)$id . ".jpg";
if (file_exists($photo_file)) { $photo_img='<img src="'.$photo_file.'" width="104" height="157" alt="" />'; }
else { $photo_img='';}


if ($edit_form)
{	 echo '
			<div class="block1">
			Загрузить фото руководителя(ширина:104px;высота:157px):<input type="file" name="head_photo" /><br/>
			'.$photo_img.'
			<br /><b> Руководитель:</b>
			<textarea id="head_bio" name="head_bio" rows=10 cols=130>'.$vbank['head_bio'][0].'</textarea>
			</div>
 		 ';
}
else
{	if ($vbank['head_bio'][0]!='')
    {     echo '
			<div class="bank-info">
			'.$photo_img.'
			<b> Руководитель:</b>&nbsp;'.$vbank['head_bio'][0].'
			</div>
 		 ';    }
}


?>

<div class="title">Новости и события</div>
<?php
$query="
		  select
		         t.news_id  id
		        ,date_format(t.news_date,'%d.%m.%Y') vdate_format
		        ,t.title
		        ,t.special
		from
		        ism_news t
		where ntype=5
		order by t.news_date desc
		limit 0,5
      ";

$vnews=array();
$rc=sql_stmt($query, 4, $vnews ,2);
?>

<div class="news-list noborder">
<ul>
  <?php
    for ($i=0;$i<sizeof($vnews['id']);$i++)
     {
       echo '
            <li><div>'.$vnews['vdate_format'][$i].'</div><a href="../article.php?id='.$vnews['id'][$i].'&type=news" title="'.$vnews['title'][$i].'">'.$vnews['title'][$i].'</a></li>
          ';
     }
   ?>

</ul>
<a class="more" href="../articles.php?type=news-bank">Архив новостей</a>
</div>

<?php
if ($edit_form)
{	echo '
	       <div class="title">История банка</div>
           <div class="block1">
	       <textarea id="history" name="history" rows=10 cols=130>'.$vbank['history'][0].'</textarea>
	       </div>

   	       <div class="title">Об акционерах</div>
           <div class="block1">
	       <textarea id="stakeholders" name="stakeholders" rows=10 cols=130>'.$vbank['stakeholders'][0].'</textarea>
	       </div>

	     ';
}
else
{	if ($vbank['history'][0]!='')
    {
     echo '
	       <div class="title">История банка</div>
           <div class="block1">'.$vbank['history'][0].'
	       </div>
 		 ';
    }

	if ($vbank['stakeholders'][0]!='')
    {
    echo '
          <div class="title">Об акционерах</div>
          <div class="block1">'.$vbank['stakeholders'][0].'
          </div>
         ';
	}}
?>


<?php
if ($edit_form)
echo '
          <input type="hidden" name="id" value="'.clean_int($id).'">
          <input type="submit"  name="edit" value="'.echoNLS('Изменить','').'"  style=" font-size: 8pt;">
          <input type="reset"   value="'.echoNLS('Отменить','').'"  style=" font-size: 8pt;">
     ';
?>
</form>
</div>

<div id="fragment-2">
<?php
  $type='bank';
  include('../includes/rating.php');
?>
</div>

<!--
<div id="fragment-3">

<form>
<div class="search-block grey-block">
<ul>
<li><div>Название</div><input type="text" name="name"></li>
<li><div>Город</div><input type="text" id="city" name="city"></li>

</ul>
</div>
</form>
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