<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Вопросы консультанту</title>
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
<div class="title"><a class="more" href="customer_questions.php">Все вопросы</a>Вопросы консультанту</div>

<?php
if (isset($edit))
{

$query="
        update  ism_questions
             set
                 status=".$status."
                 ,consultant='".$consultant."'
                 ,comments='".$comments."'
                 ,private='".$private."'
                 ,question='".$question."'
                 ,subject='".$subject."'
        where id=".$id;

//echo $query;
$result=exec_query($query);
if ($result)
  {
   echo '<div class="info-message">'.echoNLS('Данные изменены!','').'</div>';

   if ($comments!="" && $status==$TSTATUS_COMPLETED && $email!="")
   {
     //send e-mail to customer
        $mail           = new PHPMailer();
        $mail->From     = "customer-service@invest-market.kz";
        $mail->FromName = "Invest-Market.kz";
        $mail->Subject  = "Ответ на Ваш вопрос от Invest-Market.kz";
        $mail->Body    = $mail->Body    = 'Добрый день! <br> На ваш вопрос финансовому консультанту Invest-Market.kz, был получен ответ. Вы можете прочитать его, перейдя по следующей ссылке: <br><br><a href="'.$URL.'article.php?id='.$id.'&type=question">'.$URL.'article.php?id='.$id.'&type=question</a><br><br>Спасибой, что воспользовались нашим сервисом!<br>С уважением, <br> Проект Invest-Market.kz <br> <a href="mailto:customer-service@invest-market.kz">customer-service@invest-market.kz</a><br>';
        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

    $mail->AddBCC($email, "");

    if(!$mail->Send())
    {
             echo '<div class="error-message">'.echoNLS('Ошибка отправления.','').'</div>';

    } else
    {
       echo '<div class="info-message">'.echoNLS('Ответ отправлен на электронный адрес клиента.','').'</div>';
    }

   }
  }

}

//delete block
if (isset($delete))
{

$query="
        delete from  ism_questions
        where id=".$id;

//echo $query;
$result=exec_query($query);
if ($result)
  {
   echo '<div class="info-message">'.echoNLS('Вопрос удален! <br> <a href="customer_questions.php">'.echoNLS('Вернуться к списку вопросов.','').' </a>','').'</div>';
   exit();
  }

}

if (isset($grp) && $grp==2) //if admin
{

$query="
           select
                 q.id
                ,ifnull(q.subject,'no subject') subject
                ,DATE_FORMAT(q.post_date,'%d.%m.%Y') post_date
                ,comments
                ,status
                ,consultant
                ,question
                ,private
                ,ifnull(u.user_name,q.email) email
         from ism_questions q left join ism_users u on q.user_id=u.user_id
         where id=".$id;



$vquestion=array();
$rc=sql_stmt($query, 11, $vquestion ,1);

//status list
$query="
          select
                   id
                  ,desc_".echoNLS('ru','')." name
          from ism_dictionary
          where grp=23
          order by name

       ";
$vstat=array();
$rc=sql_stmt($query, 2, $vstat ,2);

if (!isset($status))  $status=$vquestion['status'][0];
$StatMenuString = menu_list($vstat['name'],$status,$vstat['id']);
$StatMenuString = '<select name="status">'.$StatMenuString.'</select>';


if (!isset($private))  $private=$vquestion['private'][0];
$PrivateMenuString = '<select name="private"><option value ="1" '.($str=($private==1)?('selected'):('')).'>Нет</option>
                                       <option value ="0" '.($str=($private==0)?('selected'):('')).'>Да</option></select>';


echo'

   <script type="text/javascript">
	$(document).ready(function(){

	$("#comments").htmlbox({
	skin:"blue",
	toolbars:[["bold","italic","underline","strike","separator","undo","redo","separator","left","center","right","justify","separator","ol","ul","indent","outdent","separator","link","unlink","image"]],
	about:false
	});

	$("#question").htmlbox({
	skin:"blue",
	toolbars:[["bold","italic","underline","strike","separator","undo","redo","separator","left","center","right","justify","separator","ol","ul","indent","outdent","separator","link","unlink","image"]],
	about:false
	});

	});
	</script>


   <form name="edit_form" method=post>
   <input type=hidden name=id value="'.$id.'">

   <div class="search-block grey-block">
   <ul>
     <li><div>'.echoNLS('№','').'/'.echoNLS('Email','').'</div>'.$vquestion['id'][0].'/'.$vquestion['email'][0].'<input type="hidden" name="email" value="'.$vquestion['email'][0].'"></li>
     <input type="hidden" name="status" value="'.$TSTATUS_COMPLETED.'">
     <li><div>'.echoNLS('Консультант','').'</div><input type=text name=consultant value="'.$vquestion['consultant'][0].'"></li>
     <li><div>'.echoNLS('Приватный вопрос','').'</div>'.$PrivateMenuString.'</li>
     <li><div>'.echoNLS('Тема','').'</div><input type=text name=subject value="'.$vquestion['subject'][0].'" style="width:400px;"></li>
    </ul>
    </div>
    <div class="search-block grey-block">
    <div>'.echoNLS('Содержание вопроса','').'</div><textarea name=question id="question" rows=7 cols=150>'.$vquestion['question'][0].'</textarea> <br />
    <div>'.echoNLS('Комментарий','').'</div><textarea name=comments id="comments" rows=10 cols=150>'.$vquestion['comments'][0].'</textarea> <br />
    <div>&nbsp;</div>
           <span>
           <input type="submit"  name="edit" value="'.echoNLS('Ответить','').'"  class="button">
           <input type="reset"   value="'.echoNLS('Отменить','').'"  class="button">
           <input type="submit"  name="delete" value="'.echoNLS('Удалить','').'" class="button">
           </span>
    </div>
  </form>
 ';
}
?>
</div>

<!-- end of main body -->

<!-- footer -->
<?php
   include '../includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>