<?php

// include main config file
$main_cfg_file=$_SERVER["DOCUMENT_ROOT"]."/main.cfg";
if (file_exists($main_cfg_file)) require($_SERVER["DOCUMENT_ROOT"]."/main.cfg");
else require('/www/invest04/www/htdocs/main.cfg');

require("mysql.inc");

if((isset($_SERVER["DOCUMENT_ROOT"])) && ($_SERVER["DOCUMENT_ROOT"]!='')) require("sessions.inc");

include_once('class.phpmailer.php');

$flowplayer_file=$_SERVER["DOCUMENT_ROOT"]."/flowplayer/flowplayer.php";
if (file_exists($flowplayer_file)) require_once($_SERVER["DOCUMENT_ROOT"]."/flowplayer/flowplayer.php");
else require_once("/www/invest04/www/htdocs/flowplayer/flowplayer.php");

$cryptinstall="./crypt/cryptographp.fct.php";
#$cryptinstall=$_SERVER["DOCUMENT_ROOT"]."/crypt/cryptographp.fct.php";
if (file_exists($cryptinstall)) include $cryptinstall;

// check if real int
function clean_int( $i )
{
  if ( is_numeric( $i ) )
      {
        return ( int ) $i;
      }
      // return False if we don't get a number
      else
      {
      return false;
      }
}


//print russian date
function russian_date() {
   $translation = array(
      "am" => "дп",
      "pm" => "пп",
      "AM" => "ДП",
      "PM" => "ПП",
      "Monday" => "Понедельник",
      "Mon" => "Пн",
      "Tuesday" => "Вторник",
      "Tue" => "Вт",
      "Wednesday" => "Среда",
      "Wed" => "Ср",
      "Thursday" => "Четверг",
      "Thu" => "Чт",
      "Friday" => "Пятница",
      "Fri" => "Пт",
      "Saturday" => "Суббота",
      "Sat" => "Сб",
      "Sunday" => "Воскресенье",
      "Sun" => "Вс",
      "January" => "Января",
      "Jan" => "Янв",
      "February" => "Февраля",
      "Feb" => "Фев",
      "March" => "Марта",
      "Mar" => "Мар",
      "April" => "Апреля",
      "Apr" => "Апр",
      "May" => "Мая",
      "May" => "Мая",
      "June" => "Июня",
      "Jun" => "Июн",
      "July" => "Июля",
      "Jul" => "Июл",
      "August" => "Августа",
      "Aug" => "Авг",
      "September" => "Сентября",
      "Sep" => "Сен",
      "October" => "Октября",
      "Oct" => "Окт",
      "November" => "Ноября",
      "Nov" => "Ноя",
      "December" => "Декабря",
      "Dec" => "Дек",
      "st" => "ое",
      "nd" => "ое",
      "rd" => "е",
      "th" => "ое",
      );
   if (func_num_args() > 1) {
      $timestamp = func_get_arg(1);
      return strtr(date(func_get_arg(0), $timestamp), $translation);
   } else {
      return strtr(date(func_get_arg(0)), $translation);
   };
}


