<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Вопросы консультанту</title>
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
<div class="title"><a class="more" href="index.php">Панель администратора</a>Вопросы консультанту</div>

<?php
if ((isset($grp) && $grp==2))
{

switch ($page_type) {
case 1:
   $where=" status=".$TSTATUS_REQUESTED;
   break;
case 2:
   $where=" status=".$TSTATUS_INPROCESS;
   break;
case 3:
   $where=" status=".$TSTATUS_COMPLETED;
   break;
}



$query="
         select  q.id
                ,ifnull(q.subject,'no subject') subject
                ,DATE_FORMAT(q.post_date,'%d.%m.%Y') post_date
                ,q.status
                ,(CASE q.status WHEN ".$TSTATUS_REQUESTED." THEN 'требует ответа' WHEN ".$TSTATUS_INPROCESS." THEN 'в обработке' ELSE 'выполнена' END) status_caption
                ,ifnull(u.user_name,q.email) user_name
         from ism_questions q left join ism_users u on q.user_id=u.user_id
         order by id desc
       ";



//echo $query;
//die();
$vquestions=array();
$rc=sql_stmt($query, 6, $vquestions ,2);


if ($rc>0)
{

echo '
<table  id="data" class="tab-table">
<thead>
       <tr>
         <th>'.echoNLS('№','').'</th>
         <th>'.echoNLS('Тема','').'</th>
         <th>'.echoNLS('Статус','').'</th>
         <th>'.echoNLS('E-mail пользователя','').'</th>
         <th>'.echoNLS('Дата','').'</th>
      </tr>
</thead>
</tbody>
 ';


for ($i=0;$i<sizeof($vquestions['id']);$i++)
   {

   if ($vquestions['status'][$i]==$TSTATUS_REQUESTED)    $vquestions['status_caption'][$i]='<font color="red">'.$vquestions['status_caption'][$i].'</font>';

   echo '
     <tr>
          <td>'.$vquestions['id'][$i].'</td>
          <td><a href="customer_question.php?id='.$vquestions['id'][$i].'">'.$vquestions['subject'][$i].'</a></td>
          <td>'.$vquestions['status_caption'][$i].'</td>
          <td>'.$vquestions['user_name'][$i].'</td>
          <td>'.$vquestions['post_date'][$i].'</td>
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