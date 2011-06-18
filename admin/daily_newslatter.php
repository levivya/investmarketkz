<?php
//load main libs
include("../lib/misc.inc");

// Connecting, selecting database
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
$message_head='<html><head></head><body  style="margin: 0px;"><div align="left"><img src="'.$URL.'media/images/logo.gif"></div><br>';

// get funds rates ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$ondate=last_rate_date(true);
$day=substr($ondate,6,4)."-".substr($ondate,3,2)."-".substr($ondate,0,2);
//$day='2008-01-30';


$message_fund_rate='';

$query="
   SELECT
           t.fund_id
           ,t.name
           ,c.name                                                                   company_name
           ,c.company_id
           ,ifnull(value_today.value,-9999)                                                        today
           ,ifnull(round(value_today.value-value_yesterday.value,3),-9999)                         yesterday
           ,ifnull(round(((value_today.value-value_yesterday.value)/value_yesterday.value)*100,3),-9999)     yesterday_persent
           ,ifnull(round(value_today.value-value_week.value,3),-9999)                              week
           ,ifnull(round(((value_today.value-value_week.value)/value_week.value)*100,3),-9999)     week_persent
           ,ifnull(round(value_today.value-value_month.value,3),-9999)                             month
           ,ifnull(round(((value_today.value-value_month.value)/value_month.value)*100,3),-9999)   month_persent
           ,ifnull(round(value_today.value-value_3month.value,3),-9999)                            3month
           ,ifnull(round(((value_today.value-value_3month.value)/value_3month.value)*100,3),-9999) 3month_persent
           ,ifnull(round(value_today.value-value_6month.value,3),-9999)                            6month
           ,ifnull(round(((value_today.value-value_6month.value)/value_6month.value)*100,3),-9999) 6month_persent
           ,ifnull(round(value_today.value-value_year.value,3),-9999)                              year
           ,ifnull(round(((value_today.value-value_year.value)/value_year.value)*100,3),-9999)     year_persent
   FROM    ism_funds t
           LEFT JOIN ism_fund_value value_yesterday  ON t.fund_id=value_yesterday.fund_id and value_yesterday.check_date=DATE_ADD(value_today.check_date,INTERVAL -1 DAY)
           LEFT JOIN ism_fund_value value_week  ON t.fund_id=value_week.fund_id and value_week.check_date=DATE_ADD(value_today.check_date,INTERVAL -7 DAY)
           LEFT JOIN ism_fund_value value_month  ON t.fund_id=value_month.fund_id and  value_month.check_date=DATE_ADD(value_today.check_date,INTERVAL -1 MONTH)
           LEFT JOIN ism_fund_value value_3month  ON t.fund_id=value_3month.fund_id and  value_3month.check_date=DATE_ADD(value_today.check_date,INTERVAL -3 MONTH)
           LEFT JOIN ism_fund_value value_6month  ON t.fund_id=value_6month.fund_id and   value_6month.check_date=DATE_ADD(value_today.check_date,INTERVAL -6 MONTH)
           LEFT JOIN ism_fund_value value_year  ON t.fund_id=value_year.fund_id and   value_year.check_date=DATE_ADD(value_today.check_date,INTERVAL -1 YEAR)
           ,ism_companies c
           ,ism_fund_value value_today
   WHERE    t.company_id=c.company_id
            and t.fund_id=value_today.fund_id
            and value_today.check_date='".$day."'
            and value_today.fund_id!=20
   ORDER BY yesterday_persent desc";
//echo $query;

$vfunds=array();
$rc=sql_stmt($query, 17, $vfunds ,2);


