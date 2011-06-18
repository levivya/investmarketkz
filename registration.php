<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Регистрация</title>
<meta name="Description" content="Регистрация" >
<meta name="Keywords" content="регистрация, v-счет">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include 'includes/scripts.php';?>
<script type="text/javascript" src="./scripts/jquery.showpassword.js"></script>
<script language="javascript">
function isValidEmailAddress(emailAddress) {
var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
return pattern.test(emailAddress);
}

$(document).ready(function() {

$('#pass').showPassword();

$("#name").keyup(function(){
var email = $("#name").val();

if(email != 0)
{
if(isValidEmailAddress(email))
{

$("#validEmail").css({ "background-image": "url('./media/images/validyes.png')" });

} else {

$("#validEmail").css({ "background-image": "url('./media/images/validno.png')" });

}

} else {

$("#validEmail").css({ "background-image": "none" });

}
});
});

function setFocus(element){
document.getElementById(element).focus();
}
</script>

<style>
#validEmail {
margin-top: 4px;
margin-left: 2px;
position: absolute;
width: 16px;
height: 16px
}

.search-block input {
background:#fff;
border:1px solid #5f85d3;
margin-right:9px;
padding:2px 7px;
vertical-align:middle;
width:150px
}

.search-block .checkbox
{padding:0 0 0 0px;
width:15px}
.search-block div {
color:#545050;
float:left;
font-size:13px;
font-weight:700;
width:80px
}
#tbody {visibility:hidden}
</style>
</head>
<?php
// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
$fname=(isset($fname))?($fname):('');
if (!isset($reged)) $reged=false;
if (!isset($msg)) $msg='';

//the form has been submited
$correct=true;
if (isset($reg))
   {
       //check for spam
       if ($body!="") header ("Location: /index.php");

       //check that such user doesn't exist in system
       $query="
        select
                  user_name
        from ism_users
        where    user_name='".$name."'
        ";

       $v_user=array();
       $rc=sql_stmt($query, 1, $v_user ,1);

       if ($rc>0)
       {
          $msg= '<div class="error-message">Указанный email уже зарегистрирован на сайте.</div>';
          $correct=false;
       }


       //if everything correct
       if  ($correct)
       {
           $crypt_password=crypt($pass);
           $subscription=($subscription=='on')?(1):(0);

           //inset data
           $query="
                    insert into ism_users(user_name,password,ugroup,ulock,subscription)
                    values ('".$name."','".$crypt_password."',4,0,".$subscription.")";

           $result=exec_query($query);

            if ($result)
              {

              //get user_id
              $query="
                       select
                               user_id
                       from ism_users
                       where    user_name='".$name."'
                      ";

              //echo  $query;
              $v_user_id=array();
              $rc=sql_stmt($query, 1, $v_user_id ,1);

             $query="
                       insert into ism_customers(user_id,first_name,planned_monthly_investment)
                       values(".$v_user_id["user_id"][0].",'".$fname."',20000)
                     ";

              $result=exec_query($query);

              $reged=true;

	          //send notification to user -- don't forget set up mail block in php.ini
			  $mail           = new PHPMailer();
			  $mail->From     = "customer-service@invest-market.kz";
			  $mail->FromName = "Invest-Market.kz";
			  $mail->Subject  = "Регистрация на Invest-Market.kz";
	          $mail->Body    = "Уважаемый(-ая) ".$fname.", <br> Вы зарегестрировались на портале \"Invest-Market.kz\".Для того чтобы активировать Ваш аккаунт, вам необходимо перейти  по следующей ссылке: <a href='".$URL."conf_reg.php?vuser_id=".$v_user_id["user_id"][0]."&act=conf'>".$URL."conf_reg.php?vuser_id=".$v_user_id["user_id"][0]."&act=conf</a>  <br><br>С уважением,<br>Служба поддержки.<br> web site:<a href='http://invest-market.kz'>www.invest-market.kz</a> <br>e-mail: <a href='mailto:support@invest-market.kz'>support@invest-market.kz</a>";
			  $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	          $mail->AddAddress($name, $fname);


                if ($mail->Send())
                {
                  $msg= '<div class="info-message">Вы успешно зарегистрировались на сайте. Для активации Вашего аккаунта на Ваш адрес отправленно письмо с инструкцией.</div>';
                }
                else
                {
                  $msg= '<div class="error-message">Возник временный сбой в системе. Для активации учетной записи свяжитесь со службой поддержки (<a href="mailto:support@invest-market.kz">support@invest-market.kz</a>)</div>';
                }
             }

        }
}
?>
<body onload="setFocus('<?php $str=($correct)?('fname'):('name'); echo $str; ?>');">
<div id="container">
<?php
$selected_menu='main';
include 'includes/header.php';
?>
<div class="mainContent" style="width:970px">
<?php
echo $msg;
if (!$reged)
{
?>
<div class="two-blocks">
<div class="left-block" style="width:430px">
<form method="post">
<div class="search-block grey-block" style="height:430px">
<ul>
<li><div>имя</div><input name="fname" id="fname" value="<?php echo $fname;?>"></li>
<li><div>email</div><input type="text" id="name" name="name"><span id="validEmail"></span></li>
<li><div>пароль</div><input name="pass" type="password" id="pass"></li>
<li><label class="label"><input type="checkbox" name="subscription" checked class="checkbox">Получать новости и котировки на почту</label></li>
<br />
<li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;<span><input class="red" type="submit" name="reg" value="Регистрация">&nbsp;<input class="button" type="reset" value="Очистить"></span></li>
<ul>
</div>
<div id="tbody"><input type="text" name="body" value="" /></div>
</form>
</div>
<div class="right-block" style="width:510px">
<font size="4px">Регистрация на сайте</font>
<br><br>
Пройдя процесс регистрации, который займет не более минуты, вы сможете оценить все возможности инвестирования в ПИФы с использованием <strong>V-Счета</strong>.
<br><br>
<img src="./media/images/vdemo.png" alt="V-Счет" border="0">
<br><br>
Мы постоянно работаем над тем, чтобы предоставить максимально полезные инструменты и уже сейчас предлагаем следующие сервисы:
<br />
<ul class="list">
<li>Вопрос финансовому консультанту</li>
<li>Актуальные котировки паев Казахстанских ПИФов на почту</li>
<li>Последние финансовые новости и аналитика</li>
</ul>
</div>
</div>
<?php
}
?>
</div>

<!-- end of main body -->

<!-- footer -->
<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>