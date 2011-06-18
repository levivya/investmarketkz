<?php
/*
display bank/company ratings Fitch/S&P
supported $type - bank
*/

//define default type
if (!isset($type)) $type='bank';

  //============== S&P ===========================================================================
  if ($edit_form)
 {
  echo '
        <script type="text/javascript">
		$(function(){
		  $.datepicker.setDefaults(
		        $.extend($.datepicker.regional["ru"])
		  );
		  $("#sp_new_date").datepicker();
		});
		</script>
        ';

  //++++++++ add S&P rating ++++++++++++++++++++++++++++++++++++++++++++++++++++
  if (isset($add_sp))
  {
   $sp_new_date_t=substr($sp_new_date,6,4)."-".substr($sp_new_date,3,2)."-".substr($sp_new_date,0,2);
   $str="insert into ism_sp_rating
                                  (
                                    imetent_id
                                   ,rating_date
                                   ,sp_long_foring
                                   ,sp_short_foring
                                   ,sp_outlook_foring
                                   ,sp_long_local
                                   ,sp_short_local
                                   ,sp_outlook_local
                                  )
         values(".$id."
                ,'".$sp_new_date_t."'
                ,".$sp_long_foring."
                ,".$sp_short_foring."
                ,".$sp_outlook_foring."
                ,".$sp_long_local."
                ,".$sp_short_local."
                ,".$sp_outlook_local."
                )";

   //echo $str;
   $result=exec_query($str);
   if ($result){echo '<div class="info-message">'.echoNLS('Данные внесены!','').'</div>';}
  }



  $query="
           select
                 'null'  id
                ,'".echoNLS('N/A','')."' desc_ru
           union
           select
                    t.id
                   ,t.desc_ru
           from ism_dictionary t
           where  t.grp=".$SP_RATING;

   $v=array();
   $rc=sql_stmt($query, 2, $v ,2);

   // sp_long_foring
   if (!isset($sp_long_foring)) $sp_long_foring=$v['id'][0];
   $SPLongForingMenuString = menu_list($v['desc_ru'],$sp_long_foring,$v['id']);
   $SPLongForingMenuString = '<select name="sp_long_foring">'.$SPLongForingMenuString.'</select>';

   // sp_long_local
   if (!isset($sp_long_local)) $sp_long_local=$v['id'][0];
   $SPLongLocalMenuString = menu_list($v['desc_ru'],$sp_long_local,$v['id']);
   $SPLongLocalMenuString = '<select name="sp_long_local">'.$SPLongLocalMenuString.'</select>';

    // sp_short_foring
   if (!isset($sp_short_foring)) $sp_short_foring=$v['id'][0];
   $SPShortForingMenuString = menu_list($v['desc_ru'],$sp_short_foring,$v['id']);
   $SPShortForingMenuString = '<select name="sp_short_foring">'.$SPShortForingMenuString.'</select>';

    // sp_short_local
   if (!isset($sp_short_local)) $sp_short_local=$v['id'][0];
   $SPShortLocalMenuString = menu_list($v['desc_ru'],$sp_short_local,$v['id']);
   $SPShortLocalMenuString = '<select name="sp_short_local">'.$SPShortLocalMenuString.'</select>';


   $query="
           select
                 'null'  id
                ,'".echoNLS('N/A','')."' desc_ru
           union
           select
                    t.id
                   ,t.desc_ru
           from ism_dictionary t
           where  t.grp=".$RATING_OUTLOOK;

   $vv=array();
   $rc=sql_stmt($query, 2, $vv ,2);

    // sp_outlook_foring
   if (!isset($sp_outlook_foring)) $sp_outlook_foring=$vv['id'][0];
   $SPOutlookForingMenuString = menu_list($vv['desc_ru'],$sp_outlook_foring,$vv['id']);
   $SPOutlookForingMenuString = '<select name="sp_outlook_foring">'.$SPOutlookForingMenuString.'</select>';

   // sp_outlook_local
   if (!isset($sp_outlook_local)) $sp_outlook_local=$vv['id'][0];
   $SPOutlookLocalMenuString = menu_list($vv['desc_ru'],$sp_outlook_local,$vv['id']);
   $SPOutlookLocalMenuString = '<select name="sp_outlook_local">'.$SPOutlookLocalMenuString.'</select>';


    // add rating S&P
    echo '
             <form name="add_sp" method=post>
             <div class="search-block grey-block">
             <ul>
             <li><div>'.echoNLS('Добавить рейтин S&P','').'</div></li>
             <li><div>'.echoNLS('Дата','').'</div>&nbsp;&nbsp;&nbsp;<input type="text" name="sp_new_date" id="sp_new_date"></li>
             <li><div>'.echoNLS('Долг рейт в ин валюте','').'</div>'.$SPLongForingMenuString.'</li>
             <li><div>'.echoNLS('Кратк рейт в ин валюте','').'</div>'.$SPShortForingMenuString.'</li>
             <li><div>'.echoNLS('Прогноз','').'</div>'.$SPOutlookForingMenuString.'</li>
             <li><div>'.echoNLS('Долг рейт в нац валюте','').'</div>'.$SPLongLocalMenuString.'</li>
             <li><div>'.echoNLS('Кратк рейт в нац валюте','').'</div>'.$SPShortLocalMenuString.'</li>
             <li><div>'.echoNLS('Прогноз','').'</div>'.$SPOutlookLocalMenuString.'</li>
             <li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="submit" name="add_sp" value="'.echoNLS('Добавить','').'"></span>
             </ul>
             </div>
             </form>
         ';


   // delete rating S&P
   if (isset($delete_sp))
  {
   $str="delete from ism_sp_rating where imetent_id=".$id." and rating_date='".$rating_date."'";
   $result=exec_query($str);
   if ($result) { echo '<div class="info-message">'.echoNLS('Данные удалены!','').'</div>';}
  }

     $query="
           select
                    t.rating_date
                   ,DATE_FORMAT(t.rating_date,'%d.%m.%Y') format_check_date
           from ism_sp_rating t
           where  t.imetent_id=".$id."
           order by t.rating_date desc";

   $vsp=array();
   $rc=sql_stmt($query, 2, $vsp ,2);

   if (!isset($rating_date)) $rating_date=$vsp['rating_date'][0];
   $SPMenuString = menu_list($vsp['format_check_date'],$rating_date,$vsp['rating_date']);
   $SPMenuString = '<select name="rating_date">'.$SPMenuString.'</select>';

   echo '
   <form name="delete_sp" method=post>
   <div class="search-block grey-block">
       <ul>
       <li><div>'.echoNLS('Удалить рейтинг S&P','').'</div>'.$SPMenuString.'&nbsp;&nbsp;<span><input type="submit" name="delete_sp" value="'.echoNLS('Удалить','').'"></span></li>
       </ul>
   </div>
   </form>
        ';
}

    //print S&P
    $query="
           select
                   t.rating_date
                   ,DATE_FORMAT(t.rating_date,'%d.%m.%Y') format_check_date
                   ,(select desc_ru from ism_dictionary where id=t.sp_long_foring) sp_long_foring_c
                   ,t.sp_long_foring
                   ,(select desc_ru from ism_dictionary where id=t.sp_short_foring) sp_short_foring_c
                   ,t.sp_short_foring
                   ,(select desc_ru from ism_dictionary where id=t.sp_outlook_foring) sp_outlook_foring_c
                   ,t.sp_outlook_foring
                   ,(select desc_ru from ism_dictionary where id=t.sp_long_local)  sp_long_local_c
                   ,t.sp_long_local
                   ,(select desc_ru from ism_dictionary where id=t.sp_short_local)  sp_short_local_c
                   ,t.sp_short_local
                   ,(select desc_ru from ism_dictionary where id=t.sp_outlook_local)  sp_outlook_local_c
                   ,t.sp_outlook_local
           from ism_sp_rating t
           where  t.imetent_id=".$id."
           order by t.rating_date asc
           ";
    $vsp_data=array();
    $rc=sql_stmt($query, 14, $vsp_data ,2);
    if ($rc>0)
    {
    echo '
    <div class="scroll-block-horiz">
    <table class="tab-table"><thead><tr><th>S&P</th>';
    for ($i=0;$i<sizeof($vsp_data['rating_date']);$i++)
	  {	  	 echo '<th class="right">'.$vsp_data['format_check_date'][$i].'</th>';
	  }
	echo '</tr></thead><tbody><tr class="odd"><td>'.echoNLS('Долгосрочный рейтинг в ин валюте','').'</td>';
	for ($i=0;$i<sizeof($vsp_data['rating_date']);$i++)
	  {
	  	 if ($i>0)
	  	 {	        if (isset($vsp_data['sp_long_foring'][$i])&&isset($vsp_data['sp_long_foring'][$i-1]))
	        {
	        if ($vsp_data['sp_long_foring'][$i] > $vsp_data['sp_long_foring'][$i-1])  echo '<td class="right"><span class="arrow2 down">'.$vsp_data['sp_long_foring_c'][$i].'</span></td>';
   	        if ($vsp_data['sp_long_foring'][$i] < $vsp_data['sp_long_foring'][$i-1])  echo '<td class="right"><span class="arrow2 up">'.$vsp_data['sp_long_foring_c'][$i].'</span></td>';
   	        if ($vsp_data['sp_long_foring'][$i] == $vsp_data['sp_long_foring'][$i-1]) echo '<td class="right">'.$vsp_data['sp_long_foring_c'][$i].'</td>';
   	        }
   	        else
   	        {   	          echo '<td class="right">'.$vsp_data['sp_long_foring_c'][$i].'</td>';   	        }
	  	 }
	  	 else
	  	 {
	  	    echo '<td class="right">'.$vsp_data['sp_long_foring_c'][$i].'</td>';
	  	 }
	  }
	echo '</tr><tr class="even"><td>'.echoNLS('Краткосрочный рейтинг в ин валюте','').'</td>';
	for ($i=0;$i<sizeof($vsp_data['rating_date']);$i++)
	  {
	  	 if ($i>0)
	  	 {
	       if (isset($vsp_data['sp_short_foring'][$i])&&isset($vsp_data['sp_short_foring'][$i-1]))
	       {
	        if ($vsp_data['sp_short_foring'][$i] > $vsp_data['sp_short_foring'][$i-1])  echo '<td class="right"><span class="arrow2 down">'.$vsp_data['sp_short_foring_c'][$i].'</span></td>';
   	        if ($vsp_data['sp_short_foring'][$i] < $vsp_data['sp_short_foring'][$i-1])  echo '<td class="right"><span class="arrow2 up">'.$vsp_data['sp_short_foring_c'][$i].'</span></td>';
   	        if ($vsp_data['sp_short_foring'][$i] == $vsp_data['sp_short_foring'][$i-1]) echo '<td class="right">'.$vsp_data['sp_short_foring_c'][$i].'</td>';
   	       }
   	       else
   	       {   	       	echo '<td class="right">'.$vsp_data['sp_short_foring_c'][$i].'</td>';   	       }
	  	 }
	  	 else
	  	 {
	  	    echo '<td class="right">'.$vsp_data['sp_short_foring_c'][$i].'</td>';
	  	 }
	  }
	echo '</tr><tr class="odd"><td>'.echoNLS('Прогноз','').'</td>';
	for ($i=0;$i<sizeof($vsp_data['rating_date']);$i++)
	  {
        echo '<td class="right">'.$vsp_data['sp_outlook_foring_c'][$i].'</td>';
	  }
	echo '</tr><tr class="even"><td>'.echoNLS('Долгосрочный рейтинг в нац валюте','').'</td>';
	for ($i=0;$i<sizeof($vsp_data['rating_date']);$i++)
	  {
    	 if ($i>0)
	  	 {
	       if (isset($vsp_data['sp_long_local'][$i])&&isset($vsp_data['sp_long_local'][$i-1]))
	       {
	        if ($vsp_data['sp_long_local'][$i] > $vsp_data['sp_long_local'][$i-1])  echo '<td class="right"><span class="arrow2 down">'.$vsp_data['sp_long_local_c'][$i].'</span></td>';
   	        if ($vsp_data['sp_long_local'][$i] < $vsp_data['sp_long_local'][$i-1])  echo '<td class="right"><span class="arrow2 up">'.$vsp_data['sp_long_local_c'][$i].'</span></td>';
   	        if ($vsp_data['sp_long_local'][$i] == $vsp_data['sp_long_local'][$i-1]) echo '<td class="right">'.$vsp_data['sp_long_local_c'][$i].'</td>';
   	       }
   	       else
   	       {
   	       	echo '<td class="right">'.$vsp_data['sp_long_local_c'][$i].'</td>';
   	       }
	  	 }
	  	 else
	  	 {
	  	    echo '<td class="right">'.$vsp_data['sp_long_local_c'][$i].'</td>';
	  	 }
	  }
	echo '</tr><tr class="odd"><td>'.echoNLS('Краткосрочный рейтинг в нац валюте','').'</td>';
	for ($i=0;$i<sizeof($vsp_data['rating_date']);$i++)
	  {
	  	if ($i>0)
	  	 {
	       if (isset($vsp_data['sp_short_local'][$i])&&isset($vsp_data['sp_short_local'][$i-1]))
	       {
	        if ($vsp_data['sp_short_local'][$i] > $vsp_data['sp_short_local'][$i-1])  echo '<td class="right"><span class="arrow2 down">'.$vsp_data['sp_short_local_c'][$i].'</span></td>';
   	        if ($vsp_data['sp_short_local'][$i] < $vsp_data['sp_short_local'][$i-1])  echo '<td class="right"><span class="arrow2 up">'.$vsp_data['sp_short_local_c'][$i].'</span></td>';
   	        if ($vsp_data['sp_short_local'][$i] == $vsp_data['sp_short_local'][$i-1]) echo '<td class="right">'.$vsp_data['sp_short_local_c'][$i].'</td>';
   	       }
   	       else
   	       {
   	       	echo '<td class="right">'.$vsp_data['sp_short_local_c'][$i].'</td>';
   	       }
	  	 }
	  	 else
	  	 {
	  	    echo '<td class="right">'.$vsp_data['sp_short_local_c'][$i].'</td>';
	  	 }
	  }
	echo '</tr><tr class="even"><td>'.echoNLS('Прогноз','').'</td>';
	for ($i=0;$i<sizeof($vsp_data['rating_date']);$i++)
	  {
	  	 echo '<td class="right">'.$vsp_data['sp_outlook_local_c'][$i].'</td>';
	  }
	echo '</tr></tbody></table></div>';
	}
  //============== END S&P ===========================================================================

   echo '<br><br>';

  //============== Fitch ===========================================================================

