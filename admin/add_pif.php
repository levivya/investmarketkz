<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Добавить ПИФ</title>
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
<div class="title"><a class="more" href="index.php">Панель администратора</a>Добавить ПИФ</div>

<script type="text/javascript">
$(function(){
$.datepicker.setDefaults(
$.extend($.datepicker.regional["ru"])
);
$("#start_date").datepicker();

$.datepicker.setDefaults(
$.extend($.datepicker.regional["ru"])
);
$("#registration_date").datepicker();

$.datepicker.setDefaults(
$.extend($.datepicker.regional["ru"])
);
$("#build_end_date").datepicker();


});
</script>


<?php
echo '
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
     </script>';


if (isset($add)) //add fund
{
 if ($name=="")
 {
   echo '<div class="error-message">'.echoNLS('Пропущенна обязательное поле "Название"!','').'</div>';
 }
else
 {

 $fstart_date=($start_date=="")?("NULL"):("'".substr($start_date,6,4)."-".substr($start_date,3,2)."-".substr($start_date,0,2)."'");
 $fregistration_date=($registration_date=="")?("NULL"):("'".substr($registration_date,6,4)."-".substr($registration_date,3,2)."-".substr($registration_date,0,2)."'");
 $fbuild_end_date=($build_end_date=="")?("NULL"):("'".substr($build_end_date,6,4)."-".substr($build_end_date,3,2)."-".substr($build_end_date,0,2)."'");

 $registration_number=($registration_number=="")?("NULL"):(ltrim($registration_number));
 $registration_number=rtrim($registration_number);
 $limit_min_sum=($limit_min_sum=="")?("NULL"):(str_replace(",", ".",$limit_min_sum ));
 $limit_min_sum=str_replace(" ", "",$limit_min_sum);
 $next_min_sum=($next_min_sum=="")?("NULL"):(str_replace(",", ".",$next_min_sum ));
 $next_min_sum=str_replace(" ", "",$next_min_sum);
 $nominal_cost=($nominal_cost=="")?("NULL"):(str_replace(",", ".",$nominal_cost ));
 $nominal_cost=str_replace(" ", "",$nominal_cost);

 $query="
         insert  into ism_funds(
                                 registration_number
                                 ,name
                                 ,company_id
                                 ,status
                                 ,fund_type
                                 ,invest_object
                                 ,start_date
                                 ,registration_date
                                 ,build_end_date
                                 ,limit_min_sum
                                 ,next_min_sum
                                 ,extra_charge
                                 ,discount
                                 ,when_buy_sell
                                 ,fund_life_time
                                 ,nominal_cost
                                 ,mc_bonus
                                 ,cra_bonus
                                 ,web_site
                                 ,general_info
                                 ,fund_expences
                                 )
         values('".$registration_number."'
               ,'".$name."'
               ,".$company_id."
               ,".$stat_id."
               ,".$type_id."
               ,".$obj_id."
               ,".$fstart_date."
               ,".$fregistration_date."
               ,".$fbuild_end_date."
               ,".$limit_min_sum."
               ,".$next_min_sum."
               ,'".$extra_charge."'
               ,'".$discount."'
               ,'".$when_buy_sell."'
               ,'".$fund_life_time."'
               ,".$nominal_cost."
               ,'".$mc_bonus."'
               ,'".$cra_bonus."'
               ,'".$web_site."'
               ,'".$general_info."'
               ,'".$fund_expences."')";

 //echo $query;

 $result=exec_query($query);
 if ($result)
   {

      //update statistics
     $fund_query="
	                 select
	                           fund_id
	                 from ism_funds
	                 where name='".$name."' and company_id=".$company_id."
	                ";
      $vfund=array();
	  $rc=sql_stmt($fund_query, 1, $vfund ,1);
      $fund_id=$vfund['fund_id'][0];

      $stat_query="
         insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
         values('ism_funds',".$fund_id.",0,current_date(),'".$user."','".$name."')";
      //echo $query;
      $result=exec_query($stat_query);

      //clear values
     $registration_number="";
     $name="";
     $start_date="";
     $registration_date="";
     $build_end_date="";
     $limit_min_sum="";
     $next_min_sum="";
     $extra_charge="";
     $discount="";
     $web_site="";
     $general_info="";
     $when_buy_sell="";
     $fund_life_time="";
     $nominal_cost="";
     $mc_bonus="";
     $cra_bonus="";
     $fund_expences="";

    echo '<div class="info-message">'.echoNLS('Данные внесены!','').'</div>';
   }


 }

}


//company list
$query="
          select
                   company_id
                  ,name
          from ism_companies
          order by name

       ";
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
$CompsMenuString = '<select name="company_id" >'.$CompsMenuString.'</select>';
}

//type list
$query="
         select
                   id
                  ,desc_".echoNLS('ru','')." name
          from ism_dictionary
          where grp=".$GRP_TYPE."
          order by name

       ";
$vtype=array();
$rc=sql_stmt($query, 2, $vtype ,2);

if (!isset($type_id))  $type_id=$vtype['id'][0];
$TypeMenuString = menu_list($vtype['name'],$type_id,$vtype['id']);
$TypeMenuString = '<select name="type_id" class="fnt" cols="71">'.$TypeMenuString.'</select>';

//status list
$query="
          select
                   id
                  ,desc_".echoNLS('ru','')." name
          from ism_dictionary
          where grp=".$GRP_STAT."
          order by name

       ";
$vstat=array();
$rc=sql_stmt($query, 2, $vstat ,2);

