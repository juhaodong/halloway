<?php
$Server_Http_Path="www.halloway.net";
if(!empty($_FILES['photos'])){
    $up_arr['logo'] = upload_logo('photos','manage/images/user','logo',$user_id,0);
    $up_arr['logo'] = $up_arr['logo'][0];
    $user_info['logo'] = $Server_Http_Path . $up_arr['logo'];
}

if( !function_exists('upload_logo')){
    function upload_logo( $key_name='photos', $logo_path='manage/images/nurse', $pre_name='logo', $salt='20160101',$encode = 1,$make=0 ){
        $result_arr = array();
        global $Server_Http_Path,$App_Error_Conf;
        //分文件夹保存
        $date_info = getdate();
        $year = $date_info['year'];
        $mon = $date_info['mon'];
        $day = $date_info['mday'];
        $logo_path = sprintf("%s/%s/%s/%s",$logo_path,$year,$mon,$day);
        if(!is_dir($logo_path)){
            $res=mkdir($logo_path,0777,true);
        }
        //图片上传
        //foreach($photos as $key => $photo ){
        $photo = $_FILES['photos'];
        $name = $key_name;

        $file_id = Input::file($name);
        if(!empty($file_id) && $file_id -> isValid()){
            $entension = $file_id -> getClientOriginalExtension();
            if($pre_name == 'baby'){
                $new_name = $pre_name . "_" . intval($salt) ."_" .time() . "_" . salt_rand(2,2);
            }else {
                $new_name = $pre_name . "_" . intval($salt) ."_" . salt_rand(2,2);
            }
            $path_id = $file_id -> move($logo_path,$new_name."_b.".$entension);
            if(!empty($path_id)){
                $path_name = $path_id->getPathName();
                $image_size=getimagesize($path_name);
                $weight=$image_size["0"];//
                $height=$image_size["1"];
                ///获取图片的高
                $photo_info['url']    = $path_name;
                $photo_info['width']  = $weight;
                $photo_info['height'] = $height;
                $result_arr[] = $photo_info;
            }else{
                $result_arr[] = $path_name;
            }
//处理图片
            if($make == 1){
                $img = Image::make($path_name)->resize(200, $height*200/$weight);
                $img->save($logo_path ."/".$new_name."_s.".$entension);
                //dd($img);
                //  return $img->response('jpg');
            }
        }
        if(empty($result_arr)){
            $response['result_code'] = -1006;
            $response['result_msg'] = $App_Error_Conf[-1006];
            return response($response);
        }
        if($encode == 1){
            $result_arr = json_encode($result_arr);
        }
    }
    return $result_arr;
}

