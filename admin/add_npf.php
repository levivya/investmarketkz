<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/li/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Добавить НПФ</title>
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
<div class="title"><a class="more" href="index.php">Панель администратора</a>Добавить НПФ</div>

<script type="text/javascript">
$(function(){
$.datepicker.setDefaults(
$.extend($.datepicker.regional["ru"])
);
$("#start_date").datepicker();
});
</script>

<?php
echo ' <script language="JavaScript">
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
     </script>';

if (isset($add)) //fund
{
 if ($name=="")
 {
   echo '<div class="error-message">'.echoNLS('Пропущенна обязательное поле "Название"!','').'</div>';
 }
else
 {

 $start_date=($start_date=="")?("NULL"):("'".substr($start_date,6,4)."-".substr($start_date,3,2)."-".substr($start_date,0,2)."'");

 $president=str_replace(" ", "", $president);
 $directors=str_replace(" ", "", $directors);
 $members_of_the_board=str_replace(" ", "", $members_of_the_board);
 $chief_accountant=str_replace(" ", "", $chief_accountant);
 $custodian=str_replace(" ", "", $custodian);
 $license=str_replace(" ", "", $license);
 $address=str_replace(" ", "", $address);
 $phone=str_replace(" ", "", $phone);
 $fax=str_replace(" ", "", $fax);
 $email=str_replace(" ", "", $email);

 $query = "insert into ism_pension_funds(
                                 name
                                 ,company_id
                                 ,status
                                 ,fund_type
                                 ,start_date
                                 ,web_site
                                 ,general_info
                                 ,president
                                 ,directors
								 ,members_of_the_board
								 ,chief_accountant
								 ,custodian
								 ,license
								 ,address
								 ,phone
								 ,fax
								 ,email)
         values('".$name."'
               ,".$company_id."
               ,".$stat_id."
               ,".$type_id."
               ,".$start_date."
               ,'".$web_site."'
               ,'".$general_info."'
               ,'".$president."'
               ,'".$directors."'
			   ,'".$members_of_the_board."'
			   ,'".$chief_accountant."'
			   ,'".$custodian."'
			   ,'".$license."'
			   ,'".$address."'
			   ,'".$phone."'
			   ,'".$fax."'
			   ,'".$email."')";

 //echo $query;

 $result=exec_query($query);
 if ($result)
   {

      //update statistics
     $fund_query="
	                 select
	                           fund_id
	                 from ism_pension_funds
	                 where name='".$name."' and company_id=".$company_id."
	                ";
      $vfund=array();
	  $rc=sql_stmt($fund_query, 1, $vfund ,1);
      $fund_id=$vfund['fund_id'][0];

      $stat_query="
         insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
         values('ism_pension_funds',".$fund_id.",0,current_date(),'".$user."','".$name."')";
      //echo $query;
      $result=exec_query($stat_query);

      //clear values
     $name="";
     $start_date="";
     $web_site="";
     $general_info="";
     $president="";
     $directors="";
	 $members_of_the_board="";
	 $chief_accountant="";
	 $custodian="";
	 $license="";
	 $address="";
	 $phone="";
	 $fax="";
	 $email="";

     echo '<div class="info-message">'.echoNLS('Данные внесены!','').'</div>';
   }

 }

}


//company list
$query="select
            company_id
            ,name
		from
			ism_pension_companies
		order
			by name";
$vcomps=array();
$rc=sql_stmt($query, 2, $vcomps ,2);

