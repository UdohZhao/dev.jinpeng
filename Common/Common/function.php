<?php
/**
 * $msg 待提示的消息
 * $url 待跳转的链接
 * $icon 这里主要有两个，5和6，代表两种表情（哭和笑）
 * $time 弹出维持时间（单位秒）
 */
function alert($msg='',$url='',$icon='',$time=3){
    $str='<script type="text/javascript" src="/Admin/Apps/Admin/View/Layouts/assets/vendor/jquery/jquery.js"></script><script type="text/javascript" src="/Public/Common/layer/layer.js"></script>';//加载jquery和layer
    $str.='<script>$(function(){layer.alert("'.$msg.'",{icon:'.$icon.',time:'.($time*1000).'});setTimeout(function(){self.location.href="'.$url.'"},2000)});</script>';//主要方法
    return $str;
}
/**
 * 无敌加密
 */
function invincibleEncrypt($password){
    return md5(crypt($password,substr($password,0,2)));
}


function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}