//post comments function
function discuss($table_name, $page, $item,$code1)
{
   //global variables
   global $ACTION, $DEL,$inputTitle,$inputPoster,$inputBody,$messageID,$user,$grp,$code;

   echo '<script language="Javascript">
             function remove_message(val)
             {
                 document.delete_message.DEL.value=val;
                 document.delete_message.submit();
             }
            </script>';

   // form for post messages
   function postForm()
    {
        global $page,$item;

        echo "
               <form method=\"post\" action=\"".$page."\">
                <input type=\"hidden\" name=\"ACTION\" value=\"POST\">
				<div class=\"send-message\">
					<div class=\"right\"><input value=\"Код авторизации\" name=\"code\" type=\"text\" onfocus=\"clear_field(this)\"/><span>";
					dsp_crypt(0,1);
		echo "	</span></div>
					<input value=\"Заголовок\" type=\"text\" name=\"inputTitle\" onfocus=\"clear_field(this)\"/>
					<input value=\"Автор\" type=\"text\" name=\"inputPoster\" onfocus=\"clear_field(this)\"/>
					<textarea cols=\"\" rows=\"\" onfocus=\"clear_field(this)\" name=\"inputBody\">Введите текст сообщения</textarea>
					<div class=\"button\"><input type=\"submit\" value=\"Поместить\"></div>
				</div>
               </form>
             ";

     }

   if($ACTION != "")
    {
       if($ACTION == "POST")
        {

            if ($inputPoster == "") $inputPoster="Aноним";

            $inputTitle = ereg_replace("'", "''", $inputTitle);
            $inputBody = ereg_replace("'", "''", $inputBody);
            $inputTitle=strip_tags($inputTitle);
            $inputBody=strip_tags($inputBody);
            $inputPoster=strip_tags($inputPoster);


            if ($inputBody!="")
            {

                if (chk_crypt($code))
                   {

                    $query = "INSERT INTO $table_name ";
                    $query .= "VALUES(0, $code1, '$inputTitle', ";
                    $query .= "'$inputPoster', ";
                    $query .= "now(), ";
                    $query .= "'$inputBody')";

                    //echo "<br>".$query;
                    $result = mysql_query($query);

                    $messageID=0;
                    }
               else
                    {
                       echo '
             					<script language="Javascript">
                 				alert("Неверный код авторизации!");
            					</script>';
                    }
           }
           else
           {
             echo '
             <script language="Javascript">
                 alert("Сообщение не может быть пустым!");
            </script>';
           }
        }
    }

    if($DEL != "")
    {

       $query="delete from $table_name where id=".clean_int($DEL);
       //echo $query;
       $result = mysql_query($query);

    }


    /******************************************************
     Show Message or show list of messages
    ******************************************************/
     echo "<div class=\"title\">Комментарий</div>";
     postForm();

     $query = "SELECT
                         id
                        ,title
                        ,poster
                        ,DATE_FORMAT(created,'%d.%m.%Y') created
                        ,body
                 from ".$table_name."
                 where code=".clean_int($code1)."
                 order by created";

      $v=array();
      $rc=sql_stmt($query, 5, $v ,2);

      if ($rc>0)
      {
        echo '
        <form method="post" target="_self" name=delete_message>
                     <INPUT TYPE="hidden" NAME="DEL" VALUE="POST">';


      for ($i=0;$i<sizeof($v['id']);$i++)
       {
            $messageID = $v['id'][$i];
            $messageTitle = $v['title'][$i];
            $messagePoster = $v['poster'][$i];
            $messageCreated = $v['created'][$i];
            $messageBody = $v['body'][$i];

            $class=(fmod(($i),2)==0)?('class="messages dark"'):('class="messages"');

			echo '
			      <div '.$class.'>
			         <div><span>'.$messageCreated.'</span>'.$messageTitle.'/'.$messagePoster.'</div>
			         '.$messageBody.'
			      </div>
			      ';
            if (isset($user)&&($grp==2))  echo "<img src=\"icons/remove.gif\" width=15 height=15 onclick=\"remove_message('".$messageID."')\" name=\"delete_message_".$messageID."\" alt=\"Удалить сообщение\">";
        }
       echo "</form>";

       }
}

//==========================================================//
// menu_list4                                               //
//==========================================================//
// return :  string with a menu: "<option> ... </option>"
//
// $arr - array with values for "<option>$arr[...]</option>"
// $addkeys - array with values for "<option value="$addkeys[...]">..."
// $selected - var or array for $addkeys values, which can be selected as default
function menu_list($arr,$selected,$addkeys)
{
  $key=0;
  if ($selected==null)        $selected='';

  if (is_array($selected)) $ARRAYKEY=1;
  else                     $ARRAYKEY=0;

  if ($addkeys==null)        $OPT=0;
  else                       $OPT=1;

  if($key>0) { $tmpstr="<option value=\" \">UNKNOWN</option>"; }
  else       { $tmpstr='';        }

  $selstr='';
  if(isset($arr))
  {
       $ret=sizeof($arr);
       if ($ret>0)      {
         $menu=''.$tmpstr;
         $i=0; while ($i<$ret) {
           if($ARRAYKEY==0) {
            if($selected!='') { if($addkeys[$i]==$selected) {
                                       $selstr="selected";
                                                        }
                                else                    {
                                       $selstr='';
                                                        }
                              }
                            }
           else        {

  if(in_array($addkeys[$i],$selected)) { $selstr="selected"; }
                        else                                 { $selstr=''; }
                }
        if($OPT==0) {
            $menu=$menu.
              "<option value=\"".
              $arr[$i]."\" ".$selstr.">".
              $arr[$i]."</option>";
                    }
        else        {
                $menu=$menu.
                "<option value=\"".
                $addkeys[$i]."\" ".$selstr.">".
                $arr[$i]."</option>";
                    }
              $i++;
                               }
                        }
       else             {
         $menu=$tmpstr."<option>"."</option>";
                        }
                     }
     else            {
       $menu=$tmpstr."<option>"."</option>";
                     }
     return $menu;
}
// This function return the array with two elements. The first is avg year income, the second is volatility
// If $ondate is not set, we take the last data
function get_sgd_volat( $ondate = "")
{

 $volat=0;
 $avg_year_income=0;
 $where=($ondate!="")?(" and check_date<='".$ondate."'"):("");


 $query="
   SELECT
            max(check_date)  last_check_date
   FROM
           ism_index_pifkz
   WHERE   income_year is not null".$where;

 $vdate=array();
 $rc=sql_stmt($query, 1, $vdate ,1);
 $last_date=$vdate['last_check_date'][0];


 $query="
   SELECT
            round(avg(income_year),2)  avg_income_year
   FROM
           ism_index_pifkz
   WHERE   check_date>=DATE_ADD('".$last_date."',INTERVAL -1 YEAR)
           and income_year is not null
          ";

 $v=array();
 $rc=sql_stmt($query, 1, $v ,1);
 $avg_year_income=$v['avg_income_year'][0];


 $query="
   SELECT
            round(sqrt(avg(((".$avg_year_income."-income_year)*(".$avg_year_income."-income_year))/100))*10,2)  avg_volat
   FROM
           ism_index_pifkz
   WHERE   check_date>=DATE_ADD('".$last_date."',INTERVAL -1 YEAR)
           and income_year is not null
          ";

 $vvolat=array();
 $rc=sql_stmt($query, 1, $vvolat ,1);
 $volat=$vvolat['avg_volat'][0];

 $data = array ("avg_year_income"  => $avg_year_income , "volat"  => $volat);

 return $data;
}

