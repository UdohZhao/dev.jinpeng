<?php
namespace Wap\Controller;
use Think\Controller;
class BaseController extends Controller {
    // 构造方法
    public function _initialize(){
      // 初始化控制器

        if( $_SESSION['userinfo']['uid'] ){
            // status
            if( $_SESSION['userinfo']['status'] == 1 ){
                echo alert('无法登录，原因是该用户已被冻结，请自行联系管理员！',U('Login/loading'),5);
                die;
            }
        }else{
            header("Location:".U('Signinorlogin/index'));
            die;
        }
      if(method_exists($this,'_auto'))
            $this->_auto();
        $infoSite=M("site")->order("id desc")->find();

        $this->assign('infoSite',$infoSite);
    }
}