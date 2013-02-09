<?php
header("Content-type: application/octet-stream"); 
header("Content-transfer-encoding: binary");
header('Cache-Control: no-cache');

echo file_get_contents('../music/'.$_GET["filename"]);
?>