//==============================================================================
// Return NLS string dependent on $lang variable
//==============================================================================
function echoNLS($nls=null,$kz=null)
{
 global $lang;
 if(!isset($lang))
 {
   $string=$nls;
 }
 else
 {

  switch ($lang)
   {
    case 'ru': $string=$nls;
    break;
    case 'kz': $string=$kz;
    break;
    default: $string=$nls;
  }
 }
 return $string;
}


// Add date PHP
function DateAdd($interval, $number, $date) {

    $date_time_array = getdate($date);
    $hours = $date_time_array['hours'];
    $minutes = $date_time_array['minutes'];
    $seconds = $date_time_array['seconds'];
    $month = $date_time_array['mon'];
    $day = $date_time_array['mday'];
    $year = $date_time_array['year'];

    switch ($interval) {
      case 'yyyy':
            $year+=$number;
            break;
        case 'q':
            $year+=($number*3);
            break;
        case 'm':
            $month+=$number;
            break;
        case 'd':
            $day+=$number;
            break;
        case 'ww':
            $day+=($number*7);
            break;
        case 'h':
            $hours+=$number;
            break;
        case 'n':
            $minutes+=$number;
            break;
        case 's':
            $seconds+=$number;
            break;
    }
       $timestamp= mktime($hours,$minutes,$seconds,$month,$day,$year);
    return $timestamp;
}

//check Strength of a password
function CheckPasswordStrength($password)
{
    $strength = 1;
    if (strlen($password)<8) $strength=0;
    return $strength;
}

function last_rate_date($format = false)
{

        $week_date=date('l');
        $minus_days=1;

        if ($week_date=='Saturday')$minus_days=1;
        if ($week_date=='Sunday')  $minus_days=2;
        if ($week_date=='Monday')  $minus_days=3;


        $query="select
                    DATE_ADD(CURRENT_DATE(),INTERVAL -".$minus_days." DAY) day
                   ,DATE_FORMAT(DATE_ADD(CURRENT_DATE(),INTERVAL -".$minus_days." DAY),'%d-%m-%Y') day_format
                    ";
        $vday=array();
        $rc=sql_stmt($query, 2, $vday ,1);
        $day=$vday['day'][0];
    $day_format=$vday['day_format'][0];


    if ($format)
    {
      return $day_format;
    }
    else
    {
      return $day;
    }
}

//get last existing rate's date

function last_rate_date2($format = false)
{

        $query="select
                    max(check_date) day
                   ,DATE_FORMAT(max(check_date),'%d-%m-%Y') day_format
            from ism_fund_value
                    ";
        $vday=array();
        $rc=sql_stmt($query, 2, $vday ,1);
        $day=$vday['day'][0];
    $day_format=$vday['day_format'][0];


    if ($format)
    {
      return $day_format;
    }
    else
    {
      return $day;
    }
}


?>
