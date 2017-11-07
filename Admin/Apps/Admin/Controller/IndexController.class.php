<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends BaseController {
    // 构造方法
    public function _auto(){

    }
    // 后台首页
    public function index(){
      // GET
      if( IS_GET === true ){
        // display
        $this->display();
        die;
      }
    }
}