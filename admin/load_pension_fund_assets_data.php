<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Загрузить данные по активам\обязательствам НПФ</title>
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
<div class="title"><a class="more" href="index.php">Панель администратора</a>Загрузить данные по активам\обязательствам НПФ</div>


<?php


if (isset($load))
{
//$fp = fopen($filename,"r");
$fp = fopen($_FILES['filename']['tmp_name'],"r");

$data=array();
$i=0;
while (false != ($line = fgets($fp,4096)))
{
           $data[$i]=split(';',$line);

           if ($i>0)
           {
             //update table
           	 $query='update ism_pension_fund_value set capital='.$data[$i][3].',liability='.$data[$i][4].' where fund_id='.$data[$i][1].' and  check_date=\''.substr($data[$i][2],6,4).'-'.substr($data[$i][2],3,2).'-'.substr($data[$i][2],0,2).'\'';
             $result=exec_query($query);
             //echo $query."<br>";
           }

           $i++;
}




echo '<div class="info-message">'.echoNLS('Данные внесены!','').'</div>';
}

echo'
                  <form name="load" enctype=multipart/form-data method=post>
                  <div class="search-block grey-block">
                  <ul>
          			<li><div>'.echoNLS('Выберите файл','').'</div><input type=file name=filename></li>
                    <li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;<span><input type=submit name=load value="'.echoNLS('загрузить','').'"></li>
                  </ul>
                  </div>
                  </form>
   ';


?>

</div>

<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>