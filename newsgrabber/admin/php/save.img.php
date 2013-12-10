<?
        include ("../../config.php");
        if (isset($align)) {$aln=$align;} else {$aln="";}
        if (isset($border)) {$bord=$border;} else {$bord="0";}
        if (isset($hor)) {$hsp=$hor;} else {$hsp="0";}
        if (isset($ver)) {$vsp=$ver;} else {$vsp="0";}

        $dir = $DOCUMENT_ROOT."i/";

        $_POST["path"] = (empty($_POST["path"])) ? $_FILES["imgfile"][name] : $_POST["path"];
        if (substr($_POST["path"], 0, 1) != "/") $_POST["path"] = "/".$_POST["path"];
	if (!empty($_FILES["imgfile"][name]) && $_FILES[imgfile][size] > 0 && ($_FILES["imgfile"]["type"]=="image/pjpeg" || $_FILES["imgfile"]["type"]=="image/gif" || $_FILES["imgfile"]["type"]=="image/png" || $_FILES["imgfile"]["type"]=="application/x-shockwave-flash")) {
          if (file_exists($DOCUMENT_ROOT.$_POST["path"])) {
             unlink($DOCUMENT_ROOT.$_POST["path"]);
          }

          if(@copy($_FILES["imgfile"][tmp_name], $DOCUMENT_ROOT.$_POST["path"])) {
              $name = $_POST["path"];
              $size = Getimagesize($_FILES["imgfile"][tmp_name]);
          } else {
              echo("<script language='javascript'>window.alert('Ошибка при загрузке файла ".$_POST["path"]."!');parent.InitF();</script>");
              exit;
          }
        } else {
          echo("<script language='javascript'>window.alert('Укажите графический файл!');parent.InitF();</script>");
          exit;
        }

        echo("<script language='javascript'>");
        echo("parent.imgwidth.value = '".$size[0]."';");
        echo("parent.imgheight.value = '".$size[1]."';");
        echo("parent.InitF();");
        echo("parent.itemtype[0].checked = true;");
        echo("parent.itemtype[1].checked = false;");
        echo("parent.chitemtype();");
        echo("parent.filename.value = '".$name."';");
        echo("parent.file = '".$name."';");
        echo("</script>");

?>