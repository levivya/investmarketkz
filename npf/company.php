<?php
        include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");

        // Connecting, selecting database
        $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

        //check for edit rights
        $edit_form=false;

		if (isset($grp) && $grp==1 && isset($id))  //if belong to company
		{
		     if (isset($company_id) && $company_id==$id) {$edit_form=true;}
		     if (!isset($company_id)){$edit_form=true;}
		}

		if (isset($grp) && $grp==2) //if admin
		{$edit_form=true;}

		if (isset($company_id)) $id=$company_id;

        //update company data
		if (isset($edit))
		{
				$query = "update
				 	ism_pension_companies
		          set
		          	name='".$name."'
	                ,director='".$director."'
	                ,address='".$address."'
	                ,phone='".$phone."'
	                ,fax='".$fax."'
	                ,email='".$email."'
	                ,web_site='".$web_site."'
	                ,general_info='".$general_info."'
	        	where
	        		company_id=".$id."";
		$result=exec_query($query);
		if ($result)
		{
		  //update statistics
		  $stat_query = "insert into
		  				  		ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
		        				values('ism_pension_companies',".$id.",1,current_date(),'".$user."','".$name."')";
		  $result=exec_query($stat_query);

    	  echo '<div class="info-message">Данные изменены!</div>';
		  }

		}



		//get company data
		$query="select
			 	name
	            ,director
	            ,address
	            ,phone
	            ,fax
				,email
	            ,web_site
	            ,general_info
	       	  from
	       	  	ism_pension_companies
	          where
	          	company_id=".clean_int($id);

		$vcomp=array();
		$rc=sql_stmt($query, 8, $vcomp ,1);

        //no data exists
        if ($rc==0)
        {
          header('Location: /404.php');
          exit;
        }



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Компания управляющая пенсионными активами (КУПА):<?php echo $vcomp['name'][0];?></title>
<meta name="Description" content="Компания управляющая пенсионными активами (КУПА)" >
<meta name="Keywords" content="нпф, пенсионный фонд, выбрать, управляющая компания, купа, доходность">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
</head>


<body>
<div id="container">
<!-- header -->
<?php
        $selected_menu='npf';
        include '../includes/header.php';

?>

<!-- main body -->
  <div class="sidebar2">
  <?php
  //get company funds
  $query="
           select
                 t.fund_id
                 ,t.name
           from ism_pension_funds t
           where t.company_id=".clean_int($id)."
           order by t.name";
  //echo $query;
  $vfunds=array();
  $rc=sql_stmt($query, 2, $vfunds ,2);

  if ($rc>0)
  {  	 echo '<div class="title">Пенсионные фонды</div><ul class="list2">';
  	 for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
     {     	echo '<li><a href="npf.php?id='.$vfunds['fund_id'][$i].'">'.$vfunds['name'][$i].'</a></li>';
     }
  	 echo '</ul>';
  }

  ?>
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="../banner.php" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
     Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
    <!-- end sidebar2 -->
  </div>

<div class="mainContent">
<div id="tabs">
      <ul>
        <li class="topic">УК <?php echo $vcomp['name'][0];?></li>
        <li class="first"><a href="#fragment-1">Общая информация</a></li>
        <!--<li><a href="#fragment-2">Аналитика</a></li>-->
      </ul>
  <div id="fragment-1">
