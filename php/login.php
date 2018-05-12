<?php

$code=$_GET['code'];
$appId = "wx147fa679cfc6cff7";
$secret = "d5c2080a4966091fb99f18aa65d71b28";
$url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appId . '&secret=' . $secret . '&js_code=' . $code . '&grant_type=authorization_code';

$html = file_get_contents($url);
echo $html;


