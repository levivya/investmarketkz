<?php
include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");
//Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
$tags_where=(isset($tags))?("and t.tags like '%".$tags."%'"):("");
$query="
       select
                  t.id
                 ,t.subject
                 ,t.question
                 ,t.comments
                 ,DATE_FORMAT(t.post_date,'%d.%m.%Y') post_date
                 ,(select count(code) from ism_questions_discuss where code=t.id) cnt
                 ,tags
                 ,UNIX_TIMESTAMP(t.post_date) last_update_nm
       from ism_questions t
       where t.private=1
             and t.status=".$TSTATUS_COMPLETED."
             ".$tags_where."
       order by t.post_date desc";
//echo $query;
$vquestions=array();
$rc=sql_stmt($query, 8, $vquestions ,2);

// Last-Modified
$LastModified_unix = filemtime(__FILE__);
$LastModified = gmdate('D, d M Y H:i:s', $LastModified_unix).' GMT';
$LastDepositUpdate = max($vquestions['last_update_nm']);
if ($LastDepositUpdate >= $LastModified_unix)  $LastModified = gmdate('D, d M Y H:i:s', $LastDepositUpdate).' GMT';
header('Last-Modified: '. $LastModified);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Задать вопрос финансовому консультанту</title>
<meta name="Description" content="Задать вопрос финансовому консультанту" >
<meta name="Keywords" content="пенсионные фонды, НПФ, ПИФы, паевые фонды, банки, депозиты, кредиты, страхование, страхование жизни, пенсия, сбережения, доходность, накопления">
<meta name="copyright" content="Invest-Market.kz">
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<?php include 'includes/scripts.php';?>
</head>

<body>
<div id="container">
<!-- header -->
<?php
        $selected_menu='main';
        include 'includes/header.php';
?>
<!-- main body -->
<noindex>
  <div class="sidebar2">
    <div class="title">Реклама</div>
    <div class="publicity">
    <iframe src="banner.php?zid=7624" name="banner" frameborder="0"  scrolling="no" hspace="0" vspace="0" align="middle" width="240px" height="390px">
                      Ваш браузер не поддерживает плавающие фреймы!</iframe>
    </div>
    <div class="title">Реклама от партнеров</div>
    <script type="text/javascript"><!--
	google_ad_client = "pub-2712511792023009";
	/* 250x250, создано 24.09.10 */
	google_ad_slot = "2344662444";
	google_ad_width = 250;
	google_ad_height = 250;
	//-->
	</script>
	<script type="text/javascript"
	src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>

    <!-- end sidebar2 -->
  </div>
</noindex>

<div class="mainContent">
<div class="title"><a name="ask_question" title="Задать вопрос консультанту">Задать вопрос финансовому консультанту</a></div>
<div class="consultant2">Если у вас возникли вопросы, касающиеся инвестирования в <a href="/pif/" target="_blank">паевые фонды</a> (ПИФы), размещения средств на <a href="/deposit/deposits.php" target="_blank">депозитах</a>, выбор <a href="/npf/" target="_blank">пенсионного фонда</a> (НПФ), либо другие вопросы из
области управления личными финансами, то вы можете задать их нашим финансовым консультантам.
<br /><br />
Перед тем как задавать свой вопрос, мы рекомендуем вам изучить <br />
раздел <a href="/articles.php?type=investor_school" target="_blank">Школа Инвестора</a> а также просмотреть <a href="#archive">архив</a> уже имеющихся ответов.
</div>
<div class="yashare-auto-init" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir"></div>

<?php
if (isset($post_question))
{
if ($subject!='' && $subject!='Тема сообщения')
  {
     $query="insert into ism_questions(subject,question,post_date,user_id)
        values('".$subject."','".$question."',current_date(),".$user_id.")";
     //echo $query;
  	 $result=exec_query($query);
  	 if ($result)
    	{
    		echo "<div class=\"info-message\">Ваш вопрос принят, наши консультанты ответят на него в течение ближайшего времени.</div>";
    		$subject='Тема сообщения';
    		$question='Введите текст сообщения';
    	}
    }
   else   {echo "<div class=\"error-message\">Заполните поле \"Тема сообщения\".</div>";}
}

