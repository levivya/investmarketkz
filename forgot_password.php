<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Востановление пароля</title>
<meta name="Description" content="Востановление пароля" >
<meta name="Keywords" content="пиф, паевой фонд, выбрать, управляющая компания, ук, доходность">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include 'includes/scripts.php';?>
</head>


<body>
<div id="container">

<?php
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
$selected_menu='main';
include 'includes/header.php';
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
<div class="title">Востановление пароля</div>

<?php

//create random password
function createRandomPassword() {

    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;

    while ($i <= 7) {

        $num = rand() % 33;

        $tmp = substr($chars, $num, 1);

        $pass = $pass . $tmp;

        $i++;
    }
    return $pass;
}

//check login name - must be valid e-mail address
echo '
	 <script language="JavaScript1.2">

     var find_result
	 function form_email(){
	 	var str=document.reg.name.value
	 	var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
	 	if (filter.test(str))
	 		find_result=true
	 	else{
	 		alert("'.echoNLS('Введено неверное имя! Введите существуещий e-mail адрес!','').'")
	 		find_result=false
	 		}
	 	return (find_result)
	 }

     function checkbae(){
	 	if (document.layers||document.getElementById||document.all)
	 	return form_email()
	 	else
	 	return true
	 }

	 </script>
     ';


function print_form()
{
global  $v_random_num;
echo '
       <form name="remind" method="post" onSubmit="return checkbae()" >
       <div class="grey-block">
       '.echoNLS('Чтобы начать восстановление пароля, введите свое имя пользователя Invest-Market.kz (ваш e-mail).','').'
       <div class="search-block">
       <ul><li><div>'.echoNLS('Имя пользователя','').'</div><input name="name" size=35>&nbsp;&nbsp;<span><input  class="button" type="submit" name="remind" value="'.echoNLS('Востановить','').'"></span></li>
       </ul>
       </div>
       </div>
       </form>
     ';
}

//the form has been submited
if (isset($remind))
   {

       $correct=true;

       //check that such user doesn't exist in system
       $query="
        select
                  password
        from ism_users
        where    user_name='".$name."'
        ";

        //echo  $query;

       $v_user=array();
       $rc=sql_stmt($query, 1, $v_user ,1);

        if ($rc == 0 && $correct)
       {
         $correct=false;
         $err_mess=echoNLS('Указанный электронный адрес не существует!','');
       }


       //if everything correct
       if  ($correct)
       {

       //generate new password
       $new_password=createRandomPassword();
       $crypt_new_password=crypt($new_password);

       //echo   "<b>".$new_password."</b><br>";

       //save new passord in the system
       $query = "
                update ism_users u
         	    set password='".$crypt_new_password."'
         	    where user_name='".$name."'";
       $result=exec_query($query);


	   //send message
		$mail           = new PHPMailer();
		$mail->From     = "admin@invest-market.kz";
		$mail->FromName = "Invest-Market.kz";
		$mail->Subject  = "Восстановление пароля - Invest-Market.kz";

		$mail->Body    = "Уважаемый Пользователь,<br><br>Ваш логин: ".$name." <br><br>Пароль: ".$new_password."<br><br>С уважением, <br>Служба поддержки.<br><br>www.invest-market.kz <br>support@invest-market.kz";
		$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

		$mail->AddAddress($name, "");

	    if($mail->Send())
	   {
	       echo '<div class="info-message">Новый пароль был выслан на указанный e-mail!</div>';
	   }
       else
       {
        echo '<div class="error-message">'.$err_mess.'</div>';
         print_form();
        }

   }
   }
else
{
//print registration form
print_form();

}
?>
<br />
</div>


<!-- end of main body -->

<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>