if (!isset($comp_id))  {$company_id=$vcomps['company_id'][0] ;}
else {$company_id=$comp_id;}
$CompsMenuString = menu_list($vcomps['name'],$company_id,$vcomps['company_id']);
if (isset($comp_id))
{
$CompsMenuString = '<select name="company_id" DISABLED>'.$CompsMenuString.'</select>
                    <input type=hidden name="company_id" value="'.$comp_id.'">';
}
else
{
$CompsMenuString = '<select name="company_id">'.$CompsMenuString.'</select>';
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

if (!isset($type_id))  $type_id=$vtype['id'];
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

if (!isset($stat_id))  $stat_id=$vstat['id'];
$StatMenuString = menu_list($vstat['name'],$stat_id,$vstat['id']);
$StatMenuString = '<select name="stat_id">'.$StatMenuString.'</select>';

echo'
<form name="add_form" method=post>
<div class="search-block grey-block">
<ul>
  <li><div>'.echoNLS('Название','').'</div><input type=text name=name value="'.$name.'"></li>
  <li><div>'.echoNLS('КУПА','').'</div>'.$CompsMenuString.'</li>
  <li><div>'.echoNLS('Тип','').'</div>'.$TypeMenuString.'</li>
  <li><div>'.echoNLS('Статус','').'</div>'.$StatMenuString.'</li>
  <li><div>'.echoNLS('Номер лицензии','').'</div><input type=text name=license value="'.$license.'"></li>
  <li><div>'.echoNLS('Дата выдачи лицензии','').'</div><input type=text name=start_date id=start_date value="'.$start_date.'"></li>
  <li><div>'.echoNLS('Председатель Правления','').'</div><input type=text name=president value="'.$president.'"></li>
  <li><div>'.echoNLS('Совет директоров','').'</div><input type=text name=directors value="'.$directors.'"></li>
  <li><div>'.echoNLS('Члены Правления','').'</div><input type=text name=members_of_the_board value="'.$members_of_the_board.'"></li>
  <li><div>'.echoNLS('Главный бухгалтер','').'</div><input type=text name=chief_accountant value="'.$chief_accountant.'"></li>
  <li><div>'.echoNLS('Кастодиан','').'</div><input type=text name=custodian value="'.$custodian.'"></li>
  <li><div>'.echoNLS('Адрес','').'</div><input type=text name=address value="'.$address.'"></li>
  <li><div>'.echoNLS('Телефон','').'</div><input type=text name=phone value="'.$phone.'"></li>
  <li><div>'.echoNLS('Факс','').'</div><input type=text name=fax value="'.$fax.'"></li>
  <li><div>'.echoNLS('e-mail','').'</div><input type=text name=email value="'.$email.'"></li>
  <li><div>'.echoNLS('Web-сайт','').'</div><input type=text name=web_site value="'.$web_site.'"></li>
  <li><div>'.echoNLS('Общая информация','').'</div><textarea name=general_info style=" font-size: 8pt;" rows=4 cols=32>'.$general_info.'</textarea></li>
  <li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;
         <span>
         <input type="submit"  name="add" value="'.echoNLS('Добавить','').'">
         <input type="reset"   value="'.echoNLS('Отменить','').'">
         </span>
     </li>
</ul>
</div>
</form>
';

//delete fund
if (isset($delete))
{
 if (isset($fund_id) && $fund_id!="")
 {

     $fund_query = "select
				        name
				    from
						ism_pension_funds
	                where
						fund_id=".$fund_id;

      $vfund=array();
	  $rc=sql_stmt($fund_query, 1, $vfund ,1);
      $name=$vfund['name'][0];


      //delete funds values
      $query = "delete
				from
					ism_pension_fund_value
                where
					fund_id=".$fund_id."";
       $result=exec_query($query);
       //delete fund
       $query = "delete
				 from
					ism_pension_funds
				 where
					fund_id=".$fund_id."";
       $result=exec_query($query);

      //update statistics
      $stat_query="insert into
				       ism_data_statistics (table_name,data_id,action,action_date,editor,comments)
                   values('ism_pension_funds',".$fund_id.",2,current_date(),'".$user."','".$name."')";
      //echo $query;
      $result=exec_query($stat_query);

  echo '<div class="info-message">'.echoNLS('Фонд удален!','').'</div>';

 }

}

if (((isset($grp) && $grp==0)) || !isset($grp))
{
    echo '<div class="info-message">'.echoNLS('У Вас нет доступа к данной странице!','').'</div>';
}
else
{
 $where="";
 if ($grp==1) $where=" where company_id=".$comp_id." ";

 $query="select
			fund_id
            ,name
         from
			ism_pension_funds
		 ".$where."
         order by
			name";

$vfunds=array();
$rc=sql_stmt($query, 2, $vfunds ,2);
if ($rc>0)
{
if (!isset($fund_id))  {$fund_id=$vfunds['fund_id'][0] ;}
$FundMenuString = menu_list($vfunds['name'],$fund_id,$vfunds['fund_id']);
$FundMenuString = '<select name="fund_id" >'.$FundMenuString.'</select>';

echo '<form name="delete_form">
      <div class="search-block grey-block">
      <ul>
          <li><div>'.echoNLS('Удалить Фонд','').'</div>'.$FundMenuString.'&nbsp;&nbsp;<span><input type="submit"  name="delete" value="'.echoNLS('Удалить','').'"></span></li>
      </ul>
      </div>
      </form>
      ';

}
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