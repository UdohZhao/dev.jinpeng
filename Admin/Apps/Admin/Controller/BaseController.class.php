<?php
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller {
    // 构造方法
    public function _initialize(){
      // 初始化控制器

        if( $_SESSION['user']['uid'] ){
            // status
            if( $_SESSION['user']['status'] == 1 ){
                echo alert('无法登录，原因是该用户已被冻结，请自行联系管理员！',U('Login/loading'),5);
                die;
            }
        }else{
            header("Location:".U('Login/index'));
            die;
        }
        // 初始化控制器
      if(method_exists($this,'_auto'))
            $this->_auto();
    }
}