<?php
session_start();
echo '$(document).ready(function(){
	$(\'#player\').html(\'<img src="images/ajax-loader.gif" />\');
    setTimeout(function(){
        $(\'#player\').delay(7000).html("<audio id=\'audio\' onended=\'play_ended();\' style=\'padding-top:2%;\' autoplay=\'autoplay\' controls=\'controls\' src=\'music/'.str_replace(' ','%20',$_SESSION["filename"]).'\'></audio> <br /> <a data-ajax=\'false\'href=\'music/'.str_replace(' ','%20',$_SESSION["filename"]).'\'><img src=\'images/download_small.gif\'>Download</a>");
    }, 5000);  });';
?>