if (!isset($stat_id))  $stat_id=$vstat['id'][0];
$StatMenuString = menu_list($vstat['name'],$stat_id,$vstat['id']);
$StatMenuString = '<select name="stat_id" class="fnt"  cols="71">'.$StatMenuString.'</select>';

//invest object list
$query="
          select
                   id
                  ,desc_".echoNLS('ru','')." name
          from ism_dictionary
          where grp=".$GRP_OBJ."

       ";
$vobj=array();
$rc=sql_stmt($query, 2, $vobj ,2);

if (!isset($obj_id))  $obj_id=$vobj['id'][0];
$ObjMenuString = menu_list($vobj['name'],$obj_id,$vobj['id']);
$ObjMenuString = '<select name="obj_id" class="fnt" cols="71">'.$ObjMenuString.'</select>';


echo'
<form name="add_form" method=post>
<div class="search-block grey-block">
<ul>
<li><div>'.echoNLS('Название','').'</div><input type=text name=name value="'.$name.'"></li>
<li><div>'.echoNLS('Управляющая компания','').'</div>'.$CompsMenuString.'</li>
<li><div>'.echoNLS('Тип','').'</div>'.$TypeMenuString.'</li>
<li><div>'.echoNLS('Статус','').'</div>'.$StatMenuString.'</li>
<li><div>'.echoNLS('Объект инвестирования','').'</div>'.$ObjMenuString.'</li>
<li><div>'.echoNLS('Фонд работает с','').'</div><input type=text name=start_date id=start_date value="'.$start_date.'"></li>
<li><div>'.echoNLS('Минимальная сумма','').'</div><input type=text name=limit_min_sum value="'.$limit_min_sum.'" onblur="IsNumeric(this)"> первый взнос</li>
<li><div>'.echoNLS('Минимальная сумма','').'</div><input type=text name=next_min_sum value="'.$next_min_sum.'" onblur="IsNumeric(this)"> послед. взносы</li>
<li><div>'.echoNLS('Купить и погасить','').'</div><textarea name=when_buy_sell cols="80">'.$when_buy_sell.'</textarea></li>
<li><div>'.echoNLS('Надбавки','').'</div><textarea name=extra_charge cols="80">'.$extra_charge.'</textarea> (ри покупке пая</li>
<li><div>'.echoNLS('Скидки','').'</div><textarea name=discount cols="80">'.$discount.'</textarea> при погашении пая</li>
<li><div>'.echoNLS('Веб страница','').'</div><input type=text name=web_site value="'.$web_site.'"></li>
<li><div>'.echoNLS('Общая информация','').'</div><textarea name=general_info cols="80">'.$general_info.'</textarea></li>
<li><div>'.echoNLS('НИН','').'</div><input type=text name=registration_number value="'.$registration_number.'">национальный идентификационный номер</li>
<li><div>'.echoNLS('Номинальная стоим-ть','').'</div><input type=text name=nominal_cost value="'.$nominal_cost.'"  onblur="IsNumeric(this)"></li>
<li><div>'.echoNLS('Дата регистрации','').'</div><input type=text name=registration_date name id=registration_date value="'.$registration_date.'"></li>
<li><div>'.echoNLS('Дата окончания фор-я','').'</div><input type=text name=build_end_date id=build_end_date value="'.$build_end_date.'"></li>
<li><div>'.echoNLS('Срок функционирования','').'</div><input type=text name=fund_life_time value="'.$fund_life_time.'"></li>
<li><div>'.echoNLS('Вознаграждения УК','').'</div><textarea name=mc_bonus  cols="80">'.$mc_bonus.'</textarea></li>
<li><div>'.echoNLS('Вознаграждения','').'</div><textarea name=cra_bonus  cols="80">'.$cra_bonus.'</textarea> кастодиану, регистратору и аудитору</li>
<li><div>'.echoNLS('Расходы фонда','').'</div><input type=text name=fund_expences value="'.$fund_expences.'"></li>
<li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="submit"  name="add" value="'.echoNLS('Добавить','').'">&nbsp;<input type="reset"   value="'.echoNLS('Отменить','').'"></span></li>
</ul>
</div>
</form>
                 ';


//delete fund
if (isset($delete))
{

 if (isset($fund_id) && $fund_id!="")
 {

     $fund_query="
	                 select
	                           name
	                 from ism_funds
	                 where fund_id=".$fund_id;

      $vfund=array();
	  $rc=sql_stmt($fund_query, 1, $vfund ,1);
      $name=$vfund['name'][0];


      //delete funds values
      $query="
              delete from ism_fund_value
              where fund_id=".$fund_id."
            ";
       $result=exec_query($query);
       //delete fund
       $query="
               delete from ism_funds
               where fund_id=".$fund_id."
             ";
       $result=exec_query($query);

      //update statistics
      $stat_query="
         insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
         values('ism_funds',".$fund_id.",2,current_date(),'".$user."','".$name."')";
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


 $query="
          select
                   fund_id
                  ,name
          from ism_funds
          ".$where."
          order by name

       ";
$vfunds=array();
$rc=sql_stmt($query, 2, $vfunds ,2);
if ($rc>0)
{
if (!isset($fund_id))  {$fund_id=$vfunds['fund_id'][0] ;}
$FundMenuString = menu_list($vfunds['name'],$fund_id,$vfunds['fund_id']);
$FundMenuString = '<select name="fund_id">'.$FundMenuString.'</select>';

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