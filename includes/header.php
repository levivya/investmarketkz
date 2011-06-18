<div id="header">
    <a class="logo" href="/"><img src="/media/images/logo.gif" width="278" height="55"  alt="вклады, депозиты банков, банки казахстана, пенсионные фонды, нпф, паевые фонды, пифы, инвестиции" /></a>
    <noindex><div class="right"><a href="/income_compare.php" rel="nofollow"><img src="/media/images/banner.png" width="469" height="60" alt="сравнить доходность инструментов" /></a></div></noindex>
    <div class="info"> <?php echo russian_date("l, j F Y  г.");  ?>
      <div class="header_icons"><a href="/sitemap.php" class="icon_map" title="Карта сайта"></a> <a href="/rss.xml" class="icon_rss" title="Канал RSS"></a></div>
      <!--<div class="exchange"> USD <span>31.5473</span> / EUR <span>45.2893</span></div>-->
    </div>
    <ul class="menu">
      <?php if($selected_menu!='main') echo '<noindex>';?>
      <li <?php if($selected_menu=='main') echo 'class="active"';?> > <a href="/">Главная</a>
        <div class="submenu"> <a href="/ask_question.php" title="Задать вопрос финансовому консультанту">Консультант</a> | <a href="/income_compare.php">Куда вложить деньги</a>|  <a href="/articles.php?type=investor_school">Школа инвестора</a> | <a href="/vportfolio.php" title="V-Счет виртуальные инвестиции">V-Счет</a> | <a href="/media_list.php">iTV-Онлайн Видео</a> <noindex>|<a href="/phpBB2/index.php" target="_blank" rel="nofollow">Форум</a> | <a href="/contact.php" rel="nofollow">Контакты</a></noindex></div>
      </li>
      <?php if($selected_menu!='main') echo '</noindex>';?>
      <?php if($selected_menu!='deposit') echo '<noindex>';?>
      <li <?php if($selected_menu=='deposit') echo 'class="active"';?> > <a href="/deposit/">Вклады</a>
        <div class="submenu"> <a href="/deposit/deposits.php">Депозиты банков Казахстана</a> | <a href="/deposit/calculator.php">Депозитный калькулятор</a>  | <a href="/deposit/banks.php">Банки Казахстана</a> | <a href="/deposit/banks_rating.php">Рейтинг банков</a></div>
      </li>
      <?php if($selected_menu!='deposit') echo '</noindex>';?>
      <?php if($selected_menu!='npf') echo '<noindex>';?>
      <li <?php if($selected_menu=='npf') echo 'class="active"';?> > <a href="/npf/" title="Пенсионные фонды Казахстана">НПФ</a>
        <div class="submenu"><a href="/npf/rating.php">Рейтинг пенсионных фондов</a> | <a href="/npf/calculator1.php">Расчет пенсии</a> | <a href="/npf/calculator2.php">Государственная пенсия</a> |  <a href="/article_archive.php?type=investor_school&subtype_id=3&title=Моя пенсия" title="Моя пенсия - советы будущему пенсионеру">Моя пенсия</a>| <a href="/im_index.php?type=npfkz">Индекс НПФКЗ</a> </div>
      </li>
      <?php if($selected_menu!='npf') echo '</noindex>';?>
      <?php if($selected_menu!='pif') echo '<noindex>';?>
      <li <?php if($selected_menu=='pif') echo 'class="active"';?> > <a href="/pif/">ПИФы</a>
        <div class="submenu"> <a href="/pif/funds.php"  title="Паевые фонды(ПИФы) Казахстана">Паевые фонды</a> | <a href="/pif/rating.php" title="Рейтинг ПИФов">Рейтинг ПИФов</a> | <a href="/pif/map.php" title="Доходность-Риск ПИФов">Доходность-Риск</a>| <a href="/pif/calculator.php" title="Калькулятор ПИФов">Калькулятор</a>| <a href="/pif/analysis.php">Анализ доходности</a>| <a href="/im_index.php?type=pifkz">Индекс ПИФКЗ</a></div>
      </li>
      <?php if($selected_menu!='pif') echo '</noindex>';?>
      <?php if($selected_menu!='news') echo '<noindex>';?>
      <li <?php if($selected_menu=='news') echo 'class="active"';?> > <a href="/articles.php">Новости</a>
        <div class="submenu"> <a href="/articles.php?type=news" title="Новости">Новости</a> | <a href="/articles.php?type=news-bank" title="Новости банков">Новости банков</a> | <a href="/articles.php?type=news-npf" title="Пенсия в Казахстане">Пенсия в Казахстане</a>| <a href="/articles.php?type=news-site" title="Новости сайта">Новости сайта</a> | <a href="/articles.php?type=analytic" title="Аналитика">Аналитика</a> | <a href="/rss.xml">RSS</a></div>
      </li>
      <?php if($selected_menu!='news') echo '</noindex>';?>
      <noindex>
      <li <?php if($selected_menu=='company') echo 'class="active last"'; else echo 'class="last"';?> > <a href="/company/">Компании</a>
        <div class="submenu"><a href="#">Компании</a> | <a href="#">Горизонтальный анализ</a> | <a href="#">Вертикальный анализ</a> <!--| <a href="#">Контакты-Компании</a> --></div>
      </li>
      </noindex>
    </ul>
    <noindex>
    <div class="reg-enter">
    <?php
    if (isset($grp)){    	if ($grp==2) echo '<a href="/admin/index.php" rel="nofollow">Администратор</a>';
    	if ($grp==4) echo '<a href="/vprofile/index.php?type=virtual" rel="nofollow">V-Портфель</a>';    }
    else
    { echo '<a href="/registration.php" rel="nofollow">Регистрация</a>';}
    ?>
    |<a href="<?php $str=((isset($user_id))?("/log_out.php"):("/log_test.php")); echo $str;?>" rel="nofollow"><?php $str=((isset($user_id))?("Выход"):("Вход")); echo $str;?></a>
    </div>
    <!-- Search -->
    <div class="search">
		<form action="http://www.google.kz/cse" id="cse-search-box" target="_blank">
		    <input type="hidden" name="cx" value="partner-pub-2712511792023009:iktfw0kn3fy" />
		    <input type="hidden" name="ie" value="utf-8" />
		    <input type="text" name="q" maxlength="255" value="&nbsp;Рейтинг фондов" onfocus="clear_field(this)" />
            <span><input type="submit" name="sa" value="Поиск" /></span>
		</form>
		<script type="text/javascript" src="http://www.google.kz/cse/brand?form=cse-search-box&amp;lang=ru"></script>
    </div>
    <?php
     $query="
			  select
			           t.news_id  article_id
			          ,date_format(t.news_date,'%d.%m.%y') vdate_format
   			          ,substr(t.title,1,70) title
                      ,t.news_date article_date
                      ,'news' link
			  from     ism_news t
			  where ntype=8
			  union
			  select
                       a.analyt_id article_id
    			       ,date_format(a.analyt_date,'%d.%m.%y') vdate_format
   			           ,substr(a.title,1,70)  title
                       ,a.analyt_date article_date
                       ,'analytic' link
			  from     ism_analytics a
			  where atype=2
              order by article_date desc
              limit 0,1
            ";
     $vnews=array();
	 $rc=sql_stmt($query, 5, $vnews ,1);
    ?>
    <div class="info2"> New! <a href="/article.php?type=<?php echo $vnews['link'][0];?>&id=<?php echo $vnews['article_id'][0];?>" title="<?php echo $vnews['title'][0];?>"><?php echo $vnews['title'][0];?></a> </div>
    </noindex>
    <!-- end #header -->
</div>