if ($rc>0)
{
$message_fund_rate='

<div style="font-size: 24px; color: #EF2473; margin: 0px; font-family:Tahoma, Arial, Helvetica, sans-serif; font-weight:100; background-color: #ffffff;">'.echoNLS('Цены паев и рейтинг доходности на ','').str_replace("-", ".",$ondate).' г.</div>
 <TABLE borderColor=red cellSpacing=0 cellPadding=1 width="100%" border=0>
   <TBODY>
     <TR>
      <TD bgColor=#a8b7d8>

        <TABLE  cellSpacing=1 cellPadding=2 width="100%" bgColor=#ffffff  border=0 style="font-size: 11px; font-family: Arial, Helvetica, Verdana, sans-serif;">
        <TBODY>
         <TR bgColor=#d2dff0 height=25>
           <td align=left rowspan=2>
          </td>
          <td align=middle width=10% rowspan=2>'.echoNLS('Фонд','').'
          </td>
          <td align=middle width=10% rowspan=2>'.echoNLS('УК','').'
          </td>
          <td align=middle width=10% rowspan=2>'.echoNLS('Стоимость пая '.$CURRENCY,'').'
          </td>
          <td align=middle width=70% bgcolor="#BED2E9" colspan=6>'.echoNLS('Изменение стоимости пая, %','').'
          </td>
         </tr>

         <TR bgColor=#d2dff0 height=25>
          <td align=middle width=10%>'.echoNLS('За день','').'
          </td>
          <td align=middle width=10%>'.echoNLS('За неделю','').'
          </td>
          <td align=middle width=10%>'.echoNLS('За месяц','').'
          </td>
          <td align=middle width=10%>'.echoNLS('За 3 месяца','').'
          </td>
          <td align=middle width=10%>'.echoNLS('За 6 месяцев','').'
          </td>
          <td align=middle width=10%>'.echoNLS('За год','').'
          </td>
        </tr>';

for ($i=0;$i<sizeof($vfunds['fund_id']);$i++)
   {

if ($vfunds['today'][$i]!=-9999)
{
  $vfunds['today'][$i]=number_format($vfunds['today'][$i], 3, ',', ' ');
}
else
{
  $vfunds['today'][$i]='-';
}


if ($vfunds['yesterday_persent'][$i]!=-9999)
{
	if ($vfunds['yesterday_persent'][$i]>0)  $value_yesterday_str='<font color="green">+&nbsp;'.$vfunds['yesterday_persent'][$i].'%</font>';
	if ($vfunds['yesterday_persent'][$i]<0)  $value_yesterday_str='<font color="red">-&nbsp;'.abs($vfunds['yesterday_persent'][$i]).'%</font>';
	if ($vfunds['yesterday_persent'][$i]==0) $value_yesterday_str=$vfunds['yesterday_persent'][$i].'%';
}
else
{
   $value_yesterday_str='-';
}


if ($vfunds['week_persent'][$i]!=-9999)
{
	if ($vfunds['week_persent'][$i]>0)  $value_week_str='<font color="green">+&nbsp;'.$vfunds['week_persent'][$i].'%</font>';
	if ($vfunds['week_persent'][$i]<0)  $value_week_str='<font color="red">-&nbsp;'.abs($vfunds['week_persent'][$i]).'%</font>';;
	if ($vfunds['week_persent'][$i]==0) $value_week_str=$vfunds['week_persent'][$i].'%';
}
else
{
    $value_week_str='-';
}


if ($vfunds['month_persent'][$i]!=-9999)
{
	if ($vfunds['month_persent'][$i]>0)  $value_month_str='<font color="green">+&nbsp;'.$vfunds['month_persent'][$i].'%</font>';
	if ($vfunds['month_persent'][$i]<0)  $value_month_str='<font color="red">-&nbsp;'.abs($vfunds['month_persent'][$i]).'%</font>';
	if ($vfunds['month_persent'][$i]==0) $value_month_str=$vfunds['month_persent'][$i].'%';
}
else
{
   $value_month_str='-';
}

if ($vfunds['3month_persent'][$i]!=-9999)
{
	if ($vfunds['3month_persent'][$i]>0)  $value_3month_str='<font color="green">+&nbsp;'.$vfunds['3month_persent'][$i].'%</font>';
	if ($vfunds['3month_persent'][$i]<0)  $value_3month_str='<font color="red">-&nbsp;'.abs($vfunds['3month_persent'][$i]).'%</font>';
	if ($vfunds['3month_persent'][$i]==0) $value_3month_str=$vfunds['3month_persent'][$i].'%';
}
else
{
   $value_3month_str='-';
}


if ($vfunds['6month_persent'][$i]!=-9999)
{
	if ($vfunds['6month_persent'][$i]>0)  $value_6month_str='<font color="green">+&nbsp;'.$vfunds['6month_persent'][$i].'%</font>';
	if ($vfunds['6month_persent'][$i]<0)  $value_6month_str='<font color="red">-&nbsp;'.abs($vfunds['6month_persent'][$i]).'%</font>';
	if ($vfunds['6month_persent'][$i]==0) $value_6month_str=$vfunds['6month_persent'][$i].'%';
}
else
{
    $value_6month_str='-';
}

if ($vfunds['year_persent'][$i]!=-9999)
{
	if ($vfunds['year_persent'][$i]>0)  $value_year_str='<font color="green">+&nbsp;'.$vfunds['year_persent'][$i].'%</font>';
	if ($vfunds['year_persent'][$i]<0)  $value_year_str='<font color="red">-&nbsp;'.abs($vfunds['year_persent'][$i]).'%</font>';
	if ($vfunds['year_persent'][$i]==0) $value_year_str=$vfunds['year_persent'][$i].'%';
}
else
{
    $value_year_str='-';
}

$str=(fmod(($i+1),2)==0)?("bgColor=#f3f3f3"):("");
$message_fund_rate.='<tr '.$str.'>
          <td align=left bgcolor="#EFEEF7">'.($i+1).'
          </td>
          <td align=left width=10%><a href="'.$URL.'fund.php?fund_id='.$vfunds['fund_id'][$i].'" target="_blank">'.$vfunds['name'][$i].'</a>
          </td>
          <td align=left width=10%><a href="'.$URL.'company_profile.php?company_id='.$vfunds['company_id'][$i].'" target="_blank">'.$vfunds['company_name'][$i].'</a>
          </td>
          <td align=middle width=10%>'.$vfunds['today'][$i].'
          </td>
          <td align=middle width=10%>'.$value_yesterday_str.'
          </td>
          <td align=middle width=10%>'.$value_week_str.'
          </td>
          <td align=middle width=10%>'.$value_month_str.'
          </td>
          <td align=middle width=10%>'.$value_3month_str.'
          </td>
          <td align=middle width=10%>'.$value_6month_str.'
          </td>
          <td align=middle width=10%>'.$value_year_str.'
          </td>
        </tr>';
     }

$message_fund_rate.='</TBODY></table></td></tr></TBODY></table><br>';
}

