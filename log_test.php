<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Вход для авторизованных пользователей</title>
<meta name="Description" content="Вход для авторизованных пользователей" >
<meta name="Keywords" content="пиф, паевой фонд, выбрать, управляющая компания, ук, доходность">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include 'includes/scripts.php';?>
<script language="javascript">
function setFocus(element){document.getElementById(element).focus();}
</script>
</head>
<body onload="setFocus('login');">
<div id="container">

<?php
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
$selected_menu='main';
include 'includes/header.php';

//remove old user
session_remove("user");
if (isset($user))unset($user);

if (!isset($log_error)) $log_error=0;
$err_mess="";
switch ($log_error) {
                       case 1:  $err_mess='<div class="error-message">Ошибка идентификации! Введенное Вами имя пользователя не существует!</div>';
					   break;
                       case 2:  $err_mess='<div class="error-message">Ошибка идентификации! Вы ввели неверный пароль!</div>';
                       break;
                       case 3:  $err_mess='<div class="error-message">Ваша учетная запись не активированна!</div>';
                       break;
                    }
if (!isset($target_page)) $target_page='';
?>

<div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="banner.php?zid=7624" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
     Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
    <!-- end sidebar2 -->
</div>

<div class="mainContent">
<div class="title">Вход для авторизованных пользователей</div>

<?php echo $err_mess; ?>
<div class="grey-block">
<form name="main_form" action="login.php" target="_self" method="post">
<input type="hidden" name="target_page" value="<?php echo $target_page; ?>">
<div class="search-block">
<ul>
<li>
    <div>email</div>
    <input type="text" name="login" size="20" id="login"> email указанный при регистрации
</li>
<li>
    <div>пароль</div>
    <input type="password" name="passwd" size="20"><a href="forgot_password.php">Забыли пароль?</a>&nbsp;|&nbsp;<a href="registration.php">Регистрация</a>
</li>
<li>
    <div>&nbsp;</div>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><input  class="button" type="button"  onclick="submit();" value="Вход"></span>
</li>
</ul>
При озникновение сложностей с авторизацией, свяжитесь со службой поддержки <a href="mailto:support@invest-market.kz">support@invest-market.kz</a>.
</div>
</form>
</div>



</div>

<!-- end of main body -->

<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>