if ($edit_form)
 {

   echo '
        <script type="text/javascript">
		$(function(){
		  $.datepicker.setDefaults(
		        $.extend($.datepicker.regional["ru"])
		  );
		  $("#ft_new_date").datepicker();
		});
		</script>
        ';


  //++++++++ add Fitch rating ++++++++++++++++++++++++++++++++++++++++++++++++++++
  if (isset($add_ft))
  {
   $ft_new_date_t=substr($ft_new_date,6,4)."-".substr($ft_new_date,3,2)."-".substr($ft_new_date,0,2);
   if ($f_support_rating =='') $f_support_rating='null';
   $str="insert into ism_fitch_rating
                                  (
                                    imetent_id
                                   ,rating_date
                                   ,f_long_foring
								   ,f_long_local
								   ,f_outlook_long
								   ,f_support_level_long
								   ,f_short_foring
								   ,f_short_local
								   ,f_individual
								   ,f_support_rating
								   ,f_outlook_national
                                  )
         values(".$id."
                ,'".$ft_new_date_t."'
                ,".$f_long_foring."
                ,".$f_long_local."
                ,".$f_outlook_long."
                ,".$f_support_level_long."
                ,".$f_short_foring."
                ,".$f_short_local."
                ,".$f_individual."
                ,".$f_support_rating."
                ,".$f_outlook_national."
                )";

   //echo $str;
   $result=exec_query($str);
   if ($result)  {echo '<div class="info-message">'.echoNLS('Данные внесены!','').'</div>';}
  }


  // long fitch rating
  $query="
           select
                 'null'  id
                ,'".echoNLS('N/A','')."' desc_ru
           union
           select
                    t.id
                   ,t.desc_ru
           from ism_dictionary t
           where  t.grp=".$FT_RATING_LONG;

   $v=array();
   $rc=sql_stmt($query, 2, $v ,2);

   // f_long_foring
   if (!isset($f_long_foring)) $f_long_foring=$v['id'][0];
   $FTLongForingMenuString = menu_list($v['desc_ru'],$f_long_foring,$v['id']);
   $FTLongForingMenuString = '<select name="f_long_foring">'.$FTLongForingMenuString.'</select>';

   // f_long_local
   if (!isset($f_long_local)) $f_long_local=$v['id'][0];
   $FTLongLocalMenuString = menu_list($v['desc_ru'],$f_long_local,$v['id']);
   $FTLongLocalMenuString = '<select name="f_long_local">'.$FTLongLocalMenuString.'</select>';

    // f_support_level_long
   if (!isset($f_support_level_long)) $f_support_level_long=$v['id'][0];
   $FTSupportLevelLongMenuString = menu_list($v['desc_ru'],$f_support_level_long,$v['id']);
   $FTSupportLevelLongMenuString = '<select name="f_support_level_long">'.$FTSupportLevelLongMenuString.'</select>';

   $query="
           select
                 'null'  id
                ,'".echoNLS('N/A','')."' desc_ru
           union
           select
                    t.id
                   ,t.desc_ru
           from ism_dictionary t
           where  t.grp=".$RATING_OUTLOOK;

   $vv=array();
   $rc=sql_stmt($query, 2, $vv ,2);

    // f_outlook_long
   if (!isset($f_outlook_long)) $f_outlook_long=$vv['id'][0];
   $FTOutlookLongMenuString = menu_list($vv['desc_ru'],$f_outlook_long,$vv['id']);
   $FTOutlookLongMenuString = '<select name="f_outlook_long">'.$FTOutlookLongMenuString.'</select>';

   // f_outlook_national
   if (!isset($f_outlook_national)) $f_outlook_national=$vv['id'][0];
   $FTOutlookNationalMenuString = menu_list($vv['desc_ru'],$f_outlook_national,$vv['id']);
   $FTOutlookNationalMenuString = '<select name="f_outlook_national">'.$FTOutlookNationalMenuString.'</select>';

   // short fitch rating
  $query="
           select
                 'null'  id
                ,'".echoNLS('N/A','')."' desc_ru
           union
           select
                    t.id
                   ,t.desc_ru
           from ism_dictionary t
           where  t.grp=".$FT_RATING_SHORT;

   $v=array();
   $rc=sql_stmt($query, 2, $v ,2);

   // f_short_foring
   if (!isset($f_short_foring)) $f_short_foring=$v['id'][0];
   $FTShortForingMenuString = menu_list($v['desc_ru'],$f_short_foring,$v['id']);
   $FTShortForingMenuString = '<select name="f_short_foring">'.$FTShortForingMenuString.'</select>';

   // f_short_local
   if (!isset($f_short_local)) $f_short_local=$v['id'][0];
   $FTShortLocalMenuString = menu_list($v['desc_ru'],$f_short_local,$v['id']);
   $FTShortLocalMenuString = '<select name="f_short_local">'.$FTShortLocalMenuString.'</select>';

   // individual fitch rating
  $query="
           select
                 'null'  id
                ,'".echoNLS('N/A','')."' desc_ru
           union
           select
                    t.id
                   ,t.desc_ru
           from ism_dictionary t
           where  t.grp=".$FT_RATING_INDIVIDUAL;

   $v=array();
   $rc=sql_stmt($query, 2, $v ,2);

   // f_individual
   if (!isset($f_individual)) $f_individual=$v['id'][0];
   $FTIndividualMenuString = menu_list($v['desc_ru'],$f_individual,$v['id']);
   $FTIndividualMenuString = '<select name="f_individual">'.$FTIndividualMenuString.'</select>';

   // add rating Fitch
   echo '
             <form name="add_ft" method=post>
             <div class="search-block grey-block">
             <ul>
             <li><div>'.echoNLS('Добавить рейтинг Fitch','').'</div></li>
             <li><div>'.echoNLS('Дата','').'</div>&nbsp;&nbsp;&nbsp;<input type="text" name="ft_new_date" id="ft_new_date"></li>
             <li><div>'.echoNLS('Долг рейт в ин валюте','').'</div>'.$FTLongForingMenuString.'</li>
             <li><div>'.echoNLS('Долг рейт в нац валюте','').'</div>'.$FTLongLocalMenuString.'</li>
             <li><div>'.echoNLS('Прогноз','').'</div>'.$FTOutlookLongMenuString.'</li>
             <li><div>'.echoNLS('Ур поддерж долг рейт','').'</div>'.$FTSupportLevelLongMenuString.'</li>
             <li><div>'.echoNLS('Кратк рейт в ин валюте','').'</div>'.$FTShortForingMenuString.'</li>
             <li><div>'.echoNLS('Кратк рейт в нац валюте','').'</div>'.$FTShortLocalMenuString.'</li>
             <li><div>'.echoNLS('Индивидуальный рейтинг','').'</div>'.$FTIndividualMenuString.'</li>
             <li><div>'.echoNLS('Рейтинг поддержки','').'</div>&nbsp;&nbsp;&nbsp;<input type="text" name="f_support_rating"></li>
             <li><div>'.echoNLS('Прогноз нац. шкала','').'</div>'.$FTOutlookNationalMenuString.'</li>
             <li><div>&nbsp;</div>&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="submit" name="add_ft" value="'.echoNLS('Добавить','').'"></span>
             </ul>
             </div>
             </form>
         ';

   // delete rating Fitch
   if (isset($delete_ft))
  {
   $str="delete from ism_fitch_rating where imetent_id=".$id." and rating_date='".$rating_date_ft."'";
   $result=exec_query($str);
   if ($result)
   {
     echo '<div class="info-message">'.echoNLS('Данные удалены!','').'</div>';
   }
  }

     $query="
           select
                    t.rating_date
                   ,DATE_FORMAT(t.rating_date,'%d.%m.%Y') format_check_date
           from ism_fitch_rating t
           where  t.imetent_id=".$id."
           order by t.rating_date desc";

   $vft=array();
   $rc=sql_stmt($query, 2, $vft ,2);

   if (!isset($rating_date_ft)) $rating_date_ft=$vft['rating_date'][0];
   $FTMenuString = menu_list($vft['format_check_date'],$rating_date_ft,$vft['rating_date']);
   $FTMenuString = '<select name="rating_date_ft">'.$FTMenuString.'</select>';


   echo '
   <form name="delete_ft" method=post>
   <div class="search-block grey-block">
       <ul>
       <li><div>'.echoNLS('Удалить рейтинг Fitch','').'</div>'.$FTMenuString.'&nbsp;&nbsp;<span><input type="submit" name="delete_ft" value="'.echoNLS('Удалить','').'"></span></li>
       </ul>
   </div>
   </form>
        ';
}

