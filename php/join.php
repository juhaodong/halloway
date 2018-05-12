<?php
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');
$id=$_GET['id'];
$userId=$_GET['userId'];
$check=$_GET['check'];
$createrId=substr($_GET['createrId'],0,-1);

$orderPath="../Orders/";

    $username=$userId;
    $orderPath=$orderPath.$id;
    //save in the total order Path
    $member=fopen($orderPath."/member.txt",'r') or die($orderPath."/member.txt");
    while (!feof($member)){

        if($username==substr(fgets($member),0,-1)){
            echo "你已经在活动中了！";
            return;
        }


    }
    if(file_exists("../Users/".$userId."/checking.txt")){
        $myfile = fopen("../Users/".$userId."/checking.txt", "r") or die("../Users/".$userId."/wantIn.txt");
        while (!feof($myfile)){
            $tmp=substr(fgets($myfile),0,-1);
            //  echo "../Users/".$userId."/wantIn.txt".$id.$tmp;
            if($id==$tmp){
                echo "正在审核中，无需重复提交审核。";
                return;
            }

        }
    }

    if($check=="否"){
        $member=fopen($orderPath."/member.txt",'a') or die($orderPath."/member.txt");
        fwrite($member,$username."\n");
        if(!file_exists("../Users/".$username)){
            mkdir("../Users/".$username,0777,true) or die(2);
        }

        $myfile = fopen("../Users/".$username."/join.txt", "a") or die( 0);
        fwrite($myfile,$id."\n");
        echo "finish";

    }else{

        /*$member=fopen($orderPath."/watch.txt",'a') or die($orderPath."/watch.txt");
        fwrite($member,$username."\n");*/

        if(!is_dir("../Users/".$username)){
            mkdir("../Users/".$username,0777,true) ;


        }
        if(!is_dir("../Users/".$createrId)){
            mkdir("../Users/".$createrId,0777,true) ;
            //deldir("../Users/".$createrId);
            $log=error_get_last();
            echo $log['message']."ok";
            return;

        }

        $myfile = fopen("../Users/".$username."/checking.txt", "a") or die( 0);


        $info=fopen("../Users/".$createrId."/wantIn.txt","a") or die("../Users/".$createrId."/wantIn.txt");

        fwrite($myfile,$id."\n");
        fwrite($info,$username.",".$id."\n");
        echo "finish";
    }

