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
		$licence_recived_date=substr($licence_recived_date,6,4)."-".substr($licence_recived_date,3,2)."-".substr($licence_recived_date,0,2);
		$query="
		        update  ism_companies
		             set  name='".$name."'
		                 ,full_name='".$full_name."'
		                 ,address='".$address."'
		                 ,licence_recived_date='".$licence_recived_date."'
		                 ,licence_number='".$licence_number."'
		                 ,phone_fax='".$phone_fax."'
		                 ,web_site='".$web_site."'
		                 ,general_info='".$general_info."'
		        where company_id=".$id."
		       ";
		//echo $query;
		$result=exec_query($query);
		if ($result)
		  {

		  //update statistics
		  $stat_query="
		        insert  into ism_data_statistics(table_name,data_id,action,action_date,editor,comments)
		        values('ism_companies',".$id.",1,current_date(),'".$user."','".$name."')";
		     //echo $query;
		  $result=exec_query($stat_query);
    	  echo '<div class="info-message">Данные изменены!</div>';
		  }

		}



		//get company data
		$query="select
		                  name
		                 ,full_name
		                 ,address
		                 ,inn
		                 ,DATE_FORMAT(licence_recived_date, '%d.%m.%Y') licence_recived_date
		                 ,licence_number
		                 ,phone_fax
		                 ,rating
		                 ,web_site
		                 ,general_info
        	  from ism_companies
              where company_id=".clean_int($id);

		$vcomp=array();
		$rc=sql_stmt($query, 10, $vcomp ,1);

		//no data exists
        if ($rc==0)
        {
          header('Location: /404.php');
          exit;
        }


		//get invest-market.kz rating
		$query="
		         select
		                  t.rating_id
		                 ,(select desc_ru from ism_dictionary where id=t.rating_id) desc_ru
		                 ,(select desc_long_ru from ism_dictionary where id=t.rating_id) desc_long_ru
		                 ,DATE_FORMAT(rating_date, '%d.%m.%Y') rating_date
		         from ism_company_rating t
		         where t.company_id = ".clean_int($id)."
		               and t.rating_date=(select max(rating_date) from ism_company_rating where company_id=".clean_int($id).")
		       ";
		//echo $query;
        $vcomp_r=array();
		$rc2=sql_stmt($query, 4, $vcomp_r ,1);

		if  ($rc2>0) $rating_string=$vcomp_r['desc_ru'][0].' ('.$vcomp_r['desc_long_ru'][0].') от '.$vcomp_r['rating_date'][0].' г. ';
		else $rating_string='нет данных';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Управляющая компания (УК):<?php echo $vcomp['name'][0];?></title>
<meta name="Description" content="Управляющая компания" >
<meta name="Keywords" content="пиф, паевой фонд, выбрать, управляющая компания, ук, доходность">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include '../includes/scripts.php';?>
</head>


