<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {
    // 构造方法
    public function _initialize(){
      // uid
      if( $_SESSION['userinfo']['uid'] ){
        // status

        if( $_SESSION['userinfo']['status'] == 1 ){
          echo alert('无法登录，原因是该用户已被冻结，请自行联系管理员！',U('Login/loading'),5);
          die;
        }
      }else{
        header("Location:".U('Login/loading'));
        die;
      }
      // 初始化控制器
      if(method_exists($this,'_auto'))
            $this->_auto();
        $info=M("site")->order("id desc")->find();

        $this->assign('info',$info);
    }
}