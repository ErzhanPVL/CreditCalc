<?php

 $myfile = fopen("newfile.txt", "r") or die("Unable to open file!");
$params=fread($myfile,filesize("newfile.txt"));
fclose($myfile);

readfile("http://".$_SERVER['HTTP_HOST']."/table.php?".$params);
 
 ?>