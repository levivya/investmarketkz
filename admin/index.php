<?php include($_SERVER["DOCUMENT_ROOT"]."/lib/misc.inc");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Панель администратора</title>
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

<div class="mainContent">
<div class="title">Панель администратора</div>


<?php
if (isset($grp) && $grp==2)
{
?>
<div class="search-block">
<ul class="list"><strong>Управление</strong>
    <li><a href="vcustomers.php">V-пользователи системы</a></li>
    <li><a href="customer_questions.php">Вопросы клиентов</a></li>
    <li><a href="../pbmadmin/admin.php">Управление баннерами</a></li>
    <li><a href="../poller/admin/adm-poller.php">Опросы</a></li>
</ul>
<ul class="list"><strong>Материалы</strong>
    <li><a href="publish_rss_news.php">Публиковать новости из RSS Feeds</a></li>
    <li><a href="add_article.php">Добавить/Удалить материал</a></li>
    <li><a href="../newsgrabber/admin/index.php">NewsGragger</a></li>
    <li><a href="admin_video.php">Добавить/Удалить МЕДИА контент</a></li>
</ul>
<ul class="list"><strong>ПИФ</strong>
    <li><a href="add_company.php">Добавить/Удалить УК</a></li>
    <li><a href="add_pif.php">Добавить/Удалить ПИФ</a></li>
    <li><a href="add_fund_values.php?type=pif">Внести данные по цене пая</a></li>
    <li><a href="load_pif_data.php">Загрузить данные по цене пая из Excel</a></li>
    <li><a href="load_fund_structure.php?type=pif">Загрузить структуру активов фондов с АФН</a> (<a href="load_fund_structure_data.xls">скачать файл-образец для загрузки</a>)</li>
</ul>
<ul class="list"><strong>НПФ</strong>
    <li><a href="add_npf_company.php">Добавить/Удалить КУПА</a></li>
    <li><a href="add_npf.php">Добавить/Удалить НПФ</a></li>
    <li><a href="add_fund_values.php?type=npf">Внести данные по УПЕ и накоплениям</a></li>
    <li><a href="load_pension_fund_data.php">Загрузить данные по УПЕ и накоплениям с АФН</a> (<a href="load_pension_fund_data.xls">скачать файл-образец для загрузки</a>)</li>
    <li><a href="load_fund_structure.php?type=npf">Загрузить структуру активов фондов с АФН</a> (<a href="load_pension_fund_structure_data.xls">скачать файл-образец для загрузки</a>)</li>
    <li><a href="load_pension_fund_assets_data.php">Загрузить данные по активам/обязательствам НПФ с АФН</a> (<a href="load_pension_fund_assets_data.xls">скачать файл-образец для загрузки</a>)</li>

</ul>
<ul class="list"><strong>Банки</strong>
    <li><a href="add_bank.php">Добавить/Удалить Банк</a></li>
    <li><a href="add_deposit.php">Добавить/Удалить Депозит</a></li>
</ul>

<ul class="list"><strong>Загрузка</strong>
    <li><a href="get_fund_data_from_investfunds.php">Загрузить статистику по ПИФам с Investfunds.kz</a> (<a href="get_list_of_logs.php">logs</a>)</li>
    <li><a href="calculate_pifkz_index.php">Расчитать индекс ПИФКЗ</a> (<a href="restore_pifkz_from_backup.php">востановить из backup</a>)</li>
    <li><a href="calculate_fund_year_avg_income.php">Расчитать СГД ПИФов</a></li>
    <li><a href="calculate_npfkz_index.php">Расчитать индекс НПФКЗ</a></li>
</ul>

</div>
<?php
}
else
{
?>
   <div class="error-message">У Вас нет доступа к данной странице!</div>

<?php
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