<?php
if (!$edit_form)
 {
  echo '
         <table class="tab-table">
		  <tr class="colored">
		    <td>Название</td>
		    <td>'.htmlspecialchars($vcomp['name'][0]).'</td>
		  </tr>
		  <tr>
          	<td>Первый руководитель</td>
            <td>'.$vcomp['director'][0].'</td>
	      </tr>
	      <tr class="colored">
	          <td>Адрес</td>
	          <td>'.$vcomp['address'][0].'</td>
	      </tr>
	      <tr>
	          <td>Телефон</td>
	          <td>'.$vcomp['Phone'][0].'</td>
	      </tr>
	      <tr class="colored">
	          <td>Факс</td>
	          <td>'.$vcomp['fax'][0].'</td>
	      </tr>
	      <tr>
	          <td>E-mail</td>
	          <td>'.$vcomp['email'][0].'</td>
	      </tr>
	      <tr class="colored">
	          <td>Сайт компании</td>
	          <td><a href="'.$vcomp['web_site'][0].'">'.$vcomp['web_site'][0].'</a></td>
	      </tr>
	      <tr>
	          <td>Дополнительно</td>
	          <td>'.$vcomp['general_info'][0].'</td>
	      </tr>
		  </table>
      ';
 }
 else
 {  echo '
        <form name="edit_form" method="post">
        <input type=hidden name=id value="'.$id.'">

         <table class="tab-table">
		  <tr class="colored">
		    <td>Название</td>
		    <td><input type=text name=name value="'.htmlspecialchars($vcomp['name'][0]).'"></td>
		  </tr>
		  <tr>
          	<td>Первый руководитель</td>
            <td><input type=text name=director value="'.$vcomp['director'][0].'"></td>
	      </tr>
	      <tr class="colored">
	          <td>Адрес</td>
	          <td><input type=text name=address value="'.$vcomp['address'][0].'"></td>
	      </tr>
	      <tr>
	          <td>Телефон</td>
	          <td><input type=text name=phone value="'.$vcomp['phone'][0].'"></td>
	      </tr>
	      <tr class="colored">
	          <td>Факс</td>
	          <td><input type=text name=fax value="'.$vcomp['fax'][0].'"></td>
	      </tr>
	      <tr>
	          <td>E-mail</td>
	          <td><input type=text name=email value="'.$vcomp['email'][0].'"></td>
	      </tr>
	      <tr class="colored">
	          <td>Сайт компании</td>
	          <td><input type=text name=web_site value="'.$vcomp['web_site'][0].'"></td>
	      </tr>
	      <tr>
	          <td>Дополнительно</td>
	          <td><textarea name=general_info rows=4 cols=80>'.$vcomp['general_info'][0].'</textarea></td>
	      </tr>
		  <tr>
	          <td></td>
	          <td>  <span>
	                <input type="submit"  name="edit" value="Изменить">
          			<input type="reset"   value="Отменить">
          			</span>
              </td>
	      </tr>
	      </table>
		  </form>
      ';
 }
?>
  </div>
<!--
  <div id="fragment-2">
<?php
//+++++++++++++++++++++ Documents +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if (isset($delete_doc))
{
  $str="delete from ism_documents where id=".$delete_doc_id;
  $result=exec_query($str);
}


if(isset($_POST['upload_doc']) && $_FILES['userfile']['size'] > 0)
{
$fileName = $_FILES['userfile']['name'];
$tmpName  = $_FILES['userfile']['tmp_name'];
$fileSize = $_FILES['userfile']['size'];
$fileType = $_FILES['userfile']['type'];

$fp      = fopen($tmpName, 'r');
$content = fread($fp, filesize($tmpName));
$content = addslashes($content);
fclose($fp);

if(!get_magic_quotes_gpc())
{
    $fileName = addslashes($fileName);
}

$str="insert into ism_documents(name,type,size,content,company_id,attached_date) values('".$fileName."','".$fileType."','".$fileSize."','".$content."',".$id.",CURDATE())";
  //echo $str;
$result=exec_query($str);

echo "<br>Документ $fileName загружен<br>";
}


$query="
          select
                    id
                   ,name
                   ,DATE_FORMAT(attached_date,'%d.%m.%Y') attached_date_f
          from ism_documents
          where company_id=".$id."
          order by attached_date desc
       ";

$vdocs=array();
$rc=sql_stmt($query, 3, $vdocs ,2);

if ($edit_form == true)
 {
   echo '<div>
        <ul>
        <li>
        <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="15000000">
		<input name="userfile" type="file" id="userfile">
		<input name="upload_doc" type="submit" class="box" id="upload" value="Загрузить">
        </form>
        </li>
         ';




if (!isset($delete_doc)) $delete_doc_id=$vdocs['id'][0];

$DocMenuString = menu_list($vdocs['name'],$delete_doc_id,$vdocs['id']);
$DocMenuString = '<select name="delete_doc_id" class="fnt" cols="71" >'.$DocMenuString.'</select>';

 echo ' <li>
        <form name="dd" method="post">
        '.$DocMenuString.'<input name="delete_doc" type="submit" value="Удалить">
        </form>
        </li>
        </ul>
        </div>';
}

if ($rc>0)
{
echo '<table class="tab-table">';

for ($i=0;$i<sizeof($vdocs['id']);$i++)
{
  $class=(fmod(($i),2)==0)?('colored'):(' ');

   echo '<tr class='.$class.'>
              <td> '.$vdocs['attached_date_f'][$i].'&nbsp;<a href="../document.php?id='.$vdocs['id'][$i].'">'.$vdocs['name'][$i].' </a></td>
         </tr>';
}
echo '</table>';

}
else
{
 echo '<div class="info-message">Нет данных!</div>';
}

?>
  </div>
</div>

</div>
-->

<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>