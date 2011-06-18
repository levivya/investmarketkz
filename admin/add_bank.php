<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/li/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Добавить Банк</title>
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
<div class="title"><a class="more" href="index.php">Панель администратора</a>Добавить Банк</div>

<script type="text/javascript">
$(function(){
  $.datepicker.setDefaults(
        $.extend($.datepicker.regional["ru"])
  );
  $("#licence_recived_date").datepicker();
});
</script>

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

if (isset($add)) //add bank
{
 if ($name=="")
 {
   echo '<div class="error-message">'.echoNLS('Пропущенна обязательное поле "Название"!','').'</div>';
 }
else
 {

 $licence_recived_date=($licence_recived_date!="")?("'".substr($licence_recived_date,6,4)."-".substr($licence_recived_date,3,2)."-".substr($licence_recived_date,0,2)."'"):("NULL");
 $query="
         insert  into ism_banks(
                                name
                               ,full_name
                               ,address
                               ,licence_recived_date
                               ,licence_number
                               ,web_site
                               ,history
                               ,head_bio
                               ,stakeholders
                               ,phone
                               )
         values(
                 '".$name."'
                ,'".$full_name."'
                ,'".$address."'
                ,".$licence_recived_date."
                ,'".$licence_number."'
                ,'".$web_site."'
                ,'".$history."'
                ,'".$head_bio."'
                ,'".$stakeholders."'
                ,'".$phone."'
                )";
 //echo $query;

 $result=exec_query($query);
 if ($result)
   {

      //update statistics
     $bank_query="
	                 select
	                           bank_id
	                 from ism_banks
	                 where name='".$name."'
	                ";
      $vbank=array();
	  $rc=sql_stmt($bank_query, 1, $vbank ,1);
      $bank_id=$vbank['bank_id'][0];

      $stat_query="
         insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
         values('ism_banks',".$bank_id.",0,current_date(),'".$user."','".$name."')";
      //echo $query;
      $result=exec_query($stat_query);


      //clear values

     $name="";
     $full_name="";
     $address="";
     $licence_recived_date="";
     $licence_number="";
     $web_site="";
     $history="";
     $head_bio="";
     $stakeholders="";

    echo '<div class="info-message">'.echoNLS('Данные внесены!','').'</div>';
   }


 }

}


   echo '
<form name="add_form" method=post>
<div class="search-block grey-block">
<ul>
<li><div>'.echoNLS('Название','').'</div><input type=text name=name value="'.$name.'"></li>
<li><div>'.echoNLS('Полное название','').'</div><input type=text name=full_name value="'.$full_name.'"></li>
<li><div>'.echoNLS('Головной офис','').'</div><input type=text name=address value="'.$address.'"></li>
<li><div>'.echoNLS('Единый номер','').'</div><input type=text name=phone value="'.$phone.'"></li>
<li><div>'.echoNLS('Дата выдачи лицензии','').'</div><input type=text name=licence_recived_date id=licence_recived_date value="'.$licence_recived_date.'"></li>
<li><div>'.echoNLS('Номер лицензии','').'</div><input type=text name=licence_number value="'.$licence_number.'"></li>
<li><div>'.echoNLS('Сайт банка','').'</div><input type=text name=web_site value="'.$web_site.'"></li>
</ul>
</div>
<div class="search-block grey-block">
<div>'.echoNLS('История','').'</div><textarea name=history id=history cols=120 rows=8>'.$history.'</textarea></br>
<div>'.echoNLS('Биография руководителя','').'</div><textarea name=head_bio id=head_bio cols=120 rows=8>'.$head_bio.'</textarea></br>
<div>'.echoNLS('Акционеры','').'</div><textarea name=stakeholders id=stakeholders cols=120 rows=8>'.$stakeholders.'</textarea></br>
&nbsp;
  <span>
    <input type="submit"  name="add" value="'.echoNLS('Добавить','').'">
    <input type="reset"   value="'.echoNLS('Отменить','').'">
  </span>
</div>
</form>
';


//delete
if (isset($delete))
{
 if (isset($bank_id) && $bank_id!="")
 {

   $bank_query="
	                 select
	                           name
	                 from ism_banks
	                 where bank_id='".$bank_id."'
	                ";
   $vbank=array();
   $rc=sql_stmt($bank_query, 1, $vbank ,1);
   $name=$vbank['name'][0];

   //delete bank
   $query="
           delete from ism_banks
           where bank_id=".$bank_id."
         ";
  $result=exec_query($query);

   //update statistics
  $stat_query="
        insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
        values('ism_banks',".$bank_id.",2,current_date(),'".$user."','".$name."')";
  //echo $query;
  $result=exec_query($stat_query);


  echo '<div class="info-message">'.echoNLS('Компания удалена!','').'</div>';

 }

}

if (isset($grp) && $grp==2)
{
$query="
          select
                   bank_id
                  ,name
          from ism_banks
          order by name

       ";
$vbanks=array();
$rc=sql_stmt($query, 2, $vbanks ,2);

if (!isset($bank_id))  {$bank_id=$vbanks['bank_id'][0] ;}
$BanksMenuString = menu_list($vbanks['name'],$bank_id,$vbanks['bank_id']);
$BanksMenuString = '<select name="bank_id">'.$BanksMenuString.'</select>';

echo '<br />
      <form name="delete_form">
      <div class="search-block grey-block">
      <ul>
          <li><div>'.echoNLS('Удалить банк','').'</div>'.$BanksMenuString.'&nbsp;&nbsp;<span><input type="submit"  name="delete" value="'.echoNLS('Удалить','').'"></span></li>
      </ul>
      </div>
      </form>
      ';
}
else
{
    echo '<div class="info-message">'.echoNLS('У Вас нет доступа к данной странице!','').'</div>';
}
?>

</div>

<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>