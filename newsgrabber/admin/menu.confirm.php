<?
$HUI_WAM1 = "ихк чщ фхф юфп-фп тбъъдеодйфе";
$HUI_WAM2 = "ХУЙ ВЫ ТУТ ЧТО-ТО РАЗЗДЕНДИТЕ";

/* ----------------------------------------------- */
/* --- nulled by someone for http://nulled.ws/ --- */
/* ----------------------------------------------- */

$_config_loaded = false;
@include ("config.php");
if ($_config_loaded !== true) {
   die("config.php not found");
}

if (file_exists($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__))) include($DOCUMENT_ROOT."/admin/lang/".$_SESSION["set_lang"]."/".basename(__FILE__));
?>
<HTML>
<HEAD>
<STYLE TYPE="text/css">
BODY   {margin-left:10; font-family:Arial; font-size:12px; background:menu}
BUTTON {width:5em}
TABLE  {font-family:Arial; font-size:12px}
P      {text-align:center}


.tablebodysm {background-color:#F1F1F1; padding:2px;}
.btndef{BORDER-BOTTOM: menu solid 1px; BORDER-LEFT: menu solid 1px; BORDER-RIGHT: menu solid 1px; BORDER-TOP: menu solid 1px;}
.btn{BORDER-BOTTOM: buttonshadow solid 1px; BORDER-LEFT: #F4F4F4 solid 1px; BORDER-RIGHT: buttonshadow solid 1px; BORDER-TOP:  #F4F4F4 solid 1px;}
.btnDown{BACKGROUND-COLOR: #D6D6CE; BORDER-BOTTOM: #F4F4F4 solid 1px;BORDER-LEFT: buttonshadow solid 1px;BORDER-RIGHT: #F4F4F4 solid 1px;BORDER-TOP:  buttonshadow solid 1px;}
</STYLE>
<script>
var menu_id = 0;
function KeyPress()
{
        if(window.event.keyCode == 27)
                window.close();
}
</script>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
</HEAD>
<BODY onKeyPress="KeyPress()">
<style>
.tb{BORDER-BOTTOM: buttonface solid 1px;BORDER-LEFT: buttonface solid 1px;BORDER-RIGHT: buttonface solid 1px;BORDER-TOP: buttonface solid 1px;HEIGHT: 19px;WIDTH: 19px;}
.button{BORDER-BOTTOM: buttonshadow solid 1px; BORDER-LEFT: buttonhighlight solid 1px; BORDER-RIGHT: buttonshadow solid 1px; BORDER-TOP:  buttonhighlight solid 1px; HEIGHT: 19px; WIDTH: 19px;}
.buttonDown{BACKGROUND-COLOR: buttonface;BORDER-BOTTOM: buttonhighlight solid 1px;BORDER-LEFT: buttonshadow solid 1px;BORDER-RIGHT: buttonhighlight solid 1px;BORDER-TOP:  buttonshadow solid 1px; HEIGHT: 19px; WIDTH: 19px;}
</style>
<SCRIPT LANGUAGE=JavaScript FOR=window EVENT=onload>
<!--
//url.value = window.dialogArguments;
// -->
</SCRIPT>
<SCRIPT LANGUAGE=JavaScript FOR=Ok EVENT=onclick>
<!--
  window.returnValue = menu_id;
  window.close();
// -->
</SCRIPT>

<CENTER>
<br>
<?
        
        $menu_table = "menu";

        $db = new Db();
        if (is_numeric($_GET["id"]) && !empty($_GET["id"]))
        $query = "SELECT menu_owner, menu_name FROM $menu_table WHERE menu_id = ".$_GET["id"];
        $menu = $db ->fetch($db->query($query));                   //menu_owner = ".$menu["menu_owner"]."  and
        $query = "SELECT menu_id, menu_name FROM $menu_table WHERE menu_id <> ".$_GET["id"]." ORDER by menu_owner, menu_order, menu_id";
        $map = $db ->fetchall($db->query($query));
?>
<table>
                <tr>
                        <td>
                            Вы удаляете раздел меню "<b><?=htmlspecialchars($menu["menu_name"])?></b>", укажите что делать с вложенными в него пунктами меню, перенести или удалить
                        </td>
                </tr>
                <tr bgcolor=<?=$value["bg"]?>>
                        <td>
                                <input type="radio" checked name="menu_id" value="0"> удалить
                                <!-- <a href="javascript:" onClick="menu_id.value = '0'; menu_name.value = 'удалить';return false; ">удалить</a> -->
                        </td>
                </tr>


<?
        foreach($map as $value) {
?>
                <tr bgcolor=<?=$value["bg"]?>>
                        <td>
                                <input type="radio" name="menu_id" value="<?=$value["menu_id"]?>" onClick="menu_id = '<?=$value["menu_id"]?>';"> <?=$value["menu_name"]?>
                                <!-- <a href="javascript:" onClick="menu_id.value = '<?=$value["menu_id"]?>'; menu_name.value = '<?=$value["menu_name"]?>';return false; "><?=$value["menu_name"]?></a> -->
                        </td>
                </tr>
<?

        }


?>
</table><br>
<BUTTON ID=Ok TYPE=SUBMIT>OK</BUTTON>&nbsp;<BUTTON ONCLICK="window.close();">Отмена</BUTTON>
</CENTER>
</BODY>
</HTML>