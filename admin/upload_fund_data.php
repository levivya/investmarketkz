<html>
<head>
  <title>Загрузить данные</title>
  <link type="text/css" href="../css/style.css" rel=stylesheet  />
  <script type="text/javascript" src="../scripts/misc.js"></script>
</head>
<body style="background:#fff;" marginright="2" marginheight="2" leftmargin="2" topmargin="2" marginwidth="2">
<div class="info-message">
Информация за каждый день вноситься в отдельную строчку в формате приведенном ниже. Если данные за определенный день существуют, они будут заменены на новые.
</div>
<form name="upload" method="post">
<input type="hidden" name="id" value="<?php echo $id;?>">
<div class="search-block grey-block">
<textarea cols="100" rows="30" name="fund_data" style="font-style: italic" onfocus="clear_field(this,'дд.мм.уууу;значение;активы;')">дд.мм.уууу;значение;активы</textarea>
<br><br>
&nbsp;&nbsp;&nbsp;&nbsp;<span><input type="submit" name="upload" class="red" value="Загрузить">&nbsp;&nbsp;<input type="button" value="Отмена" class="nyroModalClose" id="closeBut"></span>
</div>
</form>
</body>
</html>