//set default variables
$subject=(isset($subject))?($subject):('Тема сообщения');
$question=(isset($question))?($question):('Введите текст сообщения');

if (isset($user))
{
?>
<form method="post">
 <div class="send-message">
 <input type="text" name="subject" value="<?php echo $subject; ?>" onfocus="clear_field(this,'Тема сообщения')" style="width:690px;"/>
 <textarea cols="" rows="" onfocus="clear_field(this,'Введите текст сообщения')" name="question"><?php echo $question; ?></textarea>
 <div class="button red"><input value="Задать вопрос" name="post_question" type="submit" title="задать вопрос консультанту" /></div>
 </div>
</form>
<?php
}
else {echo  '<noindex><div class="info-message"><font color="red">Только зарегистрированные пользователи могут задавать вопросы.</font>&nbsp;<a href="log_test.php?target_page=ask_question.php">Авторизоваться</a>&nbsp;|&nbsp;<a href="registration.php">Регистрация</a></div></noindex>';}
?>
<div class="title"><a name="archive" title="архив вопросов">Архив вопросов</a></div>

<?php
if (!isset($page)) $page=1;
$page_amount= ceil(sizeof($vquestions['id'])/10);
$str="";
if ($page_amount>1)
{
 for ($j=1;$j<=$page_amount;$j++)
   {
      $str.='<a href="ask_question.php?page='.$j.'" >'.($k=($j==$page)?('<b>'.$j.'</b>'):($j)).'</a>&nbsp;';
   }
}
if ($rc>0)
{
$to=($page*10);
$from=$to-10;
$to=($to>sizeof($vquestions['id']))?(sizeof($vquestions['id'])):($to);

for ($i=$from;$i<$to;$i++)
   {

    $class=(fmod(($i),2)==0)?('class="messages dark"'):('class="messages"');
    $tags=explode(",", $vquestions['tags'][$i]);
    $tags_str="";
    if ($vquestions['tags'][$i]!='')
    {
     for ($j=0;$j<sizeof($tags);$j++)
     {    	$tags_str.='&nbsp;<a href="ask_question.php?page='.$page.'&tags='.trim($tags[$j]).'" title="'.trim($tags[$j]).'" style="color:grey;">'.trim($tags[$j]).'</a>,';     }
     $tags_str='| Теги:'.substr($tags_str,0,strlen($tags_str)-1);
    }
echo '
      <div '.$class.'>
         <h1><a href="article.php?id='.$vquestions['id'][$i].'&type=question" title="'.$vquestions['subject'][$i].'">'.$vquestions['subject'][$i].'</a></h1>
         <div class="question">'.$vquestions['question'][$i].'<br><br><noindex><a href="article.php?id='.$vquestions['id'][$i].'&type=question" rel="nofollow">Читать ответ</a> →</noindex></div>
         <div class="options"><a href="#ask_question" title="задать свой вопрос">Задать свой вопрос</a> '.$tags_str.'
           <div class="right" >
           Дата:'.$vquestions['post_date'][$i].'&nbsp;<noindex><a href="article.php?id='.$vquestions['id'][$i].'&type=question#comments" rel="nofollow">Комментариев</a></noindex> | <font color="gray">('.$vquestions['cnt'][$i].')</font>
           </div>
        </div>
      </div>
      ';
}
echo '<div class="pagination">'.$str.'</div>';
}
?>
</div>
<!-- end of main body -->

<!-- Google Website Optimizer Conversion Script -->
<script type="text/javascript">
if(typeof(_gat)!='object')document.write('<sc'+'ript src="http'+
(document.location.protocol=='https:'?'s://ssl':'://www')+
'.google-analytics.com/ga.js"></sc'+'ript>')</script>
<script type="text/javascript">
try {
var gwoTracker=_gat._getTracker("UA-11894353-2");
gwoTracker._trackPageview("/3020233403/goal");
}catch(err){}</script>
<!-- End of Google Website Optimizer Conversion Script -->

<!-- footer -->

<?php
   include 'includes/footer.php';
   //disconnect  from the database
   disconn($conn);
?>