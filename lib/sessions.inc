<?php
/* ============================================ */
/* Purpose of this set:                         */
/*  Original support of Session Var is badly !  */
/*                                              */
/* We have following functions:                 */
/*                                              */
/*  session_set("var1",...,"varN"); - Set Sess. */
/*     Var & save it in file or Oracle's table  */
/*                                              */
/*  session_remove("var1",...,"varN"); - Remove */
/*     Sess. Var from file or Oracle's table    */
/* ============================================ */

global $CLAER_ID;
$remlogin = false;

$time_limit=2592000;   /* 30 days as second */


/* ========= Declare SESSION ARRAY */
$SESSION_ARRAY = array();

/* ========= Get Static Var from GeoWebStaticInfo.dat */
if(isset($SESSION_TRUE) && $SESSION_TRUE=='TRUE')
{
    /* ========= If SESSION_ID absent in Cookie - set it */

    if (isset($CLAER_ID))
    {
        if(!isset($HTTP_POST_VARS['longlive']))
        {
            $SESSION_ID2=uniqid("");
            $remlogin = true;
            if(isset($longlive)) unset($longlive);
        }
    }
    if (!isset($SESSION_ID2)) $SESSION_ID2=uniqid("");

    if(!is_dir($SESSION_FILE)) htmlFatal("Bad session File Dir: $SESSION_FILE");
    if(file_exists("$SESSION_FILE"."/"."$SESSION_ID2"))
    {
        if(!($fd=fopen("$SESSION_FILE"."/"."$SESSION_ID2","rb")))
        htmlFatal("Can't read Session Var from File");
        $FSize=filesize("$SESSION_FILE"."/"."$SESSION_ID2");
        $Buffer=fread($fd,$FSize+10);
        session_restore($Buffer);
        fclose($fd);
    }

    if($remlogin)  {   if(isset($longlive)) unset($longlive);  }

    if(isset($longlive)) setcookie("SESSION_ID2","$SESSION_ID2",time()+$time_limit,"/");  /* Member me */
    else                 setcookie("SESSION_ID2","$SESSION_ID2",0,"/");  /* Dont Member me */

    if (isset($lastGrp))
    {
        $lastGrpOld = $lastGrp;
        session_set('lastGrpOld');
    }

}

/* ================== */
/* session_save       */
/* ================== */
function session_save()
{
    global $SESSION_FILE;
    if(isset($SESSION_FILE)) session_save_file();
    else                     session_save_table();
}

/* ================== */
/* session_save_file  */
/* ================== */
function session_save_file()
{
    global $SESSION_ID2,$SESSION_FILE,$SESSION_ARRAY;
    if(!is_dir($SESSION_FILE))
    htmlFatal("Bad session File Dir: $SESSION_FILE");
    if(!($fd=fopen("$SESSION_FILE"."/"."$SESSION_ID2","wb")))
    htmlFatal("Can't Save Session Var to File");
    $Str=serialize($SESSION_ARRAY);
    fwrite($fd,$Str);
    //fputs($fd,$Str);
    fclose($fd);
}

/* ================== */
/* session_save_table */
/* ================== */
function session_save_table()
{
    global $SESSION_ID2,$Cfg_Locations;
    global $SESSION_ARRAY;
    beginner("$Cfg_Locations");
    support();
    global $ADMIN,$ADMPASSWD,$ADMDATABASES;
    $conn=ora_conn($ADMIN,$ADMPASSWD,$ADMDATABASES);
    $Str=serialize($SESSION_ARRAY);
    $curs=0; $vars='';
    $quer="update sessions set CONTENT = '$Str' where session_id='$SESSION_ID2'";
    ora_stmt($conn,$curs,$quer,0,$vars,3);
    ora_commit($conn);
}

/* ========================================== */
/* ========== Declare All Session's functions */
/* ========================================== */

/* ================== */
/* session_restore    */
/* ================== */
function session_restore($inp)
{
    global $SESSION_ARRAY;
    $SESSION_ARRAY = array();
    $SESSION_ARRAY=unserialize($inp);
    if(sizeof($SESSION_ARRAY)>0)
    foreach($SESSION_ARRAY as $key => $value)
    $GLOBALS["$key"]=unserialize($value);
}

/* ================== */
/* session_set        */
/* ================== */
function session_set()
{
    global $SESSION_ARRAY;
    if (func_num_args()>0)
    {
        for($i=0; $i<func_num_args(); $i++)
        {
            $inp=func_get_arg($i);
            global ${$inp};
            $SESSION_ARRAY["$inp"]=serialize(${$inp});
        }
        session_save();
    }
}

/* ================== */
/* session_remove     */
/* ================== */
function session_remove()
{
    global $SESSION_ARRAY;
    if (func_num_args()>0)
    if(sizeof($SESSION_ARRAY)>0)
    {
        for($i=0; $i<func_num_args(); $i++)
        {
            $inp=func_get_arg($i);
            $ThisArray=$SESSION_ARRAY;
            $SESSION_ARRAY = array();
            foreach($ThisArray  as $key => $value)
            if ($key != "$inp") $SESSION_ARRAY["$key"]=$ThisArray["$key"];
        }
        session_save();
    }
}

/* ================== */
/* session_end        */
/* ================== */
function session_end()
{
    global $SESSION_ARRAY;
    $SESSION_ARRAY = array();
    session_save_cis();
}
/* ======= End ============ */
?>