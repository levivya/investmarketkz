<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Загрузить данные по цене пая из Excel</title>
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
<div class="title"><a class="more" href="index.php">Панель администратора</a>Загрузить данные по цене пая из Excel</div>


<?php
if (isset($load))
{
//$fp = fopen($filename,"r");
$fp = fopen($_FILES['filename']['tmp_name'],"r");

//delete old date
if (isset($delete))
{
$query="delete from ism_fund_value where fund_id=".$fund_id;
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
           $query="select fund_id from ism_fund_value where fund_id=".$fund_id." and check_date='".$date."'";
           //echo $query."<br>";
           $rc=sql_stmt($query, 1, $v=array() ,2);
           if ($rc>0)
           {
            //update
            $query="update ism_fund_value
                    set value=".$value."
                        ,asset_value=".$asset."
                    where fund_id=".$fund_id." and check_date='".$date."'";
           }
           else
           {
            //insert
             $query="insert into ism_fund_value(fund_id,check_date,value,asset_value)
                     values(".$fund_id.",'".$date."',".$value.",".$asset.")";

           }
          //echo $query."<br>";
          $result=exec_query($query);
}
echo '<div class="info-message">'.echoNLS('Данные внесены!','').'</div>';
}

$query="
          select
                   fund_id
                  ,name
          from ism_funds
          order by name

       ";
$vfunds=array();
$rc=sql_stmt($query, 2, $vfunds ,2);

if (!isset($fund_id))  $fund_id=$vfunds['fund_id'][0];
$FundsMenuString = menu_list($vfunds['name'],$fund_id,$vfunds['fund_id']);
$FundsMenuString = '<select name="fund_id">'.$FundsMenuString.'</select>';


echo'
                  <form name="load" enctype=multipart/form-data method=post>
                  <div class="search-block grey-block">
                  <ul>
          			<li><div>'.echoNLS('Выберите фонд','').'</div>'.$FundsMenuString.'</li>
                    <li>'.echoNLS('Удалить все данные по фонду перед загрузкой','').'<input type=checkbox name=delete></li>
          			<li><div>'.echoNLS('Выберите файл','').'</div><input type=file name=filename></li>
                    <li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;<span><input type=submit name=load value="'.echoNLS('загрузить','').'"></li>
                  </ul>
                  </div>
                  </form>
   ';

// INDEXES ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
/**
if (isset($load_index))
{
//$fp = fopen($filename,"r");
$fp = fopen($_FILES['filename_index']['tmp_name'],"r");

//delete old date
if (isset($delete_index))
{
$query="delete from ism_index_value where index_id=".$index_id;
$result=exec_query($query);
}

while (false != ($line = fgets($fp,4096)))
{
           //echo $line."<br>";
           $pos = strpos($line, ';');
           $date=substr($line,0,$pos);
           $value=substr($line,$pos+1,strlen($line)-$pos);
           $value=str_replace(",", ".",$value);
           $value=str_replace(" ", "",$value);
           //echo $asset."<br>";
           $date=substr($date,6,4)."-".substr($date,3,2)."-".substr($date,0,2);
           $query="select index_id from ism_index_value where index_id=".$index_id." and check_date='".$date."'";
           //echo $query."<br>";


           $rc=sql_stmt($query, 1, $v=array() ,2);
           if ($rc>0)
           {
            //update
            $query="update ism_index_value
                    set value=".$value."
                    where index_id=".$index_id." and check_date='".$date."'";
           }
           else
           {
            //insert
             $query="insert into ism_index_value(index_id,check_date,value)
                     values(".$index_id.",'".$date."',".$value.")";

           }
          //echo $query."<br>";
          $result=exec_query($query);

}
echo '
      <div class="info-message">'.echoNLS('Данные внесены!','').'</div>';
}
$query="
          select
                   index_id
                  ,name
          from ism_indexes

       ";
$vindexs=array();
$rc=sql_stmt($query, 2, $vindexs ,2);

if (!isset($index_id))  $index_id=$vindexs['index_id'][0];
$IndexsMenuString = menu_list($vindexs['name'],$index_id,$vindexs['index_id']);
$IndexsMenuString = '<select name="index_id" >'.$IndexsMenuString.'</select>';


echo'
                 <br>
                 <table cellSpacing=1 cellPadding=4 width="100%" border=0 bgcolor="#CCCCCC">
                  <form name="load_index" enctype=multipart/form-data method=post>
                  <tr>
                  <td colspan=2  class="block_title"><strong>'.echoNLS('индексы','').'</strong></td>
                  </tr>
                  <tr bgcolor="white">
          				<td width="50%"   class="fnt">'.echoNLS('Выберите индекс','').'
          				</td>
          			<td width="50%"   class="fnt_g">'.$IndexsMenuString.'
                    </td>
                    </tr>
                    <tr bgcolor="white">
          				<td width="50%"   class="fnt">'.echoNLS('Удалить все данные по индексу перед загрузкой','').'
          				</td>
          			<td width="50%"   class="fnt_g"><input type=checkbox name=delete_index>
                    </td>
                    </tr>
                   <tr bgcolor="white">
          				<td width="50%"   class="fnt">'.echoNLS('Выберите файл с данными','').'
          				</td>
          			<td width="50%"   class="fnt_g"><input type=file name=filename_index size=40  style=" font-size: 8pt;">
                    </td>
                    </tr>
                    <tr bgcolor="white">
          				<td width="100%"   class="fnt" colspan=2><input type=submit name=load_index value="'.echoNLS('загрузить','').'">
                    </td>
                    </tr>
                  </form>
                 </table>
   ';
*/

?>

</div>

<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>