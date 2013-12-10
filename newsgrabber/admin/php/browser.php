<?
include ("../../config.php");
?>
<HTML>
<HEAD>
<link href="<?=$HTTP_ROOT;?>tinymce/jscripts/tiny_mce/themes/advanced/css/editor_popup.css" rel="stylesheet" type="text/css" />
<script>
function KeyPress()
<!--
{
        if(window.event.keyCode == 27)
                window.close();
}
        var cur_path = "../i";
        var menutype = null;
        var menuitem = null;
        var newp = null;
        var newppos = null;
        var arAllFiles = Array();
//-->
</script>
<title>Вставка изображения</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">

<script language="JavaScript">
<!--
var file="";
cur_path = '';
cur_file = '';

function filelist_OnFileSelect(selected_dir, selected_file, width, height, type)
{
        file = selected_dir+ ((selected_file == '') ? cur_file : selected_file);
        cur_path = selected_dir;
        if (selected_file != '') cur_file = selected_file;
        fileupload.frm.path.value = file;
        filename.value = file;
        //preview.src=cur_path + '/' + file;
        if (type == 'dir') return;
        preview.src= file;

        hiddenimg.src=file;
        imgheight.value = height;
        imgwidth.value = width;
        imgtype.value = type;
        if (type == 'SWF' || type == 'SWC') {
        document.all('pr_swf').innerHTML =  '<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"'+
                                'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"'+
                                'WIDTH="100" HEIGHT="100" id="preview_swf" ALIGN="left">'+
                                '<PARAM NAME=movie VALUE="'+selected_file+'"> <PARAM NAME=quality VALUE=high> <PARAM NAME=scale VALUE=noscale> <PARAM NAME=salign VALUE=L> <PARAM NAME=bgcolor VALUE=#CCCCCC>'+
                                '<EMBED src="'+selected_file+'" quality=high scale=noscale salign=L bgcolor=#CCCCCC  WIDTH="100" HEIGHT="100" ID="preview_swf_embed" NAME="preview_swf_embed" ALIGN="left"'+
                                'TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>'+
                                '</OBJECT>';
        document.all('pr_swf').style.display = "";
        document.all('pr_image').style.display = "none";

        } else {
        document.all('pr_swf').style.display = "none";
        document.all('pr_image').style.display = "";
        }
}

function filelist_OnLoad(path, files)
{
        return;
        menutype = null;
        menuitem = null;
        newp = null;
        newppos = null;

        //document.cookie = "lopendir=" + escape(path) + ";";
        arAllFiles = files;
        cur_path = path;
        while(ddpath.length>0)ddpath.remove(0);

        var p = 1;
        var n = 0;
        var w = false;
        var allp = "";
        var oOption = document.createElement("OPTION");
        ddpath.options.add(oOption);
        oOption.innerText = "корень сайта";
        oOption.value = "";
        if(path.indexOf("/")>-1)
        {
                while(true)
                {
                        n++;
                        p = path.indexOf("/", p)+1;
                        allp = (p>0?path.substring(0, p-1):path);

                        var namep = ""
                        for(i=0; i<n; i++)
                                namep = namep + ".&nbsp;.&nbsp;";
                        namep = namep + allp.substring(allp.lastIndexOf("/")+1);

                        oOption = document.createElement("OPTION");
                        ddpath.options.add(oOption);
                        oOption.innerHTML = namep;
                        oOption.value = allp;
                        if(p<1)break;
                }
        }
        ddpath.selectedIndex = ddpath.length-1;

        fileupload.frm.path.value = cur_path + "/" + file;
        filename.value = cur_path + "/" + file;
}

function OK(okpath)
{
        var arr = new Array();
        //arr["path"] = cur_path + '/' + okpath;
        arr["path"] = okpath;
        arr["height"] = parseInt(imgheight.value);
        arr["width"] = parseInt(imgwidth.value);
        arr["type"] = imgtype.value;
        window.returnValue = arr;
        window.close();
}

function chitemtype()
{
        if(itemtype[0].checked)
        {
                load.style.display="inline";
                upload.style.display="none";
                wf.style.display="none";
        }
        else if(itemtype[1].checked)
        {
                upload.style.display="inline";
                load.style.display="none";
                wf.style.display="none";
        }
        else
        {
                WF_File();
                wf.style.display="inline";
                upload.style.display="none";
                load.style.display="none";
        }
}

function ShowSize(obj)
{
        imgwidth.value=obj.width;
        imgheight.value=obj.height;
        var W=obj.width, H=obj.height;
        if(W>100)
        {
                H=H*((100.0)/W);
                W=100;
        }

        if(H>100)
        {
                W=W*((100.0)/H);
                H=100;
        }

        if(W>100)W=100;

        preview.width=W;
        preview.height=H;
}

function WF_File()
{
        var str_file;
        var str_file = wf_filename[wf_filename.selectedIndex].value;
        preview.src=cur_path + '/' + str_file;
        hiddenimg.src=str_file;
}

function NewFileName()
{
    var str_file;
    var str_file = fileupload.frm.imgfile.value;
    file = str_file.substr(str_file.lastIndexOf("\\")+1);
    cur_file = file;
    fileupload.frm.path.value = cur_path + file;
        preview.src=fileupload.frm.imgfile.value;
        hiddenimg.src=fileupload.frm.imgfile.value;
}

