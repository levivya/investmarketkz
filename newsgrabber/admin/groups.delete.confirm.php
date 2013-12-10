<?
$HUI_WAM1 = "èõê ÷ù ôõô þôï-ôï òáúúäåîäéôå";
$HUI_WAM2 = "ÕÓÉ ÂÛ ÒÓÒ ×ÒÎ-ÒÎ ÐÀÇÇÄÅÍÄÈÒÅ";

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
var groups_id = 0;
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
  window.returnValue = groups_id;
  window.close();
// -->
</SCRIPT>

<CENTER>
<br>
<?
        $groups_table = "groups";

        if (is_numeric($_GET["id"]) && !empty($_GET["id"])) {
            $query = "SELECT groups_owner, groups_name FROM $groups_table WHERE groups_id = ".$_GET["id"];
            $menu = $db ->fetch($db->query($query));
        }
?>
<table>
                <tr>
                        <td>
                            <?=$lang["caption_delete1"]?> <b><?=$menu["groups_name"]?></b>, <?=$lang["caption_delete2"]?>:
                        </td>
                </tr>
                <tr bgcolor=<?=$value["bg"]?>>
                        <td>
                                <input type="radio" checked name="groups_id" value="0"> <?=$lang["caption_delete_all"]?>
                        </td>
                </tr>


<?
        $query = "SELECT groups_id, groups_name, groups_owner FROM groups WHERE groups_id <> '".$_GET["id"]."' and groups_owner = 0 ORDER by groups_order, groups_id";
        $groups = $db ->fetchall($db->query($query));
        if (is_array($groups))
        foreach($groups as $value) {
?>
                <tr>
                        <td>
                                <input type="radio" name="groups_id" value="<?=$value["groups_id"]?>" onClick="groups_id = '<?=$value["groups_id"]?>';"> <?=(($value["groups_owner"] == 0) ? "<b>" : "").$value["groups_name"].(($value["groups_owner"] == 0) ? "</b>" : "")?>
                        </td>
                </tr>
<?
             $query = "SELECT groups_id, groups_name, groups_owner FROM groups WHERE groups_id <> '".$_GET["id"]."' and groups_owner = ".$value["groups_id"]." ORDER by groups_order, groups_id";
             $subgroups = $db ->fetchall($db->query($query));
             foreach($subgroups as $v) {
             ?>
                    <tr>
                            <td>
                                    <input type="radio" name="groups_id" value="<?=$v["groups_id"]?>" onClick="groups_id = '<?=$v["groups_id"]?>';"> <?=(($v["groups_owner"] == 0) ? "<b>" : "").$v["groups_name"].(($v["groups_owner"] == 0) ? "</b>" : "")?>
                            </td>
                    </tr>
             <?
            }
             
        }


?>
</table><br>
<BUTTON ID=Ok TYPE=SUBMIT><?=$lang["button_ok"]?></BUTTON>&nbsp;<BUTTON ONCLICK="window.close();"><?=$lang["button_cancel"]?></BUTTON>
</CENTER>
</BODY>
</HTML>