// END get funds rates ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



// NEWS blok +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$message_news="";
$query="
   SELECT
            t.news_id
           ,t.title
           ,DATE_FORMAT(t.news_date,'%d.%m.%Y') vdate_format
   FROM    ism_news t
   WHERE   t.posted_date='".date('Y-m-d')."'
   ORDER BY news_date desc";
//echo $query;

$vnews=array();
$rc=sql_stmt($query, 3, $vnews ,2);


if ($rc>0)
{
$message_news='
               <table  bordercolor=green cellspacing=1 cellpadding=2 width="100%" bgcolor=#ffffff   border=0 style="font-size: 11px; font-family: arial, helvetica, verdana, sans-serif;">
               <tbody>
               <tr bgcolor="#06186E" height=25>
               <td><b><font color="white">&nbsp;'.echoNLS('Новости и События','').'</font></b></td>
               </tr>
               ';
for ($i=0;$i<sizeof($vnews['news_id']);$i++)
{

$str=(fmod(($i+1),2)==0)?("bgColor=#f3f3f3"):("white");

$message_news.='
    <tr '.$str.'>
     <td valign="middle"><b>'.$vnews['vdate_format'][$i].'</b>&nbsp;<a href="'.$URL.'article.php?id='.$vnews['news_id'][$i].'&type=news" target="_blank">'.$vnews['title'][$i].'</a></td>
    </tr>
    ';
}
$message_news.='</tbody>
               </table>
              ';

}

// END NEWS blok +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// ANALYTIC blok +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$message_analyt="";
$query="
   SELECT
            t.analyt_id
           ,t.title
           ,DATE_FORMAT(t.analyt_date,'%d.%m.%Y') vdate_format
   FROM    ism_analytics t
   WHERE   t.posted_date='".date('Y-m-d')."'
   ORDER BY analyt_date desc";
//echo $query;