//print Fitch

    $query="
           select
                    t.rating_date
                   ,DATE_FORMAT(t.rating_date,'%d.%m.%Y') format_check_date
                   ,(select desc_ru from ism_dictionary where id=t.f_long_foring) f_long_foring_c
                   ,t.f_long_foring
                   ,(select desc_ru from ism_dictionary where id=t.f_short_foring) f_short_foring_c
                   ,t.f_short_foring
                   ,(select desc_ru from ism_dictionary where id=t.f_long_local)  f_long_local_c
                   ,t.f_long_local
                   ,(select desc_ru from ism_dictionary where id=t.f_short_local)  f_short_local_c
                   ,t.f_short_local
                   ,(select desc_ru from ism_dictionary where id=t.f_outlook_long)  f_outlook_long_c
                   ,t.f_outlook_long
                   ,(select desc_ru from ism_dictionary where id=t.f_support_level_long)  f_support_level_long_c
                   ,t.f_support_level_long
                   ,(select desc_ru from ism_dictionary where id=t.f_individual) f_individual_c
                   ,t.f_individual
                   ,f_support_rating
                   ,(select desc_ru from ism_dictionary where id=t.f_outlook_national) f_outlook_national
           from ism_fitch_rating t
           where  t.imetent_id=".$id."
           order by  t.rating_date asc
           ";

    $vft_data=array();
    $rc=sql_stmt($query, 18, $vft_data ,2);

    if ($rc>0)
    {
    echo '<div class="scroll-block-horiz">
          <table class="tab-table top-border"><thead><tr><th>Fitch Ratings</th>';
    for ($i=0;$i<sizeof($vft_data['rating_date']);$i++)
	  {
	  	 echo '<th class="right">'.$vft_data['format_check_date'][$i].'</th>';
	  }
	echo '</tr></thead><tbody><tr class="odd"><td>'.echoNLS('Долгосрочный рейтинг в ин валюте','').'</td>';
	for ($i=0;$i<sizeof($vft_data['rating_date']);$i++)
	  {
	  	 if ($i>0)
	  	 {
	        if (isset($vft_data['f_long_foring'][$i])&&isset($vft_data['f_long_foring'][$i-1]))
	        {
	        if ($vft_data['f_long_foring'][$i] > $vft_data['f_long_foring'][$i-1])  echo '<td class="right"><span class="arrow2 down">'.$vft_data['f_long_foring_c'][$i].'</span></td>';
   	        if ($vft_data['f_long_foring'][$i] < $vft_data['f_long_foring'][$i-1])  echo '<td class="right"><span class="arrow2 up">'.$vft_data['f_long_foring_c'][$i].'</span></td>';
   	        if ($vft_data['f_long_foring'][$i] == $vft_data['f_long_foring'][$i-1]) echo '<td class="right">'.$vft_data['f_long_foring_c'][$i].'</td>';
   	        }
   	        else
   	        {
   	          echo '<td class="right">'.$vft_data['f_long_foring_c'][$i].'</td>';
   	        }
	  	 }
	  	 else
	  	 {
	  	    echo '<td class="right">'.$vft_data['f_long_foring_c'][$i].'</td>';
	  	 }
	  }
	echo '</tr><tr class="even"><td>'.echoNLS('Долгосрочный рейтинг в нац валюте','').'</td>';
	for ($i=0;$i<sizeof($vft_data['rating_date']);$i++)
	  {
	  	 if ($i>0)
	  	 {
	        if (isset($vft_data['f_long_local'][$i])&&isset($vft_data['f_long_local'][$i-1]))
	        {
	        if ($vft_data['f_long_local'][$i] > $vft_data['f_long_local'][$i-1])  echo '<td class="right"><span class="arrow2 down">'.$vft_data['f_long_local_c'][$i].'</span></td>';
   	        if ($vft_data['f_long_local'][$i] < $vft_data['f_long_local'][$i-1])  echo '<td class="right"><span class="arrow2 up">'.$vft_data['f_long_local_c'][$i].'</span></td>';
   	        if ($vft_data['f_long_local'][$i] == $vft_data['f_long_local'][$i-1]) echo '<td class="right">'.$vft_data['f_long_local_c'][$i].'</td>';
   	        }
   	        else
   	        {
   	          echo '<td class="right">'.$vft_data['f_long_local_c'][$i].'</td>';
   	        }
	  	 }
	  	 else
	  	 {
	  	    echo '<td class="right">'.$vft_data['f_long_local_c'][$i].'</td>';
	  	 }
	  }
	echo '</tr><tr class="odd"><td>'.echoNLS('Прогноз','').'</td>';
	for ($i=0;$i<sizeof($vft_data['rating_date']);$i++)
	  {
	  	    echo '<td class="right">'.$vft_data['f_outlook_long_c'][$i].'</td>';
	  }
	echo '</tr><tr class="even"><td>'.echoNLS('Уровень поддержки долгосрочного рейтинга','').'</td>';
	for ($i=0;$i<sizeof($vft_data['rating_date']);$i++)
	  {
	  	    echo '<td class="right">'.$vft_data['f_support_level_long_c'][$i].'</td>';
	  }
	echo '</tr><tr class="odd"><td>'.echoNLS('Краткосрочный рейтинг в ин валюте','').'</td>';
	for ($i=0;$i<sizeof($vft_data['rating_date']);$i++)
	  {
	  	 if ($i>0)
	  	 {
	        if (isset($vft_data['f_short_foring'][$i])&&isset($vft_data['f_short_foring'][$i-1]))
	        {
	        if ($vft_data['f_short_foring'][$i] > $vft_data['f_short_foring'][$i-1])  echo '<td class="right"><span class="arrow2 down">'.$vft_data['f_short_foring_c'][$i].'</span></td>';
   	        if ($vft_data['f_short_foring'][$i] < $vft_data['f_short_foring'][$i-1])  echo '<td class="right"><span class="arrow2 up">'.$vft_data['f_short_foring_c'][$i].'</span></td>';
   	        if ($vft_data['f_short_foring'][$i] == $vft_data['f_short_foring'][$i-1]) echo '<td class="right">'.$vft_data['f_short_foring_c'][$i].'</td>';
   	        }
   	        else
   	        {
   	          echo '<td class="right">'.$vft_data['f_short_foring_c'][$i].'</td>';
   	        }
	  	 }
	  	 else
	  	 {
	  	    echo '<td class="right">'.$vft_data['f_short_foring_c'][$i].'</td>';
	  	 }
	  }
	echo '</tr><tr class="even"><td>'.echoNLS('Краткосрочный рейтинг в нац валюте','').'</td>';
	for ($i=0;$i<sizeof($vft_data['rating_date']);$i++)
	  {
	  	 if ($i>0)
	  	 {
	        if (isset($vft_data['f_short_local'][$i])&&isset($vft_data['f_short_local'][$i-1]))
	        {
	        if ($vft_data['f_short_local'][$i] > $vft_data['f_short_local'][$i-1])  echo '<td class="right"><span class="arrow2 down">'.$vft_data['f_short_local_c'][$i].'</span></td>';
   	        if ($vft_data['f_short_local'][$i] < $vft_data['f_short_local'][$i-1])  echo '<td class="right"><span class="arrow2 up">'.$vft_data['f_short_local_c'][$i].'</span></td>';
   	        if ($vft_data['f_short_local'][$i] == $vft_data['f_short_local'][$i-1]) echo '<td class="right">'.$vft_data['f_short_local_c'][$i].'</td>';
   	        }
   	        else
   	        {
   	          echo '<td class="right">'.$vft_data['f_short_local_c'][$i].'</td>';
   	        }
	  	 }
	  	 else
	  	 {
	  	    echo '<td class="right">'.$vft_data['f_short_local_c'][$i].'</td>';
	  	 }
	  }
	echo '</tr><tr class="odd"><td>'.echoNLS('Индивидуальный рейтинг','').'</td>';
	for ($i=0;$i<sizeof($vft_data['rating_date']);$i++)
	  {
	  	 if ($i>0)
	  	 {
	        if (isset($vft_data['f_individual'][$i])&&isset($vft_data['f_individual'][$i-1]))
	        {
	        if ($vft_data['f_individual'][$i] > $vft_data['f_individual'][$i-1])  echo '<td class="right"><span class="arrow2 down">'.$vft_data['f_individual_c'][$i].'</span></td>';
   	        if ($vft_data['f_individual'][$i] < $vft_data['f_individual'][$i-1])  echo '<td class="right"><span class="arrow2 up">'.$vft_data['f_individual_c'][$i].'</span></td>';
   	        if ($vft_data['f_individual'][$i] == $vft_data['f_individual'][$i-1]) echo '<td class="right">'.$vft_data['f_individual_c'][$i].'</td>';
   	        }
   	        else
   	        {
   	          echo '<td class="right">'.$vft_data['f_individual_c'][$i].'</td>';
   	        }
	  	 }
	  	 else
	  	 {
	  	    echo '<td class="right">'.$vft_data['f_individual_c'][$i].'</td>';
	  	 }
	  }
   	echo '</tr><tr class="even"><td>'.echoNLS('Рейтинг поддержки','').'</td>';
	for ($i=0;$i<sizeof($vft_data['rating_date']);$i++)
	  {
	  	    echo '<td class="right">'.$vft_data['f_support_rating'][$i].'</td>';
	  }
	echo '</tr><tr class="odd"><td>'.echoNLS('Прогноз по рейтингу по национальной шкале','').'</td>';
	for ($i=0;$i<sizeof($vft_data['rating_date']);$i++)
	  {
	  	    echo '<td class="right">'.$vft_data['f_outlook_national'][$i].'</td>';
	  }
	echo '</tr></tbody></table></div>';
    }
  //============== END Fitch ===========================================================================
?>
