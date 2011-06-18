<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Загрузить данные по структуре фонда</title>
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
<div class="title"><a class="more" href="index.php">Панель администратора</a>Загрузить данные структуре фонда</div>


<?php

if (!isset($type)) $type='pif';

if ($type=='pif')
{ $tab1='ism_fund_structure';
 $tab2='ism_fund_structure_item';}
else
{ $tab1='ism_pension_fund_structure';
 $tab2='ism_pension_fund_structure_item';
}

if (isset($load))
{
//$fp = fopen($filename,"r");
$fp = fopen($_FILES['filename']['tmp_name'],"r");

$data=array();
$i=0;
while (false != ($line = fgets($fp,4096)))
{
           $data[$i]=split(';',$line);

           if ($i>1)
           {
             //create structure
           	 $query1='insert into '.$tab1.'(fund_id,structure_date) values('.$data[$i][1].',\''.substr($data[$i][2],6,4).'-'.substr($data[$i][2],3,2).'-'.substr($data[$i][2],0,2).'\')';
             $result=exec_query($query1);

             //get strucure_id
             $query2='select structure_id from '.$tab1.' where fund_id='.$data[$i][1].' and structure_date=\''.substr($data[$i][2],6,4).'-'.substr($data[$i][2],3,2).'-'.substr($data[$i][2],0,2).'\'';
             $vstruc_id=array();
             $rc=sql_stmt($query2, 1, $vstruc_id ,1);

             //load items
           	 for ($j=3;$j<sizeof($data[$i])-1;$j++)
               {
                  if ($data[$i][$j]!='')
                  {
                   $query3='insert into '.$tab2.'(structure_id,item,volume) values('.$vstruc_id['structure_id'][0].','.$data[1][$j].','.str_replace(',','.',$data[$i][$j]).')';
                   $result=exec_query($query3);
                  }
               }
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