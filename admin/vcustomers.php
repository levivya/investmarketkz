<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Пользователи системы</title>
<meta name="Description" content="Панель администратора" >
<meta name="Keywords" content="">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
<script type="text/javascript">
$(function(){
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

<script type="text/javascript">
function delete_user(user_id)
{  //alert(user_id);
  document.getElementById("user_id").value=user_id;
  document.forms["mainform"].submit();
}
</script>

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
<div class="title"><a class="more" href="index.php">Панель администратора</a>Управление пользователями</div>

<?php

if (isset($delete_user_id))
{
  $query="delete from ism_users where user_id=".$delete_user_id;
  $result=exec_query($query);
  $query="delete from ism_customers where user_id=".$delete_user_id;
  $result=exec_query($query);
  if ($result)     echo '<div class="info-message">'.echoNLS('Пользователь удален!','').'</div>';
}



if ((isset($grp) && $grp==2))
{
$query="
         select  u.user_id
                ,u.user_name
                ,DATE_FORMAT(u.registration_date,'%d.%m.%Y') reg_date
                ,c.first_name
                ,c.last_name
                ,u.ulock
                ,u.last_login
                ,u.visit
         from ism_users u, ism_customers c
         where u.user_id=c.user_id
               and u.ugroup=4
         order by u.user_id  desc
       ";

//echo $query;
//die();
$vcustomers=array();
$rc=sql_stmt($query, 8, $vcustomers ,2);


if ($rc>0)
{

echo '
<table  id="data" class="tab-table">
<thead>
       <tr>
          <th>'.echoNLS('V-счет','').'</th>
          <th>'.echoNLS('Имя','').'</th>
          <th>'.echoNLS('Логин','').'</th>
          <th>'.echoNLS('Зарегистрирован','').'</th>
          <th>'.echoNLS('Активация','').'</th>
          <th>'.echoNLS('Последний вход','').'</th>
          <th>'.echoNLS('Посещений','').'</th>
          <th></th>
        </tr>
</thead>
</tbody>

 ';

for ($i=0;$i<sizeof($vcustomers['user_id']);$i++)
   {
   echo '
     <tr>
          <td>'.$vcustomers['user_id'][$i].'</td>
          <td>'.$vcustomers['first_name'][$i].'</td>
          <td>'.$vcustomers['user_name'][$i].'</td>
          <td>'.$vcustomers['reg_date'][$i].'</td>
          <td>'.($str3=($vcustomers['ulock'][$i]==0)?('<a href="../conf_reg.php?vuser_id='.$vcustomers['user_id'][$i].'&act=conf">'.echoNLS('Активировать','').'</a>'):(echoNLS('Активированно',''))).'</td>
          <td>'.$vcustomers['last_login'][$i].'</td>
          <td>'.$vcustomers['visit'][$i].'</td>
          <td><a href="vcustomers.php?delete_user_id='.$vcustomers['user_id'][$i].'" ><img src="../media/images/validno.png"></a></td>
      </tr>
      ';
  }

  echo '</tbody></table>';
}
else
{
  echo "Нет данных.";
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