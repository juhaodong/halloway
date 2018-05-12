<?php
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');
$indexInfo=fopen("Orders/index.txt","r");
$dateInfo=substr(fgets($indexInfo),0,-1);
$currentData=date('YmdHi');

if(strcmp($dateInfo,$currentData)!=0){
    $index=1;
    fwrite(fopen("Orders/index.txt","w"),$currentData."\n".$index);
}else{
    $index=intval(fgets($indexInfo));
    $index=$index+1;
    fwrite(fopen("Orders/index.txt","w"),$currentData."\n".$index);

}

$orderPath="Orders/";
$index=str_pad($index,4,"0",STR_PAD_LEFT);
if($_GET['q']==1){
    error_reporting(E_ALL);
    $username=$_GET['CreaterId'];
    $limit=2;

    $id=$_GET['Type'].date('YmdHi').$index;

    $orderPath=$orderPath.$id;
    $eventFilePath= $orderPath."/".$id.".txt";

    if($_GET['EventName']==""){
        echo "请输入活动名称";
        return;
    }
    $info="";
    foreach($_GET as $index => $value) {
        if($index=="q"){
            continue;
        }
        $info .= $index . ":" . $value;
        $info .="\r\n";
    }


    /*$myfile = fopen("Users/".$username."/creat.txt", "r") /*or die("Users/".$username."/creat.txt");
     $error = error_get_last();
      echo $error['message'];
      return;

    $i=0;*/
   /* while (!feof($myfile)){

        if(strstr(fgets($myfile),$currentDate)){
            $i++;
        }
        if($i>=$limit){
            echo "每天只能发布两个活动。";
            //return;
        }
    }*/


    //save in the total order Path


    $currentDate=date('Ymd');

    if(!is_dir($orderPath)){

        mkdir( $orderPath,0777,true) /*or die($orderPath)*/;

    }
    else{
        echo "Already exist such id.";
    }



    $eventFile = fopen($eventFilePath, "w") /*or die( $eventFilePath)*/;

    $member=fopen($orderPath."/member.txt",'a');
    fwrite($member,$username."\n");
    fwrite($eventFile,$info);
    fclose($member);
    fclose($eventFile);
    //save in the users root.
    if(!file_exists("Users/".$username)){
        mkdir("Users/".$username,0777,true) or die(2);
    }

    $myfile = fopen("Users/".$username."/creat.txt", "a") or die( 0);
    fwrite($myfile,$id."\n");
    echo $id;


    return;
}
function get_allfiles($path,&$files) {
    if(is_dir($path)){
        $dp = dir($path);
        while ($file = $dp ->read()){
            if($file !="." && $file !=".."){
                get_allfiles($path."/".$file, $files);
            }
        }
        $dp ->close();
    }
    if(is_file($path)){
        $files[] =  $path;
    }
}

function get_filenamesbydir($dir){
    $files =  array();
    get_allfiles($dir,$files);
    return $files;
}
function getSingleEvent($id){

    $orderpath="Orders/".$id."/";
    $eventfile=$orderpath.$id.".txt";

    if(!file_exists($eventfile)){
        // echo "no file found at " . $eventfile."\n";
        return -1;
    }

    $file=fopen($eventfile,"r") or die(-1);
    $arr=array();
    while(!feof($file)){
        $str= fgets($file);

        $tmpArr=explode(":",$str,2);

        $tmpArr[1]=substr($tmpArr[1],0,-1);
        if($tmpArr[1]==""||$tmpArr[1]=="undefined"){
            $tmpArr[1]="unset";
        }

        if($tmpArr[0]==""){
            continue;
        }

        $arr[$tmpArr[0]]=$tmpArr[1];

    }
    if(!file_exists($orderpath."member.txt")){
        // echo "no file found at " . $eventfile."\n";
        return -1;
    }
    $file=fopen($orderpath."member.txt","r") or die(-1);
    $user=0;
    while (!feof($file)){
        $tmp=fgets($file);
        //echo $tmp;
        $tmp=substr($tmp,0,-1);
        if($tmp==$cid){
            $arr['isMember']=true;
        }
        if($tmp!=""){
            $user++;
        }

    }
    $arr["personCount"]=$user;
    $arr["id"]=$id;
    return $arr;
}
if($_GET['q']==2){



    $filenames = get_filenamesbydir("Orders");
    $arr=array();


    foreach ($filenames as $value) {
        $word=array();
        $filer=fopen($value,"r") or die();
        fgets($filer);


        array_push($word,fgets($filer));
        $test=fgets($filer);
        if($test==false){
            continue;
        }
        array_push($word,$test);
        $name=fgets($filer);
        $dis=fgets($filer);
        fgets($filer);
        fgets($filer);
        fgets($filer);
        fgets($filer);
        fgets($filer);
        $add=fgets($filer);
        fgets($filer);
        fclose($filer);


        array_push($arr,array('name' => $name,
            'position'=>$word,
            'address'=>$add,
            'discribe'=>$dis,
            'id'=>$value
            ));
    }

    echo json_encode($arr);

}
function getDirName($dir){
    $handler = opendir($dir);
    while (($filename = readdir($handler)) !== false) {//务必使用!==，防止目录下出现类似文件名“0”等情况
        if ($filename != "." && $filename != "..") {
            $files[] = $filename ;
        }
    }

    closedir($handler);
    return $files;
}
if($_GET['q']==3){



    $filenames = getDirName("Orders");


    $arr=array();

    foreach ($filenames as $value) {

        $word=getSingleEvent($value);
        if($word!=-1){
            array_push($arr,$word);
        }


    }

    echo json_encode($arr);

}
