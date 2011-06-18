<?php
require($_SERVER["DOCUMENT_ROOT"]. "/main.cfg");
$INC_DIR = $_SERVER["DOCUMENT_ROOT"]. "/lib/";
include($INC_DIR. "mysql.inc");

if(isset($id))
{
$conn=conn($DB_USER,$DB_PASS , $DB_DATABASE, $DB_HOSTNAME);
$query = "select name, type, size, content from ism_documents where id = ".clean_int($id);

$vdoc=array();
$rc=sql_stmt($query, 4, $vdoc ,1);

$size=$vdoc['size'][0];
$type=$vdoc['type'][0];
$name=$vdoc['name'][0];
$content=$vdoc['content'][0];

header("Content-length: $size");
header("Content-type: $type");
header("Content-Disposition: attachment; filename=$name");
echo $content;
disconn($conn);
exit;

}

?>