<body>
<div id="container">
<!-- header -->
<?php
        $selected_menu='pif';
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
           from ism_funds t
           where t.company_id=".clean_int($id)."  and t.status!=".$TSTATUS_DELETED."
           order by t.name";
  //echo $query;
  $vfunds=array();
  $rc=sql_stmt($query, 2, $vfunds ,2);

  if ($rc>0)
  {  	 echo '<div class="title">Паевые фонды</div><ul class="list2">';
  	 for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
     {     	echo '<li><a href="pif.php?id='.$vfunds['fund_id'][$i].'">'.$vfunds['name'][$i].'</a></li>';
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
        <li><a href="#fragment-2">Аналитика</a></li>
      </ul>
  <div id="fragment-1">
<?php
if (!$edit_form)
 {
  echo '
         <table class="tab-table">
		  <tr class="colored">
		    <td>Название</td>
		    <td>'.$vcomp['name'][0].'</td>
		  </tr>
		  <tr>
          	<td>Полное название</td>
            <td>'.$vcomp['full_name'][0].'</td>
	      </tr>
	      <tr class="colored">
	          <td>Адрес</td>
	          <td>'.$vcomp['address'][0].'</td>
	      </tr>
	      <tr>
	          <td>Дата выдачи лицензии
	          </td>
	          <td>'.$vcomp['licence_recived_date'][0].'</td>
	      </tr>
	      <tr class="colored">
	          <td>Номер лицензии</td>
	          <td>'.$vcomp['licence_number'][0].'</td>
	      </tr>
	      <tr>
	          <td>Тел., факс</td>
	          <td>'.$vcomp['phone_fax'][0].'</td>
	      </tr>
	       <tr class="colored">
	          <td>Рейтинг Invest-Market.kz</td>
	          <td>'.$rating_string.'</td>
	      </tr>
	      <tr>
	          <td>Сайт компании</td>
	          <td><noindex><a href="'.$vcomp['web_site'][0].'" rel="nofollow">'.$vcomp['web_site'][0].'</a></noindex></td>
	      </tr>
	      <tr class="colored">
	          <td>Дополнительно</td>
	          <td>'.$vcomp['general_info'][0].'</td>
	      </tr>
		  </table>
      ';
 }
 else
 {  echo '
         <script type="text/javascript">
		$(function(){
  				$.datepicker.setDefaults(
        		$.extend($.datepicker.regional["ru"])
  		);
  		$("#licence_recived_date").datepicker();
		});
		</script>

         <form name="edit_form" method="post">
         <table class="tab-table">
		  <tr class="colored">
		    <td>Название</td>
		    <td><input type=text name=name value="'.$vcomp['name'][0].'"></td>
		  </tr>
		  <tr>
          	<td>Полное название</td>
            <td><input type=text name=full_name value="'.$vcomp['full_name'][0].'"></td>
	      </tr>
	      <tr class="colored">
	          <td>Адрес</td>
	          <td><input type=text name=address value="'.$vcomp['address'][0].'"></td>
	      </tr>
	      <tr>
	          <td>Дата выдачи лицензии
	          </td>
	          <td><input id="licence_recived_date" name="licence_recived_date" value="'.$vcomp['licence_recived_date'][0].'" /></td>
	      </tr>
	      <tr class="colored">
	          <td>Номер лицензии</td>
	          <td><input type=text name=licence_number value="'.$vcomp['licence_number'][0].'"></td>
	      </tr>
	      <tr>
	          <td>Тел., факс</td>
	          <td><input type=text name=phone_fax value="'.$vcomp['phone_fax'][0].'"></td>
	      </tr>
	       <tr class="colored">
	          <td>Рейтинг Invest-Market.kz</td>
	          <td>'.$rating_string.'</td>
	      </tr>
	      <tr>
	          <td>Сайт компании</td>
	          <td><input type=text name=web_site value="'.$vcomp['web_site'][0].'"></td>
	      </tr>
	      <tr class="colored">
	          <td>Дополнительно</td>
	          <td><textarea name=general_info rows=4 cols=79>'.$vcomp['general_info'][0].'</textarea></td>
	      </tr>
		  <tr>
	          <td></td>
	          <td>
	                <input type="submit"  name="edit" value="Изменить">
          			<input type="reset"   value="Отменить">
              </td>
	      </tr>
	      </table>
		  </form>
      ';
 }
?>
  </div>
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
   echo '
        <form method="post" enctype="multipart/form-data">
        <div class="search-block grey-block">
        <ul>
        <li>
        <div>'.echoNLS('Файл','').'</div><input type="hidden" name="MAX_FILE_SIZE" value="15000000">
		<input name="userfile" type="file" id="userfile">
		</li></li>
		<div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;<span><input name="upload_doc" type="submit" class="box" id="upload" value="Загрузить"></span>
        </li>
        </ul>
        </div>
        </form>
         ';




if (!isset($delete_doc)) $delete_doc_id=$vdocs['id'][0];

$DocMenuString = menu_list($vdocs['name'],$delete_doc_id,$vdocs['id']);
$DocMenuString = '<select name="delete_doc_id" class="fnt" cols="71" >'.$DocMenuString.'</select>';

 echo '
        <form name="dd" method="post">
        <div class="search-block grey-block">
        <ul>
        <li><div>'.echoNLS('Удалить документ','').'</div>'.$DocMenuString.'<span>&nbsp;&nbsp;<input name="delete_doc" type="submit" value="Удалить"></span></li>
        </ul>
        </div>
        </form>
';
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
<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>