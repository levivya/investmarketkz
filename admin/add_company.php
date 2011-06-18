<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Добавить УК</title>
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
<div class="title"><a class="more" href="index.php">Панель администратора</a>Добавить УК</div>


<?php
if (isset($add)) //add company
{
 if ($name=="")
 {
   echo '<div class="error-message">'.echoNLS('Поле "Название" не может быть пустым!','').'</div>';
 }
else
 {
 $licence_recived_date=($licence_recived_date!="")?("'".substr($licence_recived_date,6,4)."-".substr($licence_recived_date,3,2)."-".substr($licence_recived_date,0,2)."'"):("NULL");
 $query="
         insert  into ism_companies(name,full_name,address,inn,licence_recived_date,licence_number,phone_fax,rating,web_site,general_info)
         values('".$name."','".$full_name."','".$address."','".$inn."',".$licence_recived_date.",'".$licence_number."','".$phone_fax."','".$rating."','".$web_site."','".$general_info."')";
 $result=exec_query($query);
 if ($result)
   {
     //update statistics
     $comp_query="
	                 select
	                           company_id
	                 from ism_companies
	                 where name='".$name."'
	                ";
      $vcomp=array();
	  $rc=sql_stmt($comp_query, 1, $vcomp ,1);
      $comp_id=$vcomp['company_id'][0];

      $stat_query="
         insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
         values('ism_companies',".$comp_id.",0,current_date(),'".$user."','".$name."')";
      //echo $query;
      $result=exec_query($stat_query);

      //clear values
     $name="";
     $full_name="";
   	 $address="";
     $inn="";
     $licence_recived_date="";
     $licence_number="";
     $phone_fax="";
     $rating="";
     $web_site="";
     $general_info="";


     echo '<div class="info-message">'.echoNLS('Данные внесены!','').'</div>';
   }


 }

}


echo '
   <form name="add_form">
   <div class="search-block grey-block">
   <ul>
      <li><div>'.echoNLS('Название','').'</div><input type=text name=name value="'.$name.'"></li>
      <li><div>'.echoNLS('Полное название','').'</div><input type=text name=full_name value="'.$full_name.'"></li>
      <li><div>'.echoNLS('Адрес','').'</div><input type=text name=address value="'.$address.'"></li>
      <li><div>'.echoNLS('Дата выдачи лицензии','').'</div><input type=text name=licence_recived_date value="'.$licence_recived_date.'"></li>
      <li><div>'.echoNLS('Номер лицензии','').'</div><input type=text name=licence_number value="'.$licence_number.'"></li>
      <li><div>'.echoNLS('Тел., факс','').'</div><input type=text name=phone_fax value="'.$phone_fax.'"></li>
      <li><div>'.echoNLS('Сайт компании','').'</div><input type=text name=web_site value="'.$web_site.'"></li>
      <li><div>'.echoNLS('Общая информация','').'</div><textarea name=general_info id=general_info rows=2 cols=80>'.$general_info.'</textarea><br /><br /></li>
      <li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="submit"  name="add" value="'.echoNLS('Добавить','').'">&nbsp;<input type="reset"   value="'.echoNLS('Отменить','').'"></span></li>
  </ul>
  </div>
  </form>';




//delete
if (isset($delete))
{
 if (isset($company_id) && $company_id!="")
 {

   $comp_query="
	                 select
	                           name
	                 from ism_companies
	                 where company_id='".$company_id."'
	                ";
   $vcomp=array();
   $rc=sql_stmt($comp_query, 1, $vcomp ,1);
   $comp_name=$vcomp['name'][0];


  //delete funds values
  $query="
           delete from ism_fund_value
           where fund_id in (select fund_id from ism_funds where company_id=".$company_id.")
         ";
  $result=exec_query($query);
  //delete funds
  $query="
           delete from ism_funds
           where company_id=".$company_id."
         ";
  $result=exec_query($query);
  //delete company
   $query="
           delete from ism_companies
           where company_id=".$company_id."
         ";
  $result=exec_query($query);

  //update statistics
  $stat_query="
        insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
        values('ism_companies',".$company_id.",2,current_date(),'".$user."','".$comp_name."')";
     //echo $query;
     $result=exec_query($stat_query);


  echo '<div class="info-message">'.echoNLS('Компания удалена!','').'</div>';

 }

}

// select companies
if (isset($grp) && $grp==2)
{
$query="
          select
                   company_id
                  ,name
          from ism_companies
          order by name

       ";
$vcomps=array();
$rc=sql_stmt($query, 2, $vcomps ,2);

if (!isset($company_id))  {$company_id=$vcomps['company_id'][0] ;}
$CompsMenuString = menu_list($vcomps['name'],$company_id,$vcomps['company_id']);
$CompsMenuString = '<select name="company_id" class="fnt" cols="71" >'.$CompsMenuString.'</select>';

echo '
     <form name="delete_form">
      <div class="search-block grey-block">
      <ul>
          <li><div>'.echoNLS('Удалить компанию','').'</div>'.$CompsMenuString.'&nbsp;&nbsp;<span><input type="submit"  name="delete" value="'.echoNLS('Удалить','').'"></span></li>
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