$vnews=array();
$rc=sql_stmt($query, 3, $vnews ,2);


if ($rc>0)
{
$message_analyt='
               <table  bordercolor=green cellspacing=1 cellpadding=2 width="100%" bgcolor=#ffffff  border=0 style="font-size: 11px; font-family: arial, helvetica, verdana, sans-serif;">
               <tbody>
               <tr bgcolor="#d2dff0" height=25>
               <td><b><font color="#06186E">&nbsp;'.echoNLS('Аналитика','').'</font></b></td>
               </tr>
               ';
for ($i=0;$i<sizeof($vnews['analyt_id']);$i++)
{

$str=(fmod(($i+1),2)==0)?("bgColor=#f3f3f3"):("white");

$message_analyt.='
    <tr '.$str.'>
     <td valign="middle"><b>'.$vnews['vdate_format'][$i].'</b>&nbsp;<a href="'.$URL.'article.php?id='.$vnews['analyt_id'][$i].'&type=analytic" target="_blank">'.$vnews['title'][$i].'</a></td>
    </tr>
    ';
}
$message_analyt.='</tbody>
               </table>
              ';

}

// END ANALYTIC blok +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$smi='';

if ($message_news!='' && $message_analyt!='')
{
  $smi='<table cellSpacing=0 cellPadding=1 width="100%" border=0>
        <tr><td  width="50%" valign="top">'.$message_news.'</td></tr>
        <tr><td  width="50%" valign="top"><hr color="#06186E"></td></tr>
        <tr><td  width="50%" valign="top">'.$message_analyt.'</td></tr>
        <tr><td  width="50%" valign="top"><hr color="#d2dff0"></td></tr>
        </table>';
}
else
{
   if ($message_news!='')    $smi=$message_news.'<hr color="#06186E"><br>';
   if ($message_analyt!='')  $smi=$message_analyt.'<hr color="#d2dff0"><br>';
}


if ($smi!='')
{
 $smi='<div style="font-size: 24px; color: #EF2473; margin: 0px; font-family:Tahoma, Arial, Helvetica, sans-serif; font-weight:100; background-color: #ffffff;">'.echoNLS('Отраслевой обзор СМИ','').'</div>'.$smi;
}

$message_foot='
             <div align="justify" style="font-family:Tahoma, Arial, Helvetica, sans-serif;  color:#979797; font-size: 11px;">
               '.echoNLS('По вопросам размещения рекламы, обращайтесь в Службу по работе с клиентами «Invest-Market.kz» по телефону '.$MAIN_PHONE.' либо по электронному адресy <a href="mailto:customer-service@invest-market.kz">customer-service@invest-market.kz</a>.','').'
             </div>
             <br>
             <div align="justify" style="font-family:Tahoma, Arial, Helvetica, sans-serif;  color:#979797; font-size: 11px;">
               '.echoNLS('Для отмены подписки от «Invest-Market.kz» перейдите в блок «Личные данные» на <a href="'.$URL.'">www.invest-market.kz</a> и деактивируйте опцию рассылки.','').'
             </div>
             <br>
             </body></html>
             ';



//compose a message
$message=$smi.$message_fund_rate;

//echo $message;
//die();


if ($message!='')
{
$message_body=$message_head.$message.$message_foot;
//echo $message_body;
//die();

//send message
$mail           = new PHPMailer();
$mail->From     = "customer-service@invest-market.kz";
$mail->FromName = "Invest-Market.kz";
$mail->Subject  = "Рассылка от Invest-Market.kz - ".date('d.m.Y').' г.';

$mail->Body    = $message_body;
$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

//get subscribers list
$query="
   SELECT
            t.user_name
   FROM    ism_users t
   WHERE   t.subscription=1
   ";
//echo $query;

$vnews=array();
$rc=sql_stmt($query, 1, $subscribers ,2);


if ($rc>0)
{
  for ($i=0;$i<sizeof($subscribers['user_name']);$i++)
  {
	$mail->AddBCC($subscribers['user_name'][$i], "");
  }
}

if(!$mail->Send()) {
  echo 'Failed to send mail';
} else {
  echo 'Mail sent';
}
}
else
{
  echo 'Nothing to send';
}
?>