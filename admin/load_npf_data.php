<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Загрузка данных</title>
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
<div class="title"><a class="more" href="index.php">Панель администратора</a>Загрузка данных</div>

<?php

if (isset($load))
{
	//$fp = fopen($filename,"r");
	$fp = fopen($_FILES['filename']['tmp_name'],"r");

	//delete old date
	if (isset($delete))
	{
		$query="delete from ism_pension_fund_value where fund_id=".$fund_id;
		$result=exec_query($query);
	}

	while (false != ($line = fgets($fp,4096)))
	{
           //echo $line."<br>";
           $pos = strpos($line, ';');
           $date=substr($line,0,$pos);
           $tmp=substr($line,$pos+1,strlen($line)-$pos);
           $pos2 =strpos($tmp, ';');
           $value=substr($tmp,0,$pos2);
           $value=str_replace(",", ".",$value);
           $value=str_replace(" ", "",$value);
           $asset=substr($tmp,$pos2+1,strlen($tmp)-$pos2);
           $asset=str_replace(",", ".",$asset);
           $asset=str_replace(" ", "",$asset);

           //echo $asset."<br>";
           $date=substr($date,6,4)."-".substr($date,3,2)."-".substr($date,0,2);
           $query="select fund_id from ism_pension_fund_value where fund_id=".$fund_id." and check_date='".$date."'";
           //echo $query."<br>";
           $rc=sql_stmt($query, 1, $v=array() ,2);
           if ($rc>0)
           {
            //update
            $query="update ism_pension_fund_value
                    set value=".$value."
                        ,asset_value=".$asset."
                    where fund_id=".$fund_id." and check_date='".$date."'";
           }
           else
           {
            //insert
             $query="insert into ism_pension_fund_value(fund_id,check_date,value,asset_value)
                     values(".$fund_id.",'".$date."',".$value.",".$asset.")";

           }
          //echo $query."<br>";
          $result=exec_query($query);
	}
	echo '<div class="info-message">'.echoNLS('Данные внесены!','').'</div>';
}

$query="select
			fund_id
			,name
        from
        	ism_pension_funds
		order by
			name";
$vfunds=array();
$rc=sql_stmt($query, 2, $vfunds ,2);

if (!isset($fund_id))  $fund_id=$vfunds['fund_id'][0];
$FundsMenuString = menu_list($vfunds['name'],$fund_id,$vfunds['fund_id']);
$FundsMenuString = '<select class="fnt" name="fund_id"  cols="71">'.$FundsMenuString.'</select>';


echo'
                  <form name="load" enctype=multipart/form-data method=post>
                  <div class="search-block grey-block">
                  <ul>
                  <li><div>'.echoNLS('Фонд','').'</div>'.$FundsMenuString.'</li>
         		  <li><div>'.echoNLS('Очистить','').'</div><input type=checkbox name=delete>'.echoNLS('удалить все данные по фонду перед загрузкой','').'</li>
       			  <li><div>'.echoNLS('Файл','').'</div><input type=file name=filename size=40></li>
          		  <li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;<span><input type=submit name=load value="'.echoNLS('Загрузить','').'"></span></li>
                  </div>
                  </form>
   ';

?>

</div>

<!-- end of main body -->

<!-- footer -->
<?php include '../includes/footer.php';?>
<!-- end #container -->
</div>
</body>
</html>
<?php
 //disconnect  from the database
 disconn($conn);
?>