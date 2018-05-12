<?php
//$r=deldir($_GET['q']."%0d");
echo 'good';
$e=error_get_last();
echo $e['message'];
function deldir($dir) {
    //先删除目录下的文件：
    $dh=opendir($dir);

    while ($file=readdir($dh)) {
        if($file!="." && $file!="..") {
            $fullpath=$dir."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }

    closedir($dh);
    //删除当前文件夹：
    if(rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}
return;

