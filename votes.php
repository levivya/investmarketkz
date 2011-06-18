<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Голосование</title>
<meta name="Description" content="Голосование, опросы и мнения." >
<meta name="Keywords" content="голосование, опросы, мнения">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include 'includes/scripts.php';?>
</head>

<body>
<div id="container">
<!-- header -->
<?php
        // Connecting, selecting database
        $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
        $selected_menu='main';
        include 'includes/header.php';
?>
<!-- main body -->

  <div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="banner.php?zid=7624" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
                      Ваш браузер не поддерживает плавающие фреймы!</iframe>
    <!--<a href="#"><img src="media/images/banner2.gif" width="240" height="399" alt="img" /></a> -->
    </div>
    <!-- end sidebar2 -->
  </div>

<div class="sidebar1">

<?php
exec_query("SET NAMES cp1251");

$query = "SELECT
			 id
			,pollerTitle
		  FROM  poller
		  ORDER BY id DESC";
$polles=array();
$rc=sql_stmt($query, 2, $polles, 2);
exec_query("SET NAMES utf8");


if (!isset($pollerId))$pollerId=$polles['id'][0];

echo '
        <div class="title">Голосование</div>
        <iframe src="poller/ajax-poller.php?pollerId='.$pollerId.'" name="poller" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="340px" height="300px">
                      Ваш браузер не поддерживает плавающие фреймы!</iframe>
   ';
?>
</div>
<div class="sidebar1">

<?php
$poller_list='<div class="title">Другие опросы</div><ul class="list">';
for ($i=0;$i<sizeof($polles['id']);$i++)
{
	$poller_list.='<li><a href="votes.php?pollerId='.$polles['id'][$i].'">'.$polles['pollerTitle'][$i].'</a></li>';
}
$poller_list.='</ul>';
echo $poller_list;
?>

</div>

<!-- end of main body -->
<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>