function swf_test () {

//alert(document.all['preview_swf'].preview_swf_embed.src);
//alert(document.all['preview_swf_embed'].src);

t = "";
for(i in document.all['preview_swf']) {
  t += i + ' = ' + document.all['preview_swf'][i] + '\n';
}
//alert(t);
//document.write(t);

}

//-->
</script>

</HEAD>
<BODY onKeyPress="KeyPress()">

<script language=javascript for=Ok event=onclick>
<!--
        if(itemtype[0].checked)
        {
                if(filename.value.length<=0)
                {
                        alert("Введите имя файла!");
                        return ;
                }
                OK(filename.value);
        }
        else if(itemtype[1].checked)
        {
                if(fileupload.frm.imgfile.value<=0)
                {
                        alert("Введите имя файла!");
                        return ;
                }
                fileupload.frm.submit();
        }
        else
        {
                var str_file = wf_filename[wf_filename.selectedIndex].value;
                OK(str_file);
        }
// -->
</script>

<center>

<img id=hiddenimg style="visibility:hidden; position: absolute; left:-10000; top: -10000px;" onerror="badimg = true;" onload="ShowSize(this)">
<table cellspacing=0 cellpadding="3" width="100%">
<tr>
        <td colspan="2">
                <table cellspacing=0 cellpadding="2" width="100%">
                <script>document.write('<tr><td><iframe name="filelist" src="<?=$HTTP_ROOT;?>admin/php/filelist.php?filter=' + window.dialogArguments['filter'].join(',') + '" style="width:100%"  height="180"></iframe></td>');</script>
                <td valign="top" width="100">
                        <table cellpadding="0" cellspacing="0" border="1" width="120">
                                <tr height="120"><td align="center" valign="middle">
                                <input type="hidden" name="imgtype" value="">
                                <div id="pr_image" style="width:100px; height: 100px; overflow: auto">
                                <img src="<?=$HTTP_ROOT;?>admin/editor/images/1.gif" width="100" name="preview">
                                </div>

                                <div id="pr_swf" style="display: none;">
                                </div>
                                </td></tr>
                                <tr><td>
                                <table>
                                <tr>
                                        <td align="right" width="0%" nowrap><font class="tablebodytext">Ширина:&nbsp;</font></td>
                                        <td width="100%"><input class="typeinput" type="text" size="3" name="imgwidth"></td>
                                </tr>
                                <tr>
                                        <td align="right" nowrap><font class="tablebodytext">Высота:&nbsp;</font></td>
                                        <td><input class="typeinput" type="text" size="3"  name="imgheight"></td>
                                </tr>
                                </table>
                        </table>
                </td></tr></table>
        </td>
</tr>
<tr>
        <td colspan="2"><input class="input_noborder" style="background-color: #F0F0EE;" type="radio" name="itemtype" value="n" onclick="chitemtype()" checked> <span onmousedown="itemtype[0].checked=true;chitemtype();">Открыть с сайта</span><br></td>
</tr>
<tr>
        <td colspan="2"><input class="input_noborder" style="background-color: #F0F0EE;" type="radio" name="itemtype" value="e" onclick="chitemtype()"> <span onmousedown="itemtype[1].checked=true;chitemtype();">Загрузить на сервер</span></td>
</tr>


<tr height="68" valign="top">
<td width="0%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<td width="100%">
        <div id="load">
        <table width="100%" cellpadding="3" cellspacing="0" border="0">
                <tr>
                        <td width="0%" nowrap>Имя файла:</td>
                        <td width="100%"><input type="text" style="width:100%" name="filename" value=""></td>
                </tr>
        </table>
        </div>
        <div id="wf" style="display:none">
                </div>
        <div id="upload" style="display:none">
        <iframe name="fileupload" style="width:100%" height="60" FRAMEBORDER="0"></iframe>
        <script language="JavaScript">
        <!--
        function InitF()
        {
                fileupload.document.write('<HTML><HEAD>');
                fileupload.document.write('<link href="<?=$HTTP_ROOT;?>tinymce/jscripts/tiny_mce/themes/advanced/css/editor_popup.css" rel="stylesheet" type="text/css" />');
                fileupload.document.write('<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">');
                fileupload.document.write('</HEAD>');
                fileupload.document.write('<BODY  scroll="NO">');
                fileupload.document.write('<table width="100%" cellpadding="3" cellspacing="0" border="0">');
                fileupload.document.write('<form action="<?=$HTTP_ROOT;?>admin/php/save.img.php" id="frm" method="POST" enctype="multipart/form-data">');
                fileupload.document.write('<tr><td width="0%" nowrap>Файл для загрузки:</td>');
                fileupload.document.write('<td width="100%"><input type="file" style="width:100%" name="imgfile" value="" onchange="parent.NewFileName();"></td>');
                fileupload.document.write('</tr><tr>');
                fileupload.document.write('<td width="0%" nowrap>Название на сервере:</td>');
                fileupload.document.write('<td width="100%"><input type="text" style="width:100%" name="path" value=""><input type="hidden" name="saveimg" value="Y"></td>');
                fileupload.document.write('</tr></form></table></body></html>');
        }
        InitF();
        //-->
        </script>
        </div>
</td></tr>
</table>
<BUTTON ID=Ok TYPE=SUBMIT>OK</BUTTON>&nbsp;<BUTTON ONCLICK="window.close();">Отмена</BUTTON>
</center>
</BODY>
</HTML>