<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Активация пользователя</title>
<meta name="Description" content="Активация пользователя" >
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
<div class="one-column-block">
<?php
//the form has been submited
if (isset($vuser_id)&& isset($act))
   {
      // Connecting, selecting database
      $conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);

      if ($act=="conf")
      {
      $query="
                    update ism_users set ulock=1,registration_date=CURRENT_TIMESTAMP()
                    where user_id=".$vuser_id;
      //echo $query;
      }
      else
      {
      $query="delete from ism_users where user_id=".$vuser_id;
      }
      $result=exec_query($query);

      if ($result)
              {
               if ($act=="conf")
               {
                echo '
                      <div class="text"><font size="5px">'.echoNLS('Ваша учетная запись активирована','').'</font>
                      <br>
                      Теперь вы можете воспользоваться нашими сервисами после авторизации.
                      </div>
                      <br>
						<div class="index">
						    <ul class="index-menu">
						      <li class="m1"><a href="log_test.php">V-Счет</a></li>
						      <li class="m2"><a href="log_test.php?target_page=ask_question.php">Ваш вопрос</a></li>
						    </ul>
						</div>
					  <div class="text">Если у вас возникли сложности, напишите нам на <a href="mailto:support@invest-market.kz">support@invest-market.kz</a>.</div>
                       ';
               }
               else
               {
                echo '<div class="text"><font size="5px">'.echoNLS('Учетная запись удалена!','').'</font></div>';
               }
              }

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